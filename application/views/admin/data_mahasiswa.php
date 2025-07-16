<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h6 class="text-lg font-semibold text-gray-700">Data Mahasiswa</h6>

            <div class="flex items-center space-x-4">
                
                <form action="<?= base_url('admin/data_mahasiswa'); ?>" method="get" class="flex items-center">
                    <input 
                        type="text" 
                        name="keyword" 
                        placeholder="Cari Nama/NIM..."
                        class="px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        value="<?= htmlspecialchars($this->input->get('keyword') ?? ''); ?>"
                    >
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-r-lg hover:bg-blue-700 transition duration-200"
                    >
                        Cari
                    </button>
                </form>
                
                <a href="<?= base_url('admin/importdata_mhs'); ?>" class="px-4 py-2 text-sm font-semibold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition duration-200">
                    Import Data Mahasiswa
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-sm font-semibold">No</th>
                        <th class="border border-gray-300 px-4 py-2 text-sm font-semibold">Nama</th>
                        <th class="border border-gray-300 px-4 py-2 text-sm font-semibold">NIM</th>
                        <th class="border border-gray-300 px-4 py-2 text-sm font-semibold">Tahun Masuk</th>
                        <th class="border border-gray-300 px-4 py-2 text-sm font-semibold">Pembimbing</th>
                        <th class="border border-gray-300 px-4 py-2 text-sm font-semibold">Penguji 1</th>
                        <th class="border border-gray-300 px-4 py-2 text-sm font-semibold">Penguji 2</th>
                        <th class="border border-gray-300 px-4 py-2 text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($mahasiswa as $mhs): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center text-sm"><?= $no++; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-sm"><?= htmlspecialchars($mhs->nama); ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-sm"><?= htmlspecialchars($mhs->nim); ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-sm"><?= htmlspecialchars($mhs->tahun_masuk); ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-sm"><?= htmlspecialchars(isset($mhs->pembimbing_nama) ? $mhs->pembimbing_nama : '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-sm"><?= htmlspecialchars(isset($mhs->penguji1_nama) ? $mhs->penguji1_nama : '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-sm"><?= htmlspecialchars(isset($mhs->penguji2_nama) ? $mhs->penguji2_nama : '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <a href="<?= base_url('admin/edit_mahasiswa/' . $mhs->id); ?>" class="text-blue-600 hover:text-blue-800">Edit</a>
                                <span class="mx-2">|</span>
                                <a href="<?= base_url('admin/delete_mahasiswa/' . $mhs->id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="text-red-600 hover:text-red-800">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>