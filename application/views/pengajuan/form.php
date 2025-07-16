<div class="max-w-4xl mx-auto my-10 p-6 sm:p-8 bg-white shadow-2xl rounded-xl">
    <div class="flex items-center gap-3 mb-8">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-indigo-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12.75h3.75M11.25 12h3.75m-3.75 3h3.75M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
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
    <div class="mb-8 flex justify-center border border-gray-200 rounded-lg p-1 bg-gray-100">
        <button 
            id="btnSempro" 
            type="button" 
            class="flex-1 px-4 py-2.5 rounded-md font-medium text-sm focus:outline-none transition-all duration-300 ease-in-out
                   <?= !$can_access_sempro ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-600' ?>"
            <?= !$can_access_sempro ? 'disabled title="Anda sudah lulus Seminar Proposal. Tidak dapat mengajukan lagi."' : '' ?>
        >
            Seminar Proposal
        </button>
        
        <button 
            id="btnSemhas" 
            type="button" 
            class="flex-1 px-4 py-2.5 rounded-md font-medium text-sm focus:outline-none transition-all duration-300 ease-in-out
                   <?= !$can_access_semhas ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-600' ?>"
            <?= !$can_access_semhas ? 'disabled title="Anda harus lulus Seminar Proposal (status ACC) terlebih dahulu"' : '' ?>
        >
            Seminar Hasil
        </button>
    </div>

    <?= form_open_multipart('pengajuan/store', ['id' => 'pengajuanForm']); ?>
        <input type="hidden" name="tipe_ujian" id="tipe_ujian" value="<?= htmlspecialchars($selected_type); ?>">

        <div class="space-y-8">
            <div>
                <label for="judul_skripsi" class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Skripsi/Tugas Akhir <span class="text-red-500">*</span></label>
                <input type="text" name="judul_skripsi" id="judul_skripsi" value="<?= set_value('judul_skripsi'); ?>" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm"
                       placeholder="Contoh: Pengembangan Sistem Informasi Akademik...">
            </div>

            <div id="semproFields" class="space-y-6 p-6 border border-indigo-100 rounded-lg bg-indigo-50/30">
                <h3 class="text-xl font-semibold text-indigo-700 border-b border-indigo-200 pb-3 mb-6">Berkas Seminar Proposal</h3>
                <?php foreach ($sempro_files_config as $file): ?>
                   <div class="form-group">
    <label for="<?= $file['name'] ?>_sempro" class="block text-sm font-medium text-gray-600 mb-1.5"><?= $file['label'] ?> <span class="text-red-500">*</span></label>
    <input type="file" name="<?= $file['name'] ?>" id="<?= $file['name'] ?>_sempro" required class="...">
    <small class="text-xs text-gray-500 mt-1 block">Wajib format: PDF. Ukuran maksimal: 50MB.</small>
</div>
                <?php endforeach; ?>
            </div>

            <div id="semhasFields" class="space-y-6 p-6 border border-green-100 rounded-lg bg-green-50/30" style="display: none;">
                <h3 class="text-xl font-semibold text-green-700 border-b border-green-200 pb-3 mb-6">Berkas Seminar Hasil</h3>
                <?php foreach ($semhas_files_config as $file): ?>
                     <div class="form-group">
    <label for="<?= $file['name'] ?>_semhas" class="block text-sm font-medium text-gray-600 mb-1.5"><?= $file['label'] ?> <span class="text-red-500">*</span></label>
    <input type="file" name="<?= $file['name'] ?>" id="<?= $file['name'] ?>_semhas" required class="...">
    <small class="text-xs text-gray-500 mt-1 block">Wajib format: PDF. Ukuran maksimal: 50MB.</small>
</div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-200">
             <p class="text-xs text-center text-gray-500 mb-4">Pastikan semua data dan berkas yang diunggah sudah benar dan sesuai ketentuan.</p>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out text-base flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H5.5z" /><path d="M9 13l3-2.5-3-2.5v1.5H5v2h4v1.5z" /></svg>
                Ajukan Ujian Sekarang
            </button>
        </div>
    <?= form_close(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSempro = document.getElementById('btnSempro');
    const btnSemhas = document.getElementById('btnSemhas');
    const semproFields = document.getElementById('semproFields');
    const semhasFields = document.getElementById('semhasFields');
    const tipeUjianInput = document.getElementById('tipe_ujian');
    const initialType = tipeUjianInput.value;

    function setActiveButton(type) {
        if (type === 'Sempro') {
            if (!btnSempro.disabled) {
                btnSempro.classList.remove('bg-gray-100', 'text-gray-600');
                btnSempro.classList.add('bg-indigo-600', 'text-white', 'shadow-md');
            }
            if (!btnSemhas.disabled) {
                btnSemhas.classList.remove('bg-green-600', 'text-white', 'shadow-md');
                btnSemhas.classList.add('bg-gray-100', 'text-gray-600');
            }
            semproFields.style.display = 'block';
            semhasFields.style.display = 'none';
            tipeUjianInput.value = 'Sempro';
            semhasFields.querySelectorAll('input[type="file"]').forEach(input => { input.disabled = true; });
            semproFields.querySelectorAll('input[type="file"]').forEach(input => { input.disabled = false; });
        } else { // Semhas
            if (!btnSemhas.disabled) {
                btnSemhas.classList.remove('bg-gray-100', 'text-gray-600');
                btnSemhas.classList.add('bg-green-600', 'text-white', 'shadow-md');
            }
            if (!btnSempro.disabled) {
                btnSempro.classList.remove('bg-indigo-600', 'text-white', 'shadow-md');
                btnSempro.classList.add('bg-gray-100', 'text-gray-600');
            }
            semproFields.style.display = 'none';
            semhasFields.style.display = 'block';
            tipeUjianInput.value = 'Semhas';
            semproFields.querySelectorAll('input[type="file"]').forEach(input => { input.disabled = true; });
            semhasFields.querySelectorAll('input[type="file"]').forEach(input => { input.disabled = false; });
        }
    }

    // Tambahkan listener hanya pada tombol yang aktif
    if (!btnSempro.disabled) {
        btnSempro.addEventListener('click', () => setActiveButton('Sempro'));
    }
    if (!btnSemhas.disabled) {
        btnSemhas.addEventListener('click', () => setActiveButton('Semhas'));
    }

    // Atur kondisi awal saat halaman dimuat
    setActiveButton(initialType);
});
</script>