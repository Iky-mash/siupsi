<?php
// Pastikan data mahasiswa dan dosen_options ada di view
if (!isset($mahasiswa)) {
    die('Data mahasiswa tidak ditemukan.');
}
if (!isset($dosen_options)) {
    // Inisialisasi sebagai array kosong jika tidak ada untuk menghindari error di foreach
    // Idealnya, ini harus selalu di-pass dari controller
    $dosen_options = []; 
    // atau die('Data dosen tidak ditemukan.');
}
?>


<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 sm:py-12">
        <div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-xl shadow-lg">
            <form action="<?= base_url('admin/update_mahasiswa/' . $mahasiswa->id) ?>" method="POST">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-700 mb-8 text-center">Edit Data Mahasiswa</h1>

                <?php // Notifikasi jika ada (contoh, bisa Anda tambahkan dari controller)
                if ($this->session && $this->session->flashdata('form_message')): ?>
                    <div class="mb-4 p-3 rounded-md text-sm <?php 
                        $message_type = $this->session->flashdata('form_message_type') ?? 'info';
                        if ($message_type === 'success') echo 'bg-green-100 text-green-700 border border-green-300';
                        else if ($message_type === 'error') echo 'bg-red-100 text-red-700 border border-red-300';
                        else echo 'bg-blue-100 text-blue-700 border border-blue-300';
                    ?>">
                        <?= $this->session->flashdata('form_message'); ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 gap-y-6">
                    <div>
                        <label for="nama" class="block text-sm font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" value="<?= isset($mahasiswa->nama) ? htmlspecialchars($mahasiswa->nama) : '' ?>" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out" required placeholder="Masukkan nama lengkap">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="<?= isset($mahasiswa->email) ? htmlspecialchars($mahasiswa->email) : '' ?>" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out" required placeholder="contoh@email.com">
                    </div>

                    <div>
                        <label for="nim" class="block text-sm font-semibold text-gray-600 mb-1">NIM</label>
                        <input type="text" name="nim" id="nim" value="<?= isset($mahasiswa->nim) ? htmlspecialchars($mahasiswa->nim) : '' ?>" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out" required placeholder="Masukkan NIM">
                    </div>
                   
                    <div>
                        <label for="fakultas" class="block text-sm font-semibold text-gray-600 mb-1">Fakultas</label>
                        <input type="text" name="fakultas" id="fakultas" value="<?= isset($mahasiswa->fakultas) ? htmlspecialchars($mahasiswa->fakultas) : '' ?>" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out" placeholder="Masukkan nama fakultas">
                    </div>

                    <div>
                        <label for="prodi" class="block text-sm font-semibold text-gray-600 mb-1">Program Studi</label>
                        <input type="text" name="prodi" id="prodi" value="<?= isset($mahasiswa->prodi) ? htmlspecialchars($mahasiswa->prodi) : '' ?>" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out" placeholder="Masukkan nama program studi">
                    </div>

                     <div>
                            <label for="tahun_masuk" class="block text-sm font-semibold text-gray-600 mb-1">Tahun Masuk</label>
                            <input type="number" name="tahun_masuk" id="tahun_masuk" value="<?= isset($mahasiswa->tahun_masuk) ? htmlspecialchars($mahasiswa->tahun_masuk) : '' ?>" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out" required placeholder="Contoh: 2021" min="2000" max="2099">
                        </div>

                    <div>
                        <label for="judul_skripsi" class="block text-sm font-semibold text-gray-600 mb-1">Judul Skripsi</label>
                        <input type="text" name="judul_skripsi" id="judul_skripsi" value="<?= isset($mahasiswa->judul_skripsi) ? htmlspecialchars($mahasiswa->judul_skripsi) : '' ?>" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out" placeholder="Masukkan judul skripsi (jika ada)">
                    </div>
                    
                    <div>
                        <label for="is_active" class="block text-sm font-semibold text-gray-600 mb-1">Status Akun</label>
                        <select id="is_active" name="is_active" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out">
                            <option value="1" <?= (isset($mahasiswa->is_active) && $mahasiswa->is_active == 1) ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?= (isset($mahasiswa->is_active) && $mahasiswa->is_active == 0) ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <div>
                        <label for="pembimbing" class="block text-sm font-semibold text-gray-600 mb-1">Dosen Pembimbing</label>
                        <select name="pembimbing" id="pembimbing" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out">
                            <option value="">-- Pilih Dosen Pembimbing --</option>
                            <?php if (!empty($dosen_options)): ?>
                                <?php foreach ($dosen_options as $dosen): ?>
                                    <option value="<?= $dosen->id ?>" <?= (isset($mahasiswa->pembimbing_id) && $mahasiswa->pembimbing_id == $dosen->id) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($dosen->nama); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label for="penguji1" class="block text-sm font-semibold text-gray-600 mb-1">Dosen Penguji 1</label>
                        <select name="penguji1" id="penguji1" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out">
                            <option value="">-- Pilih Dosen Penguji 1 --</option>
                             <?php if (!empty($dosen_options)): ?>
                                <?php foreach ($dosen_options as $dosen): ?>
                                    <option value="<?= $dosen->id ?>" <?= (isset($mahasiswa->penguji1_id) && $mahasiswa->penguji1_id == $dosen->id) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($dosen->nama); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label for="penguji2" class="block text-sm font-semibold text-gray-600 mb-1">Dosen Penguji 2</label>
                        <select name="penguji2" id="penguji2" class="block w-full px-3 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition duration-150 ease-in-out">
                            <option value="">-- Pilih Dosen Penguji 2 --</option>
                             <?php if (!empty($dosen_options)): ?>
                                <?php foreach ($dosen_options as $dosen): ?>
                                    <option value="<?= $dosen->id ?>" <?= (isset($mahasiswa->penguji2_id) && $mahasiswa->penguji2_id == $dosen->id) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($dosen->nama); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 text-right">
                    <a href="<?= base_url('admin/data_mahasiswa') // Ganti dengan URL daftar mahasiswa Anda ?>" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 transition duration-150 ease-in-out mr-3">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg hover:from-blue-600 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-80">
                        Update Data Mahasiswa
                    </button>
                </div>
            </form>
        </div>
    </div>
