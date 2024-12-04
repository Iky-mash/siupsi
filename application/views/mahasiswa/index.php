<div class="overflow-x-auto p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 dark:text-white">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 flex items-center space-x-2">
        <span>📝</span>
        <span>Informasi Skripsi Mahasiswa</span>
    </h2>
    <?php foreach ($mahasiswa as $mhs): ?>
    <div class="space-y-6">
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
            <span class="font-medium text-gray-600 dark:text-gray-300">Nama:</span>
            <p class="text-lg text-gray-800 dark:text-gray-200"><?php echo $mhs->nama; ?></p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
            <span class="font-medium text-gray-600 dark:text-gray-300">Email:</span>
            <p class="text-lg text-gray-800 dark:text-gray-200"><?php echo $mhs->email; ?></p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
            <span class="font-medium text-gray-600 dark:text-gray-300">Dosen Pembimbing:</span>
            <p class="text-lg text-gray-800 dark:text-gray-200"><?php echo $mhs->pembimbing_nama; ?></p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
            <span class="font-medium text-gray-600 dark:text-gray-300">Judul Skripsi:</span>
            <p class="text-lg text-gray-800 dark:text-gray-200"><?php echo $mhs->judul_skripsi; ?></p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
            <span class="font-medium text-gray-600 dark:text-gray-300">Dosen Penguji 1:</span>
            <p class="text-lg text-gray-800 dark:text-gray-200"><?php echo $mhs->penguji1_nama; ?></p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
            <span class="font-medium text-gray-600 dark:text-gray-300">Dosen Penguji 2:</span>
            <p class="text-lg text-gray-800 dark:text-gray-200"><?php echo $mhs->penguji2_nama; ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>
