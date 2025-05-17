<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mahasiswa extends CI_Controller{

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Mahasiswa_model');
        $this->load->library('session');  // Pastikan session di-load
        $this->load->helper('url');   
        $this->load->model('Pengajuan_model'); 
        $this->load->model('Penjadwalan_model');   // Helper untuk redirect
        if_logged_in();
        check_role(['Mahasiswa']);
    }

    public function mahasiswa_database() {
        // Ambil semua data mahasiswa dari tabel mahasiswa
        $query = $this->db->get('mahasiswa');  // Mengambil semua data mahasiswa
        $data['mahasiswa'] = $query->result_array();  // Hasil query disimpan dalam array 'mahasiswa'
    
        // Muat view dan kirim data mahasiswa ke view
        $this->load->view('mahasiswa_database', $data);
    }
    
  

    public function index() {
        $data['title'] = 'Dashboard Mahasiswa';
        $this->load->model('Mahasiswa_model');

        // Ambil data mahasiswa beserta nama pembimbingnya
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa_with_pembimbing();
        


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_mahasiswa', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('mahasiswa/index', $data);
        $this->load->view('templates/footer');
    }
    

    public function profil_saya() {
        $data['title'] = 'Dashboard Mahasiswa';
        
        // Ambil data user dari tabel 'user'
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        
        // Ambil data mahasiswa berdasarkan user (email atau id)
        $data['mahasiswa'] = $this->db->get_where('mahasiswa', ['email' => $this->session->userdata('email')])->row_array();
        
        // Kirimkan data ke view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_mahasiswa', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('mahasiswa/profil_saya', $data);
        $this->load->view('templates/footer');
    }
    public function edit($id) {
        $data['title'] = 'Dashboard Mahasiswa';
        // Ambil data mahasiswa berdasarkan ID
        $data['mahasiswa'] = $this->Mahasiswa_model->getMahasiswaById($id);

        if (!$data['mahasiswa']) {
            show_404(); // Jika ID tidak ditemukan
        }

        // Validasi form
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('nim', 'NIM', 'required');
        $this->form_validation->set_rules('fakultas', 'Fakultas', 'required');
        $this->form_validation->set_rules('prodi', 'Prodi', 'required');
        $this->form_validation->set_rules('judul_skripsi', 'Judul Skripsi', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar_mahasiswa', $data);
            $this->load->view('templates/navbar', $data);
            $this->load->view('mahasiswa/edit_profil', $data);
            $this->load->view('templates/footer');
        } else {
            // Proses update data
            $this->Mahasiswa_model->updateMahasiswa($id, [
                'nama' => $this->input->post('nama'),
                'email' => $this->input->post('email'),
                'nim' => $this->input->post('nim'),
                'fakultas' => $this->input->post('fakultas'),
                'prodi' => $this->input->post('prodi'),
                'judul_skripsi' => $this->input->post('judul_skripsi'),
                'is_active' => $this->input->post('is_active') ? 1 : 0,
            ]);

            // Redirect ke halaman lain
            $this->session->set_flashdata('message', 'Data berhasil diperbarui!');
            redirect('mahasiswa');
        }
    }

    public function jadwal_ujian() {
        $data['title'] = 'Dashboard Mahasiswa';
        $mahasiswa_id = $this->session->userdata('id');
        $data['pengajuan'] = $this->Pengajuan_model->get_pengajuan_by_mahasiswa($mahasiswa_id);
        $data['jadwal'] = $this->Penjadwalan_model->get_jadwal_by_mahasiswa($mahasiswa_id);
        // $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
       
        // $data['mahasiswa'] = $this->db->get_where('mahasiswa', ['email' => $this->session->userdata('email')])->row_array();
        // $mahasiswa_id = $this->session->userdata('id'); 
        // $this->load->model('Pengajuan_model');
        // $data['pengajuan'] = $this->Pengajuan_model->get_pengajuan_by_mahasiswa($mahasiswa_id);
        // $this->load->model('JadwalUjian_model');
        // $mahasiswaId = $this->session->userdata('mahasiswa_id');
        // $data['jadwal'] = $this->JadwalUjian_model->getJadwalByMahasiswa($mahasiswaId); 
        
        // Kirimkan data ke view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_mahasiswa', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('mahasiswa/jadwal_ujian', $data);
        $this->load->view('templates/footer');
    }

   // Controller: application/controllers/Mahasiswa.php
public function cetak_pdf($id_jadwal)
{
    $this->load->model('Penjadwalan_model');
    $this->load->library('pdf');

    $data['jadwal'] = $this->Penjadwalan_model->get_jadwal_by_id($id_jadwal);
    $data['title'] = 'Jadwal Ujian Mahasiswa';

    $html = $this->load->view('mahasiswa/cetak_jadwal_pdf', $data, true);

    $this->pdf->loadHtml($html);
    $this->pdf->setPaper('A4', 'portrait');
    $this->pdf->render();
    $this->pdf->stream('jadwal_ujian_' . $id_jadwal . '.pdf', ['Attachment' => false]);
}

    

    public function progres() {
        $data['title'] = 'Dashboard Mahasiswa';
        
        // Ambil data user dari tabel 'user'
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        
        // Ambil data mahasiswa berdasarkan user (email atau id)
        $data['mahasiswa'] = $this->db->get_where('mahasiswa', ['email' => $this->session->userdata('email')])->row_array();
        
        // Kirimkan data ke view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_mahasiswa', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('mahasiswa/progres', $data);
        $this->load->view('templates/footer');
    }
    
}