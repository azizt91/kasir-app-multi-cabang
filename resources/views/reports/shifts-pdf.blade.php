<!DOCTYPE html>
<html>
<head>
    <title>Laporan Shift Kasir</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { bg-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 30px; }
        .meta { margin-bottom: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN SHIFT KASIR</h2>
        <p>{{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="meta">
        <strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Kasir / Waktu</th>
                <th class="text-right">Modal Awal</th>
                <th class="text-right">Ekspektasi</th>
                <th class="text-right">Uang Fisik</th>
                <th class="text-right">Selisih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shifts as $shift)
            <tr>
                <td>
                    <strong>{{ $shift->user->name ?? 'Unknown' }}</strong><br>
                    <small>{{ $shift->start_time->format('d/m/Y H:i') }} - {{ $shift->end_time ? $shift->end_time->format('H:i') : 'Aktif' }}</small>
                </td>
                <td class="text-right">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($shift->expected_cash ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($shift->actual_cash ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">
                    @if($shift->difference == 0) Pas
                    @elseif($shift->difference > 0) +Rp {{ number_format($shift->difference, 0, ',', '.') }}
                    @else Rp {{ number_format($shift->difference, 0, ',', '.') }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
