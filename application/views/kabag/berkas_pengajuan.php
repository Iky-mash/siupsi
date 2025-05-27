<div class="max-w-full mx-auto my-10 p-6 bg-white shadow-xl rounded-xl">
    <div class="flex items-center gap-3 mb-8">
         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-blue-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h2 class="text-3xl font-bold text-gray-800">
            <?= htmlspecialchars($title); ?>
        </h2>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-700 rounded-lg text-sm flex items-center gap-2">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <?= $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            <?= $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>


    <?php if (!empty($pengajuan_list)): ?>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full whitespace-no-wrap">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-5 py-3">No.</th>
                        <th class="px-5 py-3">NIM</th>
                        <th class="px-5 py-3">Nama Mahasiswa</th>
                        <th class="px-5 py-3">Judul Skripsi</th>
                        <th class="px-5 py-3">Tipe Ujian</th>
                        <th class="px-5 py-3">Tgl. Pengajuan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $no = 1; foreach ($pengajuan_list as $item): ?>
                        <tr class="text-sm text-gray-700 hover:bg-gray-50">
                            <td class="px-5 py-3"><?= $no++; ?></td>
                            <td class="px-5 py-3"><?= htmlspecialchars($item->nim_mahasiswa); ?></td>
                            <td class="px-5 py-3"><?= htmlspecialchars($item->nama_mahasiswa); ?></td>
                            <td class="px-5 py-3 max-w-xs truncate" title="<?= htmlspecialchars($item->judul_skripsi); ?>"><?= htmlspecialchars($item->judul_skripsi); ?></td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    <?= $item->tipe_ujian == 'Sempro' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'; ?>">
                                    <?= htmlspecialchars($item->tipe_ujian); ?>
                                </span>
                            </td>
                            <td class="px-5 py-3"><?= htmlspecialchars(date('d M Y H:i', strtotime($item->tanggal_pengajuan))); ?></td>
                            <td class="px-5 py-3">
                                <?php
                                $status_class = 'bg-gray-200 text-gray-800';
                                if ($item->status == 'dikonfirmasi') $status_class = 'bg-green-200 text-green-800';
                                elseif ($item->status == 'ditolak') $status_class = 'bg-red-200 text-red-800';
                                elseif ($item->status == 'draft') $status_class = 'bg-yellow-200 text-yellow-800';
                                ?>
                                <span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full <?= $status_class; ?>">
                                    <?= ucfirst(htmlspecialchars($item->status)); ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center space-x-2">
                                <button type="button" onclick="showBerkasModal('berkasModal<?= $item->pengajuan_id; ?>')" 
                                        class="text-xs bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-md transition">
                                    Lihat Berkas
                                </button>
                                <?php if ($item->status == 'draft'): // Hanya bisa proses jika statusnya draft ?>
                                    <a href="<?= site_url('kabag/konfirmasi_pengajuan/' . $item->pengajuan_id); ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin mengkonfirmasi pengajuan ini?');"
                                       class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md transition">
                                        Konfirmasi
                                    </a>
                                    <button type="button" onclick="showTolakModal('tolakModal<?= $item->pengajuan_id; ?>')"
                                            class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md transition">
                                        Tolak
                                    </button>
                                <?php elseif ($item->status == 'ditolak'): ?>
                                     <span class="text-xs text-red-600 italic" title="<?= htmlspecialchars($item->alasan_penolakan); ?>">Sudah Ditolak</span>
                                <?php elseif ($item->status == 'dikonfirmasi'): ?>
                                     <span class="text-xs text-green-600 italic">Sudah Dikonfirmasi</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <div id="berkasModal<?= $item->pengajuan_id; ?>" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title-berkas-<?= $item->pengajuan_id; ?>" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <div class="sm:flex sm:items-start">
                                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                                📂
                                            </div>
                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-berkas-<?= $item->pengajuan_id; ?>">
                                                    Detail Berkas: <?= htmlspecialchars($item->tipe_ujian); ?> - <?= htmlspecialchars($item->nim_mahasiswa); ?>
                                                </h3>
                                                <div class="mt-4 text-sm text-gray-600 space-y-2">
                                                    <?php
                                                    $file_config_to_use = ($item->tipe_ujian == 'Sempro') ? $sempro_files_config : $semhas_files_config;
                                                    if ($item->detail_berkas) {
                                                        foreach ($file_config_to_use as $file_conf) {
                                                            $file_name_in_db = $file_conf['name']; // Ini adalah nama kolom di tabel berkas_sempro/semhas
                                                            $file_path_from_db = isset($item->detail_berkas->$file_name_in_db) ? $item->detail_berkas->$file_name_in_db : null;
                                                            echo "<p><strong>" . htmlspecialchars($file_conf['label']) . ":</strong> ";
                                                            if ($file_path_from_db) {
                                                                echo "<a href='" . base_url('uploads/' . htmlspecialchars($file_path_from_db)) . "' target='_blank' class='text-blue-600 hover:underline'>Lihat/Unduh Berkas</a></p>";
                                                            } else {
                                                                echo "<span class='text-gray-400 italic'>Tidak diunggah</span></p>";
                                                            }
                                                        }
                                                    } else {
                                                        echo "<p>Tidak ada detail berkas ditemukan.</p>";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <button type="button" onclick="closeModal('berkasModal<?= $item->pengajuan_id; ?>')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tolakModal<?= $item->pengajuan_id; ?>" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title-tolak-<?= $item->pengajuan_id; ?>" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <?= form_open('kabag/tolak_pengajuan'); ?>
                                        <input type="hidden" name="pengajuan_id" value="<?= $item->pengajuan_id; ?>">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-tolak-<?= $item->pengajuan_id; ?>">
                                                        Tolak Pengajuan: <?= htmlspecialchars($item->nim_mahasiswa); ?>
                                                    </h3>
                                                    <div class="mt-4">
                                                        <label for="alasan_penolakan_<?= $item->pengajuan_id; ?>" class="block text-sm font-medium text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                                                        <textarea id="alasan_penolakan_<?= $item->pengajuan_id; ?>" name="alasan_penolakan" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Jelaskan alasan penolakan pengajuan ini..."></textarea>
                                                        <?php
                                                        // Menampilkan error validasi spesifik untuk modal ini jika ada
                                                        $modal_error_key = 'error_modal_tolak_' . $item->pengajuan_id;
                                                        if ($this->session->flashdata($modal_error_key)): ?>
                                                            <div class="mt-2 text-xs text-red-500">
                                                                <?= $this->session->flashdata($modal_error_key); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                                                Kirim Penolakan
                                            </button>
                                            <button type="button" onclick="closeModal('tolakModal<?= $item->pengajuan_id; ?>')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                                Batal
                                            </button>
                                        </div>
                                    <?= form_close(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Berkas</h3>
            <p class="mt-1 text-sm text-gray-500">Saat ini belum ada pengajuan berkas yang perlu direview.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    function showBerkasModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }
    function showTolakModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Opsional: Tutup modal jika klik di luar kontennya atau tekan ESC
    window.addEventListener('click', function(event) {
        document.querySelectorAll('.fixed.z-50.inset-0.overflow-y-auto').forEach(modal => {
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        });
    });
    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.fixed.z-50.inset-0.overflow-y-auto:not(.hidden)').forEach(modal => {
                modal.classList.add('hidden');
            });
        }
    });
</script>