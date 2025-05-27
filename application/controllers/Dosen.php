<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen extends CI_Controller{


    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('session');  // Pastikan session di-load
        $this->load->helper('url'); 
        $this->load->model('Dosen_model');
        $this->load->model('JadwalUjian_model');  
        $this->load->model('Penjadwalan_model');    
        $this->load->model('Mahasiswa_model');    
        if_logged_in();
        check_role(['Dosen', 'Admin']);
         // Pastikan session id_dosen tersedia
         if (!$this->session->userdata('id_dosen')) {
            redirect('auth');
        }
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
        $data['title'] = 'Agenda Saya';
    
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
        
        $id_dosen_login = $this->session->userdata('id_dosen'); // Ambil ID dosen yang login
        if (!$id_dosen_login) {
            // Jika tidak ada session id_dosen, redirect ke login atau tampilkan error
            $this->session->set_flashdata('error', 'Sesi tidak valid atau Anda belum login.');
            redirect('auth'); // Ganti dengan halaman login Anda
            return;
        }
        
        $data['jadwal_ujian'] = $this->Penjadwalan_model->get_jadwal_by_dosen($id_dosen_login);
        $data['current_dosen_id'] = $id_dosen_login; // Kirim id dosen login ke view
    
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data); // Sesuaikan nama view sidebar Anda
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/jadwal_ujian', $data);
        $this->load->view('templates/footer');
    }

    public function proses_hasil_ujian() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $mahasiswa_id = $this->input->post('mahasiswa_id');
            $tipe_ujian = $this->input->post('tipe_ujian');
            $hasil_ujian = $this->input->post('hasil_ujian'); // 'ACC' atau 'Mengulang'
            // $jadwal_ujian_id = $this->input->post('jadwal_ujian_id'); // Jika Anda ingin menandai jadwal juga

            // Validasi dasar
            if (empty($mahasiswa_id) || empty($tipe_ujian) || empty($hasil_ujian)) {
                $this->session->set_flashdata('error', 'Data tidak lengkap.');
                redirect('dosen/jadwal_ujian');
                return;
            }

            // Pastikan dosen yang melakukan aksi adalah pembimbing yang sah (optional, tapi baik)
            // Ini memerlukan query tambahan untuk mengecek pembimbing_id dari jadwal_ujian terkait mahasiswa_id dan tipe_ujian
            // Untuk sementara, kita asumsikan tombol hanya muncul untuk pembimbing yang sah.

            $update_status = $this->Penjadwalan_model->update_status_mahasiswa($mahasiswa_id, $tipe_ujian, $hasil_ujian);

            if ($update_status) {
                // Opsional: Tandai juga di jadwal_ujian bahwa penilaian selesai
                // $this->Penjadwalan_model->update_jadwal_penilaian_selesai($jadwal_ujian_id);
                $this->session->set_flashdata('success', 'Hasil ujian mahasiswa berhasil disimpan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan hasil ujian mahasiswa.');
            }
        } else {
            $this->session->set_flashdata('error', 'Metode tidak diizinkan.');
        }
        redirect('dosen/jadwal_ujian');
    }

    public function profil($id = null)
    {
        $id = $this->session->userdata('id'); // pastikan session ini diset saat login

        if (!$id) {
            show_error('ID tidak ditemukan di session', 500);
        }
    
        $data['title'] = 'Profil Dosen';
        $data['dosen'] = $this->Dosen_model->get_dosen_by_id($id);
    
        if (!$data['dosen']) {
            show_404();
        }
    
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/profil', $data);
        $this->load->view('templates/footer');
    }
    
    public function tambah() {
        $this->load->view('tambah_dosen'); // Menampilkan form tambah dosen
    }

    public function simpan() {
        // Aturan Validasi
        // 'dosen.email' dan 'dosen.nip' mengasumsikan nama tabel Anda adalah 'dosen'
        // dan kolom email serta nip harus unik.
        $this->form_validation->set_rules('nama', 'Nama Dosen', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[dosen.email]',
            array(
                'is_unique' => '%s sudah terdaftar. Silakan gunakan email lain.'
            )
        );
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim|is_unique[dosen.nip]',
            array(
                'is_unique' => '%s sudah terdaftar. Silakan gunakan NIP lain.'
            )
        );

        // Pesan validasi kustom (dalam Bahasa Indonesia)
        $this->form_validation->set_message('required', '%s tidak boleh kosong.');
        $this->form_validation->set_message('valid_email', '%s tidak valid.');
        $this->form_validation->set_message('min_length', '%s minimal harus %s karakter.');

        if ($this->form_validation->run() == FALSE) {
            // Validasi gagal, tampilkan kembali form dengan pesan error
            // Simpan error validasi ke flashdata agar bisa ditampilkan setelah redirect
            // atau tampilkan view form langsung dari sini.
            // $this->session->set_flashdata('error_validation', validation_errors('<div class="text-red-600 mb-2">', '</div>'));
            // redirect('dosen/tambah'); // Redirect kembali ke halaman form tambah

            // Atau, jika view form Anda sederhana dan tidak butuh data tambahan:
            $this->load->view('dosen/tambah_dosen'); // Ganti dengan path view form Anda

        } else {
            // Validasi berhasil, lanjutkan penyimpanan
            $data = [
                'nama' => $this->input->post('nama', TRUE),
                'email' => $this->input->post('email', TRUE),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'nip' => $this->input->post('nip', TRUE),
                'role_id' => $this->input->post('role_id', TRUE), // Ambil dari hidden input
                
            ];

            $insert_id = $this->Dosen_model->tambah_dosen($data);

            if ($insert_id) {
                $this->session->set_flashdata('success', 'Data dosen berhasil ditambahkan dengan ID: ' . $insert_id);
            } else {
                $db_error = $this->Dosen_model->get_db_error(); // Ambil error dari model
                $error_message = 'Gagal menambahkan data dosen.';
                if ($db_error && !empty($db_error['message'])) {
                    // Tampilkan pesan error database jika ada (untuk debugging)
                    // HATI-HATI: Jangan tampilkan pesan error database mentah di lingkungan produksi
                    // $error_message .= ' Kesalahan Database: ' . $db_error['message'];
                    log_message('error', 'Database Error: '. $db_error['code'] . ' - ' . $db_error['message']); // Catat error ke log
                }
                $this->session->set_flashdata('error', $error_message);
            }
            // Sesuaikan redirect ke halaman daftar dosen atau halaman yang relevan
            redirect('admin/data_dosen');
        }
    }

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
            $this->load->view('dosen/form_tambah_dosen', $data);
        } else {
            // Jika validasi berhasil, kumpulkan data dari form
            $data_dosen = array(
                'nama' => $this->input->post('nama', TRUE),
                'email' => $this->input->post('email', TRUE),
                'password' => $this->input->post('password', TRUE), // Password akan di-hash di model
                'nip' => $this->input->post('nip', TRUE),
                'role_id' => $this->input->post('role_id', TRUE),
                // 'date_created' akan diisi otomatis oleh model jika tidak diset di sini
            );

            // Panggil model untuk menyimpan data
            if ($this->Dosen_model->insert_dosen($data_dosen)) {
                // Jika berhasil, set flashdata untuk notifikasi dan redirect
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Data dosen berhasil ditambahkan!</div>');
                redirect('dosen/tambah'); // Atau redirect ke halaman daftar dosen jika ada
            } else {
                // Jika gagal, set flashdata untuk notifikasi dan redirect
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Gagal menambahkan data dosen!</div>');
                redirect('dosen/tambah');
            }
        }
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
    public function rekomendasiJadwal() {
        $this->load->model('Penjadwalan_model');
        $data['title'] = 'Rekomendasi Jadwal';
    
        // Ambil rekomendasi jadwal dari database
        // $data['rekomendasi_jadwal'] = $this->Penjadwalan_model->getRekomendasiJadwal($mahasiswa_id);
    
        // $data['mahasiswa'] = $this->Penjadwalan_model->getMahasiswaById($mahasiswa_id);
    
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