<table>
    <thead>
        <tr>
            <th colspan="6" style="font-weight: bold; font-size: 14px; text-align: center;">BUKU KAS UMUM</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center;">Tanggal</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center;">Kategori</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center;">Keterangan / Catatan</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: right;">Debit (Masuk)</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: right;">Kredit (Keluar)</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: right;">Saldo Berjalan</th>
        </tr>
    </thead>
    <tbody>
        <!-- Opening Balance -->
        <tr>
            <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}</td>
            <td style="border: 1px solid #000000; font-weight: bold;">SALDO AWAL</td>
            <td style="border: 1px solid #000000; font-weight: bold;">Opening Balance</td>
            <td style="border: 1px solid #000000;">0</td>
            <td style="border: 1px solid #000000;">0</td>
            <td style="border: 1px solid #000000; font-weight: bold; background-color: #fefce8;">{{ $openingBalance }}</td>
        </tr>

        @foreach($results as $row)
            <tr>
                <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}</td>
                <td style="border: 1px solid #000000;">{{ $row->category }}</td>
                <td style="border: 1px solid #000000;">{{ $row->note }}</td>
                <td style="border: 1px solid #000000;">{{ $row->debit }}</td>
                <td style="border: 1px solid #000000;">{{ $row->kredit }}</td>
                <td style="border: 1px solid #000000;">{{ $row->balance }}</td>
            </tr>
        @endforeach
        
        <!-- Closing Balance (for visual clarity at the end) -->
        <tr>
            <td colspan="3" style="border: 1px solid #000000; font-weight: bold; text-align: right;">SALDO AKHIR (Closing Balance)</td>
            <td style="border: 1px solid #000000; font-weight: bold;">{{ collect($results)->sum('debit') }}</td>
            <td style="border: 1px solid #000000; font-weight: bold;">{{ collect($results)->sum('kredit') }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; background-color: #eef2ff;">{{ $results ? end($results)->balance ?? $openingBalance : $openingBalance }}</td>
        </tr>
    </tbody>
</table>
