<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tambah_model extends CI_Model {
    private $table = 'dosen'; 

    // Ambil semua dosen yang tersedia
   
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

}
