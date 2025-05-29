
    <div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
    <h6 class="text-xl font-semibold text-gray-700">Laporan Seminar</h6>
    <a href="<?= base_url('admin/download_laporan_seminar'); ?>" class="px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 transition duration-200 flex items-center"> <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
        Download</a>
</div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-600">No</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-600">Nama Mahasiswa</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-600">NIM</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-600">Status Sempro</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-600">Status Semhas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($mahasiswa_seminar)): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($mahasiswa_seminar as $mhs): ?>
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700 text-center"><?= $no++; ?></td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700"><?= htmlspecialchars($mhs->nama); ?></td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700"><?= htmlspecialchars($mhs->nim); ?></td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?= ($mhs->status_sempro == 'ACC' || $mhs->status_sempro == 'Lulus') ? 'bg-green-100 text-green-800' :
                                               (($mhs->status_sempro == 'Belum Mengajukan' || $mhs->status_sempro == 'Diproses') ? 'bg-yellow-100 text-yellow-800' :
                                               'bg-red-100 text-red-800'); ?>">
                                            <?= htmlspecialchars($mhs->status_sempro); ?>
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?= ($mhs->status_semhas == 'ACC' || $mhs->status_semhas == 'Lulus') ? 'bg-green-100 text-green-800' :
                                               (($mhs->status_semhas == 'Belum Mengajukan' || $mhs->status_semhas == 'Diproses') ? 'bg-yellow-100 text-yellow-800' :
                                               'bg-red-100 text-red-800'); ?>">
                                            <?= htmlspecialchars($mhs->status_semhas); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="border border-gray-300 px-4 py-10 text-center text-sm text-gray-500">
                                    Tidak ada data mahasiswa untuk ditampilkan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
