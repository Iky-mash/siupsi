<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-md rounded-md" role="alert">
            <div class="flex">
                <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                <div>
                    <p class="font-bold">Sukses</p>
                    <p class="text-sm"><?= $this->session->flashdata('success'); ?></p>
                </div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-100 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8" onclick="this.parentElement.parentElement.style.display='none';" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-md rounded-md" role="alert">
             <div class="flex">
                <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                <div>
                    <p class="font-bold">Error</p>
                    <p class="text-sm"><?= $this->session->flashdata('error'); ?></p>
                </div>
                 <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-100 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8" onclick="this.parentElement.parentElement.style.display='none';" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($jadwal_ujian)) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <?php foreach ($jadwal_ujian as $jadwal) : ?>
                <?php // Add 'relative' class to the main card div for absolute positioning of the reschedule button ?>
                <div class="bg-white shadow-xl rounded-lg overflow-hidden flex flex-col transform hover:scale-105 transition-transform duration-300 ease-in-out ">
                    
                    <?php
                    // Prepare variables needed for conditions
                    $is_pembimbing = ($jadwal['pembimbing_id'] == $current_dosen_id);
                    
                    $waktu_ujian_telah_lewat = false; 
                    $slot_waktu = $jadwal['slot_waktu']; 
                    $tanggal_ujian = $jadwal['tanggal']; 

                    if (strpos($slot_waktu, '-') !== false) {
                        $times = explode('-', $slot_waktu);
                        $end_time_str = trim($times[1]); 
                        $exam_end_datetime_str = $tanggal_ujian . ' ' . $end_time_str . ':00';
                        $exam_end_timestamp = strtotime($exam_end_datetime_str);
                        $current_timestamp = time(); 
                        if ($exam_end_timestamp !== false) {
                            $waktu_ujian_telah_lewat = ($current_timestamp > $exam_end_timestamp);
                        }
                    }
                    
                    $status_mahasiswa_awal = false;
                    $tipe_ujian_lower = strtolower($jadwal['tipe_ujian']);

                    if ($tipe_ujian_lower == 'proposal' || $tipe_ujian_lower == 'sempro') {
                        if (isset($jadwal['status_sempro']) && $jadwal['status_sempro'] == 'Belum Mengajukan') {
                            $status_mahasiswa_awal = true;
                        }
                    } elseif ($tipe_ujian_lower == 'hasil' || $tipe_ujian_lower == 'semhas' || $tipe_ujian_lower == 'skripsi' || $tipe_ujian_lower == 'sidang') {
                        if (isset($jadwal['status_semhas']) && $jadwal['status_semhas'] == 'Belum Mengajukan') {
                            $status_mahasiswa_awal = true;
                        }
                    }
                    ?>

                    <?php if ($is_pembimbing && !$waktu_ujian_telah_lewat && $status_mahasiswa_awal) : ?>
                        <a href="<?= site_url('dosen/form_reschedule_ujian/' . $jadwal['id']); ?>" 
                           class="absolute top-3 right-3 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold py-1 px-3 rounded-md shadow transition duration-150 ease-in-out z-10"
                           title="Reschedule Ujian">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M12 15h.01M12 12h.01" />
                            </svg>
                            
                        </a>
                    <?php endif; ?>

                    <div class="p-6 flex-grow">
                        <h5 class="text-xl font-semibold text-indigo-700 mb-1"><?= htmlspecialchars($jadwal['nama_mahasiswa']); ?></h5>
                        <h6 class="text-sm text-gray-500 mb-3">
                            <span class="font-medium"><?= ucfirst(htmlspecialchars($jadwal['tipe_ujian'])); ?></span> - <?= date('d M Y', strtotime($jadwal['tanggal'])); ?>
                        </h6>
                        
                        <div class="mb-3">
                            <p class="text-xs text-gray-500 uppercase font-semibold">Judul Skripsi/Proposal:</p>
                            <p class="text-gray-700 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($jadwal['judul_skripsi'])); ?></p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-x-4 mb-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Slot Waktu:</p>
                                <p class="text-gray-700"><?= htmlspecialchars($jadwal['slot_waktu']); ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Ruangan:</p>
                                <p class="text-gray-700"><?= htmlspecialchars($jadwal['nama_ruangan']); ?></p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Status Konfirmasi:</p>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                <?= ($jadwal['status_konfirmasi'] == 'menunggu') ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800'; ?>">
                                <?= ucfirst(htmlspecialchars($jadwal['status_konfirmasi'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="px-6 pt-4 pb-6 border-t border-gray-200 mt-auto">
                        <?php if ($is_pembimbing && $waktu_ujian_telah_lewat && $status_mahasiswa_awal) : ?>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-2">Tentukan Hasil Ujian:</p>
                                <div class="flex space-x-2">
                                    <form action="<?= site_url('dosen/proses_hasil_ujian'); ?>" method="POST" class="flex-1">
                                        <input type="hidden" name="mahasiswa_id" value="<?= $jadwal['mahasiswa_id_fk']; ?>">
                                        <input type="hidden" name="tipe_ujian" value="<?= htmlspecialchars($jadwal['tipe_ujian']); ?>">
                                        <input type="hidden" name="jadwal_ujian_id" value="<?= $jadwal['id']; ?>">
                                        <button type="submit" name="hasil_ujian" value="ACC" class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            ACC
                                        </button>
                                    </form>
                                    <form action="<?= site_url('dosen/proses_hasil_ujian'); ?>" method="POST" class="flex-1">
                                        <input type="hidden" name="mahasiswa_id" value="<?= $jadwal['mahasiswa_id_fk']; ?>">
                                        <input type="hidden" name="tipe_ujian" value="<?= htmlspecialchars($jadwal['tipe_ujian']); ?>">
                                        <input type="hidden" name="jadwal_ujian_id" value="<?= $jadwal['id']; ?>">
                                        <button type="submit" name="hasil_ujian" value="Mengulang" class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Mengulang
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php elseif ($is_pembimbing && !$status_mahasiswa_awal): ?>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Hasil Ujian:</p>
                                <?php
                                $status_display = 'Telah Diproses'; 
                                $badge_class = 'bg-gray-200 text-gray-800'; 
                                if ($tipe_ujian_lower == 'proposal' || $tipe_ujian_lower == 'sempro') {
                                    $status_display = isset($jadwal['status_sempro']) ? $jadwal['status_sempro'] : 'Telah Diproses';
                                } elseif ($tipe_ujian_lower == 'hasil' || $tipe_ujian_lower == 'semhas' || $tipe_ujian_lower == 'skripsi' || $tipe_ujian_lower == 'sidang') {
                                    $status_display = isset($jadwal['status_semhas']) ? $jadwal['status_semhas'] : 'Telah Diproses';
                                }

                                if ($status_display == 'ACC') {
                                    $badge_class = 'bg-green-500 text-white';
                                } elseif ($status_display == 'Mengulang') {
                                    $badge_class = 'bg-red-500 text-white';
                                }
                                ?>
                                <span class="px-3 py-1 text-sm font-bold rounded-full <?= $badge_class; ?>">
                                    <?= htmlspecialchars($status_display); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php // If not pembimbing, you might want to show some other info or nothing.
                              // This part is outside the scope of the current request but for completeness.
                              // else: ?>
                            <?php // endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-6 rounded-md shadow-md" role="alert">
            <div class="flex items-center">
                <svg class="fill-current h-6 w-6 text-blue-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5h2v6H9V5zm0 8h2v2H9v-2z"/></svg>
                <p class="text-lg">Tidak ada jadwal ujian yang tersedia atau menunggu penilaian Anda sebagai pembimbing.</p>
            </div>
        </div>
    <?php endif; ?>
</div>