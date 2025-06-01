
    <div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h6 class="text-lg font-semibold text-gray-700">Data Dosen</h6>
                <a href="<?= base_url('admin/tambah_dosen'); ?>" class="px-4 py-2 text-sm font-semibold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition duration-200">
                    Tambah Dosen
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2 text-sm font-semibold text-center">No</th>
                            <th class="border border-gray-300 px-4 py-2 text-sm font-semibold text-center">Nama</th>
                            <th class="border border-gray-300 px-4 py-2 text-sm font-semibold text-center">Email</th>
                            <th class="border border-gray-300 px-4 py-2 text-sm font-semibold text-center">NIK</th>
                            <th class="border border-gray-300 px-4 py-2 text-sm font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; // Inisialisasi variabel $no ?>
                        <?php foreach ($dosen as $d) : ?>
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 px-4 py-2 text-center text-sm"><?= $no++; ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-sm text-center"><?= htmlspecialchars($d->nama); ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-sm text-center"><?= htmlspecialchars($d->email); ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-sm text-center"><?= htmlspecialchars($d->nip); ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-center text-sm">
                                    <a href="<?= site_url('admin/edit_dosen/' . $d->id); ?>" class="text-blue-600 hover:text-blue-800">Edit</a>
                                    <span class="mx-2">|</span>
                                    <a href="<?= site_url('admin/delete_dosen/' . $d->id); ?>" onclick="return confirm('Anda yakin ingin menghapus data ini?')" class="text-red-600 hover:text-red-800">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
