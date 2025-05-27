
<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 px-6 py-2 border-b dark:border-gray-600">
      <h2 class="font-medium">Detail Mahasiswa</h2>
      <a href="<?= base_url('mahasiswa/edit/' . $mahasiswa['id']); ?>" 
         class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded hover:bg-blue-600">
        Edit
      </a>
    </div>

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
      <tbody>
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">Nama</th>
          <td class="px-6 py-4"><?= $mahasiswa['nama']; ?></td>
        </tr>
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">Email</th>
          <td class="px-6 py-4"><?= $mahasiswa['email']; ?></td>
        </tr>
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">NIM</th>
          <td class="px-6 py-4"><?= $mahasiswa['nim']; ?></td>
        </tr>
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">Fakultas</th>
          <td class="px-6 py-4"><?= $mahasiswa['fakultas']; ?></td>
        </tr>
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">Prodi</th>
          <td class="px-6 py-4"><?= $mahasiswa['prodi']; ?></td>
        </tr>
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">Judul Skripsi</th>
          <td class="px-6 py-4"><?= $mahasiswa['judul_skripsi']; ?></td>
        </tr>
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">Status</th>
          <td class="px-6 py-4"><?= $mahasiswa['is_active'] ? 'Active' : 'Inactive'; ?></td>
        </tr>
      </tbody>
    </table>

</div>
