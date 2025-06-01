
<div class="min-h-screen">

<div class="container mx-auto px-4 py-8">
  <div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-2xl shadow-soft-xl">
    <h2 class="text-2xl lg:text-3xl font-bold text-slate-700 mb-6 text-center">Edit Ruangan</h2>
    <form method="post" action="<?php echo site_url('ruangan/update_kabag/'.$ruangan->id); ?>">
      
      <div class="mb-5">
        <label for="nama_ruangan" class="block mb-2 ml-1 font-bold text-xs text-slate-700 uppercase">Nama Ruangan</label>
        <input 
          type="text" 
          name="nama_ruangan" 
          id="nama_ruangan" 
          value="<?= htmlspecialchars($ruangan->nama_ruangan); ?>" 
          required 
          class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
          placeholder="Contoh: Ruang Meeting Lt. 3"
        >
      </div>
      
      <div class="mb-5">
        <label for="kapasitas" class="block mb-2 ml-1 font-bold text-xs text-slate-700 uppercase">Kapasitas</label>
        <input 
          type="number" 
          name="kapasitas" 
          id="kapasitas" 
          value="<?= htmlspecialchars($ruangan->kapasitas); ?>" 
          required 
          class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
          placeholder="Contoh: 50"
        >
      </div>
      
      <div class="mb-6">
        <label for="tipe_seminar" class="block mb-2 ml-1 font-bold text-xs text-slate-700 uppercase">Tipe Seminar</label>
        <select 
          name="tipe_seminar" 
          id="tipe_seminar" 
          required 
          class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
        >
          <option value="" disabled <?= empty($ruangan->tipe_seminar) ? 'selected' : ''; ?>>-- Pilih Tipe Seminar --</option>
          <option value="sempro" <?= $ruangan->tipe_seminar == 'sempro' ? 'selected' : ''; ?>>Seminar Proposal</option>
          <option value="semhas" <?= $ruangan->tipe_seminar == 'semhas' ? 'selected' : ''; ?>>Seminar Hasil</option>
          </select>
      </div>
      
      <div class="text-center">
        <button 
          type="submit" 
          class="inline-block w-full sm:w-auto px-6 py-3 mt-2 mb-0 font-bold text-center text-white uppercase align-middle transition-all bg-transparent border-0 rounded-lg cursor-pointer shadow-soft-md bg-x-25 bg-150 leading-pro text-xs ease-soft-in tracking-tight-soft bg-gradient-to-tl from-blue-600 to-cyan-400 hover:scale-102 hover:shadow-soft-xs active:opacity-85"
        >
          Update Ruangan
        </button>
      </div>

    </form>
  </div>
</div>
</div>

