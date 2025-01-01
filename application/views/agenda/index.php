<?php
// Urutkan berdasarkan waktu_mulai
// Urutkan berdasarkan waktu_mulai
usort($agenda, function ($a, $b) {
    return strtotime($a->waktu_mulai) - strtotime($b->waktu_mulai);
});

// Ambil bulan dan tahun saat ini
// Ambil nilai bulan dan tahun dari parameter GET atau gunakan nilai default
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Validasi nilai bulan dan tahun
if ($month < 1 || $month > 12) {
    $month = date('n'); // Atur ke bulan saat ini jika tidak valid
}

if ($year < 1) {
    $year = date('Y'); // Atur ke tahun saat ini jika tidak valid
}

// Perbarui bulan dan tahun untuk navigasi
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

// Cek apakah bulan dan tahun valid
if (!checkdate($month, 1, $year)) {
    // Tangani error jika tidak valid
    throw new ValueError("Invalid date");
}

// Tentukan jumlah hari dalam bulan
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Tentukan hari pertama dalam bulan (0 = Minggu, 6 = Sabtu)
$first_day_of_month = date('w', strtotime("$year-$month-01"));
 // 0 (Minggu) hingga 6 (Sabtu)
?>

<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
    <div class="flex-none w-full max-w-full px-3">
        <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                <h6 class="text-2xl font-semibold text-blue-900">
                    Kalender Agenda Dosen - <?= date('F Y', strtotime("$year-$month-01")) ?>
                </h6>
                <a href="<?= base_url('agenda/add') ?>" class="inline-block bg-blue-500 text-white px-4 py-2 mt-4 rounded hover:bg-blue-600 transition">Tambah Agenda</a>
                <div class="mt-4">
                <a href="?month=<?= $month == 1 ? 12 : $month - 1 ?>&year=<?= $month == 1 ? $year - 1 : $year ?>" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Bulan Sebelumnya</a>
                <a href="?month=<?= $month == 12 ? 1 : $month + 1 ?>&year=<?= $month == 12 ? $year + 1 : $year ?>" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Bulan Berikutnya</a>
                </div>
            </div>
            <div class="flex-auto px-6 py-4">
                <!-- Header Nama Hari -->
                <div class="grid grid-cols-7 gap-4 text-center font-bold text-blue-900">
                    <div>Minggu</div>
                    <div>Senin</div>
                    <div>Selasa</div>
                    <div>Rabu</div>
                    <div>Kamis</div>
                    <div>Jumat</div>
                    <div>Sabtu</div>
                </div>

                <!-- Placeholder Grid Kalender -->
                <div class="grid grid-cols-7 gap-4 mt-4">
                    <!-- Placeholder untuk hari kosong di awal bulan -->
                    <?php for ($i = 0; $i < $first_day_of_month; $i++): ?>
                        <div class="p-4"></div>
                    <?php endfor; ?>

                    <!-- Loop untuk setiap hari di bulan -->
                    <?php for ($i = 1; $i <= $days_in_month; $i++): ?>
                        <?php
                        $current_date = "$year-$month-$i"; // Format YYYY-MM-DD
                        $day_of_week = date('w', strtotime($current_date)); // Hari dalam seminggu (0 = Minggu, 6 = Sabtu)
                        ?>
                        <div class="p-4 border rounded-lg shadow-md">
                            <h6 class="text-lg font-bold text-blue-900"><?= $i ?></h6>
                            <ul class="mt-2 space-y-2">
                                <?php foreach ($agenda as $a): ?>
                                    <?php if (date('j', strtotime($a->tanggal)) == $i && date('n', strtotime($a->tanggal)) == $month && date('Y', strtotime($a->tanggal)) == $year): ?>
                                        <li class="text-sm text-slate-700 bg-blue-100 p-2 rounded">
                                            <strong><?= $a->waktu_mulai ?> - <?= $a->waktu_selesai ?></strong><br>
                                            <?= $a->keterangan ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>
