<?php
class Agenda_model extends CI_Model {
    public function get_all_agenda($id_dosen) {
        $this->db->where('id_dosen', $id_dosen); // Hanya ambil agenda sesuai dosen
        return $this->db->get('agenda_dosen')->result_array();
    }

    public function insert_agenda($data) {
        $this->db->insert("agenda_dosen", $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            log_message('error', 'Gagal menyimpan agenda: ' . $this->db->last_query());
            return false;
        }
    }
    

    public function delete_agenda($id_agenda) {
        return $this->db->delete('agenda_dosen', ['id_agenda' => $id_agenda]);
    }
    public function simpan_slot($data) {
        return $this->db->insert_batch('agenda', $data);
    }
    public function get_by_id($id, $id_dosen) {
        return $this->db->get_where('agenda_dosen', ['id_agenda' => $id, 'id_dosen' => $id_dosen])->row_array();
    }

    public function get_by_date($tanggal, $id_dosen) {
        $this->db->select('GROUP_CONCAT(slot_waktu SEPARATOR ",") as slot_waktu');
        $this->db->where(['tanggal' => $tanggal, 'id_dosen' => $id_dosen]);
        $query = $this->db->get('agenda_dosen');
        return $query->row_array();
    }

    public function update_agenda($id_agenda, $data)
    {
        $this->db->where('id_agenda', $id_agenda);
        return $this->db->update('agenda_dosen', $data);
    }
    
    
    public function get_slots_by_agenda($id_agenda) {
        $this->db->select('slot_waktu');
        $this->db->where('id_agenda', $id_agenda);
        $query = $this->db->get('agenda_dosen');
        
        $result = $query->result_array();
        
        // Ubah menjadi array daftar slot_waktu
        $slots = array_map(function($row) {
            return $row['slot_waktu'];
        }, $result);
    
        return $slots;
    }
    public function get_slots_by_date_and_dosen($tanggal, $id_dosen) {
        $this->db->select('slot_waktu');
        $this->db->where('tanggal', $tanggal);
        $this->db->where('id_dosen', $id_dosen);
        $query = $this->db->get('agenda_dosen');
    
        $result = $query->result_array(); // Ambil semua hasil query
    
        // Ubah hasil menjadi array daftar slot waktu
        $slots = array_column($result, 'slot_waktu');
    
        return $slots; // Mengembalikan ["10:30-12:10", "13:00-14:40", "14:45-16:25"]
    }
        
    
}
?>
