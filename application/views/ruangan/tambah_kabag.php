<div class="min-h-screen">
    

<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Tambah Ruangan</h2>
    <form method="post" action="<?php echo site_url('ruangan/simpan_kabag'); ?>" class="space-y-4">
        <div>
            <label for="nama_ruangan" class="block text-sm font-medium text-gray-700">Nama Ruangan</label>
            <input type="text" id="nama_ruangan" name="nama_ruangan" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="kapasitas" class="block text-sm font-medium text-gray-700">Kapasitas</label>
            <input type="number" id="kapasitas" name="kapasitas" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="tipe_seminar" class="block text-sm font-medium text-gray-700">Tipe Seminar</label>
            <select id="tipe_seminar" name="tipe_seminar" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="sempro">Sempro</option>
                <option value="semhas">Semhas</option>
            </select>
        </div>
        <div class="pt-4">
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow">
                Simpan
            </button>
        </div>
    </form>
</div>
</div>
