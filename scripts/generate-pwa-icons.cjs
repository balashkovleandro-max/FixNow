const fs = require('fs');
const path = require('path');
const zlib = require('zlib');

const outDir = path.join(process.cwd(), 'public', 'icons');
fs.mkdirSync(outDir, { recursive: true });

const crcTable = new Uint32Array(256);
for (let n = 0; n < 256; n++) {
    let c = n;
    for (let k = 0; k < 8; k++) {
        c = (c & 1) ? (0xedb88320 ^ (c >>> 1)) : (c >>> 1);
    }
    crcTable[n] = c >>> 0;
}

function crc32(buffer) {
    let c = 0xffffffff;
    for (let i = 0; i < buffer.length; i++) {
        c = crcTable[(c ^ buffer[i]) & 255] ^ (c >>> 8);
    }

    return (c ^ 0xffffffff) >>> 0;
}

function chunk(type, data) {
    const typeBuffer = Buffer.from(type, 'ascii');
    const length = Buffer.alloc(4);
    length.writeUInt32BE(data.length, 0);

    const crc = Buffer.alloc(4);
    crc.writeUInt32BE(crc32(Buffer.concat([typeBuffer, data])), 0);

    return Buffer.concat([length, typeBuffer, data, crc]);
}

function fillRect(image, size, x, y, width, height, color) {
    const x0 = Math.max(0, Math.floor(x));
    const y0 = Math.max(0, Math.floor(y));
    const x1 = Math.min(size, Math.ceil(x + width));
    const y1 = Math.min(size, Math.ceil(y + height));

    for (let py = y0; py < y1; py++) {
        let index = (py * size + x0) * 4;

        for (let px = x0; px < x1; px++, index += 4) {
            image[index] = color[0];
            image[index + 1] = color[1];
            image[index + 2] = color[2];
            image[index + 3] = 255;
        }
    }
}

function writeIcon(filename, size) {
    const image = Buffer.alloc(size * size * 4);

    for (let y = 0; y < size; y++) {
        for (let x = 0; x < size; x++) {
            const t = (x + y) / (2 * (size - 1));
            const index = (y * size + x) * 4;

            let r;
            let g;
            let b;

            if (t < 0.55) {
                const q = t / 0.55;
                r = 37 + (124 - 37) * q;
                g = 99 + (58 - 99) * q;
                b = 235 + (237 - 235) * q;
            } else {
                const q = (t - 0.55) / 0.45;
                r = 124 + (236 - 124) * q;
                g = 58 + (72 - 58) * q;
                b = 237 + (153 - 237) * q;
            }

            const shine = Math.max(0, 1 - Math.hypot(x / size - 0.22, y / size - 0.18) * 3);
            image[index] = Math.min(255, Math.round(r + 50 * shine));
            image[index + 1] = Math.min(255, Math.round(g + 50 * shine));
            image[index + 2] = Math.min(255, Math.round(b + 50 * shine));
            image[index + 3] = 255;
        }
    }

    const white = [255, 255, 255];
    fillRect(image, size, size * 0.29, size * 0.25, size * 0.13, size * 0.52, white);
    fillRect(image, size, size * 0.40, size * 0.25, size * 0.30, size * 0.12, white);
    fillRect(image, size, size * 0.40, size * 0.44, size * 0.34, size * 0.12, white);
    fillRect(image, size, size * 0.40, size * 0.65, size * 0.31, size * 0.12, white);
    fillRect(image, size, size * 0.64, size * 0.32, size * 0.10, size * 0.20, white);
    fillRect(image, size, size * 0.66, size * 0.52, size * 0.10, size * 0.20, white);
    fillRect(image, size, size * 0.22, size * 0.16, size * 0.24, size * 0.04, [190, 205, 255]);

    const raw = Buffer.alloc((size * 4 + 1) * size);

    for (let y = 0; y < size; y++) {
        raw[y * (size * 4 + 1)] = 0;
        image.copy(raw, y * (size * 4 + 1) + 1, y * size * 4, (y + 1) * size * 4);
    }

    const ihdr = Buffer.alloc(13);
    ihdr.writeUInt32BE(size, 0);
    ihdr.writeUInt32BE(size, 4);
    ihdr[8] = 8;
    ihdr[9] = 6;

    const png = Buffer.concat([
        Buffer.from([137, 80, 78, 71, 13, 10, 26, 10]),
        chunk('IHDR', ihdr),
        chunk('IDAT', zlib.deflateSync(raw, { level: 1 })),
        chunk('IEND', Buffer.alloc(0)),
    ]);

    fs.writeFileSync(path.join(outDir, filename), png);
}

writeIcon('bon-icon-192.png', 192);
writeIcon('bon-icon-512.png', 512);
writeIcon('bon-maskable-512.png', 512);

console.log('BON PWA icons generated in public/icons');
