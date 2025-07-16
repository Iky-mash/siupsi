<main class="container mx-auto px-4 sm:px-6 py-8">
<section class="bg-white p-6 sm:p-8 rounded-lg shadow-lg mb-10">
    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-4">Selamat Datang, Admin Akademik! 👋</h2>
    <p class="text-gray-700 leading-relaxed">
        Panel ini menyediakan akses untuk mengelola berbagai aspek administrasi akademik terkait pelaksanaan ujian dan seminar.
        Pastikan semua proses terdokumentasi dan terkelola dengan akurat untuk mendukung kelancaran kegiatan akademik.
    </p>

    <div class="border-t border-gray-200 my-6"></div>

    <?php
    // Definisikan konfigurasi tampilan untuk status 'Menunggu'
    $config_menunggu = [
        'icon' => '<svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z" /></svg>',
        'bg' => 'bg-yellow-50',
        'text' => 'text-yellow-700'
    ];

    // (BARU) Definisikan konfigurasi tampilan untuk 'Sempro'
    $config_sempro = [
        'icon' => '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
        'bg' => 'bg-blue-50',
        'text' => 'text-blue-700'
    ];

    // (BARU) Definisikan konfigurasi tampilan untuk 'Semhas'
    $config_semhas = [
        'icon' => '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'bg' => 'bg-green-50',
        'text' => 'text-green-700'
    ];
    ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-2xl shadow-md p-5 <?= $config_menunggu['bg'] ?> border border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium <?= $config_menunggu['text'] ?> uppercase mb-1">
                    Berkas Pengajuan Menunggu 
                </h3>
                <p class="text-3xl font-bold text-gray-800"><?= $pengajuan_menunggu_total; ?></p>
            </div>
            <div class="bg-white p-2 rounded-full shadow-sm">
                <?= $config_menunggu['icon'] ?>
            </div>
        </div>
        
        <div class="rounded-2xl shadow-md p-5 <?= $config_menunggu['bg'] ?> border border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium <?= $config_menunggu['text'] ?> uppercase mb-1">
                    Pengajuan Ruangan Menunggu
                </h3>
                <p class="text-3xl font-bold text-gray-800"><?= $ruangan_menunggu_total; ?></p>
            </div>
            <div class="bg-white p-2 rounded-full shadow-sm">
                <?= $config_menunggu['icon'] ?>
            </div>
        </div>

        <div class="rounded-2xl shadow-md p-5 <?= $config_sempro['bg'] ?> border border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium <?= $config_sempro['text'] ?> uppercase mb-1">
                    Total Mahasiswa Sempro
                </h3>
                <p class="text-3xl font-bold text-gray-800"><?= $total_sempro; ?></p>
            </div>
            <div class="bg-white p-2 rounded-full shadow-sm">
                <?= $config_sempro['icon'] ?>
            </div>
        </div>

        <div class="rounded-2xl shadow-md p-5 <?= $config_semhas['bg'] ?> border border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium <?= $config_semhas['text'] ?> uppercase mb-1">
                    Total Mahasiswa Semhas
                </h3>
                <p class="text-3xl font-bold text-gray-800"><?= $total_semhas; ?></p>
            </div>
            <div class="bg-white p-2 rounded-full shadow-sm">
                <?= $config_semhas['icon'] ?>
            </div>
        </div>
    </div>
</section>
    <section>
        <div class="relative">
            <div class="absolute left-5 sm:left-[23px] top-2 bottom-2 w-1 bg-gray-300 rounded-full hidden sm:block" style="z-index: 0;"></div>

            <div class="mb-8 relative">
                <div class="flex items-start">
                    <div class="flex-shrink-0 z-10">
                        <span class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 text-white rounded-full text-lg font-bold ring-4 ring-gray-100 shadow">1</span>
                    </div>
                    <div class="ml-4 sm:ml-6 flex-grow bg-white p-4 sm:p-5 rounded-lg shadow-md">
                        <h3 class="text-lg sm:text-xl font-semibold text-blue-700 mb-1">Berkas Pengajuan Mahasiswa</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Melakukan verifikasi kelengkapan dan kesesuaian berkas pengajuan ujian yang diunggah oleh mahasiswa sesuai standar dan regulasi akademik yang berlaku.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-8 relative">
                <div class="flex items-start">
                    <div class="flex-shrink-0 z-10">
                        <span class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-green-500 text-white rounded-full text-lg font-bold ring-4 ring-gray-100 shadow">2</span>
                    </div>
                    <div class="ml-4 sm:ml-6 flex-grow bg-white p-4 sm:p-5 rounded-lg shadow-md">
                        <h3 class="text-lg sm:text-xl font-semibold text-green-700 mb-1">Validasi Jadwal & Ketersediaan Ruangan</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Memproses pengajuan jadwal ujian, serta memvalidasi ketersediaan dan kelayakan ruangan berdasarkan jadwal yang telah diajukan atau ditetapkan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-8 relative">
                <div class="flex items-start">
                    <div class="flex-shrink-0 z-10">
                        <span class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-red-500 text-white rounded-full text-lg font-bold ring-4 ring-gray-100 shadow">3</span>
                    </div>
                    <div class="ml-4 sm:ml-6 flex-grow bg-white p-4 sm:p-5 rounded-lg shadow-md">
                        <h3 class="text-lg sm:text-xl font-semibold text-red-700 mb-1">Riwayat Penjadwalan Ulang</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Meninjau, mencatat, dan mengelola dokumentasi riwayat perubahan jadwal ujian yang diajukan oleh dosen pembimbing atau pihak terkait, lengkap dengan justifikasinya.
                        </p>
                    </div>
                </div>
            </div>

            <div class="relative"> 
                <div class="flex items-start">
                    <div class="flex-shrink-0 z-10">
                        <span class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-purple-500 text-white rounded-full text-lg font-bold ring-4 ring-gray-100 shadow">4</span>
                    </div>
                    <div class="ml-4 sm:ml-6 flex-grow bg-white p-4 sm:p-5 rounded-lg shadow-md">
                        <h3 class="text-lg sm:text-xl font-semibold text-purple-700 mb-1">Pengelolaan Data Ruangan</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Mengadministrasikan data ruangan yang dialokasikan untuk ujian, meliputi penambahan data, pembaruan status, dan pengaturan atribut serta fasilitas pendukung.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>