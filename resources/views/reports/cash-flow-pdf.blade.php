<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Kas Umum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #444;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-blue { color: #2563eb; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .bg-gray { background-color: #f3f4f6; }
        .font-bold { font-weight: bold; }
        .small-text { font-size: 10px; color: #666; }
        
        .cat-trx { background-color: #d1fae5; color: #065f46; padding: 2px 4px; border-radius: 3px; font-weight: bold; font-size: 9px; }
        .cat-out { background-color: #ffe4e6; color: #9f1239; padding: 2px 4px; border-radius: 3px; font-weight: bold; font-size: 9px; }
        .cat-kas { background-color: #e0e7ff; color: #3730a3; padding: 2px 4px; border-radius: 3px; font-weight: bold; font-size: 9px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN BUKU KAS UMUM</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="32%">Kategori & Keterangan</th>
                <th class="text-right text-green" width="16%">Debit (Masuk)</th>
                <th class="text-right text-red" width="16%">Kredit (Keluar)</th>
                <th class="text-right font-bold" width="16%">Saldo Berjalan</th>
            </tr>
        </thead>
        <tbody>
            <!-- Opening Balance -->
            <tr class="bg-gray font-bold">
                <td class="text-center">-</td>
                <td>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</td>
                <td>SALDO AWAL (Opening Balance)</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
            </tr>

            @php $rowNum = 1; @endphp
            @forelse($results as $row)
                <tr>
                    <td class="text-center">{{ $rowNum++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                    <td>
                        <div>
                            @if(str_contains($row->category, '[TRX]'))
                                <span class="cat-trx">{{ substr($row->category, 0, 5) }}</span>
                            @elseif(str_contains($row->category, '[OUT]'))
                                <span class="cat-out">{{ substr($row->category, 0, 5) }}</span>
                            @else
                                <span class="cat-kas">{{ substr($row->category, 0, 5) }}</span>
                            @endif
                            {{ str_replace(['[TRX] ', '[OUT] ', '[KAS] '], '', $row->category) }}
                        </div>
                        @if($row->note)
                            <div class="small-text" style="margin-top: 3px;">{{ $row->note }}</div>
                        @endif
                    </td>
                    <td class="text-right text-green font-bold">
                        {{ $row->debit > 0 ? 'Rp ' . number_format($row->debit, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right text-red font-bold">
                        {{ $row->kredit > 0 ? 'Rp ' . number_format($row->kredit, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-bold">
                        Rp {{ number_format($row->balance, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">
                        Tidak ada mutasi kas pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right font-bold" style="background-color: #f8f9fa;">SALDO AKHIR (Closing Balance)</td>
                <td class="text-right font-bold" style="background-color: #eef2ff; color: #3730a3;">
                    Rp {{ number_format($results ? end($results)->balance ?? $openingBalance : $openingBalance, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
