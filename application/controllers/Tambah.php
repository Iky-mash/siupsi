<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tambah extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Tambah_model'); // Memuat Dosen_model
        $this->load->helper(array('form', 'url')); // Memuat helper form dan URL
        $this->load->library('form_validation'); // Memuat library form validation
    }

    // Fungsi untuk menampilkan form tambah dosen
   public function tambah() {
    $data['judul'] = 'Tambah Data Dosen'; // Judul halaman
          $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/tambah_dosen', $data); 
        $this->load->view('templates/footer');
}

    // Fungsi untuk memproses penambahan data dosen
  public function proses_tambah() {
    // Aturan validasi form
    $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
    $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[dosen.email]');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
    $this->form_validation->set_rules('nip', 'NIP', 'required|trim|numeric|is_unique[dosen.nip]');
    $this->form_validation->set_rules('role_id', 'Role ID', 'required|numeric');

    // Pesan error kustom (opsional, bisa disesuaikan bahasanya)
    $this->form_validation->set_message('required', '%s tidak boleh kosong.');
    $this->form_validation->set_message('valid_email', '%s tidak valid.');
    $this->form_validation->set_message('is_unique', '%s sudah terdaftar.');
    $this->form_validation->set_message('min_length', '%s minimal %s karakter.');
    $this->form_validation->set_message('numeric', '%s harus berisi angka.');

    if ($this->form_validation->run() == FALSE) {
        // Jika validasi gagal, tampilkan kembali form dengan pesan error
        $data['judul'] = 'Tambah Data Dosen';
        // 💡 FIX: Change this line to load the correct view
        $this->load->view('admin/tambah_dosen', $data);
    } else {
        // Jika validasi berhasil, kumpulkan data dari form
        $data_dosen = array(
            'nama' => $this->input->post('nama', TRUE),
            'email' => $this->input->post('email', TRUE),
            'password' => $this->input->post('password', TRUE), // Password akan di-hash di model
            'nip' => $this->input->post('nip', TRUE),
            'role_id' => $this->input->post('role_id', TRUE),
        );

        if ($this->Tambah_model->insert_dosen($data_dosen)) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Data dosen berhasil ditambahkan!</div>');
            redirect('admin/data_dosen');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Gagal menambahkan data dosen!</div>');
            redirect('admin/data_dosen');
        }
    }
}

    // Anda bisa membuat method index untuk menampilkan daftar dosen
    public function index() {
        // Contoh: $data['dosen'] = $this->Dosen_model->get_all_dosen();
        // $this->load->view('dosen/daftar_dosen', $data);
        echo "Halaman daftar dosen (belum dibuat).";
    }
}