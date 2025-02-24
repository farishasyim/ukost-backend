<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Expense Report</title>
</head>

<body>
    <table width="100%" cellpadding="4" cellspacing="0" border="1">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Pengeluaran</th>
                <th>Pengguna</th>
                <th>PIC</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $row)
                <tr>
                    <td>{{ $row['created_at'] ?? '-' }}</td>
                    <td>{{ $row['title'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td>{{ $row['user']['name'] }}</td>
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
