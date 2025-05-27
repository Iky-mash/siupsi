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
        
        // Ambil data mahasiswa beserta nama pembimbingnya
        $mahasiswa_data = $this->Mahasiswa_model->get_mahasiswa_with_pembimbing();
        
        // Jika query hanya mengembalikan satu mahasiswa (berdasarkan NIM di session),
        // dan Anda ingin mengaksesnya langsung tanpa foreach di view (meski foreach tetap aman)
        // Anda bisa kirim objek tunggal jika yakin hanya ada satu.
        // Namun, karena model mengembalikan result() yang merupakan array,
        // maka $data['mahasiswa'] akan menjadi array.
        // Jika hanya ada satu mahasiswa, array tersebut akan berisi satu elemen.
        $data['mahasiswa_list'] = $mahasiswa_data; // Ganti nama variabel agar lebih jelas itu list

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_mahasiswa', $data); // Sesuaikan dengan nama sidebar Anda
        $this->load->view('templates/navbar', $data);
        $this->load->view('mahasiswa/index', $data); // View yang akan kita modifikasi
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

// application/controllers/Mahasiswa.php (atau controller yang relevan)

public function jadwal_ujian() {
    $data['title'] = 'Dashboard Mahasiswa';
    $mahasiswa_id = $this->session->userdata('id'); 
    
    $data['status_sempro'] = $this->Pengajuan_model->get_latest_submission_by_type($mahasiswa_id, 'Sempro');
    $data['status_semhas'] = $this->Pengajuan_model->get_latest_submission_by_type($mahasiswa_id, 'Semhas');
    
    $data['jadwal'] = $this->Penjadwalan_model->get_jadwal_by_mahasiswa($mahasiswa_id); // Model sudah filter 'Disetujui'

    $data['has_approved_sempro_schedule'] = false;
    $data['has_approved_semhas_schedule'] = false;

    if (!empty($data['jadwal'])) {
        foreach ($data['jadwal'] as $j_item) {
            // Normalisasi tipe ujian dari jadwal untuk perbandingan yang konsisten
            // Jika di DB tersimpan 'sempro', 'Sempro', atau 'SEMPRO', ini akan menjadi 'Sempro'
            $schedule_tipe_ujian_normalized = ucfirst(strtolower($j_item->tipe_ujian)); 

            if ($schedule_tipe_ujian_normalized == 'Sempro') {
                $data['has_approved_sempro_schedule'] = true;
            } elseif ($schedule_tipe_ujian_normalized == 'Semhas') {
                $data['has_approved_semhas_schedule'] = true;
            }
        }
    }
    
    // Untuk Debugging (hapus atau komentari setelah selesai)
    // var_dump('Status Sempro Pengajuan:', $data['status_sempro'] ? $data['status_sempro']->status : 'Tidak ada');
    // var_dump('Has Approved Sempro Schedule:', $data['has_approved_sempro_schedule']);
    // var_dump('Status Semhas Pengajuan:', $data['status_semhas'] ? $data['status_semhas']->status : 'Tidak ada');
    // var_dump('Has Approved Semhas Schedule:', $data['has_approved_semhas_schedule']);
    // die;


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