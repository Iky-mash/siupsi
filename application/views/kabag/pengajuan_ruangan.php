

<div class="container mx-auto mt-10 px-4">
    <h2 class="text-3xl font-semibold text-center mb-6">Jadwal Ujian Skripsi</h2>

    <!-- Filter Buttons -->
    <div class="mb-6 text-center">
        <button class="btn-filter bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" data-status="All">All</button>
        <button class="btn-filter bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50" data-status="Menunggu">Menunggu</button>
        <button class="btn-filter bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50" data-status="Dikonfirmasi">Dikonfirmasi</button>
        <button class="btn-filter bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50" data-status="Ditolak">Ditolak</button>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Mahasiswa</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Judul Skripsi</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Tipe Ujian</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Tanggal</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Slot Waktu</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Ruangan</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Status Konfirmasi</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white" id="jadwal-table-body">
                <?php foreach ($jadwal as $item): ?>
                    <tr class="status-row <?= strtolower($item->status_konfirmasi) ?>">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $item->mahasiswa_nama ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?= $item->judul_skripsi ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?= $item->tipe_ujian ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?= $item->tanggal ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?= $item->slot_waktu ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?= $item->nama_ruangan ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800" id="status-<?= $item->id ?>"><?= $item->status_konfirmasi ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800">
                            <!-- Dropdown untuk mengubah status -->
                            <select class="form-select block w-full text-sm p-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="status_select_<?= $item->id ?>" onchange="updateStatus(<?= $item->id ?>)">
                                <option value="Menunggu" <?= $item->status_konfirmasi == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="Dikonfirmasi" <?= $item->status_konfirmasi == 'Dikonfirmasi' ? 'selected' : '' ?>>Dikonfirmasi</option>
                                <option value="Ditolak" <?= $item->status_konfirmasi == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                            <button class="mt-2 bg-green-500 text-white px-4 py-2 rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50" onclick="updateRoom(<?= $item->id ?>)">Update Ruangan</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Fungsi untuk memperbarui status konfirmasi
    function updateStatus(id) {
        var status = $('#status_select_' + id).val();
        var statusElement = $('#status-' + id);
        
        $.ajax({
            url: '<?= site_url('penjadwalan/updateStatus') ?>',
            type: 'POST',
            data: { id: id, status: status },
            success: function(response) {
                statusElement.text(status); // Mengupdate status di tabel
                alert('Status berhasil diperbarui!');
            },
            error: function() {
                alert('Terjadi kesalahan dalam memperbarui status.');
            }
        });
    }

    // Fungsi untuk memperbarui ruangan
    function updateRoom(id) {
        var room = prompt("Masukkan nama ruangan baru:");
        if (room) {
            $.ajax({
                url: '<?= site_url('penjadwalan/updateRoom') ?>',
                type: 'POST',
                data: { id: id, room: room },
                success: function(response) {
                    alert('Ruangan berhasil diperbarui!');
                    // Mengupdate tampilan ruangan di tabel
                    $('#ruangan-' + id).text(room);
                },
                error: function() {
                    alert('Terjadi kesalahan dalam memperbarui ruangan.');
                }
            });
        }
    }

    // Filter data berdasarkan status yang dipilih
    $(document).on('click', '.btn-filter', function() {
        var status = $(this).data('status');
        
        // Menampilkan semua data jika 'All' dipilih
        if (status === 'All') {
            $('.status-row').show();
        } else {
            // Menyembunyikan data yang tidak sesuai dengan status
            $('.status-row').each(function() {
                var rowStatus = $(this).find('td#status-' + $(this).data('id')).text().toLowerCase();
                if (rowStatus !== status.toLowerCase()) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        }
    });

