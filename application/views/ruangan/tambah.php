<h2>Tambah Ruangan</h2>
<form method="post" action="<?php echo site_url('ruangan/simpan'); ?>">
    <label>Nama Ruangan</label>
    <input type="text" name="nama_ruangan" required><br>
    <label>Kapasitas</label>
    <input type="number" name="kapasitas" required><br>
    <label>Tipe Seminar</label>
    <select name="tipe_seminar" required>
        <option value="sempro">Sempro</option>
        <option value="semhas">Semhas</option>
    </select><br>
    <button type="submit">Simpan</button>
</form>
