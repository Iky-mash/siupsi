<div class="flex flex-wrap -mx-3 mt-6">
    <div class="flex-none w-full max-w-full px-3">
        <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                <h6 class="text-2xl font-bold text-blue-900 uppercase">Edit Data Dosen</h6>
            </div>
            <form action="<?= site_url('admin/update_dosen'); ?>" method="POST" class="p-6">
                <input type="hidden" name="id" value="<?= isset($dosen_edit) ? $dosen_edit->id : ''; ?>">
                <div class="mb-4">
                    <label for="nama" class="block text-xs font-bold uppercase text-slate-400 mb-2">Nama</label>
                    <input type="text" id="nama" name="nama" value="<?= isset($dosen_edit) ? htmlspecialchars($dosen_edit->nama) : ''; ?>" class="block w-full px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-slate-500">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-xs font-bold uppercase text-slate-400 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="<?= isset($dosen_edit) ? htmlspecialchars($dosen_edit->email) : ''; ?>" class="block w-full px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-slate-500">
                </div>
                <div class="mb-4">
                    <label for="nip" class="block text-xs font-bold uppercase text-slate-400 mb-2">NIP</label>
                    <input type="text" id="nip" name="nip" value="<?= isset($dosen_edit) ? htmlspecialchars($dosen_edit->nip) : ''; ?>" class="block w-full px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-slate-500">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 text-xs font-bold uppercase text-white bg-blue-600 rounded-md shadow-md hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
