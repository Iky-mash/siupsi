<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? htmlspecialchars(ucwords(strtolower($title))) : 'Laporan Status Seminar Mahasiswa'; ?></title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt; /* Ukuran font dasar untuk tabel */
            margin: 25px;
            line-height: 1.5;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-surat .fakultas {
            font-size: 14pt; /* Diubah */
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase; /* Tetap kapital untuk nama institusi */
        }
        .kop-surat .prodi {
            font-size: 14pt; /* Diubah */
            margin-bottom: 5px;
            text-transform: uppercase; /* Tetap kapital untuk nama institusi */
            /* font-weight: bold; */ /* Uncomment jika prodi juga ingin bold */
        }

        .report-title {
            text-align: center;
            font-size: 12pt; /* Diubah */
            font-weight: bold;
            margin-bottom: 20px;
            /* text-transform: uppercase; Dihapus agar tidak semua kapital */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            font-size: 12pt; /* Diubah */
            vertical-align: top;
        }
        th {
            background-color: #e9e9e9;
            font-weight: bold;
            text-align: center;
            /* text-transform: uppercase; Dihapus dari sini agar header tidak auto kapital */
        }
        .text-center {
            text-align: center;
        }
        .status-acc {
            color: #28a745;
        }
        .status-pending {
            color: #ffc107;
        }
        .status-rejected {
            color: #dc3545;
        }
        .no-data {
            text-align: center;
            padding: 25px;
            font-style: italic;
        }
        .footer-date {
            font-size: 9pt; /* Ukuran font untuk footer tetap kecil */
            text-align: right;
            margin-top: 30px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <div class="fakultas">FAKULTAS KOMPUTER DAN TEKNIK</div>
        <div class="prodi">PROGRAM STUDI INFORMATIKA</div>
    </div>

    <div class="report-title">
        <?php
            // Menggunakan ucwords untuk membuat setiap kata diawali huruf kapital untuk judul
            $reportTitleText = isset($title) ? htmlspecialchars($title) : 'Laporan Status Seminar Mahasiswa';
            echo ucwords(strtolower($reportTitleText));
        ?>
    </div>

    <?php if (!empty($mahasiswa_seminar)): ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Nama Mahasiswa</th>
                    <th style="width: 15%;">NIM</th>
                    <th style="width: 25%;">Status Sempro</th>
                    <th style="width: 25%;">Status Semhas</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($mahasiswa_seminar as $mhs): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($mhs->nama); ?></td> <td class="text-center"><?= htmlspecialchars($mhs->nim); ?></td>
                        <td class="text-center">
                            <?php
                                // Menggunakan ucwords(strtolower()) untuk status agar menjadi Title Case
                                $sempro_status_text = htmlspecialchars(ucwords(strtolower($mhs->status_sempro)));
                                $sempro_class = '';
                                // Logika kelas status tetap sama, hanya teks yang diubah casingnya
                                if (strpos(strtoupper($mhs->status_sempro), 'ACC') !== false || strpos(strtoupper($mhs->status_sempro), 'LULUS') !== false) {
                                    $sempro_class = 'status-acc';
                                } elseif (strpos(strtoupper($mhs->status_sempro), 'BELUM MENGAJUKAN') !== false || strpos(strtoupper($mhs->status_sempro), 'DIPROSES') !== false || strpos(strtoupper($mhs->status_sempro), 'PENDING') !== false) {
                                    $sempro_class = 'status-pending';
                                }
                            ?>
                            <span class="<?= $sempro_class; ?>"><?= $sempro_status_text; ?></span>
                        </td>
                        <td class="text-center">
                            <?php
                                $semhas_status_text = htmlspecialchars(ucwords(strtolower($mhs->status_semhas)));
                                $semhas_class = '';
                                if (strpos(strtoupper($mhs->status_semhas), 'ACC') !== false || strpos(strtoupper($mhs->status_semhas), 'LULUS') !== false) {
                                    $semhas_class = 'status-acc';
                                } elseif (strpos(strtoupper($mhs->status_semhas), 'BELUM MENGAJUKAN') !== false || strpos(strtoupper($mhs->status_semhas), 'DIPROSES') !== false || strpos(strtoupper($mhs->status_semhas), 'PENDING') !== false) {
                                    $semhas_class = 'status-pending';
                                }
                            ?>
                            <span class="<?= $semhas_class; ?>"><?= $semhas_status_text; ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">Tidak ada data mahasiswa untuk ditampilkan dalam laporan.</p>
    <?php endif; ?>

    <div class="footer-date">
        Dicetak pada: <?= date('d M Y, H:i:s'); ?> WIB
    </div>

</body>
</html>