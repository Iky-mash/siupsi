<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50 p-6">

    <!-- Memastikan variabel $id_dosen ada -->
    <?php if (isset($id_dosen)): ?>
        <div class="flex flex-wrap -mx-3">
            <div class="flex-none w-full max-w-full px-3">
                <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex items-center justify-between">
                        <h6 class="text-xs font-bold uppercase text-slate-400 opacity-70">Agenda Dosen</h6>
                        <!-- Link untuk tambah agenda -->
                        <a href="<?= base_url('admin/tambah_agenda/' . $id_dosen); ?>" class="px-4 py-2 text-xs font-semibold leading-tight text-slate-400 bg-transparent border rounded hover:bg-blue-100 transition duration-200">
                            (+) Tambah Agenda
                        </a>
                    </div>
                    <div class="flex-auto px-0 pt-0 pb-2">
                        <div class="p-0 overflow-x-auto">
                            <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                                <thead class="align-bottom">
                                    <tr>
                                        <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">No</th>
                                        <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Tanggal</th>
                                        <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Waktu</th>
                                        <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Keterangan</th>
                                        <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($agenda)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($agenda as $item): ?>
                                            <tr class="hover:bg-blue-100 transition duration-200">
                                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <span class="text-xs font-semibold leading-tight text-slate-400"><?= $no++; ?></span>
                                                </td>
                                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <span class="text-xs font-semibold leading-tight text-slate-400"><?= htmlspecialchars($item->tanggal); ?></span>
                                                </td>
                                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <span class="text-xs font-semibold leading-tight text-slate-400"><?= htmlspecialchars($item->waktu_mulai) . ' - ' . htmlspecialchars($item->waktu_selesai); ?></span>
                                                </td>
                                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <span class="text-xs font-semibold leading-tight text-slate-400"><?= htmlspecialchars($item->keterangan); ?></span>
                                                </td>
                                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <a href="<?= base_url('admin/edit_agenda/' . $item->id_agenda); ?>" class="px-2 py-1 text-xs font-semibold text-blue-600 hover:bg-blue-100 rounded">Edit</a>
                                                    <a href="<?= base_url('admin/delete_agenda/' . $item->id_agenda); ?>" onclick="return confirm('Are you sure you want to delete this agenda?');" class="px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-100 rounded">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center p-4">No agenda available</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center text-red-600">ID Dosen Tidak Ditemukan</p>
    <?php endif; ?>

</body>
</html>
