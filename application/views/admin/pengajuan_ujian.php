<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50 p-6">
  <div class="flex flex-wrap -mx-3">
        <div class="flex-none w-full max-w-full px-3">
            <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                    <h6 class="text-2xl font-semibold text-blue-900">Data Pengajuan Ujian</h6>
                </div>
                <div class="flex-auto px-0 pt-0 pb-2">
                    <div class="p-0 overflow-x-auto">
                        <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                            <thead class="align-bottom">
                                <tr>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Nama Mahasiswa</th>
                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">NIM</th>
                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Judul Skripsi</th>
                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Status</th>
                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Lembar Pengesahan</th>
                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pengajuan as $item): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <!-- Nama Mahasiswa -->
                        <td class="px-6 py-4"><?= htmlspecialchars($item['nama_mahasiswa'], ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <!-- NIM -->
                        <td class="px-6 py-4"><?= htmlspecialchars($item['nim'], ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <!-- Judul Skripsi -->
                        <td class="px-6 py-4"><?= htmlspecialchars($item['judul_skripsi'], ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <!-- Status -->
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full 
                                <?= $item['status'] === 'Diajukan' ? 'bg-yellow-100 text-yellow-800' : ($item['status'] === 'Disetujui' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                <?= ucfirst($item['status']); ?>
                            </span>
                        </td>
                        <!-- Lembar Pengesahan -->
                        <td class="px-6 py-4">
                            <?php if ($item['lembar_pengesahan']): ?>
                                <a href="<?= base_url('assets/file/' . $item['lembar_pengesahan']); ?>" 
                                   target="_blank" 
                                   class="text-blue-500 hover:underline">
                                    Lihat Lembar Pengesahan
                                </a>
                            <?php else: ?>
                                <span class="text-gray-500">Tidak Ada Lembar Pengesahan</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Aksi -->
<td class="px-6 py-4">
    <div class="inline-flex gap-x-2">
        <?php if ($item['status'] === 'Diajukan'): ?>
            <a href="<?= base_url('admin/verifikasi_pengajuan/' . $item['id']); ?>" 
               class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                Setujui
            </a>
            <button type="button" 
        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition" 
        onclick="openModal(<?= $item['id']; ?>)">
    Tolak
</button>
        <?php else: ?>
            <span class="text-gray-500">Tidak Ada Aksi</span>
        <?php endif; ?>
    </div>
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

    <!-- Modal untuk alasan penolakan -->
<div id="tolakModal" class="hidden fixed inset-0 z-50 overflow-auto bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-xl font-semibold mb-4">Alasan Penolakan</h3>
        <form action="<?= base_url('admin/tolak_pengajuan/') ?>" method="post" id="tolakForm">
    <input type="hidden" name="pengajuan_id" id="pengajuan_id">
    <label for="alasan_penolakan" class="block mb-2">Alasan Penolakan:</label>
    <textarea name="alasan_penolakan" id="alasan_penolakan" class="w-full border-gray-300 rounded-md" required></textarea>
    <div class="mt-4 flex justify-end">
        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Kirim</button>
        <button type="button" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600" onclick="closeModal()">Batal</button>
    </div>
</form>
    </div>
</div>

</div>
  </div>
</div>
<script>
    function openModal(pengajuanId) {
        document.getElementById('pengajuan_id').value = pengajuanId;
        document.getElementById('tolakForm').action = '<?= base_url('admin/tolak_pengajuan/') ?>' + pengajuanId;
        document.getElementById('tolakModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('tolakModal').classList.add('hidden');
    }
</script>
</body>
</html>
