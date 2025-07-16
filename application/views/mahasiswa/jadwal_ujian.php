<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
    <div class="max-w-20xl mx-auto">

        <div class="flex justify-end mb-1 space-x-4">
            <a href="<?= site_url('pengajuan'); ?>" 
               class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm font-medium shadow">
                + Ajukan Ujian
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
                    'ACC' => ['text' => 'Ujian Telah Selesai dan dinyatakan Lulus (ACC)', 'class' => 'bg-green-100 text-green-700 border-green-300', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>'],
                    'Mengulang' => ['text' => 'Ujian Telah Selesai dan Anda harus Mengulang', 'class' => 'bg-orange-100 text-orange-700 border-orange-300', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>'],
                ];

                $pengajuan_types = [
                    'Sempro' => isset($status_sempro_pengajuan) ? $status_sempro_pengajuan : null,
                    'Semhas' => isset($status_semhas_pengajuan) ? $status_semhas_pengajuan : null,
                ];

                $ada_sesuatu_untuk_ditampilkan = false; 

                foreach ($pengajuan_types as $tipe => $pengajuan_obj) {
                    $mahasiswa_final_exam_status = null;
                    $show_final_status_card = false;
                    $show_active_submission_card = false;
                    $current_status_info = null;

                    // 1. Cek status final dari tabel mahasiswa
                    if (isset($mahasiswa)) {
                        if ($tipe == 'Sempro' && in_array($mahasiswa->status_sempro, ['ACC', 'Mengulang'])) {
                            $mahasiswa_final_exam_status = $mahasiswa->status_sempro;
                        } elseif ($tipe == 'Semhas' && in_array($mahasiswa->status_semhas, ['ACC', 'Mengulang'])) {
                            $mahasiswa_final_exam_status = $mahasiswa->status_semhas;
                        }
                    }

                    // 2. Tentukan kartu apa yang akan ditampilkan
                    if ($mahasiswa_final_exam_status) {
                        // Jika ada status final, tampilkan kartu hasil ujian
                        $current_status_info = $status_map[$mahasiswa_final_exam_status];
                        $show_final_status_card = true;
                        $ada_sesuatu_untuk_ditampilkan = true; 
                    } elseif ($pengajuan_obj) {
                        // Jika tidak ada status final, cek pengajuan aktif dari tabel pengajuan
                        $status = $pengajuan_obj->status;
                        $should_show_card = isset($status_map[$status]);

                        // Aturan tambahan: Jangan tampilkan "dikonfirmasi" jika jadwal sudah ada
                        if ($status == 'dikonfirmasi') {
                            if ($tipe == 'Sempro' && !empty($has_approved_sempro_schedule)) {
                                $should_show_card = false;
                            } elseif ($tipe == 'Semhas' && !empty($has_approved_semhas_schedule)) {
                                $should_show_card = false;
                            }
                        }
                        
                        if ($should_show_card) {
                            $current_status_info = $status_map[$status];
                            $show_active_submission_card = true;
                            $ada_sesuatu_untuk_ditampilkan = true;
                        }
                    }
                    
                    // 3. Render HTML berdasarkan flag yang sudah diatur
                    if ($show_final_status_card && $current_status_info) {
                        if ($mahasiswa_final_exam_status == 'ACC') {
                ?>
                            <div id="alert-acc-<?= strtolower($tipe); ?>" class="flex items-center justify-between p-4 border rounded-lg text-sm gap-3 <?= $current_status_info['class']; ?>">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5"><?= $current_status_info['icon']; ?></span>
                                    <div>
                                        <p class="font-semibold">Selamat! Hasil Ujian <?= htmlspecialchars($tipe); ?> Anda:</p>
                                        <p class="text-base font-medium"><?= htmlspecialchars($current_status_info['text']); ?></p>
                                        <?php if ($pengajuan_obj) : ?>
                                            <p class="text-xs mt-1">Judul Skripsi Terkait: <?= htmlspecialchars($pengajuan_obj->judul_skripsi); ?></p>
                                            <p class="text-xs">Tanggal Pengajuan Awal: <?= htmlspecialchars(date('d M Y, H:i', strtotime($pengajuan_obj->tanggal_pengajuan))); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button type="button" class="p-1 -m-1" data-dismiss-target="#alert-acc-<?= strtolower($tipe); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        <?php
                        } else { // Untuk status final selain ACC (misal: Mengulang)
                        ?>
                            <div class="p-4 border rounded-lg text-sm flex items-start gap-3 <?= $current_status_info['class']; ?>">
                                <span class="mt-0.5"><?= $current_status_info['icon']; ?></span>
                                <div>
                                    <p class="font-semibold">Hasil Ujian <?= htmlspecialchars($tipe); ?> Anda:</p>
                                    <p class="text-base font-medium"><?= htmlspecialchars($current_status_info['text']); ?></p>
                                    <?php if ($pengajuan_obj) : ?>
                                        <p class="text-xs mt-1">Judul Skripsi Terkait: <?= htmlspecialchars($pengajuan_obj->judul_skripsi); ?></p>
                                        <p class="text-xs">Tanggal Pengajuan Awal: <?= htmlspecialchars(date('d M Y, H:i', strtotime($pengajuan_obj->tanggal_pengajuan))); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                <?php
                        }
                    } elseif ($show_active_submission_card && $pengajuan_obj && $current_status_info) {
                ?>
                        <div class="p-4 border rounded-lg text-sm flex items-start gap-3 <?= $current_status_info['class']; ?>">
                            <span class="mt-0.5"><?= $current_status_info['icon']; ?></span>
                            <div>
                                <p class="font-semibold">Status Pengajuan <?= htmlspecialchars($pengajuan_obj->tipe_ujian); ?> Anda:</p>
                                <p class="text-base font-medium"><?= htmlspecialchars($current_status_info['text']); ?></p>
                                <p class="text-xs mt-1">Judul: <?= htmlspecialchars($pengajuan_obj->judul_skripsi); ?></p>
                                <p class="text-xs">Tanggal Pengajuan: <?= htmlspecialchars(date('d M Y', strtotime($pengajuan_obj->tanggal_pengajuan))); ?></p>
                                <?php if ($pengajuan_obj->status == 'ditolak' && !empty($pengajuan_obj->alasan_penolakan)): ?>
                                    <p class="text-xs mt-1 pt-1 border-t border-current"><strong>Alasan Penolakan:</strong> <?= htmlspecialchars($pengajuan_obj->alasan_penolakan); ?></p>
                                    <a href="<?= site_url('pengajuan'); ?>" class="text-xs inline-block mt-2 px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700 transition">Perbaiki Pengajuan</a>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php
                    }
                } // End foreach

                // Pesan jika sama sekali tidak ada yang ditampilkan (tidak ada status & tidak ada jadwal)
                if (!$ada_sesuatu_untuk_ditampilkan && empty($jadwal)) {
                ?>
                    <div class="p-4 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-600 text-center">
                        Anda belum memiliki pengajuan ujian Sempro atau Semhas yang aktif, terjadwal, atau hasil ujian final.
                        <a href="<?= site_url('pengajuan'); ?>" class="text-indigo-600 hover:text-indigo-800 font-semibold">Ajukan sekarang?</a>
                    </div>
                <?php
                }
                ?>
            </div>
            
            <?php if (!empty($jadwal)): ?>
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
    $status = strtolower($row->status_konfirmasi);
    $statusClass = ''; // Inisialisasi variabel

    // Gunakan switch statement untuk PHP 7
    switch ($status) {
        case 'disetujui':
            $statusClass = 'bg-green-100 text-green-700';
            break;
        case 'ditolak':
            $statusClass = 'bg-red-100 text-red-700';
            break;
        default:
            $statusClass = 'bg-yellow-100 text-yellow-700';
            break;
    }
?>
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                            <?= ucfirst(htmlspecialchars($row->status_konfirmasi)) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <?php if ($ada_sesuatu_untuk_ditampilkan): ?>
                <div class="bg-white p-6 rounded-lg shadow text-center text-gray-600">
                    Anda tidak memiliki jadwal ujian yang akan datang atau belum ada jadwal yang disetujui untuk ditampilkan saat ini.
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dismissButtons = document.querySelectorAll('[data-dismiss-target]');

    dismissButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-dismiss-target');
            const alertElement = document.querySelector(targetId);
            if (alertElement) {
                alertElement.style.display = 'none';
            }
        });
    });
});
</script>