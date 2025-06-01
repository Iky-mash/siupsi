<!-- cards -->
<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
  <!-- row 1 -->
  <div class="max-w-3xl mx-auto mt-10 p-6 bg-white shadow-xl rounded-xl">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">Profil Dosen</h1>

    <table class="w-full table-fixed bg-white rounded-lg overflow-hidden shadow">
      <tbody class="text-gray-700 text-lg">
        <tr class="border-b">
          <th class="text-left px-6 py-4 bg-gray-100 font-semibold w-1/3 align-top">Nama</th>
          <td class="px-6 py-4 w-2/3 break-words"><?= $dosen->nama; ?></td>
        </tr>
        <tr class="border-b">
          <th class="text-left px-6 py-4 bg-gray-100 font-semibold w-1/3 align-top">Email</th>
          <td class="px-6 py-4 w-2/3 break-words"><?= $dosen->email; ?></td>
        </tr>
        <tr>
          <th class="text-left px-6 py-4 bg-gray-100 font-semibold w-1/3 align-top">NIK</th>
          <td class="px-6 py-4 w-2/3 break-words"><?= $dosen->nip; ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
