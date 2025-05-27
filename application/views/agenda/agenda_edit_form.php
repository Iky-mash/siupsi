<?php
// Ambil data agenda dari controller atau set default jika tidak ada
// Contoh: $agenda = $this->some_model->get_agenda_by_date($tanggal_dari_url) ?? ['tanggal' => $tanggal_dari_url, 'id_agenda' => null, 'id_dosen' => $this->session->userdata('user_id'), 'slot_waktu' => ''];
// Pastikan $agenda['tanggal'] selalu ada nilainya.
$tanggal_agenda = htmlspecialchars($agenda['tanggal'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8');
$is_editing = isset($agenda['id_agenda']) && $agenda['id_agenda'];
$form_title = $is_editing ? 'Edit Kegiatan untuk Tanggal ' . $tanggal_agenda : 'Tambah Kegiatan untuk Tanggal ' . $tanggal_agenda;
?>

<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h2 class="text-2xl font-semibold text-slate-800 mb-6"><?= $form_title ?></h2>

    <?php if($this->session->flashdata('error_form')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow" role="alert">
            <p class="font-bold">Error!</p>
            <p><?= $this->session->flashdata('error_form'); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= site_url('agenda/store_by_date') ?>" class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl space-y-6">
        <input type="hidden" name="id_agenda" value="<?= htmlspecialchars($agenda['id_agenda'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id_dosen" value="<?= htmlspecialchars($agenda['id_dosen'] ?? $this->session->userdata('user_id') ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div>
            <label for="tanggal" class="block text-sm font-medium text-slate-700 mb-1">Tanggal:</label>
            <input type="date" name="tanggal" id="tanggal"
                   class="mt-1 block w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-md text-sm shadow-sm placeholder-slate-400
                          focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                          disabled:bg-slate-100 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none"
                   value="<?= $tanggal_agenda ?>" required readonly>
            <p class="mt-1 text-xs text-slate-500">Tanggal tidak dapat diubah melalui form ini.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Slot Waktu Tersedia:</label>
            <div class="space-y-2">
                <?php
                $all_possible_slots = ["08:45-10:25", "10:30-12:10", "13:00-14:40", "14:45-16:25"];
                $selected_slots_string = $agenda['slot_waktu'] ?? '';
                $selected_slots_array = !empty($selected_slots_string) ? array_map('trim', explode(',', $selected_slots_string)) : [];

                foreach ($all_possible_slots as $index => $slot) {
                    $slot_id = 'slot_' . str_replace([':', '-'], '', $slot); // Membuat ID unik untuk setiap slot
                    $checked = in_array($slot, $selected_slots_array) ? "checked" : "";
                    echo "<div class='flex items-center p-3 bg-slate-50 rounded-md border border-slate-200 hover:bg-slate-100 transition-colors duration-150'>
                            <input class='h-4 w-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 focus:ring-offset-1' type='checkbox' name='slot_waktu[]' value='" . htmlspecialchars($slot, ENT_QUOTES, 'UTF-8') . "' id='$slot_id' $checked>
                            <label class='ml-3 block text-sm font-medium text-slate-700 select-none' for='$slot_id'>" . htmlspecialchars($slot, ENT_QUOTES, 'UTF-8') . "</label>
                          </div>";
                }
                ?>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-4">
            <a href="<?= site_url('agenda') ?>"
               class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                Batal
            </a>
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                Simpan Kegiatan
            </button>
        </div>
    </form>
</div>