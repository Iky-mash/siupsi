<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen extends CI_Controller{


    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('session');  // Pastikan session di-load
        $this->load->helper('url'); 
        $this->load->model('Dosen_model');
        $this->load->model('JadwalUjian_model');      // Helper untuk redirect
        if_logged_in();
        check_role(['Dosen']);
    }
    public function index(){
       $data['title'] = 'Dashboard Dosen';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/index', $data);
        $this->load->view('templates/footer');
    }

    public function mahasiswa_bimbingan() {
        $data['title'] = 'Mahasiswa Bimbingan';
    
        // Ambil data user yang sedang login
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    
        // Ambil ID dosen dari tabel dosen
        $dosen = $this->db->get_where('dosen', ['email' => $this->session->userdata('email')])->row_array();
        $pembimbing_id = $dosen['id'];
    
        // Load Mahasiswa_model dan ambil data mahasiswa
        $this->load->model('Mahasiswa_model');
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa_by_pembimbing($pembimbing_id);
    
        // Tampilkan ke view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/mahasiswa_bimbingan', $data);
        $this->load->view('templates/footer');
    }

    public function agenda() {
        $data['title'] = 'Mahasiswa Bimbingan';
    
        // Ambil data user yang sedang login
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    
        // Ambil ID dosen dari tabel dosen
        $dosen = $this->db->get_where('dosen', ['email' => $this->session->userdata('email')])->row_array();
        $pembimbing_id = $dosen['id'];
    
        // Load Mahasiswa_model dan ambil data mahasiswa
        $this->load->model('Mahasiswa_model');
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa_by_pembimbing($pembimbing_id);
    
        // Tampilkan ke view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/agenda', $data);
        $this->load->view('templates/footer');
    }
    public function jadwal_ujian() {
        $data['title'] = 'Jadwal Ujian';
    
        // Ambil data user yang sedang login
    
        // Tampilkan ke view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/jadwal_ujian', $data);
        $this->load->view('templates/footer');
    }
    public function profil($id) {
        $data['title'] = 'Profil Dosen';
        $data['dosen'] = $this->Dosen_model->get_dosen_by_id($id);

        // Jika ID tidak ditemukan, tampilkan error
        if (!$data['dosen']) {
            show_404();
        }
    
        // Tampilkan ke view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/profil', $data);
        $this->load->view('templates/footer');
    }
    
    
    public function add_dosen() {
        $data = [
            'nama' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'nip' => $this->input->post('nip'),
            'role_id' => 2, // Role ID untuk Dosen
        ];
        $this->db->insert('dosen', $data);
        echo json_encode(['message' => 'Dosen berhasil ditambahkan']);
    }
    public function rekomendasiJadwal($mahasiswa_id) {
        $this->load->model('Penjadwalan_model');
        $data['title'] = 'Rekomendasi Jadwal';
    
        // Ambil rekomendasi jadwal dari database
        $data['rekomendasi_jadwal'] = $this->Penjadwalan_model->getRekomendasiJadwal($mahasiswa_id);
    
        $data['mahasiswa'] = $this->Penjadwalan_model->getMahasiswaById($mahasiswa_id);
    
        // Load view untuk halaman dosen
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/rekomendasiJadwal', $data);
        $this->load->view('templates/footer');
    }
    public function pilihJadwal() {
        $this->load->model('Penjadwalan_model');
        $pengajuan_id = $this->input->post('pengajuan_id');
        $tanggal = $this->input->post('tanggal');
        $waktu_mulai = $this->input->post('waktu_mulai');
        $waktu_selesai = $this->input->post('waktu_selesai');
    
        // Simpan jadwal terpilih ke tabel jadwal_ujian
        $this->Penjadwalan_model->simpanJadwalTerpilih($pengajuan_id, $tanggal, $waktu_mulai, $waktu_selesai);
    
        // Set flash message dan redirect
        $this->session->set_flashdata('success', 'Jadwal berhasil disimpan.');
        redirect('dosen/jadwal');
    }
        
}