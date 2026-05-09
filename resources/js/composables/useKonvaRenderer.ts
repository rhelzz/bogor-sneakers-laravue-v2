import { ref, onUnmounted, nextTick, watch } from 'vue';
import Konva from 'konva';
import { useStudioStore } from './useStudioStore';
import { useStudioHistory } from './useStudioHistory';
import { loadImage, drawFilledLayer, drawOutlineLayer } from '../utils/studio-utils';
import type { DesignElement } from '../types/studio';

const CANVAS_SIZE = 1024;

export function useKonvaRenderer() {
    const { 
        currentModelMeta, 
        activeFolderKey, 
        currentModel, 
        layerColors, 
        layerOutlines,
        activeElement,
        showToast
    } = useStudioStore();

    const { pushState, isRestoring } = useStudioHistory();

    const containerRef = ref<HTMLDivElement | null>(null);
    let stage: Konva.Stage | null = null;
    let mainLayer: Konva.Layer | null = null;
    let shoeGroup: Konva.Group | null = null;
    let elementsGroup: Konva.Group | null = null;
    let transformer: Konva.Transformer | null = null;
    let assetLoadVersion = 0;

    const layerSourceImages = ref<Record<number, HTMLImageElement>>({});
    const isSyncing = ref(false);

    const initStage = (container: HTMLDivElement) => {
        containerRef.value = container;
        const width = container.clientWidth;
        const height = container.clientHeight;

        stage = new Konva.Stage({
            container,
            width,
            height,
        });

        const pixelRatio = window.devicePixelRatio || 1;
        stage.setAttr('pixelRatio', pixelRatio);

        mainLayer = new Konva.Layer();
        stage.add(mainLayer);

        shoeGroup = new Konva.Group();
        mainLayer.add(shoeGroup);

        elementsGroup = new Konva.Group();
        mainLayer.add(elementsGroup);

        transformer = new Konva.Transformer({
            nodes: [],
            keepRatio: true,
            rotateAnchorOffset: 40,
            anchorFill: '#fff',
            anchorStroke: '#000',
            anchorSize: 10,
            anchorCornerRadius: 4,
            borderStroke: '#000',
            borderDash: [5, 5],
            boundBoxFunc: (oldBox, newBox) => {
                if (newBox.width < 20 || newBox.height < 20) return oldBox;
                return newBox;
            }
        });
        mainLayer.add(transformer);

        setupEvents();
    };

    const setupEvents = () => {
        if (!stage) return;

        stage.on('click tap', (e) => {
            if (e.target === stage) {
                deselect();
                return;
            }

            const parent = e.target.getParent();
            const target = e.target as Konva.Node;

            if (parent === shoeGroup) {
                // Layer selection logic
                const layerId = target.getAttr('layerId');
                if (layerId !== undefined) {
                    // This will be handled by UI observing activeLayerPickId
                }
                deselect();
            } else if (parent === elementsGroup) {
                const meta = target.getAttr('meta');
                if (meta) {
                    activeElement.value = meta as DesignElement;
                    transformer?.nodes([target as Konva.Shape]);
                }
            }
            mainLayer?.draw();
        });

        stage.draggable(true);

        stage.on('wheel', (e) => {
            e.evt.preventDefault();
            const oldScale = stage!.scaleX();
            const pointer = stage!.getPointerPosition()!;

            const mousePointTo = {
                x: (pointer.x - stage!.x()) / oldScale,
                y: (pointer.y - stage!.y()) / oldScale,
            };

            const newScale = e.evt.deltaY < 0 ? oldScale * 1.05 : oldScale / 1.05;
            stage!.scale({ x: newScale, y: newScale });

            const newPos = {
                x: pointer.x - mousePointTo.x * newScale,
                y: pointer.y - mousePointTo.y * newScale,
            };
            stage!.position(newPos);
            mainLayer?.draw();
        });
    };

    const deselect = () => {
        transformer?.nodes([]);
        activeElement.value = null;
        mainLayer?.draw();
    };

    const saveToHistory = () => {
        if (isRestoring.value) return;

        const elements = elementsGroup?.children?.map(node => {
            const obj = node.toObject();
            if (node instanceof Konva.Image && node.image()) {
                const img = node.image() as any;
                obj.attrs.imageSrc = img instanceof HTMLCanvasElement ? img.toDataURL() : img.src;
            }
            return obj;
        }) || [];

        pushState({
            colors: { ...layerColors.value },
            outlines: JSON.parse(JSON.stringify(layerOutlines.value)),
            elements
        });
    };

    const updateLayer = (id: number) => {
        const img = layerSourceImages.value[id];
        if (!img || !shoeGroup) return;

        // More robust lookup
        const konvaLayers = shoeGroup.getChildren()
            .filter(node => node.name() === `layer-${id}`) as Konva.Image[];

        if (konvaLayers.length === 0) {
            return;
        }

        const canvas = document.createElement('canvas');
        canvas.width = CANVAS_SIZE;
        canvas.height = CANVAS_SIZE;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const outline = layerOutlines.value[id];
        if (outline?.active) {
            const outlineCanvas = drawOutlineLayer(img, outline.color, outline.size, CANVAS_SIZE, CANVAS_SIZE);
            ctx.drawImage(outlineCanvas, 0, 0);
        }

        const color = layerColors.value[id] || '#ffffff';
        const filledCanvas = drawFilledLayer(img, color, CANVAS_SIZE, CANVAS_SIZE);
        ctx.drawImage(filledCanvas, 0, 0);

        konvaLayers.forEach(konvaImg => konvaImg.image(canvas));
        mainLayer?.draw();
    };

    // Auto-sync store to canvas
    watch(layerColors, (newColors) => {
        Object.keys(newColors).forEach(id => updateLayer(Number(id)));
    }, { deep: true });

    watch(layerOutlines, (newOutlines) => {
        Object.keys(newOutlines).forEach(id => updateLayer(Number(id)));
    }, { deep: true });

    const loadAssets = async () => {
        const loadVersion = ++assetLoadVersion;
        const isStaleLoad = () => loadVersion !== assetLoadVersion;

        if (!currentModelMeta.value || !shoeGroup || !stage) {
            if (shoeGroup && stage) {
                shoeGroup.destroyChildren();
                elementsGroup?.destroyChildren();
                layerSourceImages.value = {};
                mainLayer?.draw();
            }

            isSyncing.value = false;

            return;
        }

        isSyncing.value = true;
        shoeGroup.destroyChildren();
        elementsGroup?.destroyChildren();
        layerSourceImages.value = {};

        try {
            const meta = currentModelMeta.value;
            const baseUrl = `/shoes-svg/${activeFolderKey.value}/${currentModel.value}/`;
            const baseFile = meta.preview_base_file || meta.main_file;

            if (baseFile) {
                const img = await loadImage(baseUrl + baseFile);

                if (isStaleLoad()) {
                    return;
                }

                shoeGroup.add(new Konva.Image({
                    image: img,
                    width: CANVAS_SIZE,
                    height: CANVAS_SIZE,
                    x: (stage.width() - CANVAS_SIZE) / 2,
                    y: (stage.height() - CANVAS_SIZE) / 2,
                    listening: false
                }));
            }

            for (const layer of meta.layers) {
                const img = await loadImage(baseUrl + layer.file);

                if (isStaleLoad()) {
                    return;
                }

                layerSourceImages.value[layer.id] = img;

                if (!layerOutlines.value[layer.id]) {
                    layerOutlines.value[layer.id] = { active: false, color: '#000000', size: 2 };
                }

                const konvaLayer = new Konva.Image({
                    image: img,
                    width: CANVAS_SIZE,
                    height: CANVAS_SIZE,
                    x: (stage.width() - CANVAS_SIZE) / 2,
                    y: (stage.height() - CANVAS_SIZE) / 2,
                    name: `layer-${layer.id}`,
                    layerId: layer.id,
                    hitFunc: (context, shape) => {
                        const canvas = (shape as Konva.Image).image() as HTMLCanvasElement;
                        if (!canvas) return;
                        const pointer = stage!.getPointerPosition();
                        if (!pointer) return;
                        const transform = shape.getAbsoluteTransform().copy().invert();
                        const point = transform.point(pointer);
                        
                        const hitCanvas = document.createElement('canvas');
                        hitCanvas.width = 1; hitCanvas.height = 1;
                        const hitCtx = hitCanvas.getContext('2d')!;
                        hitCtx.drawImage(canvas, point.x, point.y, 1, 1, 0, 0, 1, 1);
                        if (hitCtx.getImageData(0, 0, 1, 1).data[3] > 10) {
                            context.beginPath(); context.rect(0, 0, shape.width(), shape.height());
                            context.closePath(); context.fillShape(shape);
                        }
                    }
                });
                shoeGroup.add(konvaLayer);
                updateLayer(layer.id);
            }

            if (isStaleLoad()) {
                return;
            }

            mainLayer?.draw();
            saveToHistory();
        } catch (err) {
            if (!isStaleLoad()) {
                console.error(err);
                showToast('Gagal memuat aset model');
            }
        } finally {
            if (!isStaleLoad()) {
                isSyncing.value = false;
            }
        }
    };

    const createPreviewURL = async (): Promise<string> => {
        if (!stage) return '';

        const oldNodes = transformer?.nodes() || [];
        transformer?.nodes([]);
        mainLayer?.draw();

        const dataURL = stage.toDataURL({ pixelRatio: 2 });
        transformer?.nodes(oldNodes);
        mainLayer?.draw();

        return dataURL;
    };

    const createPatternURL = async (): Promise<string> => {
        if (!currentModelMeta.value) return '';

        const meta = currentModelMeta.value;
        const baseUrl = `/shoes-svg/${activeFolderKey.value}/${currentModel.value}/`;

        const patternCanvas = document.createElement('canvas');
        patternCanvas.width = 2048;
        patternCanvas.height = 2048;
        const ctx = patternCanvas.getContext('2d');
        if (!ctx) return '';

        if (meta.pattern_base_file) {
            const pBase = await loadImage(baseUrl + meta.pattern_base_file);
            ctx.drawImage(pBase, 0, 0, 2048, 2048);
        }

        for (const layer of meta.pattern_layers) {
            const pLayerImg = await loadImage(baseUrl + layer.file);
            const color = layerColors.value[layer.id] || '#ffffff';
            const filled = drawFilledLayer(pLayerImg, color, 2048, 2048);
            ctx.drawImage(filled, 0, 0);

            const outline = layerOutlines.value[layer.id];
            if (outline?.active) {
                const out = drawOutlineLayer(pLayerImg, outline.color, outline.size * 2, 2048, 2048);
                ctx.drawImage(out, 0, 0);
            }
        }

        return patternCanvas.toDataURL('image/png');
    };

    const downloadURL = (url: string, filename: string) => {
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
    };

    return {
        initStage,
        loadAssets,
        updateLayer,
        saveToHistory,
        createPreviewURL,
        createPatternURL,
        downloadURL,
        isSyncing,
        getStage: () => stage,
        getMainLayer: () => mainLayer,
        getElementsGroup: () => elementsGroup,
        getTransformer: () => transformer
    };
}
