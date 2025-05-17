<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Jadwal Ujian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-6 px-4">

    <div class="container mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Daftar Jadwal Ujian</h2>

        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full text-sm text-left text-gray-500">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-gray-700">Mahasiswa ID</th>
                        <th class="px-4 py-2 text-gray-700">Judul Skripsi</th>
                        <th class="px-4 py-2 text-gray-700">Tipe Ujian</th>
                        <th class="px-4 py-2 text-gray-700">Tanggal</th>
                        <th class="px-4 py-2 text-gray-700">Slot Waktu</th>
                        <th class="px-4 py-2 text-gray-700">Ruangan</th>
                        <th class="px-4 py-2 text-gray-700">Pembimbing</th>
                        <th class="px-4 py-2 text-gray-700">Penguji 1</th>
                        <th class="px-4 py-2 text-gray-700">Penguji 2</th>
                        <th class="px-4 py-2 text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jadwal_ujian as $row): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-2"><?= $row->mahasiswa_nama ?></td>
                            <td class="px-4 py-2"><?= $row->judul_skripsi ?></td>
                            <td class="px-4 py-2"><?= $row->tipe_ujian ?></td>
                            <td class="px-4 py-2"><?= $row->tanggal ?></td>
                            <td class="px-4 py-2"><?= $row->slot_waktu ?></td>
                            <td class="px-4 py-2"><?= $row->nama_ruangan ?></td>
                            <td class="px-4 py-2"><?= $row->pembimbing_nama ?></td>
                            <td class="px-4 py-2"><?= $row->penguji1_nama ?></td>
                            <td class="px-4 py-2"><?= $row->penguji2_nama ?></td>
                            <td class="px-4 py-2"><?= $row->status_konfirmasi ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
