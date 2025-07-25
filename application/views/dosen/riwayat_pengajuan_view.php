
    <style>
       
        
        .mahasiswa-section { 
            margin-bottom: 15px; /* Jarak antar mahasiswa */
            border: 1px solid #ddd; 
            border-radius: 8px; 
            background-color: #f9f9f9; 
            overflow: hidden; /* Untuk border-radius */
        }
        
        .mahasiswa-header {
            background-color: #007bff;
            color: white;
            padding: 12px 18px;
            cursor: pointer;
            margin: 0; /* Hapus margin default dari h2/p jika digunakan sbg header */
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease;
        }
        .mahasiswa-header:hover {
            background-color: #0056b3;
        }
        .mahasiswa-header h2 { /* Jika Anda ingin tetap menggunakan H2 untuk SEO */
            margin: 0;
            font-size: 1.2em; /* Sesuaikan ukuran font */
            color: white; /* Pastikan warna teks kontras */
        }
         .mahasiswa-header .student-info {
            font-weight: bold;
        }
        .mahasiswa-header .arrow {
            transition: transform 0.3s ease;
            font-size: 1.2em;
        }
        .mahasiswa-header.active .arrow {
            transform: rotate(90deg);
        }

        .timeline-container {
            display: none; /* Sembunyikan secara default */
            padding: 20px;
            background-color: #fff; /* Latar belakang untuk konten timeline */
            border-top: 1px solid #ddd; /* Garis pemisah */
        }
        
        .timeline { list-style-type: none; position: relative; padding-left: 0; }
        .timeline:before {
            content: ''; position: absolute; left: 18px; top: 0; bottom: 0; width: 2px;
            background: #e0e0e0; z-index: 1;
        }
        .timeline-item { margin-bottom: 20px; position: relative; padding-left: 55px; padding-right: 15px; }
        .timeline-item:last-child { margin-bottom: 0; }
        
        .timeline-icon {
            position: absolute; left: 0px; top: 0; width: 38px; height: 38px;
            border-radius: 50%; color: white; display: flex; align-items: center;
            justify-content: center; font-size: 1.2em; z-index: 2; border: 2px solid white;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        .icon-pengajuan { background-color: #28a745; }
        .icon-status-ujian { background-color: #17a2b8; }
        .icon-permintaan-reschedule { background-color: #ffc107; color: #333; }
        .icon-status-reschedule { background-color: #fd7e14; }
        .icon-default { background-color: #6c757d; }

        .timeline-content { background: #f8f9fa; padding: 15px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: relative; }
        .timeline-content h5 { margin-top: 0; font-size: 1.1em; font-weight: bold; color: #333; }
        .timeline-content p { margin-bottom: 5px; font-size: 0.95em; }
        .timeline-content .time { font-size: 0.8em; color: #6c757d; display: block; margin-bottom: 8px; }
        .timeline-content .actor { font-weight: bold; color: #0056b3; }
        .timeline-content .details { margin-top: 8px; padding-top: 8px; border-top: 1px dashed #eee; font-size: 0.9em; }
        
        .badge {
            display: inline-block; padding: .3em .6em; font-size: .75em; font-weight: 700;
            line-height: 1; color: #fff; text-align: center; white-space: nowrap;
            vertical-align: baseline; border-radius: .25rem;
        }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
        .badge-warning { background-color: #ffc107; color: #212529;}
        .badge-info { background-color: #17a2b8; }
        .no-history { padding: 15px; text-align: center; color: #666; }
    </style>

    <div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
      

        <?php if (isset($message)): ?>
            <p class="no-history"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if (empty($mahasiswa_list_with_riwayat) && !isset($message)): ?>
            <p class="no-history">Belum ada data riwayat pengajuan dari mahasiswa bimbingan Anda.</p>
        <?php else: ?>
            <?php foreach ($mahasiswa_list_with_riwayat as $index => $data_mahasiswa): // Tambahkan $index untuk ID unik jika mahasiswa_id tidak ada ?>
                <div class="mahasiswa-section">
                    <div class="mahasiswa-header" onclick="toggleTimeline('timeline-<?php echo $data_mahasiswa['mahasiswa_id'] ?? $index; ?>', this)">
                        <span class="student-info">
                            <?php echo htmlspecialchars($data_mahasiswa['nama_mahasiswa']); ?>
                            (NIM: <?php echo htmlspecialchars($data_mahasiswa['nim_mahasiswa']); ?>)
                        </span>
                        <span class="arrow">&#9654;</span> </div>
                    
                    <div id="timeline-<?php echo $data_mahasiswa['mahasiswa_id'] ?? $index; ?>" class="timeline-container">
                        <?php if (empty($data_mahasiswa['riwayat'])): ?>
                            <p class="no-history">Belum ada riwayat pengajuan untuk mahasiswa ini.</p>
                        <?php else: ?>
                            <ul class="timeline">
                                <?php foreach ($data_mahasiswa['riwayat'] as $event): ?>
                                    <li class="timeline-item">
                                        <?php
                                            $icon_class = 'icon-default';
                                            $icon_char = '🔔'; // Default icon
                                            switch (strtoupper($event['type'])) {
                                                case 'PENGAJUAN AWAL':
                                                    $icon_class = 'icon-pengajuan'; $icon_char = '📝'; break;
                                                case 'STATUS PENGAJUAN UJIAN':
                                                    $icon_class = 'icon-status-ujian';
                                                    if (isset($event['title']) && str_contains(strtolower($event['title']), 'dikonfirmasi')) $icon_char = '✔️';
                                                    else if (isset($event['title']) && str_contains(strtolower($event['title']), 'ditolak')) $icon_char = '❌';
                                                    else $icon_char = 'ℹ️';
                                                    break;
                                                case 'PERMINTAAN RESCHEDULE':
                                                    $icon_class = 'icon-permintaan-reschedule'; $icon_char = '🔄'; break;
                                                case 'STATUS RESCHEDULE':
                                                    $icon_class = 'icon-status-reschedule';
                                                     if (isset($event['title']) && str_contains(strtolower($event['title']), 'disetujui')) $icon_char = '👍';
                                                     else if (isset($event['title']) && str_contains(strtolower($event['title']), 'ditolak')) $icon_char = '👎';
                                                     else if (isset($event['title']) && str_contains(strtolower($event['title']), 'menunggu')) $icon_char = '⏳';
                                                     else $icon_char = '⚙️';
                                                    break;
                                            }
                                        ?>
                                        <div class="timeline-icon <?php echo $icon_class; ?>">
                                            <?php echo $icon_char; ?>
                                        </div>
                                        <div class="timeline-content">
                                            <span class="time"><?php echo $event['datetime_str']; ?></span>
                                            <h5>
                                                <?php echo htmlspecialchars($event['title']); ?> 
                                                <?php if(isset($event['actor'])): ?>
                                                    - <span class="actor"><?php echo htmlspecialchars($event['actor']); ?></span>
                                                <?php endif; ?>
                                            </h5>
                                            <div class="details">
                                                <?php echo $event['details']; // Detail sudah di-escape di model jika perlu ?>
                                            </div>
                                            
                                            <?php if ($event['type'] == 'PENGAJUAN AWAL' && isset($event['status_saat_itu'])): ?>
                                                <p style="margin-top: 5px;">Status Ujian Saat Itu: 
                                                    <?php
                                                    $status_badge = 'badge-info';
                                                    if ($event['status_saat_itu'] == 'Dikonfirmasi') $status_badge = 'badge-success';
                                                    if ($event['status_saat_itu'] == 'Ditolak') $status_badge = 'badge-danger';
                                                    ?>
                                                    <span class="badge <?php echo $status_badge; ?>"><?php echo htmlspecialchars($event['status_saat_itu']); ?></span>
                                                    <?php if(!empty($event['catatan_kabag_saat_itu'])): ?>
                                                        <br><small><em>Catatan: <?php echo htmlspecialchars($event['catatan_kabag_saat_itu']); ?></em></small>
                                                    <?php endif; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
    function toggleTimeline(timelineId, headerElement) {
        var timelineDiv = document.getElementById(timelineId);
        var isActive = headerElement.classList.contains('active');

        // Optional: Tutup semua akordeon lain jika Anda ingin hanya satu yang terbuka
        var allHeaders = document.querySelectorAll('.mahasiswa-header');
        var allTimelineContainers = document.querySelectorAll('.timeline-container');
        
        allHeaders.forEach(function(h) {
            if (h !== headerElement) { // Jangan tutup header yang sedang diklik
                h.classList.remove('active');
            }
        });
        allTimelineContainers.forEach(function(tc) {
            if (tc.id !== timelineId) { // Jangan sembunyikan container yang sedang diklik
                 tc.style.display = 'none';
            }
        });
        // Selesai bagian opsional

        if (isActive) { // Jika sudah aktif (terbuka), maka tutup
            timelineDiv.style.display = "none";
            headerElement.classList.remove('active');
        } else { // Jika tidak aktif (tertutup), maka buka
            timelineDiv.style.display = "block";
            headerElement.classList.add('active');
        }
    }
    </script>
