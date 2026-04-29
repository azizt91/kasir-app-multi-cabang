<table>
    <thead>
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 14px; text-align: center;">LAPORAN PENJUALAN</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">Periode: {{ $startDate }} - {{ $endDate }}</th>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">RINGKASAN</td>
        </tr>
        <tr>
            <td colspan="2">Total Penjualan</td>
            <td style="font-weight: bold;">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-style: italic; padding-left: 10px;"> - Tunai / Transfer</td>
            <td style="font-style: italic;">Rp {{ number_format($summary['total_received'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-style: italic; padding-left: 10px;"> - Piutang</td>
            <td style="font-style: italic;">Rp {{ number_format($summary['total_receivables'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2">Total Pengeluaran</td>
            <td style="color: red;">(Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }})</td>
        </tr>
        <tr>
            <td colspan="2">Total Pembelian Stok</td>
            <td style="color: orange;">(Rp {{ number_format($summary['total_purchases'], 0, ',', '.') }})</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">LABA BERSIH</td>
            <td style="font-weight: bold; color: {{ $summary['net_income'] >= 0 ? 'green' : 'red' }};">Rp {{ number_format($summary['net_income'], 0, ',', '.') }}</td>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000;">Tanggal</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Kasir</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Keterangan</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Metode Pembayaran</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td style="border: 1px solid #000000;">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                <td style="border: 1px solid #000000;">{{ $transaction->user->name }}</td>
                <td style="border: 1px solid #000000;">{{ $transaction->note ?? '-' }}</td>
                <td style="border: 1px solid #000000;">{{ ucfirst($transaction->payment_method) }}</td>
                <td style="border: 1px solid #000000;">{{ $transaction->total_amount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <tr></tr>
    <tr>
        <td colspan="4" style="font-weight: bold; font-size: 12px;">RINCIAN PENGELUARAN OPERASIONAL</td>
    </tr>
    <tr>
        <th style="font-weight: bold; border: 1px solid #000000;">Tanggal</th>
        <th style="font-weight: bold; border: 1px solid #000000;">Keterangan</th>
        <th style="font-weight: bold; border: 1px solid #000000;">Dicatat Oleh</th>
        <th style="font-weight: bold; border: 1px solid #000000;">Jumlah</th>
    </tr>
    @foreach($expenses as $expense)
        <tr>
            <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($expense->date)->isoFormat('D MMM YYYY') }}</td>
            <td style="border: 1px solid #000000;">{{ $expense->description }}</td>
            <td style="border: 1px solid #000000;">{{ $expense->user->name ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $expense->amount }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="3" style="font-weight: bold; border: 1px solid #000000;">TOTAL PENGELUARAN</td>
        <td style="font-weight: bold; border: 1px solid #000000;">{{ $summary['total_expenses'] }}</td>
    </tr>
</table>

<table>
    <tr></tr>
    <tr>
        <td colspan="6" style="font-weight: bold; font-size: 12px;">RINCIAN PEMBELIAN STOK</td>
    </tr>
    <tr>
    <tr>
        <th style="font-weight: bold; border: 1px solid #000000;">Tanggal</th>
        <th style="font-weight: bold; border: 1px solid #000000;">Produk</th>
        <th style="font-weight: bold; border: 1px solid #000000;">Qty</th>
        <th style="font-weight: bold; border: 1px solid #000000;">Supplier</th>
        <th style="font-weight: bold; border: 1px solid #000000;">Catatan</th>
        <th style="font-weight: bold; border: 1px solid #000000;">Total</th>
    </tr>
    @foreach($purchases as $purchase)
        <tr>
            <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($purchase->date)->isoFormat('D MMM YYYY') }}</td>
            <td style="border: 1px solid #000000;">
                @foreach($purchase->items as $item)
                    {{ $item->product->name ?? 'Produk Dihapus' }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td style="border: 1px solid #000000;">
                @foreach($purchase->items as $item)
                    {{ $item->quantity }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td style="border: 1px solid #000000;">{{ $purchase->supplier->name ?? 'Umum' }}</td>
            <td style="border: 1px solid #000000;">{{ $purchase->note ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $purchase->total_amount }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="5" style="font-weight: bold; border: 1px solid #000000;">TOTAL PEMBELIAN STOK</td>
        <td style="font-weight: bold; border: 1px solid #000000;">{{ $summary['total_purchases'] }}</td>
    </tr>
</table>
