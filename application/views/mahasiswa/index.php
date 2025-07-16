<div class="p-4 sm:p-6 md:p-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
    
    <?php if (!empty($mahasiswa_list) && is_array($mahasiswa_list)): ?>
        <?php foreach ($mahasiswa_list as $mhs): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden mb-8 transition-all duration-300 hover:shadow-none">
                <div class="p-5 sm:p-6 space-y-5">
                    
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
                                <?= htmlspecialchars($mhs->email ?? '-'); ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NIM/Tahun Masuk</label>
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <?= htmlspecialchars($mhs->nim ?? 'N/A'); ?> / <?= htmlspecialchars($mhs->tahun_masuk ?? 'N/A'); ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fakultas/Prodi</label>
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <?= htmlspecialchars($mhs->fakultas ?? 'N/A'); ?> / <?= htmlspecialchars($mhs->prodi ?? 'N/A'); ?>
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
                                $status_sempro_actual = $mhs->status_sempro ?? 'Belum Mengajukan';
                                $sempro_display_text = ($status_sempro_actual == 'Belum Mengajukan') ? "-" : htmlspecialchars($status_sempro_actual);
                                $sempro_badge_class = 'inline-block text-xs font-semibold px-2.5 py-1 rounded-full ';
                                switch ($status_sempro_actual) {
                                    case 'ACC': $sempro_badge_class .= 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100'; break;
                                    case 'Mengulang': $sempro_badge_class .= 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100'; break;
                                    default: $sempro_badge_class .= 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100'; break;
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
                                    case 'ACC': $semhas_badge_class .= 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100'; break;
                                    case 'Mengulang': $semhas_badge_class .= 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100'; break;
                                    default: $semhas_badge_class .= 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100'; break;
                                }
                            ?>
                           <span class="<?= $semhas_badge_class ?>"><?= $semhas_display_text; ?></span>
                        </div>
                    </div>
                    <hr class="border-gray-200 dark:border-gray-700">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        
                        <div class="deadline-alert flex items-start space-x-4 p-4 rounded-lg bg-yellow-50 dark:bg-gray-800 border border-yellow-200 dark:border-yellow-700"
                             data-start-date="<?= htmlspecialchars($jadwal_sempro['tanggal_mulai'] ?? '') ?>">
                            <div class="flex-shrink-0">
                                <svg class="w-10 h-10 text-yellow-500 dark:text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200">Batas Pengumpulan Berkas Sempro</h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 timer font-mono"></p>
                            </div>
                        </div>

                        <div class="deadline-alert flex items-start space-x-4 p-4 rounded-lg bg-red-50 dark:bg-gray-800 border border-red-200 dark:border-red-700"
                             data-start-date="<?= htmlspecialchars($jadwal_semhas['tanggal_mulai'] ?? '') ?>">
                            <div class="flex-shrink-0">
                                <svg class="w-10 h-10 text-red-500 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-red-800 dark:text-red-200">Batas Pengumpulan Berkas Semhas</h4>
                                <p class="text-sm text-red-700 dark:text-red-300 timer font-mono"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
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

    <main class="container mx-auto px-4 sm:px-6 py-8">
        <section>
            <div class="relative">
                <div class="absolute left-5 sm:left-[23px] top-2 bottom-2 w-1 bg-gray-300 rounded-full hidden sm:block" style="z-index: 0;"></div>
                <div class="mb-8 relative">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 z-10">
                            <span class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 text-white rounded-full text-lg font-bold ring-4 ring-gray-100 shadow">1</span>
                        </div>
                        <div class="ml-4 sm:ml-6 flex-grow bg-white p-4 sm:p-5 rounded-lg shadow-md">
                            <h3 class="text-lg sm:text-xl font-semibold text-blue-700 mb-1">Kelola Profil Pribadi</h3>
                            <p class="text-gray-600 text-sm sm:text-base">
                                Melengkapi data profil pribadi Anda. Pastikan data Anda akurat dan terkini, karena kelengkapan profil adalah prasyarat untuk mengakses fitur pengajuan jadwal ujian.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 z-10">
                            <span class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-red-500 text-white rounded-full text-lg font-bold ring-4 ring-gray-100 shadow">2</span>
                        </div>
                        <div class="ml-4 sm:ml-6 flex-grow bg-white p-4 sm:p-5 rounded-lg shadow-md">
                            <h3 class="text-lg sm:text-xl font-semibold text-red-700 mb-1">Pengajuan Jadwal Seminar & Ujian</h3>
                            <p class="text-gray-600 text-sm sm:text-base">
                                Mengajukan permohonan jadwal seminar (proposal/hasil) atau ujian skripsi Anda sesuai dengan ketentuan, persyaratan, dan periode waktu yang telah ditetapkan oleh program studi.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="relative"> <div class="flex items-start">
                    <div class="flex-shrink-0 z-10">
                        <span class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-green-500 text-white rounded-full text-lg font-bold ring-4 ring-gray-100 shadow">3</span>
                    </div>
                    <div class="ml-4 sm:ml-6 flex-grow bg-white p-4 sm:p-5 rounded-lg shadow-md">
                        <h3 class="text-lg sm:text-xl font-semibold text-green-700 mb-1">Riwayat & Status Pengajuan</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Memantau status terkini dan meninjau riwayat seluruh pengajuan jadwal ujian Anda, mulai dari tahap pengajuan, verifikasi, hingga konfirmasi akhir jadwal.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Select all deadline alert components on the page
    const deadlineAlerts = document.querySelectorAll('.deadline-alert');

    // Function to format the deadline date for display
    const formatDeadlineDate = (date) => {
        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).format(date);
    };

    deadlineAlerts.forEach(alertElement => {
        const startDateString = alertElement.dataset.startDate;
        const timerElement = alertElement.querySelector('.timer');

        // If there's no start date or timer element, hide the alert and stop
        if (!startDateString || !timerElement) {
            alertElement.style.display = 'none';
            return;
        }

        // 1. Determine the deadline date (H-7 from the start date)
        const startDate = new Date(startDateString);
        const deadline = new Date(startDate.getTime());
        deadline.setDate(startDate.getDate() - 7);

        // 2. Create an interval timer that updates the display
        const timerInterval = setInterval(() => {
            const now = new Date().getTime();
            const distance = deadline.getTime() - now;

            // 3. Check if the deadline has passed
            if (distance < 0) {
                // If passed, clear the timer and show a static message
                clearInterval(timerInterval);
                timerElement.innerHTML = `Batas akhir pada: ${formatDeadlineDate(deadline)}`;
                // Optionally change text color to indicate it's overdue
                timerElement.classList.add('font-bold', 'text-red-600', 'dark:text-red-400');
                return;
            }

            // 4. If time remains, calculate and display the countdown
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Format the output string
            timerElement.innerHTML = `${days}h ${hours}j ${minutes}m ${seconds}d tersisa`;

        }, 1000); // Update every second
    });
});
</script>