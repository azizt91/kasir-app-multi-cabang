/**
 * ThermalPrinter — Centralized printing module for POS Web App
 * Supports: USB (WebUSB), Bluetooth (Web Bluetooth), Browser Print (window.print)
 */
const ThermalPrinter = (() => {
    // ─── State ───
    let _usbDevice = null;
    let _usbEndpoint = null;
    let _btDevice = null;
    let _btServer = null;
    let _btCharacteristic = null;
    const CHUNK_SIZE = 100; // Safe chunk size for both USB & BT
    const CHUNK_DELAY = 50; // ms delay between chunks

    // ─── Feature Detection ───
    const isUSBSupported = () => !!navigator.usb;
    const isBluetoothSupported = () => !!navigator.bluetooth;

    // ─── Preference (localStorage) ───
    const PREF_KEY = 'pos_printer_preference';
    const getSavedPreference = () => localStorage.getItem(PREF_KEY); // 'usb' | 'bluetooth' | 'browser' | null
    const savePreference = (type) => localStorage.setItem(PREF_KEY, type);

    // ─── USB Printing (WebUSB API) ───
    async function connectUSB() {
        if (!isUSBSupported()) throw new Error('Browser tidak mendukung WebUSB. Gunakan Chrome atau Edge.');

        // If already connected, reuse
        if (_usbDevice && _usbDevice.opened) {
            return _usbDevice;
        }

        const device = await navigator.usb.requestDevice({
            filters: [
                { classCode: 7 }, // Printer class
            ]
        });

        await device.open();

        // Select configuration (usually config 1)
        if (device.configuration === null) {
            await device.selectConfiguration(1);
        }

        // Find the printer interface & claim it
        let printerInterface = null;
        let endpointOut = null;

        for (const iface of device.configuration.interfaces) {
            for (const alt of iface.alternates) {
                // Class 7 = Printer
                if (alt.interfaceClass === 7) {
                    printerInterface = iface;
                    // Find OUT endpoint
                    for (const ep of alt.endpoints) {
                        if (ep.direction === 'out') {
                            endpointOut = ep;
                            break;
                        }
                    }
                    break;
                }
            }
            if (printerInterface) break;
        }

        // Fallback: if no class-7 interface, try vendor-specific or first interface with OUT endpoint
        if (!printerInterface) {
            for (const iface of device.configuration.interfaces) {
                for (const alt of iface.alternates) {
                    for (const ep of alt.endpoints) {
                        if (ep.direction === 'out') {
                            printerInterface = iface;
                            endpointOut = ep;
                            break;
                        }
                    }
                    if (endpointOut) break;
                }
                if (endpointOut) break;
            }
        }

        if (!printerInterface || !endpointOut) {
            await device.close();
            throw new Error('Tidak ditemukan interface printer pada perangkat USB ini.');
        }

        await device.claimInterface(printerInterface.interfaceNumber);

        _usbDevice = device;
        _usbEndpoint = endpointOut.endpointNumber;

        console.log(`USB Printer connected: ${device.productName || 'Unknown'}`);
        return device;
    }

    async function printUSB(data) {
        if (!_usbDevice || !_usbDevice.opened) {
            await connectUSB();
        }

        const bytes = (data instanceof Uint8Array) ? data : new TextEncoder().encode(data);

        // Send in chunks
        for (let i = 0; i < bytes.byteLength; i += CHUNK_SIZE) {
            const chunk = bytes.slice(i, i + CHUNK_SIZE);
            await _usbDevice.transferOut(_usbEndpoint, chunk);
            await _delay(CHUNK_DELAY);
        }

        console.log('USB Print: Data sent successfully');
    }

    async function disconnectUSB() {
        if (_usbDevice && _usbDevice.opened) {
            await _usbDevice.close();
            console.log('USB Printer disconnected');
        }
        _usbDevice = null;
        _usbEndpoint = null;
    }

    // ─── Bluetooth Printing (Web Bluetooth API) ───
    const BT_SERVICE_UUID = '000018f0-0000-1000-8000-00805f9b34fb';
    const BT_CHAR_UUID = '00002af1-0000-1000-8000-00805f9b34fb';

    async function connectBluetooth() {
        if (!isBluetoothSupported()) throw new Error('Browser tidak mendukung Web Bluetooth.');

        // 1. Coba gunakan device yang sudah di-pair dalam sesi ini (selama tidak refresh)
        if (!_btDevice) {
            // 2. Coba ambil dari daftar device yang sudah diizinkan browser (Chrome 85+)
            if (navigator.bluetooth.getDevices) {
                const devices = await navigator.bluetooth.getDevices();
                if (devices.length > 0) {
                    _btDevice = devices[0];
                    console.log('Menggunakan device Bluetooth dari daftar izin browser.');
                }
            }
        }

        // 3. Jika benar-benar belum ada, minta user melakukan pairing (User Gesture required)
        if (!_btDevice) {
            console.log('Meminta pairing Bluetooth baru...');
            _btDevice = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: [BT_SERVICE_UUID]
            });
            
            // Listener untuk memantau jika koneksi terputus
            _btDevice.addEventListener('gattserverdisconnected', () => {
                console.log('Bluetooth terputus secara fisik.');
                _btServer = null;
                _btCharacteristic = null;
            });
        }

        // 4. Hubungkan ke GATT Server jika belum terhubung
        if (!_btServer || !_btDevice.gatt.connected) {
            _btServer = await _btDevice.gatt.connect();
        }

        const service = await _btServer.getPrimaryService(BT_SERVICE_UUID);
        _btCharacteristic = await service.getCharacteristic(BT_CHAR_UUID);

        console.log(`Bluetooth Printer terhubung: ${_btDevice.name || 'Unknown'}`);
        return _btDevice;
    }

    async function printBluetooth(data) {
        // Cek apakah koneksi masih hidup secara fisik
        const isStillConnected = _btCharacteristic && 
                                 _btCharacteristic.service && 
                                 _btCharacteristic.service.device && 
                                 _btCharacteristic.service.device.gatt.connected;

        if (!isStillConnected) {
            console.log('Bluetooth disconnected or not initialized. Reconnecting...');
            _btCharacteristic = null; // Bersihkan memori lama yang 'stale'
            await connectBluetooth();
        }

        const bytes = (data instanceof Uint8Array) ? data : new TextEncoder().encode(data);

        for (let i = 0; i < bytes.byteLength; i += CHUNK_SIZE) {
            const chunk = bytes.slice(i, i + CHUNK_SIZE);
            await _btCharacteristic.writeValue(chunk);
            await _delay(CHUNK_DELAY);
        }

        console.log('Bluetooth Print: Data sent successfully');
    }

    function isBTConnected() {
        return !!(_btCharacteristic && 
                  _btCharacteristic.service && 
                  _btCharacteristic.service.device && 
                  _btCharacteristic.service.device.gatt.connected);
    }

    // ─── Browser Print Fallback ───
    function printBrowser(receiptHTML) {
        // Hapus iframe lama jika ada untuk mencegah penumpukan
        let oldIframe = document.getElementById('print-iframe');
        if (oldIframe) {
            oldIframe.remove();
        }

        // Buat iframe tersembunyi khusus untuk print agar terisolasi dari layout POS
        const iframe = document.createElement('iframe');
        iframe.id = 'print-iframe';
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.appendChild(iframe);

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write(`
            <html>
            <head>
                <style>
                    @page { margin: 0; size: auto; }
                    body { margin: 0; padding: 5px; background: white; }
                </style>
            </head>
            <body>
                ${receiptHTML}
            </body>
            </html>
        `);
        doc.close();

        // Tunggu gambar logo (jika ada) selesai di-load di dalam iframe sebelum mengeksekusi print
        const images = doc.getElementsByTagName('img');
        let loadedCount = 0;

        const executePrint = () => {
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }, 250);
        };

        if (images.length === 0) {
            executePrint();
        } else {
            let fired = false;
            for (let i = 0; i < images.length; i++) {
                if (images[i].complete) {
                    loadedCount++;
                    if (loadedCount === images.length && !fired) { fired = true; executePrint(); }
                } else {
                    images[i].onload = () => {
                        loadedCount++;
                        if (loadedCount === images.length && !fired) { fired = true; executePrint(); }
                    };
                    images[i].onerror = () => {
                        loadedCount++; 
                        if (loadedCount === images.length && !fired) { fired = true; executePrint(); }
                    };
                }
            }
        }
    }

    // ─── ESC/POS Image Converter ───
    async function _getImageEscPos(url, maxWidth = 384) {
        return new Promise((resolve) => {
            const img = new Image();
            // Hapus crossOrigin='Anonymous' karena di lingkungan dev (php artisan serve) tanpa header CORS khusus,
            // ini justru menyebabkan browser memblokir gambar meskipun berasal dari domain yang sama.
            // Karena ini same-origin, canvas tidak akan mengalami taint.
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let fw = img.width;
                let fh = img.height;
                
                // Optimasi Resolusi: Kompresi proporsional (dibatasi oleh maxWidth misal 160px)
                if (fw > maxWidth || fh > maxWidth) {
                    let ratio = Math.min(maxWidth / fw, maxWidth / fh);
                    fw = Math.round(fw * ratio);
                    fh = Math.round(fh * ratio);
                }
                
                // PENTING: Lebar raster/kanvas WAJIB diset 384 dot (48 bytes per row) !!
                // Printer abal-abal yang menggunakan GS v 0 seringkali GAGAL memparsing jika xL tidak tepat 48.
                const canvasWidth = 384; 
                canvas.width = canvasWidth;
                canvas.height = fh;
                const ctx = canvas.getContext('2d');
                
                // Format Handling: Background putih penuh agar area tak terpakai jadi blank & transparan tidak hitam
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvasWidth, fh);
                
                // Tempatkan logo persis di senter baris agar terlihat rapi
                const offsetX = Math.floor((canvasWidth - fw) / 2);
                ctx.drawImage(img, offsetX, 0, fw, fh);
                
                const imageData = ctx.getImageData(0, 0, canvasWidth, fh).data;
                const bytesPerRow = canvasWidth / 8; // Pasti 48
                
                // Format GS v 0 COMMAND standar untuk cetak raster bit-image
                let posCommand = [0x1D, 0x76, 0x30, 0x00, bytesPerRow & 0xFF, (bytesPerRow >> 8) & 0xFF, fh & 0xFF, (fh >> 8) & 0xFF];
                let bytes = new Uint8Array(posCommand.length + bytesPerRow * fh);
                bytes.set(posCommand);
                
                let byteIndex = posCommand.length;
                for (let y = 0; y < fh; y++) {
                    for (let x = 0; x < bytesPerRow; x++) {
                        let b = 0;
                        for (let bit = 0; bit < 8; bit++) {
                            let pxX = x * 8 + bit;
                            
                            let idx = (y * canvasWidth + pxX) * 4;
                            let r = imageData[idx];
                            let g = imageData[idx + 1];
                            let blue = imageData[idx + 2];
                            let a = imageData[idx + 3];
                            
                            // Konversi ke grayscale monokrom
                            let luminance = (0.299 * r + 0.587 * g + 0.114 * blue);
                            if (a < 128) luminance = 255; // transparent acts as white
                            
                            // Hitam / Dark -> bit aktif (print)
                            if (luminance < 128) {
                                b |= (1 << (7 - bit));
                            }
                        }
                        bytes[byteIndex++] = b;
                    }
                }
                resolve(bytes);
            };
            img.onerror = () => { 
                console.warn('Gagal memuat image logo thermal.'); 
                resolve(new Uint8Array(0)); 
            };
            img.src = url;
        });
    }

    // ─── ESC/POS Receipt Generator ───
    async function generateReceipt(tx, storeSettings, cashierName) {
        const ESC = '\x1B', GS = '\x1D';
        const isUtang = tx.payment_method === 'utang';
        const paymentLabels = {
            'cash': 'Tunai', 'utang': 'UTANG', 'card': 'Kartu',
            'ewallet': 'E-Wallet', 'transfer': 'Transfer', 'qris': 'QRIS'
        };

        const fmtRp = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
        let parts = []; // Array of Uint8Arrays
        
        let initCmd = ESC + '@' + ESC + 'a' + '\x01'; // Initialize & Center Align
        parts.push(new TextEncoder().encode(initCmd));

        // Layouting: Cek dan tambahkan LOGO jika di storeSettings tersedia
        if (storeSettings.store_logo && storeSettings.store_logo !== '') {
            let logoUrl = storeSettings.store_logo;
            if (!logoUrl.startsWith('http')) {
                const origin = window.location.origin;
                let path = logoUrl.startsWith('/') ? logoUrl : '/' + logoUrl;
                // Bypassing Windows symlink bug di php artisan serve dengan rute kostum
                if (path.includes('/storage')) {
                    path = path.replace('/storage/', '/asset-storage/');
                } else if (!path.includes('/asset-storage')) {
                    path = '/asset-storage' + path;
                }
                logoUrl = origin + path;
            }
            try {
                // Optimasi drastis: turunkan maxWidth ke 160 agar ukuran gambar turun 4x lipat (mempercepat print BLE & mencegah buffer overflow printer)
                let logoBytes = await _getImageEscPos(logoUrl, 160);
                if (logoBytes.length > 0) {
                    parts.push(logoBytes);
                    // Jarak yang rapi sebelum nama toko
                    parts.push(new TextEncoder().encode('\n'));
                }
            } catch (e) {
                console.warn('Lewati logo pada print hardware ERROR', e);
            }
        }

        let r = '';
        r += ESC + '!' + '\x18';                 // Double height+width
        r += `${storeSettings.store_name}\n`;
        r += ESC + '!' + '\x00';                 // Normal
        r += `${storeSettings.store_address}\n`;
        r += `Telp: ${storeSettings.store_phone}\n`;
        r += '================================\n';

        r += ESC + 'a' + '\x00';                 // Left
        r += `No: ${tx.transaction_code}\n`;

        const txDate = new Date(tx.created_at || Date.now());
        r += `Tgl: ${txDate.toLocaleDateString('id-ID')} ${txDate.toLocaleTimeString('id-ID')}\n`;
        r += `Kasir: ${cashierName}\n`;

        if (tx.customer_name && tx.customer_name !== 'Umum') {
            r += `Customer: ${tx.customer_name}\n`;
        }
        r += '================================\n';

        // Items
        (tx.items || []).forEach(item => {
            const name = item.product ? item.product.name : (item.name || 'Item');
            const price = parseFloat(item.price);
            const qty = parseFloat(item.quantity);
            r += `${name}\n`;
            if (item.employee) {
                r += `  (${storeSettings.employee_label || 'Pegawai'}: ${item.employee.name})\n`;
            }
            r += `  ${qty} x ${fmtRp(price)} = ${fmtRp(qty * price)}\n`;
        });

        r += '================================\n';
        r += ESC + 'a' + '\x02';                 // Right

        r += `Subtotal: ${fmtRp(tx.subtotal)}\n`;
        if (parseFloat(tx.discount) > 0) r += `Diskon: -${fmtRp(tx.discount)}\n`;
        if (parseFloat(tx.tax) > 0) r += `Pajak: ${fmtRp(tx.tax)}\n`;

        r += `Total: ${fmtRp(tx.total_amount)}\n`;
        r += `Metode: ${paymentLabels[tx.payment_method] || tx.payment_method}\n`;

        if (!isUtang) {
            r += `Bayar: ${fmtRp(tx.amount_paid)}\n`;
            r += `Kembali: ${fmtRp(tx.change_amount)}\n`;
        }

        r += ESC + 'a' + '\x01';                 // Center
        if (isUtang) {
            r += '--------------------------------\n';
            r += ESC + '!' + '\x08';             // Bold
            r += '** BELUM DIBAYAR - PIUTANG **\n';
            r += ESC + '!' + '\x00';             // Normal
        }
        r += '================================\n';

        if (storeSettings.store_description) {
            r += storeSettings.store_description + '\n\n\n';
        } else {
            r += 'Terima kasih!\n\n\n';
        }

        r += GS + 'V' + '\x41' + '\x03';        // Cut paper

        parts.push(new TextEncoder().encode(r));
        
        let totalLen = parts.reduce((acc, val) => acc + val.length, 0);
        let result = new Uint8Array(totalLen);
        let offset = 0;
        for (let p of parts) {
            result.set(p, offset);
            offset += p.length;
        }
        return result;
    }

    // ─── Browser Receipt HTML Generator ───
    function generateReceiptHTML(tx, storeSettings, cashierName) {
        const fmtRp = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
        const isUtang = tx.payment_method === 'utang';
        const paymentLabels = {
            'cash': '💵 Tunai', 'utang': '📝 UTANG', 'card': '💳 Kartu',
            'ewallet': '📱 E-Wallet', 'transfer': '🏦 Transfer', 'qris': '📲 QRIS'
        };
        const txDate = new Date(tx.created_at || Date.now());
        const items = tx.items || [];

        return `
        <div style="font-family: 'Courier New', monospace; font-size: 11px; width: 280px; padding: 10px; color: black;">
            <div style="text-align: center;">
                ${storeSettings.store_logo ? `<img src="/storage/${storeSettings.store_logo}" alt="Logo" style="max-width: 100%; max-height: 80px; display: block; margin: 0 auto 5px auto;">` : ''}
                <div style="font-size: 16px; font-weight: bold; margin-bottom: 4px;">${storeSettings.store_name}</div>
                <div>${storeSettings.store_address}</div>
                <div>Telp: ${storeSettings.store_phone}</div>
            </div>
            <div style="border-top: 1px dashed black; margin: 8px 0;"></div>
            <table style="width: 100%; font-size: 11px;">
                <tr><td>No</td><td>: ${tx.transaction_code}</td></tr>
                <tr><td>Tgl</td><td>: ${txDate.toLocaleDateString('id-ID')} ${txDate.toLocaleTimeString('id-ID')}</td></tr>
                <tr><td>Kasir</td><td>: ${cashierName}</td></tr>
                ${tx.customer_name && tx.customer_name !== 'Umum' ? `<tr><td>Customer</td><td>: ${tx.customer_name}</td></tr>` : ''}
            </table>
            <div style="border-top: 1px dashed black; margin: 8px 0;"></div>
            <div>
                ${items.map(item => {
            const name = item.product ? item.product.name : (item.name || 'Item');
            const qty = parseFloat(item.quantity);
            return `
                    <div style="margin-bottom: 5px;">
                        <div>${name}</div>
                        ${item.employee ? `<div style="font-size: 10px; margin-left: 10px;">(${storeSettings.employee_label || 'Pegawai'}: ${item.employee.name})</div>` : ''}
                        <div style="display: flex; justify-content: space-between;">
                            <span>&nbsp;&nbsp;${qty} x ${fmtRp(item.price)}</span>
                            <span>${fmtRp(qty * item.price)}</span>
                        </div>
                    </div>`;
        }).join('')}
            </div>
            <div style="border-top: 1px dashed black; margin: 8px 0;"></div>
            <div style="text-align: right;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-weight: bold;">Total:</span>
                    <span style="font-weight: bold;">${fmtRp(tx.total_amount)}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Metode:</span>
                    <span>${paymentLabels[tx.payment_method] || tx.payment_method}</span>
                </div>
                ${parseFloat(tx.discount) > 0 ? `
                <div style="display: flex; justify-content: space-between;">
                    <span>Diskon:</span><span>-${fmtRp(tx.discount)}</span>
                </div>` : ''}
                ${!isUtang ? `
                <div style="display: flex; justify-content: space-between;">
                    <span>Bayar:</span><span>${fmtRp(tx.amount_paid)}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Kembali:</span><span>${fmtRp(tx.change_amount)}</span>
                </div>` : ''}
            </div>
            ${isUtang ? `
            <div style="border-top: 1px dashed black; margin: 8px 0;"></div>
            <div style="text-align: center; font-weight: bold; padding: 8px; background: #f0f0f0; border: 1px dashed black;">
                ⚠️ BELUM DIBAYAR - PIUTANG
            </div>` : ''}
            <div style="border-top: 1px dashed black; margin: 8px 0;"></div>
            <div style="text-align: center; margin-top: 10px;">
                ${storeSettings.store_description ? storeSettings.store_description.replace(/\n/g, '<br>') : 'Terima kasih!'}
            </div>
        </div>`;
    }

    // ─── Print Method Picker Dialog ───
    function showPrintDialog(onSelect) {
        // Remove existing dialog if any
        const existing = document.getElementById('thermal-print-dialog');
        if (existing) existing.remove();

        const supportsUSB = isUSBSupported();
        const supportsBT = isBluetoothSupported();

        const overlay = document.createElement('div');
        overlay.id = 'thermal-print-dialog';
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999;';

        overlay.innerHTML = `
        <div style="background:white;border-radius:16px;padding:24px;width:360px;max-width:90vw;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <div style="text-align:center;margin-bottom:20px;">
                <div style="font-size:24px;margin-bottom:8px;">🖨️</div>
                <h3 style="font-size:18px;font-weight:bold;margin:0 0 4px 0;">Pilih Printer</h3>
                <p style="font-size:13px;color:#666;margin:0;">Pilih metode cetak struk</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                ${supportsUSB ? `
                <button onclick="ThermalPrinter._dialogSelect('usb')" style="display:flex;align-items:center;gap:12px;padding:14px 16px;border:2px solid #e5e7eb;border-radius:12px;background:white;cursor:pointer;transition:all 0.2s;font-size:14px;" onmouseover="this.style.borderColor='#6366f1';this.style.background='#f5f3ff'" onmouseout="this.style.borderColor='#e5e7eb';this.style.background='white'">
                    <span style="font-size:24px;">🔌</span>
                    <div style="text-align:left;">
                        <div style="font-weight:600;">Printer USB</div>
                        <div style="font-size:12px;color:#888;">Langsung cetak via kabel USB</div>
                    </div>
                </button>` : ''}
                ${supportsBT ? `
                <button onclick="ThermalPrinter._dialogSelect('bluetooth')" style="display:flex;align-items:center;gap:12px;padding:14px 16px;border:2px solid #e5e7eb;border-radius:12px;background:white;cursor:pointer;transition:all 0.2s;font-size:14px;" onmouseover="this.style.borderColor='#3b82f6';this.style.background='#eff6ff'" onmouseout="this.style.borderColor='#e5e7eb';this.style.background='white'">
                    <span style="font-size:24px;">📶</span>
                    <div style="text-align:left;">
                        <div style="font-weight:600;">Printer Bluetooth</div>
                        <div style="font-size:12px;color:#888;">Cetak via koneksi Bluetooth</div>
                    </div>
                </button>` : ''}
                <button onclick="ThermalPrinter._dialogSelect('browser')" style="display:flex;align-items:center;gap:12px;padding:14px 16px;border:2px solid #e5e7eb;border-radius:12px;background:white;cursor:pointer;transition:all 0.2s;font-size:14px;" onmouseover="this.style.borderColor='#10b981';this.style.background='#ecfdf5'" onmouseout="this.style.borderColor='#e5e7eb';this.style.background='white'">
                    <span style="font-size:24px;">📄</span>
                    <div style="text-align:left;">
                        <div style="font-weight:600;">Print Browser</div>
                        <div style="font-size:12px;color:#888;">Via dialog print browser (semua browser)</div>
                    </div>
                </button>
            </div>
            <div style="margin-top:16px;text-align:center;">
                <label style="font-size:12px;color:#888;cursor:pointer;">
                    <input type="checkbox" id="tp-remember" style="margin-right:4px;">
                    Ingat pilihan saya
                </label>
            </div>
            <button onclick="ThermalPrinter._dialogClose()" style="width:100%;margin-top:12px;padding:10px;border:none;border-radius:10px;background:#f3f4f6;color:#666;font-size:14px;cursor:pointer;font-weight:500;">Batal</button>
        </div>`;

        document.body.appendChild(overlay);

        // Store callback
        ThermalPrinter._dialogCallback = onSelect;
    }

    // ─── Smart Print ───
    async function print(receiptBytes, receiptHTML) {
        const pref = getSavedPreference();

        if (pref) {
            try {
                await _printWith(pref, receiptBytes, receiptHTML);
                return;
            } catch (e) {
                console.warn(`Preferred method "${pref}" failed:`, e.message);
                // Fall through to dialog
            }
        }

        // Show picker dialog
        return new Promise((resolve, reject) => {
            showPrintDialog(async (method) => {
                try {
                    await _printWith(method, receiptBytes, receiptHTML);
                    resolve();
                } catch (e) {
                    alert('Cetak gagal: ' + e.message);
                    reject(e);
                }
            });
        });
    }

    async function _printWith(method, receiptBytes, receiptHTML) {
        switch (method) {
            case 'usb':
                await printUSB(receiptBytes);
                _showToast('✅ Struk dikirim ke printer USB');
                break;
            case 'bluetooth':
                await printBluetooth(receiptBytes);
                _showToast('✅ Struk dikirim ke printer Bluetooth');
                break;
            case 'browser':
                printBrowser(receiptHTML);
                break;
            default:
                throw new Error('Metode print tidak valid');
        }
    }

    // ─── USB Test Print ───
    async function testPrintUSB() {
        const ESC = '\x1B', GS = '\x1D';
        let data = ESC + '@';
        data += ESC + 'a' + '\x01';
        data += ESC + '!' + '\x18';
        data += 'TEST PRINT\n';
        data += ESC + '!' + '\x00';
        data += '================================\n';
        data += 'Printer USB terhubung!\n';
        data += 'Tanggal: ' + new Date().toLocaleString('id-ID') + '\n';
        data += '================================\n\n\n';
        data += GS + 'V' + '\x41' + '\x03';

        await printUSB(new TextEncoder().encode(data));
    }

    // ─── Internal Helpers ───
    function _delay(ms) {
        return new Promise(r => setTimeout(r, ms));
    }

    function _showToast(message) {
        // Use SweetAlert if available, otherwise native
        if (typeof Swal !== 'undefined') {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, timer: 2000, showConfirmButton: false });
        } else {
            alert(message);
        }
    }

    function _dialogSelect(method) {
        const remember = document.getElementById('tp-remember');
        if (remember && remember.checked) {
            savePreference(method);
        }
        _dialogClose();
        if (ThermalPrinter._dialogCallback) {
            ThermalPrinter._dialogCallback(method);
        }
    }

    function _dialogClose() {
        const dialog = document.getElementById('thermal-print-dialog');
        if (dialog) dialog.remove();
    }

    // ─── Public API ───
    return {
        // Methods
        connectUSB, printUSB, disconnectUSB, testPrintUSB,
        connectBluetooth, printBluetooth,
        printBrowser,
        print,
        showPrintDialog,

        // Generators
        generateReceipt, generateReceiptHTML,

        // Utils
        isUSBSupported, isBluetoothSupported, isBTConnected,
        getSavedPreference, savePreference,

        // Internal (used by dialog inline handlers)
        _dialogSelect, _dialogClose, _dialogCallback: null,
    };
})();
