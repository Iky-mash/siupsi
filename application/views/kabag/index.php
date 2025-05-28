<main class="container mx-auto px-4 sm:px-6 py-8">

    <section class="bg-white p-6 sm:p-8 rounded-lg shadow-lg mb-10">
        <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-4">Selamat Datang, Admin Akademik! 👋</h2>
        <p class="text-gray-700 leading-relaxed">
            Panel ini menyediakan akses untuk mengelola berbagai aspek administrasi akademik terkait pelaksanaan ujian dan seminar.
            Pastikan semua proses terdokumentasi dan terkelola dengan akurat untuk mendukung kelancaran kegiatan akademik.
        </p>
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

            <div class="relative"> <div class="flex items-start">
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


<!-- <div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
 <?php
$cards = [
    'Dikonfirmasi' => [
        'icon' => '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" /></svg>',
        'bg' => 'bg-green-50',
        'text' => 'text-green-700'
    ],
    'Menunggu' => [
        'icon' => '<svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z" /></svg>',
        'bg' => 'bg-yellow-50',
        'text' => 'text-yellow-700'
    ],
    'Ditolak' => [
        'icon' => '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>',
        'bg' => 'bg-red-50',
        'text' => 'text-red-700'
    ],
];
?> -->

<!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
    <?php foreach ($status_summary as $row): 
        $status = $row->status_konfirmasi;
        $config = $cards[$status] ?? [
            'icon' => '<svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" /></svg>',
            'bg' => 'bg-gray-50',
            'text' => 'text-gray-700'
        ];
    ?>
        <div class="rounded-2xl shadow-md p-5 <?= $config['bg'] ?> border border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium <?= $config['text'] ?> uppercase mb-1">
                    <?= ucfirst($status) ?>
                </h3>
                <p class="text-3xl font-bold text-gray-800"><?= $row->total ?></p>
            </div>
            <div class="bg-white p-2 rounded-full shadow-sm">
                <?= $config['icon'] ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>


</div> -->
