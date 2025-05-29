
        <div class="min-h-screen  flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                <h6 class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 ">Data Mahasiswa Bimbingan</h6>
            </div>
            <div class="flex-auto px-0 pt-0 pb-2">
                <div class="p-0 overflow-x-auto">
                    <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                        <thead class="align-bottom">
                            <tr>
                                <th class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">No</th>
                                <th class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">Nama</th> 
                                <th class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">NIM</th>
                                <th class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">Sempro</th>
                                <th class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">Semhas</th>
                                <th class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">Penguji 1</th>
                                <th class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">Penguji 2</th>
                                <!-- <th class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">Aksi</th>  -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($mahasiswa)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($mahasiswa as $mhs): ?>
                                    <tr class="hover:bg-blue-100 transition duration-200">
                                        <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <span class="text-xs font-semibold leading-tight text-slate-400"><?= $no++; ?></span>
                                        </td>
                                        <td class="p-2 text-left align-middle bg-transparent border-b whitespace-nowrap shadow-transparent"> 
                                            <span class="text-xs font-semibold leading-tight text-slate-400"><?= htmlspecialchars($mhs->nama ?? ''); ?></span>
                                        </td>
                                        <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <span class="text-xs font-semibold leading-tight text-slate-400"><?= htmlspecialchars($mhs->nim ?? ''); ?></span>
                                        </td>
                                        <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <span class="text-xs font-semibold leading-tight px-3 py-1 rounded-md 
                                                <?php 
                                                    // Contoh pewarnaan kondisional (sesuaikan dengan nilai status Anda)
                                                    $status_sempro = strtolower($mhs->status_sempro ?? '-');
                                                    if ($status_sempro === 'selesai' || $status_sempro === 'lulus') {
                                                        echo 'bg-green-100 text-green-700';
                                                    } elseif ($status_sempro === 'proses' || $status_sempro === 'sedang berjalan') {
                                                        echo 'bg-yellow-100 text-yellow-700';
                                                    } elseif ($status_sempro === '-' || $status_sempro === 'belum') {
                                                        echo 'bg-gray-200 text-gray-700';
                                                    } else {
                                                        echo 'bg-blue-100 text-blue-700'; // Default
                                                    }
                                                ?>
                                            ">
                                                <?= htmlspecialchars($mhs->status_sempro ?? '-'); ?>
                                            </span>
                                        </td>
                                         <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <span class="text-xs font-semibold leading-tight px-3 py-1 rounded-md 
                                                <?php 
                                                    // Contoh pewarnaan kondisional (sesuaikan dengan nilai status Anda)
                                                    $status_sempro = strtolower($mhs->status_sempro ?? '-');
                                                    if ($status_sempro === 'selesai' || $status_sempro === 'lulus') {
                                                        echo 'bg-green-100 text-green-700';
                                                    } elseif ($status_sempro === 'proses' || $status_sempro === 'sedang berjalan') {
                                                        echo 'bg-yellow-100 text-yellow-700';
                                                    } elseif ($status_sempro === '-' || $status_sempro === 'belum') {
                                                        echo 'bg-gray-200 text-gray-700';
                                                    } else {
                                                        echo 'bg-blue-100 text-blue-700'; // Default
                                                    }
                                                ?>
                                            ">
                                                <?= htmlspecialchars($mhs->status_semhas ?? '-'); ?>
                                            </span>
                                        </td>
                                       
                                        <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <span class="text-xs font-semibold leading-tight text-slate-400"><?= htmlspecialchars($mhs->penguji1_nama ?? '-'); ?></span>
                                        </td>
                                        <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <span class="text-xs font-semibold leading-tight text-slate-400"><?= htmlspecialchars($mhs->penguji2_nama ?? '-'); ?></span>
                                        </td>
                                        <!-- <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <?php  ?>
                                            <a href="<?= site_url('controller_anda/method_detail/' . $mhs->id); ?>" 
                                               class="bg-indigo-500 hover:bg-indigo-700 text-white text-xs font-semibold py-1 px-3 rounded-md transition duration-150 ease-in-out">
                                                Detail
                                            </a>
                                        </td> -->
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent"> {/* Pastikan colspan sesuai jumlah kolom th (8 kolom) */}
                                        <span class="text-xs font-semibold leading-tight text-slate-400">Tidak ada mahasiswa bimbingan.</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>