<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaction->transaction_code }}</title>
    @php
        $paperWidth = ($storeSettings->paper_size ?? '58mm') === '80mm' ? 80 : 58;
    @endphp
    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: {{ $paperWidth }}mm;
            height: auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: {{ $paperWidth == 80 ? '13' : '12' }}px;
            padding: 5px;
            background-color: #fff;
        }
        @media print {
            @page {
                size: auto;
                margin: 0;
            }
            html, body {
                width: {{ $paperWidth }}mm !important;
                height: auto !important;
                overflow: hidden !important;
                margin: 0 !important;
                padding: 2px !important;
            }
            .no-print {
                display: none !important;
            }
        }
        .receipt-container {
            width: 100%;
            max-width: {{ $paperWidth }}mm;
            overflow: hidden;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed black; margin: 5px 0; }
        .border-bottom { border-bottom: 1px dashed black; margin: 5px 0; }
        .flex { display: flex; justify-content: space-between; }
        .mb-1 { margin-bottom: 2px; }
        .store-name { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        
        /* Action Buttons */
        .actions {
            margin-bottom: 15px;
            text-align: center;
            padding: 12px;
            background: #f3f4f6;
            border-radius: 8px;
        }
        .btn-group {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-family: sans-serif;
            font-weight: bold;
            font-size: 13px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-usb { background-color: #6366f1; }
        .btn-bluetooth { background-color: #2563eb; }
        .btn-print { background-color: #4b5563; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="actions no-print">
        <div class="btn-group">
            <button onclick="executePrint()" class="btn btn-print" style="width: 100%; justify-content: center;">
                🖨️ Cetak Struk
            </button>
        </div>
    </div>

    <div class="receipt-container">
    <div class="text-center">
        @if(!empty($storeSettings->store_logo))
            <img src="{{ asset('storage/' . $storeSettings->store_logo) }}" alt="Logo" style="max-width: 100%; max-height: 80px; display: block; margin: 0 auto 5px auto;">
        @endif
        <div class="store-name">{{ $storeSettings->store_name }}</div>
        <div>{{ $storeSettings->store_address }}</div>
        <div>Telp: {{ $storeSettings->store_phone }}</div>
    </div>

    <div class="border-top"></div>

    <div>
        <div class="flex">
            <span>No</span>
            <span>: {{ $transaction->transaction_code }}</span>
        </div>
        <div class="flex">
            <span>Tgl</span>
            <span>: {{ $transaction->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex">
            <span>Kasir</span>
            <span>: {{ $transaction->user->name ?? '-' }}</span>
        </div>
        @if($transaction->customer_name && $transaction->customer_name !== 'Umum')
        <div class="flex">
            <span>Cust</span>
            <span>: {{ $transaction->customer_name }}</span>
        </div>
        @endif
    </div>

    <div class="border-top"></div>

    <div>
        @foreach($transaction->items as $item)
            <div class="mb-1">
                <div>{{ $item->product->name }}</div>
                <div class="flex">
                    <!-- Gunakan floatval untuk menghapus nol tidak perlu (contoh: 2.00 jadi 2, 0.50 jadi 0.5) -->
                    <span>&nbsp;&nbsp;{{ floatval($item->quantity) }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span>{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="border-top"></div>

    <div class="text-right">
        <div class="flex font-bold">
            <span>Total:</span>
            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex">
            <span>Metode:</span>
            <span style="text-transform: capitalize;">{{ $transaction->payment_method }}</span>
        </div>
        <!-- Check POS logic: Discount and Tax display -->
        @if($transaction->discount > 0)
        <div class="flex">
            <span>Diskon:</span>
            <span>-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($transaction->tax > 0)
        <div class="flex">
            <span>Pajak:</span>
            <span>Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
        </div>
        @endif
        
        @if($transaction->payment_method !== 'utang')
        <div class="flex">
            <span>Bayar:</span>
            <span>Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</span>
        </div>
        <div class="flex">
            <span>Kembali:</span>
            <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    @if($transaction->payment_method === 'utang')
    <div class="border-top"></div>
    <div class="text-center font-bold" style="background: #f0f0f0; border: 1px dashed black; padding: 5px;">
        ⚠️ BELUM DIBAYAR - PIUTANG
    </div>
    @endif

    <div class="border-top"></div>

    <div class="text-center" style="margin-top: 5px; margin-bottom: 0;">
        {!! nl2br(e($storeSettings->store_description)) !!}
    </div>
    </div><!-- /.receipt-container -->

    <script src="{{ asset('js/thermal-printer.js') }}?v={{ filemtime(public_path('js/thermal-printer.js')) }}"></script>
    <script>
        const transaction = @json($transaction);
        const storeSettings = @json($storeSettings);
        const authUser = { name: "{{ $transaction->user->name ?? '-' }}" };

        async function doPrintUSB() {
            try {
                const receiptData = await ThermalPrinter.generateReceipt(transaction, storeSettings, authUser.name);
                await ThermalPrinter.printUSB(receiptData);
            } catch (error) {
                console.error('USB print error:', error);
                alert('Gagal mencetak via USB: ' + error.message);
            }
        }

        async function doPrintBluetooth() {
            try {
                const receiptData = await ThermalPrinter.generateReceipt(transaction, storeSettings, authUser.name);
                await ThermalPrinter.printBluetooth(receiptData);
            } catch (error) {
                console.error('Bluetooth print error:', error);
                alert('Gagal mencetak via Bluetooth: ' + error.message);
            }
        }

        function executePrint() {
            const defaultMethod = storeSettings.default_printer || 'browser';
            if (defaultMethod === 'bluetooth') {
                doPrintBluetooth();
            } else {
                window.print();
            }
        }

        // Otomatis print saat halaman dibuka sesuai pengaturan printer default
        window.onload = function() {
            const defaultMethod = storeSettings.default_printer || 'browser';
            
            if (defaultMethod === 'browser') {
                // Browser print diizinkan auto-print tanpa user gesture
                window.print();
            } else {
                // Bluetooth dan USB secara keamanan browser mewajibkan ada 'klik' (User Gesture).
                // Jika dipaksa jalan otomatis di sini, browser akan langsung menolak dan
                // memunculkan pesan error "Must be handling a user gesture".
                // Oleh karena itu, biarkan user mengklik tombol "Cetak Struk" secara manual
                // agar muncul jendela popup pairing perangkatnya.
                console.log("Menunggu user mengklik tombol cetak untuk memunculkan dialog pairing " + defaultMethod);
            }
        }
    </script>
</body>
</html>
