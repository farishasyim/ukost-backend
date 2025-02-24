<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Income Report</title>
</head>

<body>
    <table width="100%" cellpadding="4" cellspacing="0" border="1">
        <thead>
            <tr>
                <th>Tanggal Bayar</th>
                <th>Invoice</th>
                <th>Pengguna</th>
                <th>Kamar / Kategori</th>
                <th>Status</th>
                <th>Periode Tagihan</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $row)
                <tr>
                    <td>{{ $row['date'] ?? '-' }}</td>
                    <td>{{ $row['invoice'] }}</td>
                    <td>{{ $row['pivotRoom']['user']['name'] ?? '-' }}</td>
                    <td>{{ $row['pivotRoom']['room']['name'] ?? '-' }} /
                        {{ $row['pivotRoom']['room']['category']['name'] ?? '-' }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ date('F Y', strtotime($row['start_period'])) }}</td>
                    <td>Rp.{{ number_format($row['price']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

<script>
    window.print()
</script>
