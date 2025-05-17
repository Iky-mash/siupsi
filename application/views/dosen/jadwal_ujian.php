<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Ujian Dosen</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        .badge-menunggu {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-disetujui {
            background-color: #28a745;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">📅 Jadwal Ujian Anda</h2>

    <?php if (!empty($jadwal_ujian)) : ?>
        <div class="row">
            <?php foreach ($jadwal_ujian as $jadwal) : ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary font-weight-bold"><?= $jadwal['nama_mahasiswa']; ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= ucfirst($jadwal['tipe_ujian']); ?> - <?= date('d M Y', strtotime($jadwal['tanggal'])); ?></h6>
                            <p class="card-text text-dark mb-2"><strong>Judul Skripsi:</strong><br><?= $jadwal['judul_skripsi']; ?></p>
                            <p class="mb-1"><strong>Slot Waktu:</strong> <?= $jadwal['slot_waktu']; ?></p>
                            <p class="mb-1"><strong>Ruangan:</strong> <?= $jadwal['nama_ruangan']; ?></p>
                            <span class="badge 
                                <?= ($jadwal['status_konfirmasi'] == 'menunggu') ? 'badge-menunggu' : 'badge-disetujui'; ?>">
                                <?= ucfirst($jadwal['status_konfirmasi']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="alert alert-info">Tidak ada jadwal ujian yang tersedia.</div>
    <?php endif; ?>
</div>
</body>
</html>
