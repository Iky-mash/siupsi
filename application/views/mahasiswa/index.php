<div class="p-4 sm:p-6 md:p-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
    

    <?php if (!empty($mahasiswa_list) && is_array($mahasiswa_list)): ?>
        <?php foreach ($mahasiswa_list as $mhs): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden mb-8 transition-all duration-300 hover:shadow-none">
                <div class="bg-gradient-to-br from-slate-700 to-slate-900 dark:from-slate-800 dark:to-black p-5 sm:p-6">
                    
                <div class="p-5 sm:p-6 space-y-5">
                    <hr class="border-gray-200 dark:border-gray-700">

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Judul Skripsi</label>
                        <p class="mt-1 text-md font-medium text-gray-800 dark:text-white">
                            <?= !empty($mhs->judul_skripsi) ? htmlspecialchars($mhs->judul_skripsi) : '<span class="italic text-gray-400 dark:text-gray-500">Belum mengajukan judul</span>'; ?>
                        </p>
                    </div>

                     <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</label>
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <?= htmlspecialchars($mhs->email) ; '<span class="italic text-gray-400 dark:text-gray-500">Belum ditentukan</span>'; ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NIM</label>
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <?= htmlspecialchars($mhs->nim)  ;'<span class="italic text-gray-400 dark:text-gray-500">Belum ditentukan</span>'; ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fakultas/Prodi</label>
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <?=  htmlspecialchars($mhs->fakultas ?? 'N/A'); ?> / <?= htmlspecialchars($mhs->prodi ?? 'N/A'); '<span class="italic text-gray-400 dark:text-gray-500">Belum ditentukan</span>'; ?>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dosen Pembimbing</label>
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <?= !empty($mhs->pembimbing_nama) ? htmlspecialchars($mhs->pembimbing_nama) : '<span class="italic text-gray-400 dark:text-gray-500">Belum ditentukan</span>'; ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dosen Penguji 1</label>
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <?= !empty($mhs->penguji1_nama) ? htmlspecialchars($mhs->penguji1_nama) : '<span class="italic text-gray-400 dark:text-gray-500">Belum ditentukan</span>'; ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dosen Penguji 2</label>
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <?= !empty($mhs->penguji2_nama) ? htmlspecialchars($mhs->penguji2_nama) : '<span class="italic text-gray-400 dark:text-gray-500">Belum ditentukan</span>'; ?>
                            </p>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Status Seminar Proposal (Sempro)</label>
                           <?php
                                // Ambil status aktual, default ke 'Belum Mengajukan' jika null atau kosong
                                $status_sempro_actual = $mhs->status_sempro ?? 'Belum Mengajukan';
                                // Teks yang akan ditampilkan
                                $sempro_display_text = ($status_sempro_actual == 'Belum Mengajukan') ? "-" : htmlspecialchars($status_sempro_actual);
                                
                                $sempro_badge_class = 'inline-block text-xs font-semibold px-2.5 py-1 rounded-full ';
                                // Logika warna badge tetap berdasarkan status aktual
                                switch ($status_sempro_actual) {
                                    case 'ACC':
                                        $sempro_badge_class .= 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100';
                                        break;
                                    case 'Mengulang':
                                        $sempro_badge_class .= 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100';
                                        break;
                                    default: // 'Belum Mengajukan' atau status lain yang tidak terdefinisi
                                        $sempro_badge_class .= 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100';
                                        // Jika ingin warna berbeda untuk "-", bisa tambahkan kondisi di sini
                                        // if ($sempro_display_text == '-') {
                                        //    $sempro_badge_class = 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100'; // Contoh badge netral
                                        // }
                                        break;
                                }
                            ?>
                            <span class="<?= $sempro_badge_class ?>"><?= $sempro_display_text; ?></span>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Status Seminar Hasil (Semhas)</label>
                            <?php
                                $status_semhas_actual = $mhs->status_semhas ?? 'Belum Mengajukan';
                                $semhas_display_text = ($status_semhas_actual == 'Belum Mengajukan') ? "-" : htmlspecialchars($status_semhas_actual);

                                $semhas_badge_class = 'inline-block text-xs font-semibold px-2.5 py-1 rounded-full ';
                                switch ($status_semhas_actual) {
                                    case 'ACC':
                                        $semhas_badge_class .= 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100';
                                        break;
                                    case 'Mengulang':
                                        $semhas_badge_class .= 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100';
                                        break;
                                    default: // 'Belum Mengajukan'
                                        $semhas_badge_class .= 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100';
                                        // if ($semhas_display_text == '-') {
                                        //    $semhas_badge_class = 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100'; // Contoh badge netral
                                        // }
                                        break;
                                }
                            ?>
                           <span class="<?= $semhas_badge_class ?>"><?= $semhas_display_text; ?></span>
                        </div>
                    </div>
                </div> </div> <?php endforeach; ?>
    <?php else: ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 sm:p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0zM15.172 10.828a4 4 0 01-2.343.622m-2.343-.622a4 4 0 00-2.343.622m0 0a4 4 0 002.343 2.343m2.343-2.343a4 4 0 012.343-2.343m0 0a4 4 0 012.343 2.343m-2.343-2.343a4 4 0 00-2.343-2.343m-2.343 2.343a4 4 0 00-2.343-2.343" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Data Tidak Ditemukan</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Informasi skripsi untuk mahasiswa tidak dapat ditemukan.
                <?php if (!$this->session->userdata('nim')): ?>
                    <br> Pastikan Anda telah login atau NIM mahasiswa tersedia.
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
</div>