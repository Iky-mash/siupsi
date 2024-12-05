<div class="flex flex-wrap -mx-3">
    <div class="flex-none w-full max-w-full px-3">
        <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                <h6 class="text-2xl font-semibold text-blue-900">Tambah Agenda</h6>
            </div>
            <div class="flex-auto px-6 pt-6 pb-2">
                <form action="<?= base_url('agenda/store') ?>" method="post" class="space-y-4">
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-slate-700">Tanggal</label>
                        <input type="date" name="tanggal" required class="block w-full mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label for="waktu_mulai" class="block text-sm font-medium text-slate-700">Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" required class="block w-full mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label for="waktu_selesai" class="block text-sm font-medium text-slate-700">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" required class="block w-full mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-slate-700">Keterangan</label>
                        <input type="text" name="keterangan" class="block w-full mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <button type="submit" class="inline-block px-6 py-2 text-sm font-medium text-white bg-blue-500 rounded shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
