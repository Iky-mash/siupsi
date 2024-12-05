<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Pengajuan_model'); 
        $this->load->model('Mahasiswa_model');
        $this->load->model('Dosen_model');
        $this->load->library('session');  // Pastikan session di-load
        $this->load->helper('url');  
        if_logged_in();
        check_role(['Admin']); 
    }
    public function index() {
        $data['title'] = 'Dashboard Admin';
    
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }
    // Menampilkan halaman utama admin dengan daftar mahasiswa yang belum punya pembimbing
    public function data_pembimbingPenguji() {
        $data['title'] = 'Dashboard Admin';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        
        // Ambil mahasiswa yang belum memiliki dosen pembimbing
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa_without_pembimbing_and_penguji();

        
        // Ambil daftar dosen yang tersedia
        $data['dosen'] = $this->Dosen_model->get_all_dosen();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/data_pembimbingPenguji', $data);
        $this->load->view('templates/footer');
    }
    public function data_mahasiswa() {
        $this->load->model('Mahasiswa_model'); // Pastikan model dimuat
        $data['mahasiswa'] = $this->Mahasiswa_model->get_all_mahasiswa_with_details();
  
        $data['title'] = 'Dashboard Admin';
        

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/data_mahasiswa', $data); 
        $this->load->view('templates/footer');
    }
    public function data_dosen() {
        $data['dosen'] = $this->Dosen_model->get_all_dosen(); // Mengambil data dosen
  
        $data['title'] = 'Dashboard Admin';
        

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/data_dosen', $data); 
        $this->load->view('templates/footer');
    }

    // Proses pengaitan dosen pembimbing ke mahasiswa
    public function assign_pembimbing_and_penguji() {
        $mahasiswa_id = $this->input->post('mahasiswa_id');
        $dosen_pembimbing_id = $this->input->post('dosen_pembimbing_id');
        $dosen_penguji1_id = $this->input->post('dosen_penguji1_id');
        $dosen_penguji2_id = $this->input->post('dosen_penguji2_id');
        
        // Validasi input
        if ($mahasiswa_id && $dosen_pembimbing_id && $dosen_penguji1_id && $dosen_penguji2_id) {
            // Pastikan dosen penguji tidak sama dengan dosen pembimbing
            if ($dosen_pembimbing_id != $dosen_penguji1_id && $dosen_pembimbing_id != $dosen_penguji2_id) {
                // Pastikan dosen penguji 1 tidak sama dengan dosen penguji 2
                if ($dosen_penguji1_id != $dosen_penguji2_id) {
                    // Assign pembimbing dan penguji menggunakan model
                    if ($this->Mahasiswa_model->assign_pembimbing_dan_penguji($mahasiswa_id, $dosen_pembimbing_id, $dosen_penguji1_id, $dosen_penguji2_id)) {
                        $this->session->set_flashdata('success', 'Dosen pembimbing dan dosen penguji berhasil ditentukan.');
                    } else {
                        $this->session->set_flashdata('error', 'Terjadi kesalahan, coba lagi.');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Dosen Penguji 1 tidak boleh sama dengan Dosen Penguji 2.');
                }
            } else {
                $this->session->set_flashdata('error', 'Dosen penguji tidak boleh sama dengan dosen pembimbing.');
            }
        } else {
            $this->session->set_flashdata('error', 'Mohon pilih mahasiswa, dosen pembimbing, dan dosen penguji.');
        }
        redirect('admin');
    }
    
      // Menampilkan pengajuan ujian skripsi
      public function pengajuan_ujian() {
        $data['pengajuan'] = $this->Pengajuan_model->get_pengajuan_ujian();
        $data['title'] = 'Dashboard Admin';
        

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/pengajuan_ujian', $data);
        $this->load->view('templates/footer');
    }

    // Menyetuju atau Menolak Pengajuan Ujian
    public function verifikasi_pengajuan($pengajuan_id) {
        $this->load->model('JadwalUjian_model');
        $this->load->model('Pengajuan_model');
    
        // Ambil data pengajuan berdasarkan ID
        $pengajuan = $this->Pengajuan_model->get_pengajuan_by_id($pengajuan_id);
    
        // Periksa apakah pengajuan valid
        if ($pengajuan) {
            // Update status pengajuan menjadi 'Disetujui'
            $this->Pengajuan_model->update_status($pengajuan_id, 'Disetujui');
    
            // Dapatkan rekomendasi jadwal ujian
            $jadwal_rekomendasi = $this->JadwalUjian_model->generateJadwalRekomendasi($pengajuan_id);
    
            if ($jadwal_rekomendasi) {
                // Simpan jadwal rekomendasi ke database
                $this->JadwalUjian_model->simpanJadwal($jadwal_rekomendasi);
    
                // Redirect atau tampilkan hasil jadwal rekomendasi
                $this->session->set_flashdata('success', 'Pengajuan disetujui dan jadwal ujian berhasil dibuat.');
                redirect('admin/jadwal_ujian');
            } else {
                $this->session->set_flashdata('error', 'Tidak ada jadwal yang tersedia untuk rekomendasi.');
                redirect('admin/pengajuan_ujian');
            }
        } else {
            $this->session->set_flashdata('error', 'Pengajuan tidak ditemukan.');
            redirect('admin/pengajuan_ujian');
        }
    }
    

    // Menolak pengajuan ujian dengan alasan
    public function tolak_pengajuan($pengajuan_id) {
        $alasan = $this->input->post('alasan_penolakan');
        $this->Pengajuan_model->update_status_pengajuan($pengajuan_id, 'Ditolak', $alasan);
        $this->session->set_flashdata('error', 'Pengajuan ujian ditolak.');
        redirect('admin/pengajuan_ujian');
    }
      // Method untuk menampilkan halaman edit mahasiswa
      public function edit_mahasiswa($id) {
        $data['title'] = 'Dashboard Admin';
        // Ambil data mahasiswa berdasarkan ID
        $data['mahasiswa'] = $this->Mahasiswa_model->getMahasiswaBy_Id($id);
        $data['dosen_options'] = $this->Mahasiswa_model->getDosenOptions(); // Ambil semua dosen untuk dropdown

        if (!$data['mahasiswa']) {
            // Jika mahasiswa tidak ditemukan, redirect ke halaman lain
            $this->session->set_flashdata('error', 'Mahasiswa tidak ditemukan.');
            redirect('admin');
        }
        // Tampilkan halaman edit
      

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/edit_mahasiswa', $data);
        $this->load->view('templates/footer');
    }

    // Method untuk menangani update data mahasiswa
    public function update_mahasiswa($id) {
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('nim', 'NIM', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    
        // Validasi server-side untuk memastikan dosen pembimbing dan penguji berbeda
        $pembimbing = $this->input->post('pembimbing');
        $penguji1 = $this->input->post('penguji1');
        $penguji2 = $this->input->post('penguji2');
    
        if ($pembimbing == $penguji1 || $pembimbing == $penguji2 || $penguji1 == $penguji2) {
            $this->session->set_flashdata('error', 'Dosen pembimbing, penguji 1, dan penguji 2 harus berbeda.');
            redirect('admin/edit_mahasiswa/' . $id);
            return;
        }
    
        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, tampilkan form edit kembali
            $data['mahasiswa'] = $this->Mahasiswa_model->getMahasiswaById($id);
            $data['dosen_options'] = $this->Mahasiswa_model->getDosenOptions();
            $this->load->view('edit_mahasiswa', $data);
        } else {
            // Proses pembaruan data
            $updateData = array(
                'nama' => $this->input->post('nama'),
                'email' => $this->input->post('email'),
                'nim' => $this->input->post('nim'),
                'fakultas' => $this->input->post('fakultas'),
                'prodi' => $this->input->post('prodi'),
                'judul_skripsi' => $this->input->post('judul_skripsi'),
                'is_active' => $this->input->post('is_active') ? 1 : 0,
                'pembimbing_id' => $this->input->post('pembimbing'),
                'penguji1_id' => $this->input->post('penguji1'),
                'penguji2_id' => $this->input->post('penguji2')
            );
    
            // Panggil method untuk update data di model
            $updateStatus = $this->Mahasiswa_model->updateMahasiswa($id, $updateData);
    
            if ($updateStatus) {
                $this->session->set_flashdata('success', 'Data mahasiswa berhasil diperbarui.');
                redirect('admin/data_mahasiswa');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data mahasiswa.');
                redirect('edit_mahasiswa' . $id);
            }
        }
    }
    public function delete_mahasiswa($id) {
        $this->load->model('Mahasiswa_model'); // Pastikan Anda telah membuat model Mahasiswa
        if ($this->Mahasiswa_model->delete_mahasiswa($id)) {
            $this->session->set_flashdata('success', 'Data mahasiswa berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data mahasiswa.');
        }
        redirect('admin/data_mahasiswa'); // Redirect ke halaman data mahasiswa
    }
    

    //DOSEN
    public function edit_dosen($id)
    {
        $data['dosen_edit'] = $this->Dosen_model->get_dosen_by_id($id);
        $data['dosen'] = $this->Dosen_model->get_all_dosen();

     

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/edit_dosen', $data);
        $this->load->view('templates/footer');
    }

    public function update_dosen()
    {
        $id = $this->input->post('id');
        $data = [
            'nama' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'nip' => $this->input->post('nip'),
        ];

        $this->Dosen_model->update_dosen($id, $data);
        redirect('admin/data_dosen');
    }

    public function delete_dosen($id)
    {
        $this->Dosen_model->delete_dosen($id);
        redirect('admin/data_dosen');
    }
    
}