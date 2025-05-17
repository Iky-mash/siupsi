<h2 class="text-2xl font-bold mb-4">Daftar Ruangan</h2>
<a href="<?= site_url('ruangan/tambah'); ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
    Tambah Ruangan
</a>

<div class="flex flex-col md:flex-row gap-10 mt-6">
    <!-- Tabel Sempro -->
    <div class="w-full md:w-1/2">
        <h3 class="text-xl font-semibold mb-2">Ruangan SEMPRO</h3>
        <div class="overflow-x-auto rounded shadow">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Nama</th>
                        <th class="px-4 py-2 border">Kapasitas</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ruangan_sempro as $r): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border"><?= $r->id; ?></td>
                        <td class="px-4 py-2 border"><?= $r->nama_ruangan; ?></td>
                        <td class="px-4 py-2 border"><?= $r->kapasitas; ?></td>
                        <td class="px-4 py-2 border">
                            <a href="<?= site_url('ruangan/edit/'.$r->id); ?>" class="text-blue-600 hover:underline">Edit</a> |
                            <a href="<?= site_url('ruangan/hapus/'.$r->id); ?>" onclick="return confirm('Hapus data?')" class="text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Semhas -->
    <div class="w-full md:w-1/2">
        <h3 class="text-xl font-semibold mb-2">Ruangan SEMHAS</h3>
        <div class="overflow-x-auto rounded shadow">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Nama</th>
                        <th class="px-4 py-2 border">Kapasitas</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ruangan_semhas as $r): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border"><?= $r->id; ?></td>
                        <td class="px-4 py-2 border"><?= $r->nama_ruangan; ?></td>
                        <td class="px-4 py-2 border"><?= $r->kapasitas; ?></td>
                        <td class="px-4 py-2 border">
                            <a href="<?= site_url('ruangan/edit/'.$r->id); ?>" class="text-blue-600 hover:underline">Edit</a> |
                            <a href="<?= site_url('ruangan/hapus/'.$r->id); ?>" onclick="return confirm('Hapus data?')" class="text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
