<h2>Edit Agenda</h2>
<form action="<?= base_url('agenda/update/' . $agenda->id_agenda) ?>" method="post">
    <label for="tanggal">Tanggal</label>
    <input type="date" name="tanggal" value="<?= $agenda->tanggal ?>" required><br>
    <label for="waktu_mulai">Waktu Mulai</label>
    <input type="time" name="waktu_mulai" value="<?= $agenda->waktu_mulai ?>" required><br>
    <label for="waktu_selesai">Waktu Selesai</label>
    <input type="time" name="waktu_selesai" value="<?= $agenda->waktu_selesai ?>" required><br>
    <label for="keterangan">Keterangan</label>
    <input type="text" name="keterangan" value="<?= $agenda->keterangan ?>"><br>
    <button type="submit">Simpan</button>
</form>
