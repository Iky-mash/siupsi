
<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Jadwal Pekan Seminar</h2>
        <button onclick="toggleForm()" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
            Edit Jadwal
        </button>
    </div>

    <!-- NOTIFIKASI -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            <?= $this->session->flashdata('success'); ?>
        </div>
    <?php elseif ($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <?= $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- FORM EDIT JADWAL (HIDDEN) -->
    <div id="form-jadwal" class="hidden bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-700 mb-4">Edit Jadwal</h3>
        <form action="<?= base_url('admin/update_jadwal') ?>" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">Jenis Seminar</label>
                <select name="jenis" class="w-full border-gray-300 rounded-lg p-2">
                    <option value="sempro">Seminar Proposal</option>
                    <option value="semhas">Seminar Hasil</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="w-full border-gray-300 rounded-lg p-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="w-full border-gray-300 rounded-lg p-2" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Simpan</button>
        </form>
    </div>

    <!-- MENAMPILKAN HASIL JADWAL -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- CARD SEMPRO -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-800">Pekan Seminar Proposal</h3>
            <?php if ($sempro): ?>
                <p class="text-gray-600 mt-2">Tanggal: <strong><?= date('d M Y', strtotime($sempro['tanggal_mulai'])) ?></strong> - <strong><?= date('d M Y', strtotime($sempro['tanggal_selesai'])) ?></strong></p>
            <?php else: ?>
                <p class="text-gray-600 mt-2 italic">Belum ada jadwal</p>
            <?php endif; ?>
        </div>

        <!-- CARD SEMHAS -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-800">Pekan Seminar Hasil</h3>
            <?php if ($semhas): ?>
                <p class="text-gray-600 mt-2">Tanggal: <strong><?= date('d M Y', strtotime($semhas['tanggal_mulai'])) ?></strong> - <strong><?= date('d M Y', strtotime($semhas['tanggal_selesai'])) ?></strong></p>
            <?php else: ?>
                <p class="text-gray-600 mt-2 italic">Belum ada jadwal</p>
            <?php endif; ?>
        </div>
    </div>

<!-- DAFTAR RUANGAN -->
<div class="mt-10">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Ruangan</h2>
        <a href="<?= site_url('ruangan/tambah'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Tambah Ruangan
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- SEMPRO -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Ruangan SEMPRO</h3>
            <div class="overflow-x-auto rounded">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            
                            <th class="px-4 py-2 border">Nama</th>
                            <th class="px-4 py-2 border">Kapasitas</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ruangan_sempro as $r): ?>
                        <tr class="hover:bg-gray-50">
                           
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

        <!-- SEMHAS -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Ruangan SEMHAS</h3>
            <div class="overflow-x-auto rounded">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            
                            <th class="px-4 py-2 border">Nama</th>
                            <th class="px-4 py-2 border">Kapasitas</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ruangan_semhas as $r): ?>
                        <tr class="hover:bg-gray-50">
                            
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
</div>

</div>


