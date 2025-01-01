<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50 p-6">
    <div class="flex flex-wrap -mx-3">
        <div class="flex-none w-full max-w-full px-3">
            <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex items-center justify-between">
                    <h6 class="text-xs font-bold uppercase text-slate-400 opacity-70">Data Dosen</h6>
                    <a href="<?= base_url('admin/data_pembimbingPenguji'); ?>" class="px-4 py-2 text-xs font-semibold leading-tight text-slate-400 bg-transparent border rounded hover:bg-blue-100 transition duration-200">
                        (+) Pembimbing dan Penguji
                    </a>
                </div>
                <div class="flex-auto px-0 pt-0 pb-2">
                    <div class="p-0 overflow-x-auto">
                        <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                            <thead class="align-bottom">
                                <tr>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Nama Mahasiswa</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">NIM</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Judul Skripsi</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Penentuan Pembimbing dan Penguji</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mahasiswa as $mhs): ?>
                                    <tr class="hover:bg-blue-100 transition duration-200">
                                    <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
    <span class="text-xs font-semibold leading-tight text-slate-400"><?php echo isset($mhs->nama) ? htmlspecialchars($mhs->nama) : 'data tidak ditemukan'; ?></span>
</td>
<td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
    <span class="text-xs font-semibold leading-tight text-slate-400"><?php echo isset($mhs->nim) ? htmlspecialchars($mhs->nim) : 'data tidak ditemukan'; ?></span>
</td>
<td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
    <span class="text-xs font-semibold leading-tight text-slate-400"><?php echo isset($mhs->judul_skripsi) ? htmlspecialchars($mhs->judul_skripsi) : 'data tidak ditemukan'; ?></span>
</td>

                                        <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <form action="<?php echo base_url('admin/assign_pembimbing_and_penguji'); ?>" method="post" class="space-y-4">
                                                <input type="hidden" name="mahasiswa_id" value="<?php echo $mhs->id; ?>">

                                                <div class="space-y-2">
                                                    <label for="dosen_pembimbing" class="block text-xs font-semibold leading-tight  text-gray-700">Dosen Pembimbing</label>
                                                    <select name="dosen_pembimbing_id" required class="block w-full px-3 py-2 text-xs font-semibold leading-tight border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option value="">Pilih Dosen Pembimbing</option>
                                                        <?php foreach ($dosen as $dsn): ?>
                                                            <option value="<?php echo $dsn->id; ?>" <?php echo ($mhs->pembimbing_id == $dsn->id) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($dsn->nama); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="space-y-2">
                                                    <label for="dosen_penguji1" class="block text-xs font-semibold leading-tight text-gray-700">Dosen Penguji 1</label>
                                                    <select name="dosen_penguji1_id" required class="block w-full px-3 py-2 text-xs font-semibold leading-tight border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option value="">Pilih Dosen Penguji 1</option>
                                                        <?php foreach ($dosen as $dsn): ?>
                                                            <option value="<?php echo $dsn->id; ?>" <?php echo ($mhs->penguji1_id == $dsn->id) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($dsn->nama); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="space-y-2">
                                                    <label for="dosen_penguji2" class="block text-xs font-semibold leading-tight text-gray-700">Dosen Penguji 2</label>
                                                    <select name="dosen_penguji2_id" required class="block w-full px-3 py-2 text-xs font-semibold leading-tight border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option  value="">Pilih Dosen Penguji 2</option>
                                                        <?php foreach ($dosen as $dsn): ?>
                                                            <option value="<?php echo $dsn->id; ?>" <?php echo ($mhs->penguji2_id == $dsn->id) ? 'selected' : ''; ?>>
                                                               <span class="text-xs font-semibold leading-tight text-gray-700"> <?php echo htmlspecialchars($dsn->nama); ?></span>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <button type="submit" class="w-full py-2 px-4 text-xs font-semibold leading-tight bg-blue-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-200">
                                                    Tentukan Pembimbing dan Penguji
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
