export const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val);
};

export const loadImage = (src: string): Promise<HTMLImageElement> => {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = src;
    });
};

export function drawFilledLayer(img: HTMLImageElement, color: string, w: number, h: number): HTMLCanvasElement {
    const canvas = document.createElement('canvas');
    canvas.width = w;
    canvas.height = h;
    const ctx = canvas.getContext('2d');

    if (!ctx) {
        return canvas;
    }

    ctx.drawImage(img, 0, 0, w, h);
    ctx.globalCompositeOperation = 'source-in';
    ctx.fillStyle = color;
    ctx.fillRect(0, 0, w, h);

    return canvas;
}

export function drawOutlineLayer(img: HTMLImageElement, color: string, size: number, originalW: number, originalH: number): HTMLCanvasElement {
    const s = size;
    const padding = s * 2;
    const w = originalW + padding;
    const h = originalH + padding;

    const canvas = document.createElement('canvas');
    canvas.width = w;
    canvas.height = h;
    const ctx = canvas.getContext('2d');

    if (!ctx) {
        return canvas;
    }

    // Draw the image multiple times shifted around a circle to create the outline
    const dArr = [-1, -1, 0, -1, 1, -1, -1, 0, 1, 0, -1, 1, 0, 1, 1, 1, -0.7, -0.7, 0.7, -0.7, -0.7, 0.7, 0.7, 0.7];

    // First, draw all the shifted images (adding 's' to center the shifts in the padded canvas)
    for (let i = 0; i < dArr.length; i += 2) {
        ctx.drawImage(img, s + (dArr[i] * s), s + (dArr[i + 1] * s), originalW, originalH);
    }

    // Colorize the expanded shape
    ctx.globalCompositeOperation = 'source-in';
    ctx.fillStyle = color;
    ctx.fillRect(0, 0, w, h);

    // Punch a hole exactly where the original image will sit
    ctx.globalCompositeOperation = 'destination-out';
    ctx.drawImage(img, s, s, originalW, originalH);

    // Draw the original image back into the hole
    ctx.globalCompositeOperation = 'source-over';
    ctx.drawImage(img, s, s, originalW, originalH);

    return canvas;
}

export const generatePaletteFromDataUrl = (dataUrl: string): Promise<string[]> => {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            if (!ctx) {
                resolve([]);
                return;
            }

            canvas.width = 100;
            canvas.height = 100;
            ctx.drawImage(img, 0, 0, 100, 100);

            const imageData = ctx.getImageData(0, 0, 100, 100).data;
            const colors = new Set<string>();

            for (let i = 0; i < imageData.length; i += 40) {
                const r = imageData[i];
                const g = imageData[i+1];
                const b = imageData[i+2];

                const qr = Math.round(r / 15) * 15;
                const qg = Math.round(g / 15) * 15;
                const qb = Math.round(b / 15) * 15;

                const hex = '#' + ((1 << 24) + (qr << 16) + (qg << 8) + qb).toString(16).slice(1);
                colors.add(hex);

                if (colors.size >= 12) break;
            }

            resolve(Array.from(colors));
        };
        img.src = dataUrl;
    });
};
