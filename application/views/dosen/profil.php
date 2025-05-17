<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
</head>
<body>
    <h1>Profil Dosen</h1>
    <table border="1" cellpadding="10" cellspacing="0">
    <tr>
<tr>
    <th>Nama</th>
    <td><?= $dosen->nama; ?></td>
</tr>
<tr>
    <th>Email</th>
    <td><?= $dosen->email; ?></td>
</tr>
<tr>
    <th>NIP</th>
    <td><?= $dosen->nip; ?></td>
</tr>


    </table>

</body>
</html>
