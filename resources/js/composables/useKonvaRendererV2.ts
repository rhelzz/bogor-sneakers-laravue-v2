import Konva from 'konva';
import { ref, onUnmounted } from 'vue';

import type { ShoeTypeCatalog } from '../data/studio-v2-catalog';
import { loadImage, drawFilledLayer } from '../utils/studio-utils';

const CANVAS_SIZE = 1024;
const MIN_STAGE_SCALE = 0.2;
const MAX_STAGE_SCALE = 4;
const WHEEL_ZOOM_SENSITIVITY = 0.0025;

// Base model is always m1 — the shoe silhouette never changes when cycling accents.
const BASE_MODEL = 1;

export function useKonvaRendererV2() {
    const containerRef = ref<HTMLDivElement | null>(null);
    const isSyncing = ref(false);

    let stage: Konva.Stage | null = null;
    let mainLayer: Konva.Layer | null = null;
    let shoeGroup: Konva.Group | null = null;
    let resizeObserver: ResizeObserver | null = null;
    let loadVersion = 0;
    let viewFrame = 0;
    let pendingView: { scale: number; x: number; y: number } | null = null;
    let activeViewTween: Konva.Tween | null = null;

    // Cache: "m{model}_aksen{slot}" → HTMLImageElement
    const imageCache = new Map<string, HTMLImageElement>();

    // Current source image per slot — for instant recolorization without re-fetch
    const slotSourceImages: Record<number, HTMLImageElement> = {};

    const cacheKey = (model: number, slot: number) => `m${model}_aksen${slot}`;

    const fetchImage = async (src: string, key?: string): Promise<HTMLImageElement> => {
        if (key && imageCache.has(key)) {
            return imageCache.get(key)!;
        }

        const img = await loadImage(src);

        if (key) {
            imageCache.set(key, img);
        }

        return img;
    };

    const fetchImageOptional = async (src: string): Promise<HTMLImageElement | null> => {
        try {
            return await fetchImage(src);
        } catch {
            return null;
        }
    };

    // ─── Resize / View ───────────────────────────────────────────────────────

    const handleResize = () => {
        if (!stage || !containerRef.value) {
            return;
        }

        const w = containerRef.value.clientWidth;
        const h = containerRef.value.clientHeight;

        if (w === stage.width() && h === stage.height()) {
            return;
        }

        stage.width(w);
        stage.height(h);
        const padding = Math.min(w, h) * 0.1;
        const fitScale = Math.min((w - padding) / CANVAS_SIZE, (h - padding) / CANVAS_SIZE);
        const s = Math.min(1, Math.max(MIN_STAGE_SCALE, fitScale));
        applyView(s, { x: (w / 2) * (1 - s), y: (h / 2) * (1 - s) });
    };

    const applyView = (scale: number, pos: { x: number; y: number }) => {
        if (!stage) {
            return;
        }

        stage.scale({ x: scale, y: scale });
        stage.position(pos);
        mainLayer?.batchDraw();
    };

    const scheduleView = (scale: number, pos: { x: number; y: number }) => {
        pendingView = { scale, x: pos.x, y: pos.y };

        if (viewFrame) {
            return;
        }

        viewFrame = window.requestAnimationFrame(() => {
            viewFrame = 0;

            if (!pendingView) {
                return;
            }

            applyView(pendingView.scale, { x: pendingView.x, y: pendingView.y });
            pendingView = null;
        });
    };

    const clamp = (v: number) => Math.min(MAX_STAGE_SCALE, Math.max(MIN_STAGE_SCALE, v));

    const stopTween = () => {
        activeViewTween?.destroy();
        activeViewTween = null;
    };

    const zoomToPoint = (nextScale: number, point: { x: number; y: number }, animated = false) => {
        if (!stage) {
            return;
        }

        const oldScale = pendingView?.scale ?? stage.scaleX();
        const oldPos = { x: pendingView?.x ?? stage.x(), y: pendingView?.y ?? stage.y() };
        const newScale = clamp(nextScale);

        if (Math.abs(newScale - oldScale) < 0.001) {
            return;
        }

        const sp = { x: (point.x - oldPos.x) / oldScale, y: (point.y - oldPos.y) / oldScale };
        const pos = { x: point.x - sp.x * newScale, y: point.y - sp.y * newScale };

        stopTween();

        if (!animated) {
            scheduleView(newScale, pos);

            return;
        }

        pendingView = null;
        activeViewTween = new Konva.Tween({
            node: stage, duration: 0.3, easing: Konva.Easings.EaseOut,
            scaleX: newScale, scaleY: newScale, x: pos.x, y: pos.y,
            onFinish: () => {
                mainLayer?.batchDraw();
                stopTween();
            },
        });
        activeViewTween.play();
    };

    const zoomBy = (factor: number, animated = true) => {
        if (!stage) {
            return;
        }

        zoomToPoint(stage.scaleX() * factor, { x: stage.width() / 2, y: stage.height() / 2 }, animated);
    };

    const resetView = () => {
        if (!stage) {
            return;
        }

        stopTween();
        const padding = Math.min(stage.width(), stage.height()) * 0.1;
        const fitScale = Math.min(
            (stage.width() - padding) / CANVAS_SIZE,
            (stage.height() - padding) / CANVAS_SIZE
        );
        const s = Math.min(1, Math.max(MIN_STAGE_SCALE, fitScale));
        activeViewTween = new Konva.Tween({
            node: stage, duration: 0.35, easing: Konva.Easings.EaseOut,
            scaleX: s, scaleY: s,
            x: (stage.width() / 2) * (1 - s),
            y: (stage.height() / 2) * (1 - s),
            onFinish: () => {
                mainLayer?.batchDraw();
                stopTween();
            },
        });
        activeViewTween.play();
    };

    // ─── Stage Init ──────────────────────────────────────────────────────────

    const initStage = (container: HTMLDivElement) => {
        containerRef.value = container;

        stage = new Konva.Stage({
            container,
            width: container.clientWidth,
            height: container.clientHeight,
        });
        stage.setAttr('pixelRatio', window.devicePixelRatio || 1);

        mainLayer = new Konva.Layer({ imageSmoothingEnabled: true });
        stage.add(mainLayer);

        shoeGroup = new Konva.Group();
        mainLayer.add(shoeGroup);

        stage.draggable(true);
        stage.on('wheel', (e) => {
            e.evt.preventDefault();
            const pointer = stage!.getPointerPosition();

            if (!pointer) {
                return;
            }

            const delta = Math.max(-60, Math.min(60, e.evt.deltaY));
            const factor = Math.exp(-delta * WHEEL_ZOOM_SENSITIVITY);
            zoomToPoint((pendingView?.scale ?? stage!.scaleX()) * factor, pointer, false);
        });

        resizeObserver?.disconnect();
        resizeObserver = new ResizeObserver(handleResize);
        resizeObserver.observe(container);
    };

    // ─── Canvas helpers ───────────────────────────────────────────────────────

    const shoeOffset = () => ({
        x: stage ? (stage.width() - CANVAS_SIZE) / 2 : 0,
        y: stage ? (stage.height() - CANVAS_SIZE) / 2 : 0,
    });

    const makeColoredCanvas = (img: HTMLImageElement, color: string): HTMLCanvasElement =>
        drawFilledLayer(img, color, CANVAS_SIZE, CANVAS_SIZE);

    // ─── Load / Update ────────────────────────────────────────────────────────

    /**
     * Full initial load — loads base, all enabled accent slots, and overlay in PARALLEL.
     * Uses catalog.allAccentSlots (exact files on disk) so gaps are handled correctly.
     */
    const loadAllLayers = async (
        catalog: ShoeTypeCatalog,
        accentModels: Record<number, number>,
        accentColors: Record<number, string>,
        accentEnabled: Record<number, boolean> = {}
    ) => {
        if (!stage || !shoeGroup) {
            return;
        }

        const version = ++loadVersion;
        const stale = () => version !== loadVersion;

        isSyncing.value = true;
        shoeGroup.destroyChildren();
        Object.keys(slotSourceImages).forEach(k => delete slotSourceImages[Number(k)]);
        imageCache.clear();

        try {
            const { x, y } = shoeOffset();

            // Only load slots that exist in the catalog AND are enabled
            const validSlots = catalog.allAccentSlots
                .map(slot => {
                    const model = accentModels[slot];

                    return (model
                        && catalog.accentSlots[model]?.includes(slot)
                        && accentEnabled[slot] !== false)
                        ? { slot, model }
                        : null;
                })
                .filter((s): s is { slot: number; model: number } => s !== null);

            const [baseImg, overlayImg, ...accentImgs] = await Promise.all([
                fetchImage(catalog.baseSvg(BASE_MODEL)),
                fetchImageOptional(catalog.overlaySvg(BASE_MODEL)),
                ...validSlots.map(({ slot, model }) =>
                    fetchImage(catalog.aksenSvg(model, slot), cacheKey(model, slot))
                ),
            ]);

            if (stale()) {
                return;
            }

            shoeGroup.add(new Konva.Image({
                image: baseImg,
                width: CANVAS_SIZE, height: CANVAS_SIZE, x, y,
                name: 'base', listening: false,
            }));

            validSlots.forEach(({ slot }, i) => {
                const img = accentImgs[i] as HTMLImageElement;
                slotSourceImages[slot] = img;

                shoeGroup!.add(new Konva.Image({
                    image: makeColoredCanvas(img, accentColors[slot] || '#ffffff'),
                    width: CANVAS_SIZE, height: CANVAS_SIZE, x, y,
                    name: `aksen-${slot}`,
                    listening: false,
                }));
            });

            if (overlayImg) {
                shoeGroup.add(new Konva.Image({
                    image: overlayImg,
                    width: CANVAS_SIZE, height: CANVAS_SIZE, x, y,
                    name: 'overlay', listening: false,
                }));
            }

            mainLayer?.draw();
            resetView();
        } catch (err) {
            if (!stale()) {
                console.error('[V2] loadAllLayers error:', err);
            }
        } finally {
            if (!stale()) {
                isSyncing.value = false;
            }
        }
    };

    /**
     * Swap one accent slot to a different model variant.
     * Canvas stays visible — no isSyncing spinner.
     */
    const updateSlotModel = async (
        catalog: ShoeTypeCatalog,
        slot: number,
        modelNum: number,
        color: string
    ) => {
        if (!shoeGroup) {
            return;
        }

        try {
            const key = cacheKey(modelNum, slot);
            const img = await fetchImage(catalog.aksenSvg(modelNum, slot), key);

            if (!shoeGroup) {
                return;
            }

            slotSourceImages[slot] = img;
            const colored = makeColoredCanvas(img, color);

            const existing = shoeGroup.findOne<Konva.Image>(`.aksen-${slot}`);

            if (existing) {
                existing.image(colored);
            } else {
                const { x, y } = shoeOffset();

                const node = new Konva.Image({
                    image: colored,
                    width: CANVAS_SIZE, height: CANVAS_SIZE, x, y,
                    name: `aksen-${slot}`, listening: false,
                });
                shoeGroup.add(node);
                shoeGroup.findOne('.overlay')?.moveToTop();
            }

            mainLayer?.draw();
        } catch (err) {
            console.error('[V2] updateSlotModel error:', err);
        }
    };

    /**
     * Recolorize an already-loaded slot without re-fetching the SVG.
     * Instant — no async, no spinner.
     */
    const updateSlotColor = (slot: number, color: string) => {
        const img = slotSourceImages[slot];

        if (!img || !shoeGroup) {
            return;
        }

        const existing = shoeGroup.findOne<Konva.Image>(`.aksen-${slot}`);

        if (existing) {
            existing.image(makeColoredCanvas(img, color));
            mainLayer?.draw();
        }
    };

    /**
     * Show or hide a slot node without re-fetching.
     * Returns true if the slot needs to be loaded (was skipped during initial load).
     */
    const setSlotEnabled = (slot: number, enabled: boolean): boolean => {
        const node = shoeGroup?.findOne<Konva.Image>(`.aksen-${slot}`);

        if (!enabled) {
            if (node) {
                node.visible(false);
                mainLayer?.draw();
            }

            return false;
        }

        if (node) {
            node.visible(true);

            mainLayer?.draw();

            return false;
        }

        return true; // no node exists — slot was skipped; caller must load it
    };

    // ─── Export ───────────────────────────────────────────────────────────────

    const createPreviewURL = (): string => {
        if (!stage) {
            return '';
        }

        const oldScale = stage.scaleX();
        const oldPos = stage.position();
        const { x, y } = shoeOffset();

        stage.scale({ x: 1, y: 1 });
        stage.position({ x: -x, y: -y });
        mainLayer?.draw();

        const url = stage.toDataURL({
            x: 0, y: 0,
            width: CANVAS_SIZE, height: CANVAS_SIZE,
            pixelRatio: 2,
            mimeType: 'image/png',
        });

        stage.scale({ x: oldScale, y: oldScale });
        stage.position(oldPos);
        mainLayer?.draw();

        return url;
    };

    const createPatternURL = async (
        catalog: ShoeTypeCatalog,
        accentModels: Record<number, number>,
        accentColors: Record<number, string>,
        accentEnabled: Record<number, boolean> = {},
        layerOrder: number[] = []
    ): Promise<string> => {
        const TARGET = 2048;
        const canvas = document.createElement('canvas');
        canvas.width = TARGET;
        canvas.height = TARGET;
        const ctx = canvas.getContext('2d');

        if (!ctx) {
            return '';
        }

        const pBase = await fetchImage(catalog.polaBaseSvg(BASE_MODEL));
        ctx.drawImage(pBase, 0, 0, TARGET, TARGET);

        const allPolaSlots = new Set(Object.values(catalog.polaSlots).flat());

        // Draw bottom-to-top matching canvas z-order:
        // layerOrder[0] = topmost in canvas, so reverse it to draw bottom-first.
        // Fall back to numeric order if layerOrder is not provided.
        const orderedSlots = layerOrder.length > 0
            ? [...layerOrder].reverse().filter(s => allPolaSlots.has(s))
            : [...allPolaSlots].sort((a, b) => a - b);

        for (const slot of orderedSlots) {
            if (accentEnabled[slot] === false) {
                continue;
            }

            const model = accentModels[slot];

            if (!model || !catalog.polaSlots[model]?.includes(slot)) {
                continue;
            }

            const img = await fetchImage(catalog.polaAksenSvg(model, slot));

            const color = accentColors[slot] || '#ffffff';
            ctx.drawImage(drawFilledLayer(img, color, TARGET, TARGET), 0, 0);
        }

        return canvas.toDataURL('image/png');
    };

    /**
     * Reorder accent slots in canvas to match the given z-order.
     * layerOrder[0] = topmost layer (rendered on top of all other accents).
     */
    const reorderSlots = (layerOrder: number[]) => {
        if (!shoeGroup) {
            return;
        }

        shoeGroup.findOne<Konva.Image>('.base')?.moveToBottom();

        // Apply from bottom-most to top-most so layerOrder[0] ends up on top
        for (let i = layerOrder.length - 1; i >= 0; i--) {
            shoeGroup.findOne<Konva.Image>(`.aksen-${layerOrder[i]}`)?.moveToTop();
        }

        shoeGroup.findOne<Konva.Image>('.overlay')?.moveToTop();
        mainLayer?.draw();
    };

    const downloadURL = (url: string, filename: string) => {
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
    };

    // ─── Cleanup ──────────────────────────────────────────────────────────────

    const destroyStage = () => {
        resizeObserver?.disconnect();
        resizeObserver = null;

        if (viewFrame) {
            window.cancelAnimationFrame(viewFrame);
            viewFrame = 0;
        }

        pendingView = null;
        stopTween();
        stage?.destroy();
        stage = null;
        mainLayer = null;
        shoeGroup = null;
        imageCache.clear();
    };

    onUnmounted(destroyStage);

    return {
        initStage,
        loadAllLayers,
        updateSlotModel,
        updateSlotColor,
        setSlotEnabled,
        createPreviewURL,
        createPatternURL,
        downloadURL,
        isSyncing,
        zoomBy,
        resetView,
        reorderSlots,
        getStage: () => stage,
    };
}
