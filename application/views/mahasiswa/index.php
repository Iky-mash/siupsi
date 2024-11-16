<?php 
$user = $this->session->userdata(); 
?>
<!-- cards -->
<div class="w-full h-full px-6 py-6 mx-auto">
  <!-- row 1 -->
  <div class="max-w-sm mx-auto bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200 h-full">
    <div class="p-6 h-full flex flex-col">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">📝 Informasi Skripsi Mahasiswa</h2>

      <div class="space-y-4 flex-grow">
        <div>
          <span class="font-medium text-gray-600">Nama:</span>
          <p class="text-gray-800"><?= $user['nama']; ?></p>
        </div>
        <div>
          <span class="font-medium text-gray-600">Email:</span>
          <p class="text-gray-800"><?= $user['email']; ?></p>
        </div>

        <div>
          <span class="font-medium text-gray-600">Dosen Pembimbing:</span>
          <p class="text-gray-800">Dr. Andi Wijaya, M.Sc.</p>
        </div>

        <div>
          <span class="font-medium text-gray-600">Judul Skripsi:</span>
          <p class="text-gray-800">"Analisis Pengaruh Teknologi Terhadap Produktivitas Kerja di Era Digital"</p>
        </div>
      </div>
    </div>
  </div>
</div>
