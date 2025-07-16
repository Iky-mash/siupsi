<div class="container mx-auto px-4 py-8">

  <div class="max-w-xl mx-auto bg-white p-6 sm:p-8 rounded-2xl shadow-soft-xl mb-10">
    <h2 class="text-xl font-bold text-slate-700 mb-6 text-center">Upload File Excel untuk Import User Mahasiswa</h2>

    <?php if ($this->session->flashdata('message')): ?>
      <div class="border border-yellow-400 bg-yellow-100 text-yellow-700 px-4 py-3 rounded relative mb-4 text-sm" role="alert">
        <span class="block sm:inline"><?php echo $this->session->flashdata('message'); ?></span>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" action="<?php echo site_url('excel_import/import'); // Pastikan URL ini benar ?>">
      <div class="mb-6">
        <label for="file-upload" class="block mb-2 ml-1 font-bold text-xs text-slate-700 uppercase">Pilih File Excel</label>
        <input 
          type="file" 
          name="file" 
          id="file-upload"
          required 
          accept=".xls, .xlsx"
          class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-fuchsia-300 focus:ring-1 focus:ring-fuchsia-300"
        >
        <p class="mt-1 text-xs text-gray-500">Format file yang didukung: .xls, .xlsx</p>
      </div>
      <button 
        type="submit" 
        class="inline-block w-full px-6 py-3 mt-2 font-bold text-center text-white uppercase align-middle transition-all bg-transparent border-0 rounded-lg cursor-pointer shadow-soft-md bg-x-25 bg-150 leading-pro text-xs ease-soft-in tracking-tight-soft bg-gradient-to-tl from-blue-600 to-cyan-400 hover:scale-102 hover:shadow-soft-xs active:opacity-85"
      >
        Import Data Mahasiswa
      </button>
    </form>
  </div>

  <div class="max-w-3xl mx-auto bg-white p-6 sm:p-8 rounded-2xl shadow-soft-xl">
    <h3 class="text-lg font-bold text-slate-700 mb-1 text-center sm:text-left">Pedoman Format File Excel untuk Data Mahasiswa</h3>
    <p class="text-sm text-slate-600 mb-5 text-center sm:text-left">
      Pastikan file Excel yang Anda unggah mengikuti format di bawah ini.
      Baris pertama adalah header dan harus sesuai dengan "Nama Kolom". Data mahasiswa dimulai dari baris kedua.
    </p>

    <div class="mb-6 text-center sm:text-left">
        <a href="<?php echo base_url('assets/templates/Template_data_mahasiswa.xlsx'); ?>" download
           class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 shadow-soft-xs transition-all ease-soft-in">
            <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z" />
                <path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z" />
            </svg>
            Unduh Template Excel
        </a>
    </div>
    <div class="overflow-x-auto rounded-lg border border-gray-200">
      <table class="min-w-full text-sm divide-y divide-gray-200">
        <thead class="bg-slate-100">
          <tr>
            <th class="px-3 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">No.</th>
            <th class="px-3 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Nama Kolom di Excel (Header)</th>
            <th class="px-3 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Contoh Isi</th>
            <th class="px-3 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Keterangan</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 text-slate-700">
          <tr>
            <td class="px-3 py-2.5">1</td>
            <td class="px-3 py-2.5 font-medium">Nama</td>
            <td class="px-3 py-2.5">WASIA TURRAHMAH</td>
            <td class="px-3 py-2.5">Nama Lengkap Mahasiswa.</td>
          </tr>
          <tr>
            <td class="px-3 py-2.5">2</td>
            <td class="px-3 py-2.5 font-medium">Email</td>
            <td class="px-3 py-2.5">213200228@almaata.ac.id</td>
            <td class="px-3 py-2.5">Email unik mahasiswa (untuk login).</td>
          </tr>
          <tr>
            <td class="px-3 py-2.5">3</td>
            <td class="px-3 py-2.5 font-medium">Password</td>
            <td class="px-3 py-2.5">213200228</td>
            <td class="px-3 py-2.5">Password default (akan di-hash sistem). Dianjurkan sama dengan NIM untuk impor awal.</td>
          </tr>
          <tr>
            <td class="px-3 py-2.5">4</td>
            <td class="px-3 py-2.5 font-medium">NIM</td>
            <td class="px-3 py-2.5">213200228</td>
            <td class="px-3 py-2.5">Nomor Induk Mahasiswa.</td>
          </tr>
          <tr>
            <td class="px-3 py-2.5">5</td>
            <td class="px-3 py-2.5 font-medium">Fakultas</td>
            <td class="px-3 py-2.5">Fakultas Tek S1 Informatika</td>
            <td class="px-3 py-2.5">Nama Fakultas Mahasiswa.</td>
          </tr>
          <tr>
            <td class="px-3 py-2.5">6</td>
            <td class="px-3 py-2.5 font-medium">Prodi</td>
            <td class="px-3 py-2.5">S1 Informatika</td>
            <td class="px-3 py-2.5">Nama Program Studi Mahasiswa.</td>
          </tr>
           <tr>
            <td class="px-3 py-2.5">7</td>
            <td class="px-3 py-2.5 font-medium">Tahun Masuk</td>
            <td class="px-3 py-2.5">2021</td>
            <td class="px-3 py-2.5">Tahun Masuk Mahasiswa</td>
          </tr>
          <tr>
            <td class="px-3 py-2.5">8</td>
            <td class="px-3 py-2.5 font-medium">Pembimbing 1 ID</td>
            <td class="px-3 py-2.5">20</td>
            <td class="px-3 py-2.5">ID Dosen Pembimbing 1 (sesuai ID unik dosen di database). Kosongkan jika belum ada.</td>
          </tr>
          <tr>
            <td class="px-3 py-2.5">9</td>
            <td class="px-3 py-2.5 font-medium">Penguji 1 ID</td>
            <td class="px-3 py-2.5">22</td>
            <td class="px-3 py-2.5">ID Dosen Penguji 1 (sesuai ID unik dosen di database). Kosongkan jika belum ada.</td>
          </tr>
          <tr>
            <td class="px-3 py-2.5">10</td>
            <td class="px-3 py-2.5 font-medium">Penguji 2 ID</td>
            <td class="px-3 py-2.5">21</td>
            <td class="px-3 py-2.5">ID Dosen Penguji 2 (sesuai ID unik dosen di database). Kosongkan jika belum ada.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="mt-4 text-xs text-gray-600 leading-relaxed">
      <strong>Catatan Penting:</strong>
      <ul class="list-disc list-inside pl-1 mt-1">
        <li>Pastikan urutan kolom di file Excel Anda sama persis dengan urutan di atas.</li>
        <li>Nama header kolom di file Excel Anda (baris pertama) harus sama persis dengan yang tertulis di kolom "Nama Kolom di Excel (Header)".</li>
        <li>Untuk kolom ID Dosen (Pembimbing dan Penguji), jika mahasiswa belum memiliki dosen yang ditugaskan, biarkan sel tersebut <strong>benar-benar kosong</strong>. Jangan diisi dengan angka 0, tanda strip (-), atau teks "kosong".</li>
        <li>Sistem akan mengimpor data dari baris kedua hingga baris terakhir yang berisi data.</li>
      </ul>
    </p>
  </div>

</div>