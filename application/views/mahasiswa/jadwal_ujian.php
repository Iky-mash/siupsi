<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
  <!-- row 1 -->
  <div class="container mx-auto mt-8 flex justify-between items-center">
    <h2 class="text-2xl font-semibold text-gray-800">Detail Pengajuan Ujian Skripsi</h2>
    <a href="<?= base_url('pengajuan/form'); ?>" 
       class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition duration-200">
      Ajukan Jadwal Baru
    </a>
  </div>

  <?php if (!empty($pengajuan)): ?>
    <?php foreach ($pengajuan as $item): ?>
      <div class="bg-white shadow-md rounded-lg p-4 mt-8 mb-4">
        <div class="grid grid-cols-2 gap-4">
          <div class="font-medium text-gray-700">Judul Skripsi</div>
          <div class="text-gray-800 pl-2"><?= htmlspecialchars($item['judul_skripsi']); ?></div>

          <div class="font-medium text-gray-700">Status</div>
          <div class="pl-2">
            <span class="px-2 py-1 rounded-full 
                <?= $item['status'] === 'Ditolak' ? 'bg-red-100 text-red-800' : 
                ($item['status'] === 'Disetujui' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                <?= ucfirst($item['status']); ?>
            </span>
          </div>

          <div class="font-medium text-gray-700">Lembar Pengesahan</div>
          <div class="pl-2">
            <?php if ($item['lembar_pengesahan']): ?>
              <a href="<?= base_url('assets/file/' . $item['lembar_pengesahan']); ?>" 
                 target="_blank" 
                 class="text-blue-500 hover:underline">
                 Lihat Lembar Pengesahan
              </a>
            <?php else: ?>
              <span class="text-gray-500">Tidak Ada Lembar Pengesahan</span>
            <?php endif; ?>
          </div>

          <div class="font-medium text-gray-700">Alasan Penolakan</div>
          <div class="pl-2">
            <?= $item['alasan_penolakan'] ? htmlspecialchars($item['alasan_penolakan']) : '-'; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="mt-4 text-gray-500">Anda belum mengajukan ujian skripsi.</p>
  <?php endif; ?>
</div>
