<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
    }

    // Endpoint untuk menambahkan Admin
    public function add_admin()
{
    // Baca input JSON
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada data yang diterima'
        ]);
        return;
    }

    // Validasi data
    $this->form_validation->set_data($input); // Gunakan data JSON untuk validasi
    $this->form_validation->set_rules('nama', 'Nama', 'required');
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[admin.email]');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

    if ($this->form_validation->run() == FALSE) {
        echo json_encode([
            'status' => 'error',
            'message' => validation_errors()
        ]);
    } else {
        $data = [
            'nama' => $input['nama'],
            'email' => $input['email'],
            'password' => password_hash($input['password'], PASSWORD_DEFAULT),
            'role_id' => 1,
            'date_created' => time()
        ];
        $this->db->insert('admin', $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Admin berhasil ditambahkan'
        ]);
    }
}

    
    // Endpoint untuk menambahkan Dosen
    public function add_dosen()
    {
        // Baca input JSON
        $input = json_decode(file_get_contents('php://input'), true);
    
        if (empty($input)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tidak ada data yang diterima'
            ]);
            return;
        }
    
        // Validasi input
        $this->form_validation->set_data($input);
    
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[dosen.email]');
        $this->form_validation->set_rules('nip', 'NIP', 'required|is_unique[dosen.nip]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
    
        if ($this->form_validation->run() == FALSE) {
            // Kirim respons error jika validasi gagal
            echo json_encode([
                'status' => 'error',
                'message' => validation_errors()
            ]);
        } else {
            // Memastikan bahwa data 'password' dan 'nama' tidak kosong
            if (empty($input['password']) || empty($input['nama'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Nama dan Password tidak boleh kosong'
                ]);
                return;
            }
    
            // Proses password hash dan insert data ke database
            $data = [
                'nama' => $input['nama'],
                'email' => $input['email'],
                'nip' => $input['nip'],
                'password' => password_hash($input['password'], PASSWORD_DEFAULT),
                'role_id' => 2, // Role untuk Dosen
                'date_created' => time()
            ];
    
            // Simpan data ke database
            $this->db->insert('dosen', $data);
    
            echo json_encode([
                'status' => 'success',
                'message' => 'Dosen berhasil ditambahkan'
            ]);
        }
    }
    // Endpoint untuk menambahkan Mahasiswa
public function add_mahasiswa()
{
    // Baca input JSON
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada data yang diterima'
        ]);
        return;
    }

    // Validasi input
    $this->form_validation->set_data($input);
    
    $this->form_validation->set_rules('nama', 'Nama', 'required');
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[mahasiswa.email]');
    $this->form_validation->set_rules('nim', 'NIM', 'required|is_unique[mahasiswa.nim]');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

    if ($this->form_validation->run() == FALSE) {
        // Kirim respons error jika validasi gagal
        echo json_encode([
            'status' => 'error',
            'message' => validation_errors()
        ]);
    } else {
        // Memastikan bahwa data 'password' dan 'nama' tidak kosong
        if (empty($input['password']) || empty($input['nama'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Nama dan Password tidak boleh kosong'
            ]);
            return;
        }

        // Proses password hash dan insert data ke database
        $data = [
            'nama' => $input['nama'],
            'email' => $input['email'],
            'nim' => $input['nim'],
            'password' => password_hash($input['password'], PASSWORD_DEFAULT),
            'role_id' => 3, // Role untuk Mahasiswa
            'date_created' => time()
        ];

        // Simpan data ke database
        $this->db->insert('mahasiswa', $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Mahasiswa berhasil ditambahkan'
        ]);
    }
}

    
}
