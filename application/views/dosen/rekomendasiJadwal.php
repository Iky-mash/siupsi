<h3><?= $title ?></h3>
<p>Silakan pilih salah satu jadwal untuk ujian skripsi mahasiswa <strong><?= $mahasiswa['nama'] ?></strong>.</p>

<form action="<?= base_url('dosen/pilihJadwal') ?>" method="post">
    <input type="hidden" name="pengajuan_id" value="<?= $mahasiswa['mahasiswa_id'] ?>">
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Pilih</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rekomendasi_jadwal as $jadwal): ?>
                <tr>
                    <td><?= $jadwal['tanggal'] ?></td>
                    <td><?= $jadwal['waktu_mulai'] ?></td>
                    <td><?= $jadwal['waktu_selesai'] ?></td>
                    <td>
                        <input type="radio" name="tanggal" value="<?= $jadwal['tanggal'] ?>" required>
                        <input type="hidden" name="waktu_mulai" value="<?= $jadwal['waktu_mulai'] ?>">
                        <input type="hidden" name="waktu_selesai" value="<?= $jadwal['waktu_selesai'] ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
</form>
