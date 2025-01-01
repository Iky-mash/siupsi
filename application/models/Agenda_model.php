<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Agenda_model extends CI_Model {
    public function get_agenda_by_dosen($id_dosen) {
        $this->db->select('a.*, d.nama');
        $this->db->from('agenda_dosen a');
        $this->db->join('dosen d', 'a.id_dosen = d.id', 'inner');
        $this->db->where('a.id_dosen', $id_dosen);
    
        $query = $this->db->get();
        echo $this->db->last_query(); // Debug query
        return $query->result();
    }
    
    
    public function get_agenda_by_id($id_agenda) {
        return $this->db->get_where('agenda_dosen', ['id_agenda' => $id_agenda])->row();
    }

    public function insert_agenda($data) {
        $this->db->insert('agenda_dosen', $data);
    }

    public function update_agenda($id_agenda, $data) {
        $this->db->where('id_agenda', $id_agenda)->update('agenda_dosen', $data);
    }

    public function delete_agenda($id_agenda) {
        $this->db->where('id_agenda', $id_agenda)->delete('agenda_dosen');
    }
    public function getAgendaDosen($dosenIds, $startDate, $endDate) {
        $this->db->where_in('id_dosen', $dosenIds);
        $this->db->where('tanggal >=', $startDate);
        $this->db->where('tanggal <=', $endDate);
        $query = $this->db->get('agenda_dosen');
        return $query->result_array();
    }

    public function saveJadwalUjian($data) {
        $this->db->insert('jadwal_ujian', $data);
        return $this->db->insert_id();
    }
}
