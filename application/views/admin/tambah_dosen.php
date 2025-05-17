<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-gray-700">Tambah Data Dosen</h1>
        
        <?php if ($this->session->flashdata('success')): ?>
            <p class="text-green-600 mb-4 font-semibold"><?= $this->session->flashdata('success') ?></p>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <p class="text-red-600 mb-4 font-semibold"><?= $this->session->flashdata('error') ?></p>
        <?php endif; ?>
        
        <form action="<?= site_url('dosen/simpan'); ?>" method="POST" class="space-y-4">
            <div class="flex items-center">
                <label for="nama" class="w-32 font-bold text-gray-600">Nama:</label>
                <input type="text" id="nama" name="nama" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 p-2">
            </div>

            <div class="flex items-center">
                <label for="email" class="w-32 font-bold text-gray-600">Email:</label>
                <input type="email" id="email" name="email" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 p-2">
            </div>

            <div class="flex items-center">
                <label for="password" class="w-32 font-bold text-gray-600">Password:</label>
                <input type="password" id="password" name="password" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 p-2">
            </div>

            <div class="flex items-center">
                <label for="nip" class="w-32 font-bold text-gray-600">NIP:</label>
                <input type="text" id="nip" name="nip" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 p-2">
            </div>

            <input type="hidden" name="role_id" value="2">

            <div class="text-right">
                <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>
