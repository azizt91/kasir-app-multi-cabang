<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold;">LAPORAN SHIFT KASIR</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;">Periode: {{ $startDate }} s/d {{ $endDate }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; background-color: #f2f2f2;">Kasir</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Waktu Mulai</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Waktu Selesai</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Modal Awal</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Ekspektasi Tunai</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Uang Fisik</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Selisih</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Catatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shifts as $shift)
        <tr>
            <td>{{ $shift->user->name ?? 'Unknown' }}</td>
            <td>{{ $shift->start_time->format('d/m/Y H:i') }}</td>
            <td>{{ $shift->end_time ? $shift->end_time->format('d/m/Y H:i') : 'Aktif' }}</td>
            <td>{{ $shift->starting_cash }}</td>
            <td>{{ $shift->expected_cash }}</td>
            <td>{{ $shift->actual_cash }}</td>
            <td>{{ $shift->difference }}</td>
            <td>{{ $shift->notes }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
