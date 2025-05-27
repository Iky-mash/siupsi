
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-10 text-center">
            <h1 class="text-4xl font-bold text-gray-800 tracking-tight"><?= htmlspecialchars($title); ?></h1>
        </header>

        <?php if ($this->session->flashdata('error_form')): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-8 shadow-md rounded-md" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-red-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5h2v6H9V5zm0 8h2v2H9v-2z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold">Perhatian!</p>
                        <p class="text-sm"><?= $this->session->flashdata('error_form'); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <div class="p-6 sm:p-8 lg:p-10">
                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-6 border-b pb-3">Detail Jadwal Saat Ini</h2>
                    <?php if ($jadwal_detail): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-500 block mb-0.5">Mahasiswa:</span>
                                <p class="text-gray-700 text-base"><?= htmlspecialchars($jadwal_detail->mahasiswa_nama ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500 block mb-0.5">NIM:</span>
                                <p class="text-gray-700 text-base"><?= htmlspecialchars($jadwal_detail->mahasiswa_nim ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500 block mb-0.5">Tipe Ujian:</span>
                                <p class="text-gray-700 text-base"><?= htmlspecialchars(ucfirst($jadwal_detail->tipe_ujian) ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500 block mb-0.5">Tanggal:</span>
                                <p class="text-gray-700 text-base"><?= htmlspecialchars(date('d F Y', strtotime($jadwal_detail->tanggal)) ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500 block mb-0.5">Waktu:</span>
                                <p class="text-gray-700 text-base"><?= htmlspecialchars($jadwal_detail->slot_waktu ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500 block mb-0.5">Ruangan:</span>
                                <p class="text-gray-700 text-base"><?= htmlspecialchars($jadwal_detail->nama_ruangan ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 rounded-md">
                            <p>Detail jadwal tidak dapat dimuat atau tidak ditemukan.</p>
                        </div>
                    <?php endif; ?>
                </section>

                <hr class="my-8 border-gray-200">

                <section>
                    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Formulir Penjadwalan Ulang</h2>
                    <?= form_open('dosen/process_reschedule_request', ['class' => 'space-y-6']); ?>
                        <input type="hidden" name="original_jadwal_id" value="<?= htmlspecialchars($original_jadwal_id); ?>">

                        <div>
                            <label for="reason_reschedule" class="block text-sm font-medium text-gray-700 mb-1">
                                Alasan Penjadwalan Ulang <span class="text-red-600">*</span>
                            </label>
                            <textarea name="reason_reschedule" id="reason_reschedule" rows="5" required
                                      class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm
                                             focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                             sm:text-sm transition-colors duration-150"
                                      placeholder="Jelaskan secara detail alasan mengapa jadwal ini perlu dijadwalkan ulang..."><?= set_value('reason_reschedule'); ?></textarea>
                            <?= form_error('reason_reschedule', '<p class="mt-2 text-xs text-red-600">', '</p>'); ?>
                        </div>

                        <div class="pt-2 flex items-center justify-end space-x-4 border-t border-gray-200 mt-8 pt-6">
                            <a href="<?= site_url('dosen/jadwal_saya'); // Sesuaikan dengan URL daftar jadwal dosen ?>"
                               class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 rounded-lg transition-colors duration-150 ease-in-out">
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex justify-center items-center px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-lg shadow-sm transition-colors duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Ajukan Penjadwalan Ulang
                            </button>
                        </div>
                    <?= form_close(); ?>
                </section>
            </div>
        </div>
    </div>
