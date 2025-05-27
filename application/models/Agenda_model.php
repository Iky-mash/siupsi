<?php
class Agenda_model extends CI_Model {

    // ... (existing functions: get_all_agenda, get_all_agenda_raw, insert_agenda, etc.) ...

    // Fungsi asli Anda, mungkin perlu diubah namanya agar lebih jelas jika ada fungsi lain
    public function get_all_agenda($id_dosen) {
        // Fungsi ini menggabungkan slot_waktu per tanggal, jadi mungkin tidak ideal untuk $data['agenda'] di index jika Anda ingin memprosesnya secara individual di PHP awal
        // Sebaiknya kita buat fungsi baru get_all_agenda_raw() untuk mengambil semua baris apa adanya.
        $this->db->select('id_agenda, id_dosen, tanggal, GROUP_CONCAT(DISTINCT slot_waktu ORDER BY slot_waktu SEPARATOR ",") as slot_waktu', FALSE);
        $this->db->where('id_dosen', $id_dosen);
        $this->db->group_by(['id_dosen', 'tanggal']); // Group by tanggal dan dosen
        $this->db->order_by('tanggal', 'ASC');
        return $this->db->get('agenda_dosen')->result_array();
    }
    
    // Fungsi baru untuk mengambil data mentah tanpa GROUP_CONCAT, berguna untuk list asli di view index
    public function get_all_agenda_raw() {
        $this->db->order_by('tanggal', 'ASC'); // Urutkan jika perlu
        return $this->db->get('agenda_dosen')->result_array();
    }

    public function insert_agenda($data) {
        // Cek duplikasi berdasarkan id_dosen dan tanggal sebelum insert
        $this->db->where('id_dosen', $data['id_dosen']);
        $this->db->where('tanggal', $data['tanggal']);
        $query = $this->db->get('agenda_dosen');

        if ($query->num_rows() > 0) {
            // Jika sudah ada, lakukan update
            $existing_row = $query->row_array();
            // Pastikan data hanya berisi slot_waktu untuk update yang ini
            return $this->update_agenda($existing_row['id_agenda'], ['slot_waktu' => $data['slot_waktu']]);
        } else {
            // Jika belum ada, lakukan insert
            $this->db->insert("agenda_dosen", $data);
            return $this->db->affected_rows() > 0;
        }
    }

    public function delete_agenda($id_agenda) {
        return $this->db->delete('agenda_dosen', ['id_agenda' => $id_agenda]);
    }

    public function get_by_id($id, $id_dosen) {
        return $this->db->get_where('agenda_dosen', ['id_agenda' => $id, 'id_dosen' => $id_dosen])->row_array();
    }

    public function get_by_date($tanggal, $id_dosen) {
        $this->db->select('id_agenda, id_dosen, tanggal, GROUP_CONCAT(DISTINCT slot_waktu ORDER BY slot_waktu SEPARATOR ",") as slot_waktu', FALSE); // Ditambahkan id_agenda
        $this->db->where(['tanggal' => $tanggal, 'id_dosen' => $id_dosen]);
        $this->db->group_by(['id_dosen', 'tanggal', 'id_agenda']); // Ditambahkan id_agenda ke group by
        $query = $this->db->get('agenda_dosen');
        return $query->row_array();
    }

    public function update_agenda($id_agenda, $data) {
        $this->db->where('id_agenda', $id_agenda);
        return $this->db->update('agenda_dosen', $data);
    }
    
    public function update_agenda_by_date_dosen($tanggal, $id_dosen, $data) {
        $this->db->where('tanggal', $tanggal);
        $this->db->where('id_dosen', $id_dosen);
        return $this->db->update('agenda_dosen', $data);
    }

    public function get_agenda_ids_by_date_dosen($tanggal, $id_dosen) {
        $this->db->select('id_agenda');
        $this->db->where('tanggal', $tanggal);
        $this->db->where('id_dosen', $id_dosen);
        return $this->db->get('agenda_dosen')->result_array();
    }
    
    public function get_entry_by_date_dosen($tanggal, $id_dosen) {
        $this->db->where('tanggal', $tanggal);
        $this->db->where('id_dosen', $id_dosen);
        return $this->db->get('agenda_dosen')->row_array();
    }

    public function get_slots_by_agenda($id_agenda) {
        $this->db->select('slot_waktu');
        $this->db->where('id_agenda', $id_agenda);
        $query = $this->db->get('agenda_dosen');
        $row = $query->row_array();
        if ($row && !empty($row['slot_waktu'])) {
            return explode(',', $row['slot_waktu']);
        }
        return [];
    }

    public function get_slots_by_date_and_dosen_string($tanggal, $id_dosen) {
        $this->db->select('GROUP_CONCAT(DISTINCT slot_waktu ORDER BY slot_waktu SEPARATOR ",") as slot_waktu', FALSE);
        $this->db->where('tanggal', $tanggal);
        $this->db->where('id_dosen', $id_dosen);
        $this->db->group_by(['tanggal', 'id_dosen']);
        $query = $this->db->get('agenda_dosen');
        return $query->row_array();
    }

    /**
     * Get confirmed exam schedules for a lecturer.
     *
     * @param int $id_dosen The ID of the lecturer.
     * @return array Array of exam schedules.
     */
    public function get_confirmed_exams_by_dosen($id_dosen) {
        $this->db->select('ju.id, ju.mahasiswa_id, ju.judul_skripsi, ju.tipe_ujian, ju.tanggal, ju.slot_waktu, ju.ruangan_id, r.nama_ruangan'); // Assuming 'ruangan' table has 'nama_ruangan' and 'id'
        $this->db->from('jadwal_ujian ju');
        $this->db->join('ruangan r', 'r.id = ju.ruangan_id', 'left'); // LEFT JOIN to get room name. Adjust 'r.id' if primary key of ruangan is different.
        $this->db->where('ju.status_konfirmasi', 'Dikonfirmasi');
        $this->db->group_start(); // Start grouping for OR conditions
        $this->db->where('ju.pembimbing_id', $id_dosen);
        $this->db->or_where('ju.penguji1_id', $id_dosen);
        $this->db->or_where('ju.penguji2_id', $id_dosen);
        $this->db->group_end(); // End grouping
        $this->db->order_by('ju.tanggal', 'ASC');
        $this->db->order_by('ju.slot_waktu', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_exam_slots_for_date_dosen($tanggal, $id_dosen) {
    $this->db->select('slot_waktu'); // Kolom slot_waktu dari tabel jadwal_ujian
    $this->db->from('jadwal_ujian');
    $this->db->where('tanggal', $tanggal);
    $this->db->where('status_konfirmasi', 'Dikonfirmasi');
    $this->db->group_start();
    $this->db->where('pembimbing_id', $id_dosen);
    $this->db->or_where('penguji1_id', $id_dosen);
    $this->db->or_where('penguji2_id', $id_dosen);
    $this->db->group_end();
    $query = $this->db->get();
    $exam_slot_entries = $query->result_array();

    $occupied_individual_slots = [];

    // Definisikan semua kemungkinan slot agenda per jam (sesuaikan dengan sistem Anda)
    // Ini sebaiknya konsisten dengan daftar slot yang ditampilkan di form edit.
    $possible_agenda_slots = [];
    for ($h = 7; $h <= 17; $h++) { // Contoh: 07:00 hingga 17:00
        $possible_agenda_slots[] = sprintf('%02d:00', $h);
    }
    // Jika Anda menggunakan slot 30 menit, logikanya perlu disesuaikan.
    // Contoh untuk slot 30 menit:
    // for ($h = 7; $h <= 17; $h++) {
    //     $possible_agenda_slots[] = sprintf('%02d:00', $h);
    //     if ($h < 17) { // Hindari 17:30 jika batas atas adalah 17:00 utk slot mulai
    //          $possible_agenda_slots[] = sprintf('%02d:30', $h);
    //     }
    // }


    foreach ($exam_slot_entries as $exam_entry) {
        $exam_slot_str = $exam_entry['slot_waktu']; // Misal: "08:00-10:00" atau "14:00"

        if (strpos($exam_slot_str, '-') !== false) {
            // Jika formatnya rentang, misal "08:00-10:00"
            list($start_time_str, $end_time_str) = explode('-', $exam_slot_str);
            $start_hour = (int)substr(trim($start_time_str), 0, 2);
            $end_hour = (int)substr(trim($end_time_str), 0, 2); // Ujian berakhir di jam ini (misal 10:00 berarti slot 08:xx dan 09:xx terpakai)

            foreach ($possible_agenda_slots as $agenda_slot) {
                $agenda_slot_hour = (int)substr($agenda_slot, 0, 2);
                // Jika slot agenda (misal 08:00) berada dalam rentang ujian (misal 08:00-10:00),
                // maka slot 08:00 dan 09:00 dianggap terisi.
                if ($agenda_slot_hour >= $start_hour && $agenda_slot_hour < $end_hour) {
                    $occupied_individual_slots[] = $agenda_slot;
                }
            }
        } else {
            // Jika formatnya slot tunggal, misal "14:00" (diasumsikan durasi 1 jam)
            $trimmed_slot = trim($exam_slot_str);
            if (in_array($trimmed_slot, $possible_agenda_slots)) {
                 $occupied_individual_slots[] = $trimmed_slot;
            }
        }
    }
    return array_unique($occupied_individual_slots);
}
}
?>