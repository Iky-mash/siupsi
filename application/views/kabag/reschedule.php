<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0">
            <?= htmlspecialchars($title); ?>
        </h1>
        </div>

    <?php if ($this->session->flashdata('message')): ?>
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 shadow-md rounded-md" role="alert">
            <?= $this->session->flashdata('message'); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-md rounded-md" role="alert">
            <?= $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-md rounded-md" role="alert">
            <?= $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-xl rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No.</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jadwal Asli</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Diminta Oleh</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Permintaan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alasan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jadwal Baru (Jika Ada)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindakan Kabag</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($riwayat_list)): ?>
                        <?php $no = 1; foreach ($riwayat_list as $item): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-medium"><?= htmlspecialchars($item['mahasiswa_nama_orig'] ?? 'N/A'); ?> (<?= htmlspecialchars($item['mahasiswa_nim_orig'] ?? 'N/A'); ?>)</div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars(ucfirst($item['tipe_ujian_orig']) ?? 'N/A'); ?></div>
                                    <div class="text-xs text-gray-500">
                                        <?= htmlspecialchars(isset($item['tanggal_orig']) ? date('d M Y', strtotime($item['tanggal_orig'])) : 'N/A'); ?>
                                        (<?= htmlspecialchars($item['slot_waktu_orig'] ?? ''); ?>)
                                    </div>
                                    <div class="text-xs text-gray-400">Ruang: <?= htmlspecialchars($item['ruangan_orig'] ?? 'N/A'); ?></div>
                                    <div class="text-xs text-blue-500">ID Jadwal Asli: <?= htmlspecialchars($item['original_jadwal_id']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?= htmlspecialchars($item['requester_nama'] ?? ucfirst($item['requested_by_user_type'])); ?>
                                    <?php if($item['requested_by_user_type'] == 'dosen'): ?>
                                        <span class="block text-xs text-gray-500">(ID: <?= htmlspecialchars($item['requested_by_user_id']); ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars(date('d M Y, H:i', strtotime($item['request_timestamp']))); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs" title="<?= htmlspecialchars($item['reason_for_reschedule']); ?>">
                                    <div class="truncate w-48"> <?= nl2br(htmlspecialchars($item['reason_for_reschedule'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php
                                        $status = $item['reschedule_status'];
                                        $badge_class = 'bg-gray-200 text-gray-800';
                                        if ($status == 'requested') {
                                            $badge_class = 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-300';
                                        } elseif ($status == 'kabag_approved') {
                                            $badge_class = 'bg-green-100 text-green-800 ring-1 ring-green-300';
                                        } elseif ($status == 'kabag_rejected' || $status == 'failed') {
                                            $badge_class = 'bg-red-100 text-red-800 ring-1 ring-red-300';
                                        }
                                    ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badge_class; ?>">
                                        <?= ucfirst(str_replace('_', ' ', htmlspecialchars($status))); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php if (!empty($item['new_jadwal_id'])): ?>
                                        <div class="text-xs text-gray-500">
                                            <?= htmlspecialchars(isset($item['tanggal_new']) ? date('d M Y', strtotime($item['tanggal_new'])) : 'N/A'); ?>
                                            (<?= htmlspecialchars($item['slot_waktu_new'] ?? ''); ?>)
                                        </div>
                                        <div class="text-xs text-gray-400">Ruang: <?= htmlspecialchars($item['ruangan_new'] ?? 'N/A'); ?></div>
                                        <div class="text-xs text-green-500">ID Jadwal Baru: <?= htmlspecialchars($item['new_jadwal_id']); ?></div>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                 <td class="px-6 py-4 text-sm text-gray-600 max-w-xs" title="<?= htmlspecialchars($item['kabag_notes'] ?? ''); ?>">
                                    <div class="text-xs text-gray-500">
                                        <?= htmlspecialchars(isset($item['kabag_action_timestamp']) ? date('d M Y, H:i', strtotime($item['kabag_action_timestamp'])) : ''); ?>
                                    </div>
                                    <div class="truncate w-48"> <?= nl2br(htmlspecialchars($item['kabag_notes'] ?? '-')); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-lg font-medium">Tidak Ada Riwayat</p>
                                    <p class="text-sm">Belum ada riwayat permintaan penjadwalan ulang yang tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </div>
</div>