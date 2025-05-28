<div class="min-h-screen flex flex-col px-6 py-6 mx-auto bg-white shadow-xl rounded-xl">

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
                            <td class="px-5 py-3 max-w-xs truncate" title="<?= htmlspecialchars($item->judul_skripsi); ?>">
                            <?php
                                 $judul_skripsi_raw = $item->judul_skripsi; // Judul asli
                                 $full_title_tooltip = htmlspecialchars($judul_skripsi_raw); // Untuk atribut title yang aman

                                 $max_display_length = 20;
                                 $ellipsis = '...';
                                 $judul_to_display_processed = $judul_skripsi_raw; // Defaultnya, tampilkan judul asli

                                 if (mb_strlen($judul_skripsi_raw) > $max_display_length) {
       
                                 $judul_to_display_processed = mb_substr($judul_skripsi_raw, 0, $max_display_length - mb_strlen($ellipsis)) . $ellipsis;
                                 }
    
   
                                 $judul_to_display_safe = htmlspecialchars($judul_to_display_processed);
    
                                 echo $judul_to_display_safe;
                                 ?>
                                </td>
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

                        <div id="berkasModal<?= $item->pengajuan_id; ?>" class="fixed z-[60] inset-0 overflow-y-auto hidden" aria-labelledby="modal-title-berkas-<?= $item->pengajuan_id; ?>" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                    
                                    <div class="flex items-center justify-between px-6 py-4 border-b rounded-t">
                                        <h3 class="text-xl font-semibold text-gray-800 flex items-center" id="modal-title-berkas-<?= $item->pengajuan_id; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2 text-blue-600">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                            </svg>
                                            Detail Berkas: <?= htmlspecialchars($item->tipe_ujian); ?> - <?= htmlspecialchars($item->nim_mahasiswa); ?>
                                        </h3>
                                        <button type="button" onclick="closeModal('berkasModal<?= $item->pengajuan_id; ?>')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>

                                    <div class="px-6 py-5 space-y-4">
                                        <?php
                                        $file_config_to_use = ($item->tipe_ujian == 'Sempro') ? ($sempro_files_config ?? []) : ($semhas_files_config ?? []);
                                        if ($item->detail_berkas && !empty($file_config_to_use)) {
                                        ?>
                                            <ul class="divide-y divide-gray-200">
                                            <?php
                                            foreach ($file_config_to_use as $file_conf) {
                                                $file_name_in_db = $file_conf['name']; // Ini adalah nama kolom di tabel berkas_sempro/semhas
                                                $file_path_from_db = isset($item->detail_berkas->$file_name_in_db) ? $item->detail_berkas->$file_name_in_db : null;
                                            ?>
                                                <li class="py-3 hover:bg-gray-50 -mx-2 px-2 rounded-md transition-colors duration-150">
                                                    <div class="flex items-center justify-between space-x-3">
                                                        <div class="flex items-center min-w-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500 mr-3 flex-shrink-0">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                            </svg>
                                                            <p class="text-sm font-medium text-gray-700 truncate">
                                                                <?= htmlspecialchars($file_conf['label']); ?>
                                                            </p>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <?php if ($file_path_from_db): ?>
                                                                <a href="<?= base_url('uploads/' . rawurlencode(htmlspecialchars($file_path_from_db))); ?>" target="_blank" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline py-1 px-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                                                    Lihat Berkas
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1.5">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                                    </svg>
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="inline-block px-2 py-1 text-xs font-medium text-gray-500 bg-gray-100 rounded-full italic">Tidak diunggah</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php
                                            } // end foreach file_config
                                            ?>
                                            </ul>
                                        <?php
                                        } else { // end if detail_berkas
                                        ?>
                                            <div class="text-center py-8">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-12 w-12 text-gray-400">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                </svg>
                                                <p class="mt-3 text-sm text-gray-600">Tidak ada detail berkas yang dapat ditampilkan.</p>
                                                <p class="text-xs text-gray-400 mt-1">Pastikan konfigurasi berkas telah diatur dengan benar.</p>
                                            </div>
                                        <?php
                                        } // end else detail_berkas
                                        ?>
                                    </div>

                                    <div class="bg-gray-50 px-6 py-3 sm:flex sm:flex-row-reverse rounded-b">
                                        <button type="button" onclick="closeModal('berkasModal<?= $item->pengajuan_id; ?>')" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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