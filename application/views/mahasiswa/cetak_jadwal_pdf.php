<body style="font-family: 'Times New Roman', Times, serif; line-height: 1.8; margin: 60px; font-size: 16px; text-align: justify;">

  <p style="text-align: right;">Yogyakarta, <?= date('d F Y') ?></p>

  <p>
    Kepada Yth:<br>
    Kepala Bagian Akademik<br>
    Universitas Alma Ata<br>
    di Tempat
  </p>

  <p><strong>Perihal: Permohonan Izin Penggunaan Ruangan Ujian</strong></p>

  <p>Dengan hormat,</p>

  <p>
    Sehubungan dengan pelaksanaan ujian mahasiswa Program Studi Informatika, Fakultas Ilmu Komputer, Universitas Alma Ata, dengan ini kami mengajukan permohonan izin penggunaan ruangan sesuai dengan jadwal berikut:
  </p>

  <div style="margin-left: 40px; font-family: 'Times New Roman', Times, serif;">
  <table style="border-collapse: collapse;">
    <tr>
      <td style="padding: 4px 10px 4px 0;">Nama</td>
      <td>: <?= $jadwal->mahasiswa_nama ?></td>
    </tr>
    <tr>
      <td style="padding: 4px 10px 4px 0;">NIM</td>
      <td>: <?= $jadwal->mahasiswa_nim ?></td>
    </tr>
    <tr>
      <td style="padding: 4px 10px 4px 0;">Waktu</td>
      <td>: <?= date('d F Y', strtotime($jadwal->tanggal)) ?>, <?= $jadwal->slot_waktu ?></td>
    </tr>
    <tr>
      <td style="padding: 4px 10px 4px 0; vertical-align: top;">Judul</td>
      <td>: <?= $jadwal->judul_skripsi ?></td>
    </tr>
    <tr>
      <td style="padding: 4px 10px 4px 0;">Ruangan</td>
      <td>: <?= $jadwal->nama_ruangan ?></td>
    </tr>
  </table>
</div>

  <p>
    Demikian permohonan ini kami sampaikan. Besar harapan kami agar permohonan ini dapat dikabulkan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
  </p>

 <!-- Bagian penutupan "Hormat Kami" di sebelah kanan dengan margin kanan -->
 <div style="display: flex; justify-content: flex-end; margin-top: 50px; margin-right: 40px;">
    <div style="text-align: right; line-height: 1.5;"> <!-- Added line-height to ensure vertical alignment -->
      <p style="margin: 0;">Hormat kami,<br>
      Dosen Pembimbing</p>
      <br><br>
      <p style="margin: 0;"><strong><u><?= $jadwal->pembimbing_nama ?></u></strong></p>
      <p style="margin: 0;">NIDN: <?= $jadwal->pembimbing_nip ?></p>
    </div>
  </div>

</body>
