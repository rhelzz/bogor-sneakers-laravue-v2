import { ref, onUnmounted, nextTick, watch } from 'vue';
import Konva from 'konva';
import { useStudioStore } from './useStudioStore';
import { useStudioHistory } from './useStudioHistory';
import { loadImage, drawFilledLayer, drawOutlineLayer } from '../utils/studio-utils';
import type { DesignElement } from '../types/studio';

const CANVAS_SIZE = 1024;
const MIN_STAGE_SCALE = 0.35;
const MAX_STAGE_SCALE = 4;
const WHEEL_ZOOM_SENSITIVITY = 0.0025;

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
    let viewFrame = 0;
    let pendingView: { scale: number; x: number; y: number } | null = null;
    let activeViewTween: Konva.Tween | null = null;

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

        mainLayer = new Konva.Layer({
            imageSmoothingEnabled: true
        });
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

    const clampScale = (scale: number) => Math.min(MAX_STAGE_SCALE, Math.max(MIN_STAGE_SCALE, scale));

    const drawMainLayer = () => {
        mainLayer?.batchDraw();
    };

    const applyStageView = (scale: number, position: { x: number; y: number }) => {
        if (!stage) return;

        stage.scale({ x: scale, y: scale });
        stage.position(position);
        drawMainLayer();
    };

    const scheduleStageView = (scale: number, position: { x: number; y: number }) => {
        pendingView = {
            scale,
            x: position.x,
            y: position.y
        };

        if (viewFrame) {
            return;
        }

        viewFrame = window.requestAnimationFrame(() => {
            viewFrame = 0;

            if (!pendingView) {
                return;
            }

            applyStageView(pendingView.scale, {
                x: pendingView.x,
                y: pendingView.y
            });
            pendingView = null;
        });
    };

    const stopStageTweens = () => {
        activeViewTween?.destroy();
        activeViewTween = null;
    };

    const zoomToPoint = (nextScale: number, point: { x: number; y: number }, animated = false, duration = 0.25) => {
        if (!stage) return;

        const oldScale = pendingView?.scale ?? stage.scaleX();
        const oldPosition = {
            x: pendingView?.x ?? stage.x(),
            y: pendingView?.y ?? stage.y()
        };
        const newScale = clampScale(nextScale);

        if (Math.abs(newScale - oldScale) < 0.001) {
            return;
        }

        const stagePoint = {
            x: (point.x - oldPosition.x) / oldScale,
            y: (point.y - oldPosition.y) / oldScale,
        };
        const position = {
            x: point.x - stagePoint.x * newScale,
            y: point.y - stagePoint.y * newScale,
        };

        stopStageTweens();

        if (!animated) {
            scheduleStageView(newScale, position);
            return;
        }

        pendingView = null;

        activeViewTween = new Konva.Tween({
            node: stage,
            duration,
            easing: Konva.Easings.EaseOut,
            scaleX: newScale,
            scaleY: newScale,
            x: position.x,
            y: position.y,
            onFinish: () => {
                drawMainLayer();
                activeViewTween?.destroy();
                activeViewTween = null;
            }
        });
        activeViewTween.play();
    };

    const zoomBy = (factor: number, animated = true) => {
        if (!stage) return;

        zoomToPoint(stage.scaleX() * factor, {
            x: stage.width() / 2,
            y: stage.height() / 2
        }, animated);
    };

    const resetView = () => {
        if (!stage) return;

        stopStageTweens();

        activeViewTween = new Konva.Tween({
            node: stage,
            duration: 0.35,
            easing: Konva.Easings.EaseOut,
            scaleX: 1,
            scaleY: 1,
            x: 0,
            y: 0,
            onFinish: () => {
                drawMainLayer();
                activeViewTween?.destroy();
                activeViewTween = null;
            }
        });
        activeViewTween.play();
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
            drawMainLayer();
        });

        stage.draggable(true);

        stage.on('wheel', (e) => {
            e.evt.preventDefault();
            if (!stage) return;

            const pointer = stage.getPointerPosition();

            if (!pointer) {
                return;
            }

            const delta = Math.max(-60, Math.min(60, e.evt.deltaY));
            const factor = Math.exp(-delta * WHEEL_ZOOM_SENSITIVITY);
            zoomToPoint((pendingView?.scale ?? stage.scaleX()) * factor, pointer, false);
        });
    };

    const deselect = () => {
        transformer?.nodes([]);
        activeElement.value = null;
        drawMainLayer();
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
        drawMainLayer();
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
                drawMainLayer();
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

                const hitCanvas = document.createElement('canvas');
                hitCanvas.width = 1;
                hitCanvas.height = 1;
                const hitCtx = hitCanvas.getContext('2d');

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

                        if (!hitCtx) return;
                        hitCtx.clearRect(0, 0, 1, 1);
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

            drawMainLayer();
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

    const createPreviewURL = async (checkoutData?: any): Promise<string> => {
        if (!stage) return '';

        // Temporary hide transformer
        const oldNodes = transformer?.nodes() || [];
        transformer?.nodes([]);
        mainLayer?.draw();

        // 1. Capture the raw design
        const designDataURL = stage.toDataURL({ 
            pixelRatio: 2,
            mimeType: 'image/png',
            quality: 1
        });

        // Restore UI
        transformer?.nodes(oldNodes);
        mainLayer?.draw();

        if (!checkoutData) return designDataURL;

        // 2. Create a high-end branded Result Card
        const cardCanvas = document.createElement('canvas');
        const W = 1600;
        const H = 2000;
        cardCanvas.width = W;
        cardCanvas.height = H;
        const ctx = cardCanvas.getContext('2d');
        if (!ctx) return designDataURL;

        // Background: Soft Studio Gray
        ctx.fillStyle = '#F5F5F7';
        ctx.fillRect(0, 0, W, H);

        // Header: Brand Label
        ctx.fillStyle = '#1A1A1A';
        ctx.font = '900 48px Montserrat, Lexend, sans-serif';
        ctx.letterSpacing = '12px';
        ctx.textAlign = 'center';
        ctx.fillText('BOGOR SNEAKERS', W / 2, 100);
        
        ctx.fillStyle = '#1A1A1A40';
        ctx.font = '700 20px Montserrat, Lexend, sans-serif';
        ctx.letterSpacing = '4px';
        ctx.fillText('CUSTOM STUDIO EXCLUSIVE', W / 2, 140);

        // Main Design Area (Centered)
        const designImg = await loadImage(designDataURL);
        const designW = W * 0.9;
        const designH = designW * (designImg.height / designImg.width);
        ctx.drawImage(designImg, (W - designW) / 2, 250, designW, designH);

        // Info Section (Bottom Card)
        const infoY = H - 550;
        ctx.fillStyle = '#FFFFFF';
        ctx.beginPath();
        ctx.roundRect(100, infoY, W - 200, 450, 60);
        ctx.fill();
        
        // Shadow effect for the info card
        ctx.shadowColor = 'rgba(0,0,0,0.05)';
        ctx.shadowBlur = 40;
        ctx.shadowOffsetY = 20;

        // Divider
        ctx.strokeStyle = '#F0F0F0';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(W / 2, infoY + 60);
        ctx.lineTo(W / 2, infoY + 390);
        ctx.stroke();

        ctx.shadowBlur = 0; // Reset shadow

        // Left Side: Order Details
        ctx.textAlign = 'left';
        ctx.fillStyle = '#1A1A1A40';
        ctx.font = '900 16px Montserrat, sans-serif';
        ctx.letterSpacing = '3px';
        ctx.fillText('ORDER DETAILS', 160, infoY + 100);

        ctx.fillStyle = '#1A1A1A';
        ctx.font = '900 42px Montserrat, sans-serif';
        ctx.letterSpacing = '-1px';
        ctx.fillText(checkoutData.name.toUpperCase(), 160, infoY + 170);

        ctx.fillStyle = '#1A1A1A80';
        ctx.font = '700 24px Montserrat, sans-serif';
        ctx.fillText(`${activeFolderKey.value} • ${currentModelMeta.value?.label}`, 160, infoY + 220);
        
        ctx.fillStyle = '#4F46E5'; // Indigo
        ctx.font = '800 28px Montserrat, sans-serif';
        ctx.fillText(`SIZE ${checkoutData.shoeSize}`, 160, infoY + 320);

        // Right Side: Verification
        ctx.textAlign = 'right';
        ctx.fillStyle = '#1A1A1A40';
        ctx.font = '900 16px Montserrat, sans-serif';
        ctx.letterSpacing = '3px';
        ctx.fillText('STATUS', W - 160, infoY + 100);

        ctx.fillStyle = '#10B981'; // Matcha/Emerald
        ctx.font = '900 32px Montserrat, sans-serif';
        ctx.fillText('VERIFIED DESIGN', W - 160, infoY + 170);

        const date = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        ctx.fillStyle = '#1A1A1A40';
        ctx.font = '700 20px Montserrat, sans-serif';
        ctx.fillText(date.toUpperCase(), W - 160, infoY + 220);

        // Bottom Footer
        ctx.textAlign = 'center';
        ctx.fillStyle = '#1A1A1A20';
        ctx.font = '800 14px Montserrat, sans-serif';
        ctx.letterSpacing = '4px';
        ctx.fillText('GENERATED BY BOGOR SNEAKERS CUSTOM STUDIO', W / 2, H - 100);

        return cardCanvas.toDataURL('image/png', 1.0);
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

    const destroyStage = () => {
        if (viewFrame) {
            window.cancelAnimationFrame(viewFrame);
            viewFrame = 0;
        }

        pendingView = null;
        stopStageTweens();
        stage?.destroy();
        stage = null;
        mainLayer = null;
        shoeGroup = null;
        elementsGroup = null;
        transformer = null;
    };

    onUnmounted(destroyStage);

    return {
        initStage,
        loadAssets,
        updateLayer,
        saveToHistory,
        zoomBy,
        resetView,
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
