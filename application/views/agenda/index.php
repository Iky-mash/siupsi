<?php
// Ambil data agenda berdasarkan ID dosen yang sedang login
$id_dosen = $_SESSION['id_dosen']; // Pastikan sesi sudah dimulai sebelumnya
$agenda = array_filter($agenda, function ($a) use ($id_dosen) {
    return $a['id_dosen'] == $id_dosen;
});

// Urutkan agenda berdasarkan tanggal
usort($agenda, function ($a, $b) {
    return strtotime($a['tanggal']) - strtotime($b['tanggal']);
});

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

if ($month < 1 || $month > 12) $month = date('n');
if ($year < 1) $year = date('Y');

if (isset($_GET['next'])) {
    $month++;
    if ($month > 12) {
        $month = 1;
        $year++;
    }
} elseif (isset($_GET['prev'])) {
    $month--;
    if ($month < 1) {
        $month = 12;
        $year--;
    }
}

$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$first_day_of_month = date('w', strtotime("$year-$month-01"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Agenda Dosen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
</head>
<body>
<div class="flex items-center justify-between mb-4">
    <h6 class="text-lg font-semibold text-gray-700">Kalender Ketersediaan Dosen</h6>
    <!-- <a href="<?= base_url('agenda/tambah'); ?>" class="px-4 py-2 text-sm font-semibold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition duration-200">
        Tambah Ketersediaan Saya
    </a> -->
</div>

<div id="calendar"></div>

<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        dayClick: function(date, jsEvent, view) {
    var day = date.day(); // 0 = Minggu, 6 = Sabtu
    if (day !== 0 && day !== 6) { 
        window.location.href = "<?= site_url('agenda/edit_by_date/') ?>" + date.format('YYYY-MM-DD');
    }
}
,
        events: [
            <?php foreach ($agenda as $event): ?>
            {
                title: "<?= str_replace(',', '\n', $event['slot_waktu']) ?>",
                start: "<?= $event['tanggal'] ?>",
                allDay: true,
                url: "<?= site_url('agenda/edit/'.$event['id_agenda']) ?>"
            },
            <?php endforeach; ?>
        ]
    });
});
</script>

</body>
</html>
