<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Pengajuan Ujian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6 text-center">Data Pengajuan Ujian Mahasiswa</h1>
        
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fakultas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Prodi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $no = 1; foreach ($pengajuan as $p): ?>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap"><?= $no++; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $p['nama']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $p['nim']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $p['fakultas']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $p['prodi']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $p['tanggal_pengajuan']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?= $p['status'] == 'Disetujui' ? 'bg-green-100 text-green-800' : 
                                     ($p['status'] == 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                <?= $p['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pengajuan)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data pengajuan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
