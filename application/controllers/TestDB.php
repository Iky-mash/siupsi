<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TestDB extends CI_Controller
{
    public function index()
    {
        $this->load->database(); // Memuat library database

        if ($this->db->conn_id) {
            echo "Koneksi ke database berhasil!";
        } else {
            echo "Gagal terhubung ke database.";
        }
    }
}
