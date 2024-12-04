<h2 class="text-2xl font-semibold mb-4">Daftar Mahasiswa Bimbingan</h2>
<table class="min-w-full table-auto border-collapse border border-gray-300">
    <thead>
        <tr>
            <th class="px-4 py-2 text-left bg-gray-100 border-b">Nama</th>
            <th class="px-4 py-2 text-left bg-gray-100 border-b">NIM</th>
            <th class="px-4 py-2 text-left bg-gray-100 border-b">Judul Skripsi</th>
            <th class="px-4 py-2 text-left bg-gray-100 border-b">Program Studi</th>
            <th class="px-4 py-2 text-left bg-gray-100 border-b">Fakultas</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($mahasiswa)): ?>
            <?php foreach ($mahasiswa as $mhs): ?>
                <tr class="border-b">
                    <td class="px-4 py-2"><?php echo $mhs->nama; ?></td>
                    <td class="px-4 py-2"><?php echo $mhs->nim; ?></td>
                    <td class="px-4 py-2"><?php echo $mhs->judul_skripsi; ?></td>
                    <td class="px-4 py-2"><?php echo $mhs->prodi; ?></td>
                    <td class="px-4 py-2"><?php echo $mhs->fakultas; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="px-4 py-2 text-center">Tidak ada mahasiswa bimbingan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
