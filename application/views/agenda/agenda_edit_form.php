<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Agenda</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Agenda</h2>

    <form method="POST" action="<?= site_url('agenda/store_by_date/'.($agenda['id_agenda'] ?? '')) ?>">
        <input type="hidden" name="id_agenda" value="<?= htmlspecialchars($agenda['id_agenda'] ?? '') ?>">

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal:</label>
            <input type="date" name="tanggal" class="form-control" 
                   value="<?= htmlspecialchars($agenda['tanggal'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Slot Waktu:</label><br>

            <?php
            // Slot waktu yang tersedia
            $slots = ["08:45-10:25", "10:30-12:10", "13:00-14:40", "14:45-16:25"];

            // Pastikan slot_waktu tidak NULL sebelum diolah
            $agenda['slot_waktu'] = $agenda['slot_waktu'] ?? '';

            // Pastikan slot waktu dari database dalam format array
            $selected_slots = explode(',', $agenda['slot_waktu']);

            foreach ($slots as $slot) {
                $checked = in_array($slot, $selected_slots) ? "checked" : ""; 
                echo "<div class='form-check'>
                        <input class='form-check-input' type='checkbox' name='slot_waktu[]' value='$slot' $checked>
                        <label class='form-check-label'>$slot</label>
                      </div>";
            }
            ?>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="<?= site_url('agenda') ?>" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
