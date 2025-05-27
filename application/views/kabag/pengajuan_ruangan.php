<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
    <?php if ($this->session->flashdata('success')): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4 shadow" role="alert">
        <p class="font-bold">Sukses</p>
        <p><?= $this->session->flashdata('success') ?></p>
    </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-4 shadow" role="alert">
        <p class="font-bold">Error</p>
        <p><?= $this->session->flashdata('error') ?></p>
    </div>
    <?php endif; ?>

    <div class="mb-6 flex flex-wrap justify-center gap-4">
        <button class="btn-filter bg-yellow-500 text-white px-5 py-2 rounded-md shadow hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-500" data-status="Menunggu">Menunggu</button>
        <button class="btn-filter bg-green-500 text-white px-5 py-2 rounded-md shadow hover:bg-green-600 focus:ring-2 focus:ring-green-500" data-status="Dikonfirmasi">Dikonfirmasi</button>
        <button class="btn-filter bg-red-500 text-white px-5 py-2 rounded-md shadow hover:bg-red-600 focus:ring-2 focus:ring-red-500" data-status="Ditolak">Ditolak</button>
        <button class="btn-filter bg-blue-500 text-white px-5 py-2 rounded-md shadow hover:bg-blue-600 focus:ring-2 focus:ring-blue-500" data-status="All">Semua</button>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-center">Mahasiswa</th>
                    <th class="px-4 py-3 text-center">Judul</th>
                    <th class="px-4 py-3 text-center">Tipe Ujian</th>
                    <th class="px-4 py-3 text-center">Tanggal</th>
                    <th class="px-4 py-3 text-center">Waktu</th>
                    <th class="px-4 py-3 text-center">Ruangan</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                <?php foreach ($jadwal as $item): ?>
                    <?php 
                        $bgStatus = match(strtolower($item->status_konfirmasi)) { 
                            'menunggu' => 'bg-yellow-50', 
                            'dikonfirmasi' => 'bg-green-50', 
                            'ditolak' => 'bg-red-50', 
                            default => 'bg-white', 
                        }; 
                        $textStatus = match(strtolower($item->status_konfirmasi)) { 
                            'menunggu' => 'text-yellow-700', 
                            'dikonfirmasi' => 'text-green-700', 
                            'ditolak' => 'text-red-700', 
                            default => 'text-gray-700', 
                        }; 
                        $icon = match(strtolower($item->status_konfirmasi)) { 
                            'menunggu' => '⏳', 
                            'dikonfirmasi' => '✅', 
                            'ditolak' => '❌', 
                            default => 'ℹ️', 
                        }; 
                    ?>
                    <tr class="<?= $bgStatus ?> border-b border-gray-200 hover:bg-gray-50" data-status="<?= strtolower($item->status_konfirmasi) ?>">
                        <td class="px-4 py-3 text-center font-semibold"><?= htmlspecialchars($item->mahasiswa_nama) ?></td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->judul_skripsi) ?></td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->tipe_ujian) ?></td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->tanggal) ?></td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->slot_waktu) ?></td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->nama_ruangan) ?></td>
                        <td class="px-4 py-3 text-center <?= $textStatus ?>">
                            <span class="inline-flex items-center gap-1">
                                <?= $icon ?> <?= htmlspecialchars($item->status_konfirmasi) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button class="btn-update-status bg-indigo-500 text-white px-4 py-2 rounded-md shadow hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-500 text-xs" 
                                    data-id="<?= $item->id ?>" 
                                    data-current-status="<?= strtolower($item->status_konfirmasi) ?>"
                                    data-tipe-ujian="<?= htmlspecialchars(strtolower($item->tipe_ujian)) ?>"
                                    data-tanggal="<?= htmlspecialchars($item->tanggal) ?>"
                                    data-slot-waktu="<?= htmlspecialchars($item->slot_waktu) ?>"
                                    data-pengajuan-id="<?= $item->pengajuan_id ?? '' // Ensure pengajuan_id is available ?>">
                                Update Status
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="statusUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" style="display: none; z-index: 1000;">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Update Status Jadwal</h2>
            <button id="closeModalBtn" class="text-gray-600 hover:text-gray-800 text-2xl">&times;</button>
        </div>
        
        <form id="updateStatusForm" action="<?= base_url('kabag/update_status') ?>" method="post">
            <input type="hidden" name="id" id="modalJadwalId">
            <input type="hidden" name="pengajuan_id" id="modalPengajuanId">
            <input type="hidden" name="rejection_reason" id="modalRejectionReason">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Status Baru:</label>
                <div class="space-y-2">
                    <div>
                        <input type="radio" name="status_konfirmasi" id="statusMenunggu" value="Menunggu" class="mr-2">
                        <label for="statusMenunggu">Menunggu</label>
                    </div>
                    <div>
                        <input type="radio" name="status_konfirmasi" id="statusDikonfirmasi" value="Dikonfirmasi" class="mr-2">
                        <label for="statusDikonfirmasi">Dikonfirmasi</label>
                    </div>
                    <div>
                        <input type="radio" name="status_konfirmasi" id="statusDitolak" value="Ditolak" class="mr-2">
                        <label for="statusDitolak">Ditolak</label>
                    </div>
                </div>
            </div>

            <div id="ditolakOptions" class="mt-6 border-t pt-4" style="display: none;">
                <p class="text-gray-700 font-semibold mb-3">Alasan Penolakan:</p>
                <div class="space-y-3">
                    <div>
                        <input type="radio" name="alasan_ditolak_radio" id="alasanSemuaRuanganPenuh" value="all_rooms_full" class="mr-2">
                        <label for="alasanSemuaRuanganPenuh">Ruangan terpakai (jadwalkan ulang otomatis)?</label>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" id="cancelModalBtn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Batal</button>
                <button type="submit" id="submitModalBtn" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    // Fungsi untuk menerapkan filter
    function applyFilter(selectedStatus) {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            if (selectedStatus === 'all' || rowStatus === selectedStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Atur event listener untuk tombol filter
    document.querySelectorAll('.btn-filter').forEach(button => {
        button.addEventListener('click', () => {
            const selectedStatus = button.getAttribute('data-status').toLowerCase();
            applyFilter(selectedStatus);
        });
    });

    // Terapkan filter default "Menunggu" saat halaman dimuat
    const defaultFilterStatus = 'menunggu';
    applyFilter(defaultFilterStatus);

    // --- Sisa Modal script Anda tetap sama ---
    const modal = document.getElementById('statusUpdateModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const updateStatusForm = document.getElementById('updateStatusForm');
    const modalJadwalId = document.getElementById('modalJadwalId');
    const modalPengajuanId = document.getElementById('modalPengajuanId'); // Ambil elemen ini
    const modalRejectionReason = document.getElementById('modalRejectionReason');
    
    const statusMenungguRadio = document.getElementById('statusMenunggu');
    const statusDikonfirmasiRadio = document.getElementById('statusDikonfirmasi');
    const statusDitolakRadio = document.getElementById('statusDitolak');
    
    const ditolakOptionsDiv = document.getElementById('ditolakOptions');
    const alasanSemuaRuanganPenuhRadio = document.getElementById('alasanSemuaRuanganPenuh');

    // let currentTipeUjian = ''; // Anda bisa uncomment jika masih diperlukan
    // let currentTanggal = '';   // Anda bisa uncomment jika masih diperlukan
    // let currentSlotWaktu = ''; // Anda bisa uncomment jika masih diperlukan

    document.querySelectorAll('.btn-update-status').forEach(button => {
        button.addEventListener('click', () => {
            modalJadwalId.value = button.dataset.id;
            modalPengajuanId.value = button.dataset.pengajuanId || ''; // Isi pengajuanId
            // currentTipeUjian = button.dataset.tipeUjian; 
            // currentTanggal = button.dataset.tanggal;     
            // currentSlotWaktu = button.dataset.slotWaktu; 

            const currentStatus = button.dataset.currentStatus;
            if (currentStatus === 'menunggu') statusMenungguRadio.checked = true;
            else if (currentStatus === 'dikonfirmasi') statusDikonfirmasiRadio.checked = true;
            else if (currentStatus === 'ditolak') statusDitolakRadio.checked = true;
            else { 
                statusMenungguRadio.checked = false;
                statusDikonfirmasiRadio.checked = false;
                statusDitolakRadio.checked = false;
            }
            
            // Reset Ditolak options
            ditolakOptionsDiv.style.display = 'none';
            alasanSemuaRuanganPenuhRadio.checked = false; 
            modalRejectionReason.value = '';

            // Show Ditolak options if 'Ditolak' is pre-selected or selected
            if (statusDitolakRadio.checked) {
                ditolakOptionsDiv.style.display = 'block';
            }

            modal.style.display = 'flex';
        });
    });

    closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
    cancelModalBtn.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    [statusMenungguRadio, statusDikonfirmasiRadio, statusDitolakRadio].forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.id === 'statusDitolak' && this.checked) {
                ditolakOptionsDiv.style.display = 'block';
            } else {
                ditolakOptionsDiv.style.display = 'none';
                alasanSemuaRuanganPenuhRadio.checked = false; 
                modalRejectionReason.value = '';
            }
        });
    });

    updateStatusForm.addEventListener('submit', function(event) {
        const selectedStatus = document.querySelector('input[name="status_konfirmasi"]:checked');
        if (!selectedStatus) {
            event.preventDefault();
            alert('Silakan pilih status baru.');
            return;
        }

        if (selectedStatus.value === 'Ditolak') {
            const selectedAlasan = document.querySelector('input[name="alasan_ditolak_radio"]:checked');
            if (!selectedAlasan) {
                event.preventDefault();
                alert('Jika status "Ditolak", silakan pilih alasan penolakan.');
                return;
            }
            modalRejectionReason.value = selectedAlasan.value;
        } else {
            modalRejectionReason.value = ''; 
        }
    });
});
</script>