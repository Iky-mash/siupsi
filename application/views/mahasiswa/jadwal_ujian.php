<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
  <!-- row 1 -->
  <div class="container mx-auto flex justify-between items-center">
    <h2 class="text-2xl font-semibold text-gray-800">Detail Pengajuan Ujian Skripsi</h2>
    <a href="<?= base_url('pengajuan/form'); ?>" 
       class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition duration-200">
      Ajukan Jadwal Baru
    </a>
  </div>

  <?php if (!empty($pengajuan)): ?>
    <?php foreach ($pengajuan as $item): ?>
        <div class="flex flex-col w-full p-6 mt-6 bg-white shadow-soft-xl rounded-2xl">
            <h6 class="px-6 py-3 font-bold text-left uppercase align-middle text-xs text-slate-400 opacity-70">Detail Pengajuan</h6>
            <div class="grid grid-cols-2 gap-4 px-6 py-4">
                <div class="font-bold text-slate-400">Judul Skripsi</div>
                <div class="text-slate-500"><?= htmlspecialchars($item['judul_skripsi']); ?></div>

                <div class="font-bold text-slate-400">Status</div>
                <div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        <?= $item['status'] === 'Ditolak' ? 'bg-red-100 text-red-600' : 
                        ($item['status'] === 'Disetujui' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'); ?>">
                        <?= ucfirst($item['status']); ?>
                    </span>
                </div>

                <div class="font-bold text-slate-400">Lembar Pengesahan</div>
                <div>
                    <?php if ($item['lembar_pengesahan']): ?>
                        <a href="<?= base_url('assets/file/' . $item['lembar_pengesahan']); ?>" 
                           target="_blank" 
                           class="text-blue-500 hover:underline font-semibold">
                           Lihat Lembar Pengesahan
                        </a>
                    <?php else: ?>
                        <span class="text-slate-400">Tidak Ada Lembar Pengesahan</span>
                    <?php endif; ?>
                </div>

                <div class="font-bold text-slate-400">Alasan Penolakan</div>
                <div class="text-slate-500">
                    <?= $item['alasan_penolakan'] ? htmlspecialchars($item['alasan_penolakan']) : '-'; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="mt-4 text-center text-slate-400">Anda belum mengajukan ujian skripsi.</p>
<?php endif; ?>

