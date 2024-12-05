<!-- cards -->
<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
  <!-- row 1 -->
  <div class="flex items-center justify-center flex-grow">
  <h3>Jadwal Ujian Dosen</h3>

<?php if (empty($jadwal)): ?>
    <p>Tidak ada jadwal ujian.</p>
<?php else: ?>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Tugas Akhir</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jadwal as $index => $item): ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= $item['thesis_title']; ?></td>
                    <td><?= $item['tanggal']; ?></td>
                    <td><?= $item['waktu_mulai']; ?> - <?= $item['waktu_selesai']; ?></td>
                    <td><?= $item['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

  </div>
</div>
