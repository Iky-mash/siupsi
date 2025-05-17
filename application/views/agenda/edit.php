<h2>Edit Agenda</h2>
<form method="POST" action="<?= site_url('agenda/update/'.$agenda['id_agenda']) ?>">
    <label for="tanggal">Tanggal:</label>
    <input type="date" name="tanggal" value="<?= htmlspecialchars($agenda['tanggal']) ?>" required>
    
    <label>Slot Waktu:</label><br>
    <?php
    // Slot waktu yang tersedia
    $slots = ["08:45-10:25", "10:30-12:10", "13:00-14:40", "14:45-16:25"];

    // Pastikan slot waktu dari database dalam format array
    if (!is_array($agenda['slot_waktu'])) {
        $agenda['slot_waktu'] = explode(',', $agenda['slot_waktu']); // Ubah dari string ke array
    }

    foreach ($slots as $slot) {
        $checked = in_array($slot, $agenda['slot_waktu']) ? "checked" : ""; 
        echo "<label><input type='checkbox' name='slot_waktu[]' value='$slot' $checked> $slot</label><br>";
    }
    ?>

    <button type="submit">Simpan Perubahan</button>
</form>
