<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen_model extends CI_Model {

    // Ambil semua dosen yang tersedia
    public function get_all_dosen() {
       
        return $this->db->get('dosen')->result_array(); 
    }
    public function getAllDosen() {
        $this->db->select('id, nama');
        $this->db->from('dosen');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_dosen_by_id($id)
    {
        return $this->db->get_where('dosen', ['id' => $id])->row();
    }

    public function update_dosen($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('dosen', $data);
    }

    public function delete_dosen($id)
    {
        $this->db->delete('dosen', ['id' => $id]);
    }
    public function get_mahasiswa_by_pembimbing($dosen_id) {
        $this->db->select('mahasiswa.id, mahasiswa.nama, mahasiswa.nim, mahasiswa.judul_skripsi');
        $this->db->from('mahasiswa');
        $this->db->where('mahasiswa.pembimbing_id', $dosen_id); // Filter berdasarkan pembimbing
        $query = $this->db->get();
        return $query->result();
    }
    
}
