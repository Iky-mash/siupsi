<div class="bg-gray-50 min-h-screen py-8 sm:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-2xl">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-200">
                <?= $title ?? 'Impor Agenda dari Excel' ?>
            </h2>

            <?php if($this->session->flashdata('message')): ?>
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-md shadow-sm" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-700"><?= $this->session->flashdata('message'); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($this->session->flashdata('error')): ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-md shadow-sm" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-700"><?= $this->session->flashdata('error'); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-6 text-sm" role="alert">
                <p class="font-semibold"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>Perhatian:</p>
                <p class="mt-1">Semua data agenda yang diimpor akan diatribusikan kepada Anda (dosen yang sedang login).</p>
            </div>

            <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                <p class="text-gray-700 leading-relaxed mb-2 font-medium text-gray-800">Panduan Format File Excel:</p>
                <p class="text-sm text-gray-600 mb-3">Silakan unggah file Excel (.xlsx atau .xls) dengan format berikut. Baris pertama akan dianggap sebagai header dan dilewati.</p>
                <ul class="list-disc list-inside space-y-2 text-sm text-gray-600">
                    <li>Kolom A: <strong>tanggal</strong> (Format: YYYY-MM-DD, contoh: <code class="bg-gray-200 px-1 py-0.5 rounded text-xs text-gray-700">2024-12-31</code>)</li>
                    <li>Kolom B: <strong>slot_waktu</strong> (Contoh: <code class="bg-gray-200 px-1 py-0.5 rounded text-xs text-gray-700">08:45-10:25</code> atau <code class="bg-gray-200 px-1 py-0.5 rounded text-xs text-gray-700">08:45-10:25,13:00-14:40</code> jika lebih dari satu slot dalam satu hari)</li>
                </ul>
            </div>

            <?= form_open_multipart('agenda/process_import_excel', ['class' => 'space-y-6']); ?>
                <div>
                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-1">Pilih File Excel:</label>
                    <input type="file" name="excel_file" id="excel_file" required accept=".xlsx, .xls"
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                                  file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold
                                  file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 transition duration-150 ease-in-out">
                    <p class="mt-1 text-xs text-gray-500">Format yang diterima: .xlsx, .xls</p>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-start space-y-3 sm:space-y-0 sm:space-x-3 pt-2">
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        Impor Agenda
                    </button>
                    <a href="<?= site_url('agenda') ?>"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Kembali ke Kalender
                    </a>
                </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>