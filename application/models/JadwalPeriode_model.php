<?php
class JadwalPeriode_model extends CI_Model {
    public function get_periode() {
        $this->db->select('tanggal_mulai, tanggal_selesai');
        return $this->db->get('jadwal_periode')->result_array();
    }
    public function get_slots_by_date($date) {
        return $this->db->where('tanggal', $date)->get('jadwal_periode')->result();
    }
    
}
?>
