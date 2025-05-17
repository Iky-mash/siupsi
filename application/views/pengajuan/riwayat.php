<!-- Riwayat Pengajuan -->
<div class="max-w-6xl mx-auto mt-10 p-6 bg-white shadow-lg rounded-xl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        📄 Riwayat Pengajuan Ujian
    </h2>

    <?php if (!empty($pengajuan)) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($pengajuan as $item): ?>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="mb-4">
                        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide">
                            <?= $item->tipe_ujian; ?>
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-800 leading-tight"><?= $item->judul_skripsi; ?></h3>
                    
                    <div class="mt-2 text-sm text-gray-600 space-y-1">
                        <p><strong>📅 Tanggal:</strong> <?= $item->tanggal_pengajuan; ?></p>
                        <p>
                            <strong>📌 Status:</strong>
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium
                                <?= $item->status == 'draft' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                <?= ucfirst($item->status); ?>
                            </span>
                        </p>
                    </div>

                    <div class="mt-4 flex flex-col space-y-2 text-sm">
                        <a href="<?= base_url('uploads/' . $item->file_lembar_bimbingan); ?>" target="_blank" class="text-blue-600 hover:underline">📘 Lihat Lembar Bimbingan</a>
                        <a href="<?= base_url('uploads/' . $item->file_lembar_pengesahan); ?>" target="_blank" class="text-green-600 hover:underline">📗 Lihat Lembar Pengesahan</a>
                    </div>

                    <?php if ($item->status == 'draft'): ?>
                        <form action="<?= site_url('Pengajuan/konfirmasi'); ?>" method="post" class="mt-4">
                            <input type="hidden" name="pengajuan_id" value="<?= $item->id; ?>">
                            <p class="text-xs text-red-500 mb-2">Pastikan Anda telah benar mengisi berkas!</p>
                            <button type="submit"
                                    onclick="return confirm('Apakah Anda yakin sudah mengecek berkas pengajuan tersebut?');"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-4 py-2 rounded-md transition">
                                ✅ Konfirmasi Selesai Berkas
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="mt-4 inline-block text-green-600 text-sm font-semibold">✔️ Sudah Dikonfirmasi</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-gray-500 text-center mt-6">
            Belum ada pengajuan yang Anda lakukan.
        </div>
    <?php endif; ?>
</div>
