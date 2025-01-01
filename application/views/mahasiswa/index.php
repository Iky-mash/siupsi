<div class="relative overflow-x-auto p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 dark:text-white">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 flex items-center space-x-2">
        <span>📝</span>
        <span>Informasi Skripsi Mahasiswa</span>
    </h2>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Detail</th>
                <th scope="col" class="px-6 py-3">Informasi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mahasiswa as $mhs): ?>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Nama
                    </th>
                    <td class="px-6 py-4">
                        <?= $mhs->nama; ?>
                    </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Email
                    </th>
                    <td class="px-6 py-4">
                        <?= $mhs->email; ?>
                    </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Dosen Pembimbing
                    </th>
                    <td class="px-6 py-4">
                        <?= $mhs->pembimbing_nama; ?>
                    </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Judul Skripsi
                    </th>
                    <td class="px-6 py-4">
                        <?= $mhs->judul_skripsi; ?>
                    </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Dosen Penguji 1
                    </th>
                    <td class="px-6 py-4">
                        <?= $mhs->penguji1_nama; ?>
                    </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Dosen Penguji 2
                    </th>
                    <td class="px-6 py-4">
                        <?= $mhs->penguji2_nama; ?>
                    </td>
                </tr>
                <!-- <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Status
                    </th>
                    <td class="px-6 py-4">
                        <?= $mhs->is_active; ?>
                    </td>
                </tr> -->
            <?php endforeach; ?>
        </tbody>
    </table>
    
</div>
