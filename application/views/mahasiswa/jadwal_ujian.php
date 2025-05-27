<div class="max-w-6xl mx-auto">

<div class="flex justify-end mb-1 space-x-4">
    <a href="<?= site_url('pengajuan'); ?>" 
       class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm font-medium shadow">
     + Ajukan Ujian
    </a>

    <a href="<?= site_url('mahasiswa/riwayat_pengajuan'); ?>" 
       class="inline-block bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition duration-200 text-sm font-medium shadow">
     📄 Lihat Riwayat Pengajuan
    </a>
</div>

<div class="max-w-6xl mx-auto my-10 p-6 bg-white shadow-xl rounded-xl">
    <div class="mb-8 space-y-4">
        <?php
        $status_map = [
            'draft' => ['text' => 'Sedang dalam Pengecekan oleh Akademik', 'class' => 'bg-yellow-100 text-yellow-700 border-yellow-300', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>'],
            'dikonfirmasi' => ['text' => 'Telah Dikonfirmasi dan Menunggu Penjadwalan', 'class' => 'bg-green-100 text-green-700 border-green-300', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>'],
            'ditolak' => ['text' => 'Ditolak (Silakan cek alasan penolakan dan perbaiki)', 'class' => 'bg-red-100 text-red-700 border-red-300', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>'],
            'dijadwalkan' => ['text' => 'Sudah Dijadwalkan', 'class' => 'bg-blue-100 text-blue-700 border-blue-300', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>'],
        ];

        $pengajuan_types = [
            'Sempro' => isset($status_sempro) ? $status_sempro : null,
            'Semhas' => isset($status_semhas) ? $status_semhas : null,
        ];

        $ada_pengajuan_aktif_untuk_ditampilkan = false; // Flag baru
        foreach ($pengajuan_types as $tipe => $pengajuan) {
            if ($pengajuan) { // Jika ada data pengajuan untuk tipe ini
                $status_info = isset($status_map[$pengajuan->status]) ? $status_map[$pengajuan->status] : ['text' => 'Status Tidak Dikenal', 'class' => 'bg-gray-100 text-gray-700 border-gray-300', 'icon' => ''];
                
                $show_this_pengajuan_status_box = true;

                // Logika untuk menyembunyikan status "dikonfirmasi menunggu jadwal" jika sudah ada jadwal approve
                if ($pengajuan->status == 'dikonfirmasi') { // Ini adalah status "Telah Dikonfirmasi dan Menunggu Penjadwalan"
                    if ($tipe == 'Sempro' && $has_approved_sempro_schedule) {
                        $show_this_pengajuan_status_box = false;
                    } elseif ($tipe == 'Semhas' && $has_approved_semhas_schedule) {
                        $show_this_pengajuan_status_box = false;
                    }
                }
                
                // Jika pengajuan statusnya adalah 'dijadwalkan', dan memang ada jadwal yang disetujui untuk tipe ini,
                // maka jangan tampilkan status 'dijadwalkan' dari pengajuan, karena jadwalnya akan muncul di bawah.
                // Ini opsional, tergantung apakah Anda ingin status 'dijadwalkan' tetap muncul atau tidak.
                // if ($pengajuan->status == 'dijadwalkan') {
                //     if ($tipe == 'Sempro' && $has_approved_sempro_schedule) {
                //          $show_this_pengajuan_status_box = false;
                //     } elseif ($tipe == 'Semhas' && $has_approved_semhas_schedule) {
                //          $show_this_pengajuan_status_box = false;
                //     }
                // }


                if ($show_this_pengajuan_status_box) {
                    $ada_pengajuan_aktif_untuk_ditampilkan = true; // Set flag ini jika ada box yang ditampilkan
        ?>
                <div class="p-4 border rounded-lg text-sm flex items-start gap-3 <?= $status_info['class']; ?>">
                    <span class="mt-0.5"><?= $status_info['icon']; ?></span>
                    <div>
                        <p class="font-semibold">Status Pengajuan <?= htmlspecialchars($pengajuan->tipe_ujian); ?> Anda:</p>
                        <p class="text-base font-medium"><?= htmlspecialchars($status_info['text']); ?></p>
                        <p class="text-xs mt-1">Judul: <?= htmlspecialchars($pengajuan->judul_skripsi); ?></p>
                        <p class="text-xs">Tanggal Pengajuan: <?= htmlspecialchars(date('d M Y, H:i', strtotime($pengajuan->tanggal_pengajuan))); ?></p>
                        <?php if ($pengajuan->status == 'ditolak' && !empty($pengajuan->alasan_penolakan)): ?>
                            <p class="text-xs mt-1 pt-1 border-t border-current"><strong>Alasan Penolakan:</strong> <?= htmlspecialchars($pengajuan->alasan_penolakan); ?></p>
                            <a href="<?= site_url('pengajuan'); ?>" class="text-xs inline-block mt-2 px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700 transition">Perbaiki Pengajuan</a>
                        <?php endif; ?>
                    </div>
                </div>
        <?php
                }
            }
        }

        // Menggunakan flag baru untuk pesan "Anda belum memiliki pengajuan..."
        if (!$ada_pengajuan_aktif_untuk_ditampilkan && empty($jadwal)) { // Hanya tampilkan jika tidak ada status pengajuan DAN tidak ada jadwal
        ?>
            <div class="p-4 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-600 text-center">
                Anda belum memiliki pengajuan ujian Sempro atau Semhas yang aktif atau terjadwal.
                <a href="<?= site_url('pengajuan'); ?>" class="text-indigo-600 hover:text-indigo-800 font-semibold">Ajukan sekarang?</a>
            </div>
        <?php
        }
        ?>
    </div>
    
    <?php if (!empty($jadwal)): // $jadwal sekarang hanya berisi yang disetujui ?>
 
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($jadwal as $row): ?>
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 p-5 border-l-4 border-blue-500">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm font-medium text-blue-600 uppercase"><?= $row->tipe_ujian ?></span>
                <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full"><?= date('d M Y', strtotime($row->tanggal)) ?></span>
            </div>

            <h2 class="text-lg font-semibold text-gray-800 mb-2"><?= htmlspecialchars($row->judul_skripsi) ?></h2>

            <div class="text-sm text-gray-600 mb-2">
                <p><strong>🕒 Waktu:</strong> <?= htmlspecialchars($row->slot_waktu) ?></p>
                <p><strong>🏛️ Ruangan:</strong> <?= htmlspecialchars($row->nama_ruangan) ?></p>
            </div>

            <div class="text-sm text-gray-700 mb-2">
                <p><strong>👨‍🏫 Pembimbing:</strong> <?= htmlspecialchars($row->pembimbing_nama) ?></p>
                <p><strong>👩‍⚖️ Penguji 1:</strong> <?= htmlspecialchars($row->penguji1_nama) ?></p>
                <p><strong>👩‍⚖️ Penguji 2:</strong> <?= htmlspecialchars($row->penguji2_nama) ?></p>
            </div>

            <div class="mt-3 flex justify-between items-center">
                <?php
                  // Karena jadwal sudah pasti 'Disetujui' dari query, kita bisa sederhanakan ini
                  // Namun, jika ada kemungkinan status lain masuk (meski query sudah filter), biarkan saja.
                  $status = strtolower($row->status_konfirmasi);
                  $statusClass = match ($status) {
                      'disetujui' => 'bg-green-100 text-green-700',
                      'ditolak'   => 'bg-red-100 text-red-700', // Seharusnya tidak muncul karena query
                      default     => 'bg-yellow-100 text-yellow-700' // Seharusnya tidak muncul
                  };
                ?>
                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                    <?= ucfirst(htmlspecialchars($row->status_konfirmasi)) ?>
                </span>
                
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php elseif (!$ada_pengajuan_aktif_untuk_ditampilkan): // Jika tidak ada jadwal DAN tidak ada status pengajuan yang ditampilkan sebelumnya ?>
    <div class="bg-white p-6 rounded-lg shadow text-center text-gray-600">
        Anda belum memiliki jadwal ujian yang disetujui.
    </div>
    <?php endif; ?>
</div>

</div>
</div>