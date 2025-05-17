<div class="max-w-6xl mx-auto">
  <h1 class="text-3xl font-bold text-gray-800 mb-6">📅 Jadwal Ujian Saya</h1>

  <div class="flex justify-end mb-6 space-x-4">
    <a href="<?= site_url('pengajuan/form'); ?>" 
       class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm font-medium shadow">
      + Ajukan Ujian
    </a>

    <a href="<?= site_url('pengajuan/riwayat'); ?>" 
       class="inline-block bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition duration-200 text-sm font-medium shadow">
      📄 Lihat Riwayat Pengajuan
    </a>
  </div>

  <?php if (!empty($jadwal)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($jadwal as $row): ?>
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 p-5 border-l-4 border-blue-500">
          <div class="flex justify-between items-center mb-3">
            <span class="text-sm font-medium text-blue-600 uppercase"><?= $row->tipe_ujian ?></span>
            <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full"><?= date('d M Y', strtotime($row->tanggal)) ?></span>
          </div>

          <h2 class="text-lg font-semibold text-gray-800 mb-2"><?= $row->judul_skripsi ?></h2>

          <div class="text-sm text-gray-600 mb-2">
            <p><strong>🕒 Waktu:</strong> <?= $row->slot_waktu ?></p>
            <p><strong>🏛️ Ruangan:</strong> <?= $row->nama_ruangan ?></p>
          </div>

          <div class="text-sm text-gray-700 mb-2">
            <p><strong>👨‍🏫 Pembimbing:</strong> <?= $row->pembimbing_nama ?></p>
            <p><strong>👩‍⚖️ Penguji 1:</strong> <?= $row->penguji1_nama ?></p>
            <p><strong>👩‍⚖️ Penguji 2:</strong> <?= $row->penguji2_nama ?></p>
          </div>

          <div class="mt-3 flex justify-between items-center">
            <?php
              $status = strtolower($row->status_konfirmasi);
              $statusClass = match ($status) {
                'disetujui' => 'bg-green-100 text-green-700',
                'ditolak'   => 'bg-red-100 text-red-700',
                default     => 'bg-yellow-100 text-yellow-700'
              };
            ?>
            <span class="inline-block px-3 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
              <?= ucfirst($row->status_konfirmasi) ?>
            </span>
           <!-- Contoh dalam tabel daftar jadwal -->
<a href="<?= site_url('mahasiswa/cetak_pdf/' . $row->id); ?>"
   class="text-sm bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700 transition"
   target="_blank">
   🖨️ Cetak PDF
</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="bg-white p-6 rounded-lg shadow text-center text-gray-600">
      Anda belum memiliki jadwal ujian.
    </div>
  <?php endif; ?>
</div>
