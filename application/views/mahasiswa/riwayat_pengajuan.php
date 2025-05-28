 <style>
        /* Anda bisa menggunakan CSS yang sama atau mirip dengan halaman dosen untuk konsistensi */
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 20px; color: #333; }
        
        .student-info-header {
            padding: 15px 20px;
            background-color: #e9ecef; /* Warna latar yang lembut */
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 5px solid #007bff; /* Aksen biru di kiri */
        }
        .student-info-header h2 {
            margin: 0 0 5px 0;
            font-size: 1.6em;
            color: #007bff;
        }
        .student-info-header p {
            margin: 0 0 3px;
            font-size: 1em;
            color: #495057;
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
        .no-history { padding: 20px; text-align: center; color: #555; background-color: #f0f0f0; border-radius: 5px;}
    </style>
<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
 
        <?php if (isset($mahasiswa_detail) && $mahasiswa_detail): ?>
            <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200">
    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
        <?php echo htmlspecialchars($mahasiswa_detail['nama']); ?>
    </h2>
    <div class="space-y-3 text-gray-700">
        <p>
            <strong class="font-medium text-gray-600 w-32 inline-block">NIM</strong>
            <span class="font-medium text-gray-500 mr-2">:</span>
            <?php echo htmlspecialchars($mahasiswa_detail['nim']); ?>
        </p>

        <?php if(isset($mahasiswa_detail['prodi']) && !empty($mahasiswa_detail['prodi'])): ?>
            <p>
                <strong class="font-medium text-gray-600 w-32 inline-block">Program Studi</strong>
                <span class="font-medium text-gray-500 mr-2">:</span>
                <?php echo htmlspecialchars($mahasiswa_detail['prodi']); ?>
            </p>
        <?php endif; ?>

        <?php if(isset($mahasiswa_detail['fakultas']) && !empty($mahasiswa_detail['fakultas'])): ?>
            <p>
                <strong class="font-medium text-gray-600 w-32 inline-block">Fakultas</strong>
                <span class="font-medium text-gray-500 mr-2">:</span>
                <?php echo htmlspecialchars($mahasiswa_detail['fakultas']); ?>
            </p>
        <?php endif; ?>
        
        </div>
</div>
        <?php else: ?>
            <p class="no-history">Informasi detail mahasiswa tidak dapat dimuat.</p>
        <?php endif; ?>

        <?php if (empty($riwayat_pengajuan)): ?>
            <p class="no-history">Anda belum memiliki riwayat pengajuan ujian.</p>
        <?php else: ?>
            <ul class="timeline">
                <?php foreach ($riwayat_pengajuan as $event): ?>
                    <li class="timeline-item">
                        <?php
                            $icon_class = 'icon-default';
                            $icon_char = '🔔'; 
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
