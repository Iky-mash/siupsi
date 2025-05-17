<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kabag</title>
</head>
<body>
    <h1>Selamat Datang di Dashboard Kabag</h1>
    <p>Halo, <?= $this->session->userdata('nama'); ?>!</p>
    <p>Email: <?= $this->session->userdata('email'); ?></p>
    <a href="<?= site_url('auth/logout'); ?>">Logout</a>
</body>
</html>
