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
        $this->load->model('Penjadwalan_model'); 
        $this->load->model('Riwayat_ujian_model');  // Helper untuk redirect
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

    // Load Mahasiswa_model (bisa juga di autoload atau constructor controller)
    $this->load->model('Mahasiswa_model');
    $this->load->model('Pengajuan_model'); // Pastikan sudah diload
    $this->load->model('Penjadwalan_model'); // Pastikan sudah diload

    // 1. Ambil data mahasiswa lengkap, termasuk status_sempro dan status_semhas
    $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa_by_id($mahasiswa_id);

    // 2. Ambil status pengajuan terbaru (untuk notifikasi status pengajuan)
    $data['status_sempro_pengajuan'] = $this->Pengajuan_model->get_latest_submission_by_type($mahasiswa_id, 'Sempro');
    $data['status_semhas_pengajuan'] = $this->Pengajuan_model->get_latest_submission_by_type($mahasiswa_id, 'Semhas');

    // 3. Ambil semua jadwal yang disetujui
    $all_approved_jadwal = $this->Penjadwalan_model->get_jadwal_by_mahasiswa($mahasiswa_id); // Model sudah filter 'Disetujui'/'Dikonfirmasi'

    $data['has_approved_sempro_schedule'] = false;
    $data['has_approved_semhas_schedule'] = false;
    $filtered_jadwal = [];

    // 4. Filter jadwal berdasarkan status_sempro/status_semhas dari tabel mahasiswa
    if (!empty($all_approved_jadwal) && isset($data['mahasiswa'])) {
        foreach ($all_approved_jadwal as $j_item) {
            $schedule_tipe_ujian_normalized = ucfirst(strtolower($j_item->tipe_ujian));
            $show_this_schedule = true;

            if ($schedule_tipe_ujian_normalized == 'Sempro') {
                // Jika status Sempro di tabel mahasiswa adalah ACC atau Mengulang, JANGAN tampilkan jadwal Sempro
                if ($data['mahasiswa']->status_sempro == 'ACC' || $data['mahasiswa']->status_sempro == 'Mengulang') {
                    $show_this_schedule = false;
                } else {
                    $data['has_approved_sempro_schedule'] = true; // Tetap set flag ini jika ada jadwal Sempro yang valid untuk ditampilkan
                }
            } elseif ($schedule_tipe_ujian_normalized == 'Semhas') {
                // Jika status Semhas di tabel mahasiswa adalah ACC atau Mengulang, JANGAN tampilkan jadwal Semhas
                if ($data['mahasiswa']->status_semhas == 'ACC' || $data['mahasiswa']->status_semhas == 'Mengulang') {
                    $show_this_schedule = false;
                } else {
                    $data['has_approved_semhas_schedule'] = true; // Tetap set flag ini jika ada jadwal Semhas yang valid untuk ditampilkan
                }
            }

            if ($show_this_schedule) {
                $filtered_jadwal[] = $j_item;
            }
        }
    }
    $data['jadwal'] = $filtered_jadwal; // Gunakan jadwal yang sudah difilter

    // Recalculate has_approved_xxx_schedule based on the $filtered_jadwal for consistency with the view logic
    // that hides "dikonfirmasi menunggu jadwal" if a schedule is present.
    $data['has_approved_sempro_schedule'] = false;
    $data['has_approved_semhas_schedule'] = false;
    if (!empty($data['jadwal'])) {
        foreach ($data['jadwal'] as $j_item) {
            $schedule_tipe_ujian_normalized = ucfirst(strtolower($j_item->tipe_ujian));
            if ($schedule_tipe_ujian_normalized == 'Sempro') {
                $data['has_approved_sempro_schedule'] = true;
            } elseif ($schedule_tipe_ujian_normalized == 'Semhas') {
                $data['has_approved_semhas_schedule'] = true;
            }
        }
    }
    
    // Untuk Debugging (hapus atau komentari setelah selesai)
    // var_dump('Data Mahasiswa:', $data['mahasiswa']);
    // var_dump('Status Sempro Pengajuan:', $data['status_sempro_pengajuan'] ? $data['status_sempro_pengajuan']->status : 'Tidak ada');
    // var_dump('Mahasiswa Status Sempro:', $data['mahasiswa'] ? $data['mahasiswa']->status_sempro : 'Tidak ada data mahasiswa');
    // var_dump('Has Approved Sempro Schedule (after filter):', $data['has_approved_sempro_schedule']);
    // var_dump('Status Semhas Pengajuan:', $data['status_semhas_pengajuan'] ? $data['status_semhas_pengajuan']->status : 'Tidak ada');
    // var_dump('Mahasiswa Status Semhas:', $data['mahasiswa'] ? $data['mahasiswa']->status_semhas : 'Tidak ada data mahasiswa');
    // var_dump('Has Approved Semhas Schedule (after filter):', $data['has_approved_semhas_schedule']);
    // var_dump('Filtered Jadwal:', $data['jadwal']);
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
     public function riwayat_pengajuan() {
        // Ambil ID mahasiswa dari session
        $mahasiswa_id = $this->session->userdata('id');

        // Ambil detail mahasiswa
        $data['mahasiswa_detail'] = $this->Riwayat_ujian_model->get_mahasiswa_detail($mahasiswa_id);
        
        // Ambil riwayat pengajuan ujian untuk mahasiswa ini
        $data['riwayat_pengajuan'] = $this->Riwayat_ujian_model->get_riwayat_by_mahasiswa($mahasiswa_id);

        $data['page_title'] = "Riwayat Pengajuan Ujian Saya";

        // Jika detail mahasiswa tidak ditemukan (seharusnya tidak terjadi jika session valid)
        if (!$data['mahasiswa_detail']) {
            $this->session->set_flashdata('error', 'Data mahasiswa tidak ditemukan.');
            // Bisa redirect ke dashboard mahasiswa atau halaman error
            redirect('mahasiswa/dashboard'); // Sesuaikan
            return;
        }
        
        // Load view untuk menampilkan riwayat
        // Anda mungkin punya struktur template sendiri (header, footer)
        // $this->load->view('mahasiswa/template/header_mahasiswa', $data);
       
         $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar_mahasiswa', $data);
    $this->load->view('templates/navbar', $data);
    $this->load->view('mahasiswa/riwayat_pengajuan', $data);
    $this->load->view('templates/footer');
    }
    
}