<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item .label {
            font-weight: bold;
            color: #666;
            font-size: 11px;
        }
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <p><strong>Minimarket POS System</strong></p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>

    <div class="summary">
        <h3 style="margin-top: 0; text-align: center;">RINGKASAN PENJUALAN</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">TOTAL PENJUALAN</div>
                <div class="value">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</div>
                <div style="font-size: 10px; color: #666; margin-top: 3px;">
                    Tunai: Rp {{ number_format($summary['total_received'], 0, ',', '.') }}<br>
                    Piutang: Rp {{ number_format($summary['total_receivables'], 0, ',', '.') }}
                </div>
            </div>
            <div class="summary-item">
                <div class="label">PENGELUARAN OPERASIONAL</div>
                <div class="value" style="color: #d32f2f;">(Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }})</div>
            </div>
            <div class="summary-item">
                <div class="label">PEMBELIAN STOK</div>
                <div class="value" style="color: #ed6c02;">(Rp {{ number_format($summary['total_purchases'], 0, ',', '.') }})</div>
            </div>
            <div class="summary-item">
                <div class="label">LABA BERSIH</div>
                <div class="value" style="color: {{ $summary['net_income'] >= 0 ? '#2e7d32' : '#d32f2f' }};">
                    Rp {{ number_format($summary['net_income'], 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div style="text-align: center; margin-top: 15px; font-size: 10px; color: #666;">
            * Laba Bersih = Total Penjualan - (Pengeluaran Operasional + Pembelian Stok)
        </div>
    </div>

    <h3>DETAIL TRANSAKSI</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Keterangan</th>
                <th class="text-right">Total</th>
                <th class="text-center">Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->user->name }}</td>
                    <td>{{ $transaction->note ?? '-' }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
                    <td class="text-center">{{ ucfirst($transaction->payment_method) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" class="text-center">TOTAL</td>
                <td class="text-right">Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Expense Details -->
    <h3 style="margin-top: 30px; color: #d32f2f;">RINCIAN PENGELUARAN OPERASIONAL</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 40%;">Keterangan</th>
                <th style="width: 20%;">Dicatat Oleh</th>
                <th style="width: 20%;" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $expense)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->date)->isoFormat('D MMM YYYY') }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->user->name ?? '-' }}</td>
                    <td class="text-right" style="color: #d32f2f;">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data pengeluaran</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #ffebee; font-weight: bold;">
                <td colspan="4" class="text-center">TOTAL PENGELUARAN</td>
                <td class="text-right" style="color: #d32f2f;">Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Purchase Details -->
    <h3 style="margin-top: 30px; color: #ed6c02;">RINCIAN PEMBELIAN STOK</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">Produk</th>
                <th style="width: 5%;">Qty</th>
                <th style="width: 15%;">Supplier</th>
                <th style="width: 15%;">Catatan</th>
                <th style="width: 20%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $index => $purchase)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($purchase->date)->isoFormat('D MMM YYYY') }}</td>
                    <td>
                        <ul style="padding-left: 15px; margin: 0;">
                            @foreach($purchase->items as $item)
                                <li>{{ $item->product->name ?? 'Produk Dihapus' }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-center">
                        <ul style="list-style-type: none; padding: 0; margin: 0;">
                            @foreach($purchase->items as $item)
                                <li>{{ $item->quantity }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $purchase->supplier->name ?? 'Umum' }}</td>
                    <td>{{ $purchase->note ?? '-' }}</td>
                    <td class="text-right" style="color: #ed6c02;">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data pembelian stok</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #fff3e0; font-weight: bold;">
                <td colspan="6" class="text-center">TOTAL PEMBELIAN STOK</td>
                <td class="text-right" style="color: #ed6c02;">Rp {{ number_format($summary['total_purchases'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh sistem POS Minimarket</p>
        <p>© {{ date('Y') }} Minimarket POS System - All Rights Reserved</p>
    </div>
</body>
</html>
