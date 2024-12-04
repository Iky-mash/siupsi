<div class="container mx-auto mt-8">
    <h2 class="text-2xl font-semibold text-gray-800">Pengajuan Ujian Skripsi</h2>

    <!-- Flash Message -->
    <?php if ($this->session->flashdata('message')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mt-4">
            <?= $this->session->flashdata('message'); ?>
        </div>
    <?php endif; ?>

    <!-- Form Pengajuan -->
    <?= form_open_multipart('pengajuan/submit', ['class' => 'mt-6']); ?>
        <div class="mb-4">
            <label for="judul_skripsi" class="block text-gray-700 font-medium mb-2">Judul Skripsi</label>
            <input 
                type="text" 
                id="judul_skripsi" 
                name="judul_skripsi" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                required>
        </div>

        <div class="mb-4">
            <label for="lembar_pengesahan" class="block text-gray-700 font-medium mb-2">Lembar Pengesahan (PDF)</label>
            <input 
                type="file" 
                id="lembar_pengesahan" 
                name="lembar_pengesahan" 
                class="block w-full text-sm text-gray-500 border border-gray-300 rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                accept="application/pdf" 
                required>
        </div>

        <button 
            type="submit" 
            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
            Ajukan
        </button>
    <?= form_close(); ?>
</div>
