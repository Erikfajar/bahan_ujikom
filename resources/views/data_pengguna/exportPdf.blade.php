@php
    $Konversi = new \App\Helpers\Konversi(); //panggil no static function
    $Tanggal = new \App\Helpers\Tanggal(); //panggil no static function
@endphp
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Pengguna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    <style type="text/css">
        table tr td,
        table tr th {
            font-size: 9pt;
        }

        table tr th {
            font-weight: bold;
            text-align: center;
            background: #f4f4f4;
        }
    </style>
    <center>
        <h4>DATA PENGGUNA</h4>
        <p>Waktu Export : {{ date('d-m-Y H:i') }}</p> <!-- SET KAPAN USER EXPORT -->
    </center>

    <table class='table table-bordered'>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Hak Akses</th>
                <th>Tanggal Buat Akun</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1; @endphp <!-- MEMBUAT VAR $NO DENGAN NILAI 1 -->
            @if (count($data))
                @foreach ($data as $dt)
                    <!-- MEMBUAT PERULANGAN UNTUK MENAMPILKAN HASIL DARI DB -->
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $dt->name ?? '' }}</td>
                        <td>{{ $dt->email ?? '' }}</td>
                        <td>{{ $dt->namerole ?? '' }}</td>
                        <td>{{ isset($dt->created_at) ? $Tanggal->ind($dt->created_at ?? '', '/') : '' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

</body>

</html>
