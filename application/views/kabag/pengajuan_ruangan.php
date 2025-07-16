<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

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
                    <th class="px-4 py-3 text-center">Tingkat Urgensi</th>
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
                                      $bgStatus = 'bg-white'; // Nilai default
    switch (strtolower($item->status_konfirmasi)) {
        case 'menunggu':
            $bgStatus = 'bg-yellow-50';
            break;
        case 'dikonfirmasi':
            $bgStatus = 'bg-green-50';
            break;
        case 'ditolak':
            $bgStatus = 'bg-red-50';
            break;
    }

    // 2. Menentukan $textStatus
    $textStatus = 'text-gray-700'; // Nilai default
    switch (strtolower($item->status_konfirmasi)) {
        case 'menunggu':
            $textStatus = 'text-yellow-700';
            break;
        case 'dikonfirmasi':
            $textStatus = 'text-green-700';
            break;
        case 'ditolak':
            $textStatus = 'text-red-700';
            break;
    }

    // 3. Menentukan $icon
    $icon = 'ℹ️'; // Nilai default
    switch (strtolower($item->status_konfirmasi)) {
        case 'menunggu':
            $icon = '⏳';
            break;
        case 'dikonfirmasi':
            $icon = '✅';
            break;
        case 'ditolak':
            $icon = '❌';
            break;
    }
                    ?>
                    <tr class="<?= $bgStatus ?> border-b border-gray-200 hover:bg-gray-50" data-status="<?= strtolower($item->status_konfirmasi) ?>">
                        <td class="px-4 py-3 text-center font-semibold"><?= htmlspecialchars($item->mahasiswa_nama) ?></td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->judul_skripsi) ?></td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->tipe_ujian) ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php
                            // Logika PHP untuk badge urgensi
                            $urgency_class = 'bg-gray-100 text-gray-800';
                            $urgency_text = 'Normal';
                            $urgency_tooltip = 'Prioritas reguler';
                            $pulse_html = '';
                            if (isset($item->nilai_ms)) {
                                if ($item->nilai_ms >= 5) {
                                    $urgency_class = 'bg-red-200 text-red-800';
                                    $urgency_text = 'Kritis';
                                    $urgency_tooltip = 'Mahasiswa tingkat akhir (Masa studi ≥ 7 tahun)';
                                    $pulse_html = '<span class="flex absolute h-2 w-2 top-0 right-0 -mt-1 -mr-1"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span></span>';
                                } elseif ($item->nilai_ms == 4) {
                                    $urgency_class = 'bg-yellow-200 text-yellow-800';
                                    $urgency_text = 'Sangat Tinggi';
                                    $urgency_tooltip = 'Prioritas sangat tinggi (Masa studi 6 tahun)';
                                    $pulse_html = '<span class="flex absolute h-2 w-2 top-0 right-0 -mt-1 -mr-1"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span></span>';
                                } elseif ($item->nilai_ms == 3) {
                                    $urgency_class = 'bg-blue-200 text-blue-800';
                                    $urgency_text = 'Tinggi';
                                    $urgency_tooltip = 'Prioritas tinggi (Masa studi 5 tahun)';
                                    $pulse_html = '<span class="flex absolute h-2 w-2 top-0 right-0 -mt-1 -mr-1"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span></span>';
                                } elseif ($item->tipe_ujian == 'Semhas') {
                                    $urgency_class = 'bg-green-200 text-green-800';
                                    $urgency_text = 'Tinggi';
                                    $urgency_tooltip = 'Prioritas tinggi (Ujian Seminar Hasil)';
                                }
                            }
                            ?>
                            <span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full <?= $urgency_class; ?>" title="<?= $urgency_tooltip; ?>">
                                <span class="relative inline-flex items-center">
                                    <?= $urgency_text; ?>
                                    <?= $pulse_html; ?>
                                </span>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->tanggal) ?></td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->slot_waktu) ?></td>
                        <td class="px-4 py-3 text-center"><?= htmlspecialchars($item->nama_ruangan) ?></td>
                        <td class="px-4 py-3 text-center <?= $textStatus ?>">
                            <span class="inline-flex items-center gap-1">
                                <?= $icon ?> <?= htmlspecialchars(ucfirst($item->status_konfirmasi)) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button class="btn-update-status bg-indigo-500 text-white px-4 py-2 rounded-md shadow hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-500 text-xs" 
                                    data-id="<?= $item->id ?>" 
                                    data-current-status="<?= strtolower($item->status_konfirmasi) ?>"
                                    data-tipe-ujian="<?= htmlspecialchars(strtolower($item->tipe_ujian)) ?>"
                                    data-tanggal="<?= htmlspecialchars($item->tanggal) ?>"
                                    data-slot-waktu="<?= htmlspecialchars($item->slot_waktu) ?>"
                                    data-pengajuan-id="<?= $item->pengajuan_id ?? '' ?>">
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
            <input type="hidden" name="new_ruangan_id" id="modalNewRuanganId">
            <input type="hidden" name="new_ruangan_name" id="modalNewRuanganName">
            <input type="hidden" name="new_tanggal" id="modalNewTanggal">
            <input type="hidden" name="new_slot_waktu" id="modalNewSlotWaktu">

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
                        <input type="radio" name="alasan_ditolak_radio" id="alasanRuanganTidakTersedia" value="room_unavailable" class="mr-2">
                        <label for="alasanRuanganTidakTersedia">Ruangan yang dipilih tidak tersedia</label>
                    </div>
                    <div id="recommendedRoomsContainer" class="mt-2 mb-2 pl-6" style="display: none;">
                        <p class="text-xs font-semibold text-gray-700">Pilih Rekomendasi Ruangan Lain (Jadwal Sama):</p>
                        <div id="recommendedRoomsList" class="space-y-1 text-xs text-gray-600 max-h-32 overflow-y-auto"></div>
                        <p id="noRoomsMessage" class="text-xs text-red-500" style="display: none;">Tidak ada ruangan lain yang tersedia.</p>
                        <p id="loadingRoomsMessage" class="text-xs text-blue-500" style="display: none;">Mencari ruangan...</p>
                    </div>

                    <div>
                        <input type="radio" name="alasan_ditolak_radio" id="alasanSemuaRuanganPenuh" value="all_rooms_full" class="mr-2">
                        <label for="alasanSemuaRuanganPenuh">Semua ruangan penuh (Jadwalkan ulang otomatis)</label>
                    </div>
                    <div id="rescheduleResultContainer" class="mt-2 mb-2 pl-6 text-sm" style="display: none;">
                         <p id="loadingRescheduleMessage" class="text-blue-500">Mencari jadwal alternatif...</p>
                         <p id="rescheduleResultMessage" class="text-green-700 font-semibold"></p>
                         <p id="noRescheduleMessage" class="text-red-500"></p>
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
    // --- Selektor Elemen ---
    const modal = document.getElementById('statusUpdateModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const updateStatusForm = document.getElementById('updateStatusForm');

    // Input form
    const modalJadwalId = document.getElementById('modalJadwalId');
    const modalPengajuanId = document.getElementById('modalPengajuanId');
    const modalRejectionReason = document.getElementById('modalRejectionReason');
    const modalNewRuanganId = document.getElementById('modalNewRuanganId');
    const modalNewRuanganName = document.getElementById('modalNewRuanganName');
    const modalNewTanggal = document.getElementById('modalNewTanggal');
    const modalNewSlotWaktu = document.getElementById('modalNewSlotWaktu');
    
    // Radio status
    const statusMenungguRadio = document.getElementById('statusMenunggu');
    const statusDikonfirmasiRadio = document.getElementById('statusDikonfirmasi');
    const statusDitolakRadio = document.getElementById('statusDitolak');
    
    // Elemen opsi "Ditolak"
    const ditolakOptionsDiv = document.getElementById('ditolakOptions');
    const alasanRuanganTidakTersediaRadio = document.getElementById('alasanRuanganTidakTersedia');
    const alasanSemuaRuanganPenuhRadio = document.getElementById('alasanSemuaRuanganPenuh');

    // Elemen "Ganti Ruangan"
    const recommendedRoomsContainer = document.getElementById('recommendedRoomsContainer');
    const recommendedRoomsList = document.getElementById('recommendedRoomsList');
    const noRoomsMessage = document.getElementById('noRoomsMessage');
    const loadingRoomsMessage = document.getElementById('loadingRoomsMessage');

    // Elemen "Jadwal Ulang"
    const rescheduleResultContainer = document.getElementById('rescheduleResultContainer');
    const loadingRescheduleMessage = document.getElementById('loadingRescheduleMessage');
    const rescheduleResultMessage = document.getElementById('rescheduleResultMessage');
    const noRescheduleMessage = document.getElementById('noRescheduleMessage');

    // --- Variabel State ---
    let currentTipeUjian = ''; 
    let currentTanggal = '';  
    let currentSlotWaktu = ''; 

    // --- Fungsi Inti ---

    /**
     * Memfilter tabel jadwal utama berdasarkan status yang dipilih.
     * @param {string} selectedStatus - Status untuk filter ('menunggu', 'dikonfirmasi', dll.).
     */
    function applyFilter(selectedStatus) {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const shouldShow = selectedStatus.toLowerCase() === 'all' || rowStatus === selectedStatus.toLowerCase();
            row.style.display = shouldShow ? 'table-row' : 'none';
        });
    }

    /**
     * Mereset modal ke kondisi awal sebelum dibuka.
     */
    function resetModalState() {
        updateStatusForm.reset();
        ditolakOptionsDiv.style.display = 'none';
        recommendedRoomsContainer.style.display = 'none';
        rescheduleResultContainer.style.display = 'none';
        recommendedRoomsList.innerHTML = '';
        loadingRoomsMessage.style.display = 'none';
        noRoomsMessage.style.display = 'none';
        loadingRescheduleMessage.style.display = 'none';
        rescheduleResultMessage.textContent = '';
        noRescheduleMessage.textContent = '';
    }

    /**
     * Mengambil data rekomendasi ruangan untuk slot waktu yang SAMA.
     */
    function fetchRecommendedRooms() {
        recommendedRoomsList.innerHTML = ''; 
        noRoomsMessage.style.display = 'none';
        loadingRoomsMessage.style.display = 'block';

        const params = new URLSearchParams({
            tipe_ujian: currentTipeUjian,
            tanggal: currentTanggal,
            slot_waktu: currentSlotWaktu
        });

        fetch(`<?= base_url('kabag/get_recommended_rooms') ?>?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            loadingRoomsMessage.style.display = 'none';
            if (data && data.length > 0) {
                data.forEach(room => {
                    const roomDiv = document.createElement('div');
                    roomDiv.className = 'py-1';
                    const radioId = `room_opt_${room.id}`;
                    roomDiv.innerHTML = `
                        <input type="radio" name="recommended_room_option" value="${room.id}" id="${radioId}" class="mr-1" data-room-name="${room.nama_ruangan}">
                        <label for="${radioId}" class="cursor-pointer hover:text-blue-600">${room.nama_ruangan} (Kapasitas: ${room.kapasitas})</label>
                    `;
                    recommendedRoomsList.appendChild(roomDiv);
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

    /**
     * Mengambil jadwal alternatif berikutnya (slot waktu BERBEDA).
     */
    function fetchAlternativeSchedule() {
        loadingRescheduleMessage.style.display = 'block';
        rescheduleResultMessage.textContent = '';
        noRescheduleMessage.textContent = '';
         const jadwalId = document.getElementById('modalJadwalId').value;

    // Pastikan jadwalId tidak kosong sebelum melanjutkan
    if (!jadwalId) {
        loadingRescheduleMessage.style.display = 'none';
        noRescheduleMessage.textContent = 'Error: Tidak dapat menemukan ID Jadwal.';
        console.error("Kesalahan: modalJadwalId tidak memiliki nilai.");
        return;
    }

        const params = new URLSearchParams({
            tipe_ujian: currentTipeUjian,
            tanggal_mulai: currentTanggal,
             slot_waktu_original: currentSlotWaktu,
              jadwal_id: jadwalId 
        });

        fetch(`<?= base_url('kabag/cari_jadwal_alternatif') ?>?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(result => {
            loadingRescheduleMessage.style.display = 'none';
            if (result.success) {
                rescheduleResultMessage.textContent = `Rekomendasi Jadwal Baru: ${result.data.formatted_date}, Sesi ${result.data.slot_waktu}`;
                modalNewTanggal.value = result.data.tanggal;
                modalNewSlotWaktu.value = result.data.slot_waktu;
            } else {
                noRescheduleMessage.textContent = result.message || 'Tidak ada jadwal alternatif yang tersedia.';
            }
        })
        .catch(error => {
            loadingRescheduleMessage.style.display = 'none';
            console.error('Error fetching alternative schedule:', error);
            noRescheduleMessage.textContent = 'Gagal menghubungi server.';
        });
    }

    // --- Event Handler ---

    /**
     * Menangani pemilihan rekomendasi ruangan.
     * @param {Event} event - Event 'change' dari radio button.
     */
    function handleRecommendedRoomSelection(event) {
        if (event.target.checked) {
            modalNewRuanganId.value = event.target.value;
            modalNewRuanganName.value = event.target.dataset.roomName;
            
            // Ini adalah penjadwalan ulang yang dikonfirmasi, bukan penolakan.
            statusDikonfirmasiRadio.checked = true;
            statusDitolakRadio.checked = false;
            ditolakOptionsDiv.style.display = 'none'; 
            recommendedRoomsContainer.style.display = 'none';
        }
    }

    // --- Pendaftaran Event Listener ---

    // 1. Tombol filter
    document.querySelectorAll('.btn-filter').forEach(button => {
        button.addEventListener('click', () => {
            const status = button.getAttribute('data-status');
            applyFilter(status);
        });
    });

    // 2. Tombol "Update Status" di setiap baris tabel
    document.querySelectorAll('.btn-update-status').forEach(button => {
        button.addEventListener('click', () => {
            resetModalState();

            // Isi modal dengan data dari baris yang diklik
            modalJadwalId.value = button.dataset.id;
            modalPengajuanId.value = button.dataset.pengajuanId || '';
            currentTipeUjian = button.dataset.tipeUjian;
            currentTanggal = button.dataset.tanggal;
            currentSlotWaktu = button.dataset.slotWaktu;

            // Pilih radio button sesuai status saat ini
            const currentStatus = button.dataset.currentStatus;
            document.getElementById(`status${currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1)}`).checked = true;

            modal.style.display = 'flex';
        });
    });

    // 3. Tombol tutup/batal pada modal
    closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
    cancelModalBtn.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (event) => {
        if (event.target === modal) modal.style.display = 'none';
    });

    // 4. Radio button status utama di dalam modal
    [statusMenungguRadio, statusDikonfirmasiRadio, statusDitolakRadio].forEach(radio => {
        radio.addEventListener('change', function() {
            // Tampilkan opsi "Ditolak" hanya jika "Ditolak" dipilih
            ditolakOptionsDiv.style.display = (this.id === 'statusDitolak' && this.checked) ? 'block' : 'none';
        });
    });
    
    // 5. Radio button "Alasan Ditolak"
    [alasanRuanganTidakTersediaRadio, alasanSemuaRuanganPenuhRadio].forEach(radio => {
        radio.addEventListener('change', function() {
            // Sembunyikan semua sub-kontainer dulu
            recommendedRoomsContainer.style.display = 'none';
            rescheduleResultContainer.style.display = 'none';
            
            // Bersihkan value dari opsi lain untuk mencegah data tercampur
            modalNewRuanganId.value = '';
            modalNewRuanganName.value = '';
            modalNewTanggal.value = '';
            modalNewSlotWaktu.value = '';

            if (this.checked) {
                if (this.id === 'alasanRuanganTidakTersedia') {
                    recommendedRoomsContainer.style.display = 'block';
                    fetchRecommendedRooms(); 
                } else if (this.id === 'alasanSemuaRuanganPenuh') {
                    rescheduleResultContainer.style.display = 'block';
                    fetchAlternativeSchedule();
                }
            }
        });
    });

    // 6. Pengiriman Form
    updateStatusForm.addEventListener('submit', function(event) {
        const selectedStatusRadio = document.querySelector('input[name="status_konfirmasi"]:checked');
        if (!selectedStatusRadio) {
            event.preventDefault();
            alert('Silakan pilih status baru.');
            return;
        }
        
        const selectedStatusValue = selectedStatusRadio.value;

        // Jika jadwal baru (tanggal & slot) direkomendasikan, ini adalah penjadwalan ulang otomatis.
        // Statusnya harus 'Dikonfirmasi' secara otomatis.
        if (modalNewTanggal.value && modalNewSlotWaktu.value) {
            statusDikonfirmasiRadio.checked = true;
            modalRejectionReason.value = ''; // Bukan penolakan
        } 
        // Jika statusnya 'Ditolak' tanpa penjadwalan ulang otomatis
        else if (selectedStatusValue === 'Ditolak') {
            const selectedAlasan = document.querySelector('input[name="alasan_ditolak_radio"]:checked');
            // Alasan wajib diisi jika tidak ada alternatif yang dipilih (baik ruangan maupun jadwal)
            if (!selectedAlasan && !modalNewRuanganId.value) {
                event.preventDefault();
                alert('Jika status "Ditolak", silakan pilih alasan penolakan.');
                return;
            }
            modalRejectionReason.value = selectedAlasan ? selectedAlasan.value : '';
        } 
        // Untuk status lain (Menunggu, atau Dikonfirmasi dengan ganti ruangan)
        else {
            modalRejectionReason.value = '';
        }
    });

    // --- Inisialisasi ---
    // Terapkan filter default saat halaman dimuat
    applyFilter('menunggu');
});
</script>