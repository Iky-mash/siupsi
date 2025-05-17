<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pekan_model extends CI_Model {
    
    public function get_jadwal($jenis) {
        return $this->db->get_where('jadwal_periode', ['jenis' => $jenis])->row_array();
    }

    public function update_jadwal($jenis, $data) {
        $cek = $this->get_jadwal($jenis);
        if ($cek) {
            $this->db->where('jenis', $jenis);
            return $this->db->update('jadwal_periode', $data);
        } else {
            $data['jenis'] = $jenis;
            return $this->db->insert('jadwal_periode', $data);
        }
    }
}
?>
