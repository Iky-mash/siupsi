<!-- form_pengajuan.php -->
<div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg mt-10">
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">Form Pengajuan Ujian</h1>
    
    <form action="<?php echo site_url('Pengajuan/simpan'); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Judul Skripsi</label>
            <input type="text" name="judul_skripsi" placeholder="Judul Skripsi" required
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tipe Ujian</label>
            <select name="tipe_ujian" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="Sempro">Sempro</option>
                <option value="Semhas">Semhas</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Upload Lembar Bimbingan</label>
            <input type="file" name="file_lembar_bimbingan" required
                   class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Upload Lembar Pengesahan</label>
            <input type="file" name="file_lembar_pengesahan" required
                   class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-100 file:text-green-700 hover:file:bg-green-200">
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
            Simpan
        </button>
    </form>
</div>
