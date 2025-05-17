<h2>Edit Ruangan</h2>
<form method="post" action="<?php echo site_url('ruangan/update/'.$ruangan->id); ?>">
    <label>Nama Ruangan</label>
    <input type="text" name="nama_ruangan" value="<?= $ruangan->nama_ruangan; ?>" required><br>
    <label>Kapasitas</label>
    <input type="number" name="kapasitas" value="<?= $ruangan->kapasitas; ?>" required><br>
    <label>Tipe Seminar</label>
    <select name="tipe_seminar" required>
        <option value="sempro" <?= $ruangan->tipe_seminar == 'sempro' ? 'selected' : ''; ?>>Sempro</option>
        <option value="semhas" <?= $ruangan->tipe_seminar == 'semhas' ? 'selected' : ''; ?>>Semhas</option>
    </select><br>
    <button type="submit">Update</button>
</form>
