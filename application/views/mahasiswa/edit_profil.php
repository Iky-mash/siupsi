<div class="container mx-auto mt-8 max-w-3xl bg-white p-6 shadow-lg rounded-lg">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Data Mahasiswa</h2>

    <?php if(validation_errors()): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" class="space-y-4">
        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" id="nama" name="nama" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 <?= form_error('nama') ? 'border-red-500' : ''; ?>" 
                value="<?= $mahasiswa['nama']; ?>">
            <?php if(form_error('nama')): ?>
                <p class="text-sm text-red-600 mt-1"><?= form_error('nama'); ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 <?= form_error('email') ? 'border-red-500' : ''; ?>" 
                value="<?= $mahasiswa['email']; ?>">
            <?php if(form_error('email')): ?>
                <p class="text-sm text-red-600 mt-1"><?= form_error('email'); ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="nim" class="block text-sm font-medium text-gray-700">NIM</label>
            <input type="text" id="nim" name="nim" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 <?= form_error('nim') ? 'border-red-500' : ''; ?>" 
                value="<?= $mahasiswa['nim']; ?>">
            <?php if(form_error('nim')): ?>
                <p class="text-sm text-red-600 mt-1"><?= form_error('nim'); ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="fakultas" class="block text-sm font-medium text-gray-700">Fakultas</label>
            <input type="text" id="fakultas" name="fakultas" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" 
                value="<?= $mahasiswa['fakultas']; ?>">
        </div>
        <div>
            <label for="prodi" class="block text-sm font-medium text-gray-700">Prodi</label>
            <input type="text" id="prodi" name="prodi" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" 
                value="<?= $mahasiswa['prodi']; ?>">
        </div>
        <div>
            <label for="judul_skripsi" class="block text-sm font-medium text-gray-700">Judul Skripsi</label>
            <input type="text" id="judul_skripsi" name="judul_skripsi" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" 
                value="<?= $mahasiswa['judul_skripsi']; ?>">
        </div>
        <!-- <div>
            <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="is_active" name="is_active" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                <option value="1" <?= $mahasiswa['is_active'] ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?= !$mahasiswa['is_active'] ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div> -->
        <div>
            <button type="submit" 
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:ring focus:ring-indigo-200">
                Simpan
            </button>
        </div>
    </form>
</div>
