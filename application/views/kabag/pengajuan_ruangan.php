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
            <input type="hidden" name="pengajuan_id" id="modalPengajuanId"> <input type="hidden" name="rejection_reason" id="modalRejectionReason">
            <input type="hidden" name="new_ruangan_id" id="modalNewRuanganId">
            <input type="hidden" name="new_ruangan_name" id="modalNewRuanganName"> <div class="mb-4">
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
                        <input type="radio" name="alasan_ditolak_radio" id="alasanRuanganTidakTersedia" value="room_unavailable" class="mr-2">
                        <label for="alasanRuanganTidakTersedia">Ruangan yang dipilih tidak tersedia</label>
                    </div>
                    <div id="recommendedRoomsContainer" class="mt-2 mb-2 pl-6" style="display: none;">
                        <p class="text-xs font-semibold text-gray-700">Pilih Rekomendasi Ruangan Lain (Tipe & Jadwal Sama):</p>
                        <div id="recommendedRoomsList" class="space-y-1 text-xs text-gray-600 max-h-32 overflow-y-auto">
                            </div>
                        <p id="noRoomsMessage" class="text-xs text-red-500" style="display: none;">Tidak ada ruangan lain yang tersedia pada jadwal ini.</p>
                        <p id="loadingRoomsMessage" class="text-xs text-blue-500" style="display: none;">Mencari ruangan...</p>
                    </div>
                    <div>
                        <input type="radio" name="alasan_ditolak_radio" id="alasanSemuaRuanganPenuh" value="all_rooms_full" class="mr-2">
                        <label for="alasanSemuaRuanganPenuh">Semua ruangan penuh (jadwalkan ulang otomatis)?</label>
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
    // ... (filter functions remain the same) ...
    function applyFilter(selectedStatus) {
        console.log(`DEBUG applyFilter: Memulai filter untuk status = "${selectedStatus}"`);
        const rows = document.querySelectorAll('tbody tr');
        console.log(`DEBUG applyFilter: Ditemukan ${rows.length} baris (<tr>) di dalam <tbody>.`);

        if (rows.length === 0 && document.querySelector('table tbody')) {
            console.warn("DEBUG applyFilter: Tidak ada elemen <tr> ditemukan di dalam <tbody>. Apakah tabel sudah terisi data?");
        }

        let visibleRowCount = 0;
        let processedRowCount = 0;

        rows.forEach((row, index) => {
            processedRowCount++;
            const rowStatus = row.getAttribute('data-status');
            // Untuk log yang lebih detail pada setiap baris (aktifkan jika perlu, bisa sangat banyak):
            // console.log(`DEBUG applyFilter: Baris ke-${index}, data-status="${rowStatus}", status filter="${selectedStatus}"`);

            if (!rowStatus) {
                console.warn(`DEBUG applyFilter: Baris ke-${index} TIDAK MEMILIKI atribut data-status.`);
            }

            // Pastikan perbandingan dilakukan dengan cara yang konsisten (misal, lowercase)
            const conditionMet = selectedStatus.toLowerCase() === 'all' || (rowStatus && rowStatus.toLowerCase() === selectedStatus.toLowerCase());

            if (conditionMet) {
                row.style.display = 'table-row'; // Lebih eksplisit untuk tabel
                // console.log(`DEBUG applyFilter: Baris ke-${index} (status: "${rowStatus}") ditampilkan.`);
                visibleRowCount++;
            } else {
                row.style.display = 'none';
                // console.log(`DEBUG applyFilter: Baris ke-${index} (status: "${rowStatus}") disembunyikan.`);
            }
        });
        console.log(`DEBUG applyFilter: Selesai filter untuk status "${selectedStatus}". Jumlah baris diproses: ${processedRowCount}. Jumlah baris ditampilkan: ${visibleRowCount}.`);
        console.log("-------------------------------------------");
    }

    // Atur event listener untuk tombol filter
    const filterButtons = document.querySelectorAll('.btn-filter');
    console.log(`DEBUG Setup: Ditemukan ${filterButtons.length} tombol dengan class '.btn-filter'.`);
    if (filterButtons.length === 0) {
        console.warn("DEBUG Setup: TIDAK ADA tombol dengan class '.btn-filter' ditemukan. Periksa nama class pada tombol filter Anda di HTML.");
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const statusFromButton = button.getAttribute('data-status');
            if (!statusFromButton) {
                console.error("DEBUG Klik Filter: Tombol filter yang diklik tidak memiliki atribut 'data-status'. Tombol:", button);
                return;
            }
            const selectedStatus = statusFromButton.toLowerCase(); // Ambil dan ubah ke lowercase
            console.log(`DEBUG Klik Filter: Tombol filter diklik, status yang dipilih: "${selectedStatus}"`);
            applyFilter(selectedStatus);
        });
    });

    // Terapkan filter default "Menunggu" saat halaman dimuat
    console.log("DEBUG Setup: Menerapkan filter default 'menunggu' saat halaman dimuat.");
    applyFilter('menunggu');


    const modal = document.getElementById('statusUpdateModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const updateStatusForm = document.getElementById('updateStatusForm');
    const modalJadwalId = document.getElementById('modalJadwalId');
    const modalPengajuanId = document.getElementById('modalPengajuanId');
    const modalRejectionReason = document.getElementById('modalRejectionReason');
    const modalNewRuanganId = document.getElementById('modalNewRuanganId');
    const modalNewRuanganName = document.getElementById('modalNewRuanganName');
    
    const statusMenungguRadio = document.getElementById('statusMenunggu');
    const statusDikonfirmasiRadio = document.getElementById('statusDikonfirmasi');
    const statusDitolakRadio = document.getElementById('statusDitolak');
    
    const ditolakOptionsDiv = document.getElementById('ditolakOptions');
    const alasanRuanganTidakTersediaRadio = document.getElementById('alasanRuanganTidakTersedia');
    const alasanSemuaRuanganPenuhRadio = document.getElementById('alasanSemuaRuanganPenuh');

    const recommendedRoomsContainer = document.getElementById('recommendedRoomsContainer');
    const recommendedRoomsList = document.getElementById('recommendedRoomsList');
    const noRoomsMessage = document.getElementById('noRoomsMessage');
    const loadingRoomsMessage = document.getElementById('loadingRoomsMessage');

    let currentTipeUjian = ''; 
    let currentTanggal = '';  
    let currentSlotWaktu = ''; 

    function resetModalState() {
        // Reset status radios based on actual current status (done in btn-update-status click)
        // Reset Ditolak options
        ditolakOptionsDiv.style.display = 'none';
        alasanRuanganTidakTersediaRadio.checked = false;
        alasanSemuaRuanganPenuhRadio.checked = false; 
        modalRejectionReason.value = '';

        // Reset recommendation section
        recommendedRoomsContainer.style.display = 'none';
        recommendedRoomsList.innerHTML = '';
        noRoomsMessage.style.display = 'none';
        loadingRoomsMessage.style.display = 'none';
        modalNewRuanganId.value = '';
        modalNewRuanganName.value = '';
        
        // Clear any dynamically added radio buttons for recommended rooms
        const oldRecommendedRadios = document.querySelectorAll('input[name="recommended_room_option"]');
        oldRecommendedRadios.forEach(radio => radio.removeEventListener('change', handleRecommendedRoomSelection)); // Clean up listeners if any were attached directly like this before
        recommendedRoomsList.innerHTML = ''; // Clear list content
    }

    document.querySelectorAll('.btn-update-status').forEach(button => {
        button.addEventListener('click', () => {
            resetModalState(); // Reset state first

            modalJadwalId.value = button.dataset.id;
            modalPengajuanId.value = button.dataset.pengajuanId || '';
            currentTipeUjian = button.dataset.tipeUjian; 
            currentTanggal = button.dataset.tanggal;     
            currentSlotWaktu = button.dataset.slotWaktu; 

            const currentStatus = button.dataset.currentStatus;
            if (currentStatus === 'menunggu') statusMenungguRadio.checked = true;
            else if (currentStatus === 'dikonfirmasi') statusDikonfirmasiRadio.checked = true;
            else if (currentStatus === 'ditolak') statusDitolakRadio.checked = true;
            else { 
                statusMenungguRadio.checked = false;
                statusDikonfirmasiRadio.checked = false;
                statusDitolakRadio.checked = false;
            }
            
            // Trigger change on status radios to ensure dependent UI updates correctly
            // This is important if statusDitolakRadio is checked, to show ditolakOptionsDiv
            if (statusDitolakRadio.checked) {
                 ditolakOptionsDiv.style.display = 'block';
            } else {
                 ditolakOptionsDiv.style.display = 'none';
            }
            // If currentStatus is Ditolak, you might want to pre-select the reason if it's stored
            // and potentially show recommendations if the reason was 'room_unavailable'.
            // For now, we keep it simple: options are shown, user re-selects reason.

            modal.style.display = 'flex';
        });
    });

    closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
    cancelModalBtn.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (event) => {
        if (event.target === modal) modal.style.display = 'none';
    });

    // Main status radio change listeners
    [statusMenungguRadio, statusDikonfirmasiRadio, statusDitolakRadio].forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.id === 'statusDitolak' && this.checked) {
                ditolakOptionsDiv.style.display = 'block';
                // If user explicitly selects "Ditolak", clear any new room choice
                modalNewRuanganId.value = '';
                modalNewRuanganName.value = '';
                // Uncheck recommended room radios if any were dynamically added & checked
                const recommendedRadios = recommendedRoomsList.querySelectorAll('input[name="recommended_room_option"]');
                recommendedRadios.forEach(rr => rr.checked = false);
            } else {
                ditolakOptionsDiv.style.display = 'none';
                alasanRuanganTidakTersediaRadio.checked = false;
                alasanSemuaRuanganPenuhRadio.checked = false;
                modalRejectionReason.value = '';
                recommendedRoomsContainer.style.display = 'none';
            }
        });
    });
    
    // "Alasan Ditolak" radio change listeners
    [alasanRuanganTidakTersediaRadio, alasanSemuaRuanganPenuhRadio].forEach(radio => {
        radio.addEventListener('change', function() {
            recommendedRoomsContainer.style.display = 'none'; // Hide by default
            if (this.id === 'alasanRuanganTidakTersedia' && this.checked) {
                recommendedRoomsContainer.style.display = 'block';
                fetchRecommendedRooms();
            } else { // For 'alasanSemuaRuanganPenuh' or if 'alasanRuanganTidakTersedia' is unchecked
                 // Clear new room selection if the reason changes from "room_unavailable"
                modalNewRuanganId.value = '';
                modalNewRuanganName.value = '';
                const recRadios = recommendedRoomsList.querySelectorAll('input[name="recommended_room_option"]');
                recRadios.forEach(rr => rr.checked = false);
            }
        });
    });

    function handleRecommendedRoomSelection(event) {
        if (event.target.checked) {
            modalNewRuanganId.value = event.target.value;
            modalNewRuanganName.value = event.target.dataset.roomName;

            // Automatically set status to "Dikonfirmasi"
            statusDikonfirmasiRadio.checked = true;
            
            // Ensure "Ditolak" is unchecked and its options (including recommendations) are hidden
            statusDitolakRadio.checked = false;
            ditolakOptionsDiv.style.display = 'none'; 
            modalRejectionReason.value = ''; // Clear any rejection reason
            // Uncheck sub-reason radios
            alasanRuanganTidakTersediaRadio.checked = false;
            alasanSemuaRuanganPenuhRadio.checked = false;
            recommendedRoomsContainer.style.display = 'none'; // Hide the list after selection
        }
    }

    function fetchRecommendedRooms() {
        recommendedRoomsList.innerHTML = ''; 
        noRoomsMessage.style.display = 'none';
        loadingRoomsMessage.style.display = 'block';

        if (!currentTipeUjian || !currentTanggal || !currentSlotWaktu) {
            loadingRoomsMessage.style.display = 'none';
            recommendedRoomsList.innerHTML = '<li class="text-red-500">Error: Data jadwal tidak lengkap.</li>';
            return;
        }
        const params = new URLSearchParams({
            tipe_ujian: currentTipeUjian,
            tanggal: currentTanggal,
            slot_waktu: currentSlotWaktu
        });

        fetch(`<?= base_url('kabag/get_recommended_rooms') ?>?${params.toString()}`, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            loadingRoomsMessage.style.display = 'none';
            if (data && data.length > 0) {
                data.forEach(room => {
                    const roomDiv = document.createElement('div');
                    roomDiv.className = 'py-1';
                    const radioId = `room_opt_${room.id}`;
                    roomDiv.innerHTML = `
                        <input type="radio" name="recommended_room_option" value="${room.id}" id="${radioId}" class="mr-1" data-room-name="${room.nama_ruangan}">
                        <label for="${radioId}" class="cursor-pointer hover:text-blue-600">${room.nama_ruangan} (Kapasitas: ${room.kapasitas}, Tipe: ${room.tipe_seminar})</label>
                    `;
                    recommendedRoomsList.appendChild(roomDiv);
                    // Attach event listener to the newly created radio button
                    document.getElementById(radioId).addEventListener('change', handleRecommendedRoomSelection);
                });
            } else {
                noRoomsMessage.style.display = 'block';
            }
        })
        .catch(error => {
            loadingRoomsMessage.style.display = 'none';
            console.error('Error fetching recommended rooms:', error);
            recommendedRoomsList.innerHTML = '<li class="text-red-500">Gagal memuat rekomendasi.</li>';
        });
    }

    updateStatusForm.addEventListener('submit', function(event) {
        const selectedStatusRadio = document.querySelector('input[name="status_konfirmasi"]:checked');
        if (!selectedStatusRadio) {
            event.preventDefault();
            alert('Silakan pilih status baru.');
            return;
        }

        const selectedStatusValue = selectedStatusRadio.value;

        // If a new room has been selected, the status should be 'Dikonfirmasi'
        if (modalNewRuanganId.value && selectedStatusValue !== 'Dikonfirmasi') {
            // This case should ideally be prevented by the JS that auto-selects 'Dikonfirmasi'
            // but as a fallback:
            alert('Jika memilih ruangan rekomendasi, status akan otomatis Dikonfirmasi.');
            statusDikonfirmasiRadio.checked = true; // Force it
             // Ensure Ditolak options are hidden if this rare case happens
            ditolakOptionsDiv.style.display = 'none';
            modalRejectionReason.value = '';
        }


        if (selectedStatusValue === 'Ditolak') {
            // Only require rejection reason if no new room is chosen (new_ruangan_id is empty)
            if (!modalNewRuanganId.value) {
                const selectedAlasan = document.querySelector('input[name="alasan_ditolak_radio"]:checked');
                if (!selectedAlasan) {
                    event.preventDefault();
                    alert('Jika status "Ditolak" dan tidak memilih ruangan rekomendasi, silakan pilih alasan penolakan.');
                    return;
                }
                modalRejectionReason.value = selectedAlasan.value;
            } else {
                // If new room is chosen, Ditolak status is overridden, so clear rejection reason
                modalRejectionReason.value = '';
            }
        } else { // If status is not Ditolak (e.g. Menunggu or Dikonfirmasi, possibly with new room)
            modalRejectionReason.value = ''; 
        }
    });
});
</script>