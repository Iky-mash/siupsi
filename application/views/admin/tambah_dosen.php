<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($judul) ? $judul : 'Form Dosen'; ?></title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], select {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #45a049; }
        .error { color: red; font-size: 0.9em; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo isset($judul) ? $judul : 'Form Tambah Data Dosen'; ?></h2>

        <?php
        // Menampilkan pesan flashdata jika ada
        if ($this->session->flashdata('pesan')) {
            echo $this->session->flashdata('pesan');
        }
        ?>

        <?php echo form_open('admin/tambah_dosen'); ?>

        <div class="form-group">
            <label for="nama">Nama Lengkap:</label>
            <input type="text" name="nama" id="nama" value="<?php echo set_value('nama'); ?>" required>
            <?php echo form_error('nama', '<div class="error">', '</div>'); ?>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo set_value('email'); ?>" required>
            <?php echo form_error('email', '<div class="error">', '</div>'); ?>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <?php echo form_error('password', '<div class="error">', '</div>'); ?>
        </div>

        <div class="form-group">
            <label for="nip">NIK:</label>
            <input type="text" name="nip" id="nip" value="<?php echo set_value('nip'); ?>" required>
            <?php echo form_error('nip', '<div class="error">', '</div>'); ?>
        </div>

        <div class="form-group">
            <label for="role_id">Role ID: (Role ID dosen = 2)</label>
            <input type="number" name="role_id" id="role_id" value="<?php echo set_value('role_id', '2'); // Default value 2 sesuai data Anda ?>" required>
            <?php echo form_error('role_id', '<div class="error">', '</div>'); ?>
        </div>

        <div class="form-group">
            <input type="submit" value="Simpan Data Dosen">
        </div>

        <?php echo form_close(); ?>
    </div>
</body>
</html>