<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen_model extends CI_Model {
    private $table = 'dosen'; 

    // Ambil semua dosen yang tersedia
    public function get_all_dosen() {
       
        return $this->db->get('dosen')->result_array(); 
    }
public function tambah_dosen($data) {
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id(); // Mengembalikan ID dari baris yang baru saja dimasukkan
        }
        return FALSE; // Mengembalikan FALSE jika terjadi kegagalan
    }

    /**
     * Mengambil pesan error database terakhir.
     *
     * @return array Array yang berisi 'code' dan 'message' error, atau array kosong jika tidak ada error.
     */
    public function get_db_error() {
        return $this->db->error(); // Mengembalikan array [code, message]
    }
    
     // Fungsi untuk menambahkan data dosen baru
    public function insert_dosen($data) {
        // Hash password sebelum disimpan
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        // Set tanggal dibuat jika belum ada
        if (!isset($data['date_created']) || empty($data['date_created'])) {
            $data['date_created'] = date('Y-m-d H:i:s');
        }
        return $this->db->insert('dosen', $data);
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
