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
        img.crossOrigin = 'Anonymous';
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            if (!ctx) return resolve([]);

            // Ukuran sampel kecil untuk performa, tapi cukup detail
            const S = 100;
            canvas.width = S;
            canvas.height = S;
            ctx.drawImage(img, 0, 0, S, S);

            const data = ctx.getImageData(0, 0, S, S).data;
            const colorCounts: Record<string, number> = {};
            
            // 1. Quantization & Counting
            // Kita gunakan bitwise shift untuk grouping warna yang sangat mirip (32 levels per channel)
            for (let i = 0; i < data.length; i += 4) {
                const a = data[i + 3];
                if (a < 128) continue; // Skip transparan

                const r = data[i] >> 3;
                const g = data[i + 1] >> 3;
                const b = data[i + 2] >> 3;
                const key = (r << 10) | (g << 5) | b;
                colorCounts[key] = (colorCounts[key] || 0) + 1;
            }

            // 2. Sort by popularity
            const sortedColors = Object.entries(colorCounts)
                .map(([key, count]) => ({
                    r: (Number(key) >> 10) << 3,
                    g: ((Number(key) >> 5) & 31) << 3,
                    b: (Number(key) & 31) << 3,
                    count
                }))
                .sort((a, b) => b.count - a.count);

            // 3. Filter for diversity (Min Distance check)
            const palette: string[] = [];
            const rgbPalette: {r: number, g: number, b: number}[] = [];
            const minDistance = 60; // Ambang batas jarak warna (Euclidean)

            for (const col of sortedColors) {
                // Lewati jika terlalu dekat dengan warna yang sudah ada di palet
                const isTooClose = rgbPalette.some(p => {
                    const distance = Math.sqrt(
                        Math.pow(p.r - col.r, 2) + 
                        Math.pow(p.g - col.g, 2) + 
                        Math.pow(p.b - col.b, 2)
                    );
                    return distance < minDistance;
                });

                if (!isTooClose) {
                    const hex = '#' + [col.r, col.g, col.b]
                        .map(x => x.toString(16).padStart(2, '0'))
                        .join('');
                    palette.push(hex);
                    rgbPalette.push(col);
                }

                if (palette.length >= 16) break; // Ambil maksimal 16 warna
            }

            // Jika palet terlalu sedikit, turunkan jarak dan coba lagi (opsional)
            resolve(palette);
        };
        img.src = dataUrl;
    });
};
