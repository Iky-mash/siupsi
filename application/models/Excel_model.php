<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel_model extends CI_Model {

    public function insert($data) {
        // Cek apakah email sudah ada di database
        $this->db->where('email', $data['email']);
        $query = $this->db->get('mahasiswa');

        if ($query->num_rows() > 0) {
            // Jika email sudah ada, kembalikan false sebagai indikasi duplikasi
            return false;
        } else {
            // Jika email belum ada, lakukan insert
            return $this->db->insert('mahasiswa', $data);
        }
    }
}
?>
