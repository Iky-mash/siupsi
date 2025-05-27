
<div class="container mt-4">
    <h2>Edit Agenda untuk ID: <?= htmlspecialchars($agenda['id_agenda']) ?></h2>

    <?php if($this->session->flashdata('error_form')): ?>
        <div class="alert alert-danger">
            <?= $this->session->flashdata('error_form'); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= site_url('agenda/update/'.$agenda['id_agenda']) ?>">
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal:</label>
            <input type="date" name="tanggal" class="form-control" 
                   value="<?= htmlspecialchars($agenda['tanggal']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Slot Waktu:</label><br>
            <?php
            // Slot waktu yang tersedia secara umum
            $all_possible_slots = ["08:45-10:25", "10:30-12:10", "13:00-14:40", "14:45-16:25"];

            // Slot waktu yang sudah dipilih (dari database, berupa array dari controller setelah explode)
            $selected_slots_array = $agenda['slot_waktu'] ?? []; // Controller edit() sudah explode menjadi array

            foreach ($all_possible_slots as $slot) {
                $checked = in_array($slot, $selected_slots_array) ? "checked" : ""; 
                echo "<div class='form-check'>
                        <input class='form-check-input' type='checkbox' name='slot_waktu[]' value='$slot' id='slot_edit_$slot' $checked>
                        <label class='form-check-label' for='slot_edit_$slot'>$slot</label>
                      </div>";
            }
            ?>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="<?= site_url('agenda') ?>" class="btn btn-secondary">Batal</a>
    </form>
</div>
