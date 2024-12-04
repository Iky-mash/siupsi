<?php
// Pastikan data mahasiswa ada di view
if (!isset($mahasiswa)) {
    die('Data mahasiswa tidak ditemukan.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="relative overflow-x-auto">
        <form action="<?= base_url('admin/update_mahasiswa/' . $mahasiswa->id) ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6">Edit Data Mahasiswa</h1>

            <div class="flex items-center mb-4">
                <label for="nama" class="font-bold text-slate-600 mr-2 w-32">Nama:</label>
                <input type="text" name="nama" id="nama" value="<?= isset($mahasiswa->nama) ? htmlspecialchars($mahasiswa->nama) : '' ?>" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full" required>
            </div>

            <div class="flex items-center mb-4">
                <label for="email" class="font-bold text-slate-600 mr-2 w-32">Email:</label>
                <input type="email" name="email" id="email" value="<?= isset($mahasiswa->email) ? htmlspecialchars($mahasiswa->email) : '' ?>" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full" required>
            </div>

            <div class="flex items-center mb-4">
                <label for="nim" class="font-bold text-slate-600 mr-2 w-32">NIM:</label>
                <input type="text" name="nim" id="nim" value="<?= isset($mahasiswa->nim) ? htmlspecialchars($mahasiswa->nim) : '' ?>" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full" required>
            </div>

            <div class="flex items-center mb-4">
                <label for="fakultas" class="font-bold text-slate-600 mr-2 w-32">Fakultas:</label>
                <input type="text" name="fakultas" id="fakultas" value="<?= isset($mahasiswa->fakultas) ? htmlspecialchars($mahasiswa->fakultas) : '' ?>" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full" required>
            </div>

            <div class="flex items-center mb-4">
                <label for="prodi" class="font-bold text-slate-600 mr-2 w-32">Prodi:</label>
                <input type="text" name="prodi" id="prodi" value="<?= isset($mahasiswa->prodi) ? htmlspecialchars($mahasiswa->prodi) : '' ?>" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full" required>
            </div>

            <div class="flex items-center mb-4">
                <label for="judul_skripsi" class="font-bold text-slate-600 mr-2 w-32">Judul Skripsi:</label>
                <input type="text" name="judul_skripsi" id="judul_skripsi" value="<?= isset($mahasiswa->judul_skripsi) ? htmlspecialchars($mahasiswa->judul_skripsi) : '' ?>" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full" required>
            </div>

            <div class="flex items-center mb-4">
                <label for="is_active" class="font-bold text-slate-600 mr-2 w-32">Status:</label>
                <select id="is_active" name="is_active" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full">
                    <option value="1" <?= $mahasiswa->is_active ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?= !$mahasiswa->is_active ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <!-- Pembimbing -->
<div class="flex items-center mb-4">
    <label for="pembimbing" class="font-bold text-slate-600 mr-2 w-32">Pembimbing:</label>
    <select name="pembimbing" id="pembimbing" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full">
        <option value="">-- Pilih Pembimbing --</option>
        <?php foreach ($dosen_options as $dosen): ?>
            <option value="<?= $dosen->id ?>" <?= ($mahasiswa->pembimbing_id == $dosen->id) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($dosen->nama); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Select untuk Dosen Penguji 1 -->
<div class="flex items-center mb-4">
    <label for="penguji1" class="font-bold text-slate-600 mr-2 w-32">Penguji 1:</label>
    <select name="penguji1" id="penguji1" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full">
        <option value="">-- Pilih Penguji 1 --</option>
        <?php foreach ($dosen_options as $dosen): ?>
            <option value="<?= $dosen->id ?>" <?= ($mahasiswa->penguji1_id == $dosen->id) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($dosen->nama); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Select untuk Dosen Penguji 2 -->
<div class="flex items-center mb-4">
    <label for="penguji2" class="font-bold text-slate-600 mr-2 w-32">Penguji 2:</label>
    <select name="penguji2" id="penguji2" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 w-full">
        <option value="">-- Pilih Penguji 2 --</option>
        <?php foreach ($dosen_options as $dosen): ?>
            <option value="<?= $dosen->id ?>" <?= ($mahasiswa->penguji2_id == $dosen->id) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($dosen->nama); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>



            <div id="form-mahasiswa" class="text-right">
                <button type="submit"  class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-600">Update</button>
            </div>
        </form>
    </div>
</body>



</html>


