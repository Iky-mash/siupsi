<?php
// Variabel $title dari controller
$page_title = isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'Kalender Agenda Dosen';
?>

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/id.js"></script> <style>
        /* Kustomisasi FullCalendar agar lebih menyatu dengan Tailwind */
        .fc button { /* Tombol Navigasi FC */
            background-color: transparent !important;
            border: 1px solid #D1D5DB !important; /* slate-300 */
            color: #374151 !important; /* slate-700 */
            text-transform: capitalize !important;
            box-shadow: none !important;
            padding: 0.375rem 0.75rem !important; /* py-1.5 px-3 */
            font-size: 0.875rem !important; /* text-sm */
            font-weight: 500 !important; /* medium */
            border-radius: 0.375rem !important; /* rounded-md */
            transition: all 0.15s ease-in-out;
        }
        .fc button:hover {
            background-color: #F3F4F6 !important; /* slate-100 */
            border-color: #9CA3AF !important; /* slate-400 */
        }
        .fc .fc-state-active { /* Tombol Aktif (misal: Bulan, Minggu) */
            background-color: #4F46E5 !important; /* indigo-600 */
            border-color: #4F46E5 !important;
            color: white !important;
        }
        .fc .fc-button-primary:disabled {
             background-color: #E5E7EB !important; /* slate-200 */
             border-color: #D1D5DB !important;
             color: #9CA3AF !important; /* slate-400 */
        }
        .fc-toolbar h2 { /* Judul Kalender (Bulan Tahun) */
            font-size: 1.25rem !important; /* text-xl */
            font-weight: 600 !important; /* semibold */
            color: #1F2937 !important; /* slate-800 */
        }
        .fc-day-header { /* Header Hari (Sen, Sel, ...) */
             background-color: #F9FAFB !important; /* slate-50 */
             padding: 0.5rem 0 !important;
             font-weight: 500 !important;
             color: #4B5563 !important; /* slate-600 */
             font-size: 0.875rem !important;
             border-bottom: 1px solid #E5E7EB; /* slate-200 */
             text-transform: uppercase;
        }
        .fc-event { /* Event di Kalender */
            border-radius: 0.375rem !important; /* rounded-md */
            padding: 0.2rem 0.4rem !important; /* sedikit padding */
            font-size: 0.8rem !important; /* text-xs agar lebih muat */
            border-width: 1px !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .fc-event-title { /* Bagian judul utama event */
            white-space: pre-line !important; /* Agar \n berfungsi */
            font-weight: 600 !important; /* semibold */
        }
        .fc-description { /* Class kustom untuk detail di bawah judul */
            display: block !important;
            font-size: 0.9em !important; /* relatif terhadap .fc-event */
            font-style: normal !important;
            font-weight: 400 !important; /* normal */
            margin-top: 2px !important;
            opacity: 0.85;
        }
        .fc-today { /* Sel Hari Ini */
            background-color: #E0E7FF !important; /* indigo-100 */
        }
        .fc-time-grid .fc-slats td { /* Garis waktu di agendaWeek/Day */
            border-color: #F3F4F6; /* slate-100 */
        }
        .fc-unthemed td, .fc-unthemed th {
            border-color: #E5E7EB; /* slate-200 */
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 leading-normal tracking-tight bg-gray-50">

<div class="min-h-screen flex flex-col">
    <main class="flex-grow">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3 sm:mb-0">
                    <?= $page_title; ?>
                </h1>
                 <a href="<?= site_url('agenda/import_excel_form'); ?>"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-black bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    Impor Ketersediaan (Excel)
                </a>
            </div>

            <?php if($this->session->flashdata('message')): ?>
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-md shadow-sm" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-700"><?= $this->session->flashdata('message'); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($this->session->flashdata('error')): ?>
                 <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-md shadow-sm" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-700"><?= $this->session->flashdata('error'); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-white p-4 sm:p-6 rounded-xl shadow-2xl">
                <div id="calendar" class="text-sm"></div>
            </div>
        </div>
    </main>

   
</div>

<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        locale: 'id',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listWeek'
        },
        buttonText: {
            today:    'Hari Ini',
            month:    'Bulan',
            week:     'Minggu',
            day:      'Hari',
            list:     'Agenda'
        },
        navLinks: true,
        editable: false,
        eventLimit: true, 
        eventLimitText: "agenda lainnya",
        dayClick: function(date, jsEvent, view) {
            window.location.href = "<?= site_url('agenda/edit_by_date/') ?>" + date.format('YYYY-MM-DD');
        },
        events: [
            
            <?php foreach ($agenda as $event): ?>
            {
                title: "Kegiatan saya", // Dibuat lebih singkat
                description: <?php
                                if (!empty($event['slot_waktu'])) {
                                    $slots = array_map('trim', explode(',', $event['slot_waktu'])); // 1. Pisahkan dengan koma, lalu trim setiap slot
                                    echo json_encode(implode("\n", $slots)); // 2. Gabungkan kembali dengan \n
                                } else {
                                    echo json_encode("Belum ada slot");
                                }
                             ?>,
                start: "<?= htmlspecialchars($event['tanggal'], ENT_QUOTES, 'UTF-8') ?>",
                allDay: true,
                url: "<?= site_url('agenda/edit_by_date/'.htmlspecialchars($event['tanggal'], ENT_QUOTES, 'UTF-8')) ?>",
                color: '#10B981', // Tailwind green-500
                borderColor: '#059669', // Tailwind green-600 (lebih gelap untuk border)
                textColor: 'white'
            },
            <?php endforeach; ?>


            <?php if (isset($jadwal_ujian) && !empty($jadwal_ujian)): ?>
                <?php foreach ($jadwal_ujian as $ujian): ?>
                {
                    title: "Ujian <?= htmlspecialchars(ucfirst($ujian['tipe_ujian']), ENT_QUOTES, 'UTF-8') ?>", // Judul lebih spesifik
                    description: <?= json_encode(
                        // "Tipe: " . htmlspecialchars(ucfirst($ujian['tipe_ujian']), ENT_QUOTES, 'UTF-8') . // Sudah di title
                        "" . htmlspecialchars($ujian['slot_waktu'], ENT_QUOTES, 'UTF-8') .
            
                        "\n" . (isset($ujian['nama_ruangan']) && !empty($ujian['nama_ruangan']) ? htmlspecialchars($ujian['nama_ruangan'], ENT_QUOTES, 'UTF-8') : ($ujian['ruangan_id'] ? 'Ruang ID: '.htmlspecialchars($ujian['ruangan_id'], ENT_QUOTES, 'UTF-8') : 'N/A'))
                    ) ?>,
                    start: "<?= htmlspecialchars($ujian['tanggal'], ENT_QUOTES, 'UTF-8') ?>",
                    allDay: true,
                    color: '#3B82F6', // Tailwind blue-500
                    borderColor: '#2563EB', // Tailwind blue-600
                    textColor: 'white',
                    // url: "<?= site_url('jadwal_ujian/detail/'.$ujian['id']) ?>" // Opsional
                },
                <?php endforeach; ?>
            <?php endif; ?>
        ],
       eventRender: function(event, element) {
            var titleHtml = '<span class="fc-event-title">' + event.title + '</span>';
            var descriptionHtml = '';

            if (event.description) {
                // event.description sekarang adalah string seperti "Slot1\nSlot2\nSlot3" dari PHP
                var formattedDescription = event.description.replace(/\\n/g, "<br/>"); // Ubah \n menjadi <br/>
                descriptionHtml = '<span class="fc-description">' + formattedDescription + '</span>';
            }
            
            var contentEl = element.find('.fc-content');
            if (contentEl.length === 0) { // Fallback jika struktur FullCalendar sedikit berbeda
                contentEl = element.children().first(); // Coba ambil anak pertama
                if (contentEl.length === 0) { // Jika masih tidak ada, gunakan elemen event itu sendiri
                    contentEl = element;
                }
            }
            contentEl.html(titleHtml + descriptionHtml); // Set HTML ke .fc-content atau fallback

            // Tooltip standar browser
            if (event.description) {
                var tooltipText = event.title + '\n---\n' + event.description.replace(/\\n/g, '\n');
                element.attr('title', tooltipText);
            }
        },
        // aspectRatio: 1.8, // Sesuaikan jika perlu
        // height: 'auto', // Atau tinggi tetap
        views: {
            month: { eventLimit: 3 }, // Batasi jumlah event per hari di tampilan bulan
            agendaWeek: { columnHeaderFormat: 'ddd, D/M' },
            agendaDay: { columnHeaderFormat: 'dddd, D MMMM' }
        }
    });
});
</script>

</body>
</html>