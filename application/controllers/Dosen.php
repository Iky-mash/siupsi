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
         $this->load->model('Riwayat_ujian_model');
           $this->load->model('Pengajuan_model');   
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
        $data['title'] = 'Bimbingan';
    
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
public function riwayat_ujian() {
         $data['title'] = 'Riwayat Ujian';
        $dosen_id = $this->session->userdata('id_dosen');

        if (!$dosen_id) {
            echo "Sesi dosen tidak ditemukan. Silakan login terlebih dahulu.";
            return;
        }
        
        $data['page_title'] = "Riwayat Pengajuan Ujian Mahasiswa";

        // 1. Dapatkan daftar ID mahasiswa bimbingan dosen yang login
        $mahasiswa_ids = $this->Riwayat_ujian_model->get_mahasiswa_bimbingan_ids($dosen_id);
        
        $data['mahasiswa_list_with_riwayat'] = [];

        if (!empty($mahasiswa_ids)) {
            foreach ($mahasiswa_ids as $mahasiswa_id_obj) {
                $mahasiswa_id = $mahasiswa_id_obj['mahasiswa_id'];
                
                // 2. Dapatkan detail mahasiswa (nama, nim)
                // Asumsi Anda punya tabel mahasiswa dengan kolom 'id', 'nama', 'nim'
                $mahasiswa_detail = $this->Riwayat_ujian_model->get_mahasiswa_detail($mahasiswa_id);
                
                // 3. Dapatkan riwayat pengajuan untuk setiap mahasiswa
                $riwayat_events = $this->Riwayat_ujian_model->get_riwayat_by_mahasiswa($mahasiswa_id);
                
                // Hanya tambahkan mahasiswa jika ada riwayat atau detail mahasiswa ditemukan
                if ($mahasiswa_detail || !empty($riwayat_events)) {
                     $data['mahasiswa_list_with_riwayat'][] = [
                        'nama_mahasiswa' => $mahasiswa_detail['nama'] ?? 'Mahasiswa ID: ' . $mahasiswa_id,
                        'nim_mahasiswa' => $mahasiswa_detail['nim'] ?? 'N/A',
                        'mahasiswa_id' => $mahasiswa_id,
                        'riwayat' => $riwayat_events
                    ];
                }
            }
        } else {
             $data['message'] = "Tidak ada mahasiswa bimbingan yang memiliki riwayat pengajuan.";
        }

        // Load view, sesuaikan path jika perlu
        // $this->load->view('templates/header_dosen', $data); // Contoh header
   
          $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/riwayat_pengajuan_view', $data);
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
      public function form_reschedule_ujian($original_jadwal_id) {
        // Pastikan dosen yang login adalah pembimbing dari jadwal ini
        // atau memiliki hak untuk melakukan reschedule
        $data['jadwal_detail'] = $this->Penjadwalan_model->get_jadwal_id($original_jadwal_id); // Ambil detail jadwal

        if (!$data['jadwal_detail']) {
            $this->session->set_flashdata('error', 'Detail jadwal tidak ditemukan.');
            redirect('dosen/jadwal_ujian'); // Atau halaman daftar jadwal dosen
            return;
        }

        // Anda bisa menambahkan pengecekan apakah dosen yang login berhak me-reschedule jadwal ini
        // Misalnya, cek apakah $this->session->userdata('user_id') == $data['jadwal_detail']->pembimbing_id

        $data['title'] = 'Form Penjadwalan Ulang Ujian';
        $data['original_jadwal_id'] = $original_jadwal_id;


          $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dosen/form_reschedule_ujian_view', $data);
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

   

   public function process_reschedule_request() {
    // 1. Atur aturan validasi
    $this->form_validation->set_rules('original_jadwal_id', 'ID Jadwal Asli', 'required|numeric', [
        'required' => '%s harus diisi.',
        'numeric' => '%s harus berupa angka.'
    ]);
    $this->form_validation->set_rules('reason_reschedule', 'Alasan Penjadwalan Ulang', 'required|trim|min_length[10]', [
        'required' => '%s harus diisi.',
        'min_length' => '%s minimal harus 10 karakter.'
    ]);

    $original_jadwal_id = $this->input->post('original_jadwal_id');

    // Jika validasi gagal, kembalikan ke form
    if ($this->form_validation->run() == FALSE) {
        $this->session->set_flashdata('error_form', validation_errors());
        // Redirect kembali ke form reschedule dengan ID jadwalnya
        if (!empty($original_jadwal_id)) {
            redirect('dosen/form_reschedule_ujian/' . $original_jadwal_id);
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan, ID jadwal tidak ditemukan.');
            redirect('dosen/jadwal_ujian');
        }
        return;
    }

    // Memulai transaction. Semua query setelah ini akan di-buffer.
    $this->db->trans_start();

    // Inisialisasi variabel hasil untuk digunakan di luar scope transaction
    $hasil_reschedule = ['success' => false, 'error' => 'Proses tidak diinisialisasi.'];

    // Data dari form yang sudah valid
    $reason_for_reschedule = $this->input->post('reason_reschedule');
    $dosen_id = $this->session->userdata('id_dosen');
    $dosen_nama = $this->session->userdata('nama');

    // Pengecekan sesi dosen
    if (empty($dosen_id)) {
        log_message('error', 'CRITICAL: Dosen ID tidak ditemukan di session saat proses reschedule.');
        $this->session->set_flashdata('error', 'Sesi Anda tidak valid. Silakan login kembali.');
        // Tidak perlu rollback karena belum ada query, tapi redirect tetap perlu
        redirect('auth');
        // `trans_complete` akan dipanggil di akhir, jadi biarkan saja.
    } else {
        // 2. Ambil data jadwal_ujian asli (pastikan model sudah diperbaiki)
        $original_jadwal = $this->Penjadwalan_model->get_jadwal_id($original_jadwal_id);

        if (!$original_jadwal || empty($original_jadwal->pengajuan_id)) {
            log_message('error', "Proses Reschedule: Data jadwal asli ID {$original_jadwal_id} tidak ditemukan atau tidak memiliki pengajuan_id.");
            $this->session->set_flashdata('error', 'Data jadwal asli tidak ditemukan atau tidak valid untuk diproses.');
            // Tidak menghentikan transaction, biarkan selesai dan gagal secara alami.
        } else {
            $pengajuan_id_asli = $original_jadwal->pengajuan_id;

            // 3. Tandai jadwal_ujian asli sebagai 'Ditolak' atau status lain yang relevan
            $this->db->where('id', $original_jadwal_id);
            $this->db->update('jadwal_ujian', [
                'status_konfirmasi' => 'Ditolak',
                'catatan_kabag' => 'Jadwal lama. Diajukan penjadwalan ulang oleh Dosen: ' . ($dosen_nama ?? 'ID:'.$dosen_id) . '. Alasan: ' . $reason_for_reschedule
            ]);
            log_message('info', "Jadwal ID {$original_jadwal_id} ditandai Ditolak karena permintaan reschedule.");

            // 4. Catat permintaan ini ke tabel history
            $history_data = [
                'original_jadwal_id' => $original_jadwal_id,
                'requested_by_user_type' => 'dosen',
                'requested_by_user_id' => $dosen_id,
                'reason_for_reschedule' => $reason_for_reschedule,
                'reschedule_status' => 'requested' // Status awal
            ];
            $this->db->insert('jadwal_reschedule_history', $history_data);
            $reschedule_history_id = $this->db->insert_id();
            log_message('info', "Permintaan reschedule dicatat di history ID {$reschedule_history_id}.");

            // 5. Ambil data_pengajuan lengkap untuk proses penjadwalan ulang
            $pengajuan_data = $this->Pengajuan_model->get_pengajuan_by_id($pengajuan_id_asli);

            if (!$pengajuan_data) {
                log_message('error', "Proses Reschedule: Data pengajuan asli (ID: {$pengajuan_id_asli}) tidak ditemukan.");
                $this->db->where('id', $reschedule_history_id)->update('jadwal_reschedule_history', ['reschedule_status' => 'failed', 'kabag_notes' => 'Data pengajuan asli tidak ditemukan.']);
            } else {
                // Pastikan semua ID dosen lengkap
                $mhs_detail = $this->Penjadwalan_model->get_mahasiswa($pengajuan_data['mahasiswa_id']);
                if ($mhs_detail) {
                    $pengajuan_data['pembimbing_id'] = $pengajuan_data['pembimbing_id'] ?? $mhs_detail['pembimbing_id'] ?? null;
                    $pengajuan_data['penguji1_id'] = $pengajuan_data['penguji1_id'] ?? $mhs_detail['penguji1_id'] ?? null;
                    $pengajuan_data['penguji2_id'] = $pengajuan_data['penguji2_id'] ?? $mhs_detail['penguji2_id'] ?? null;
                }

                if (empty($pengajuan_data['pembimbing_id']) || empty($pengajuan_data['penguji1_id']) || empty($pengajuan_data['penguji2_id'])) {
                    log_message('error', "Proses Reschedule: ID dosen tidak lengkap untuk pengajuan_id {$pengajuan_id_asli}.");
                    $this->db->where('id', $reschedule_history_id)->update('jadwal_reschedule_history', ['reschedule_status' => 'failed', 'kabag_notes' => 'Data dosen pembimbing/penguji tidak lengkap pada pengajuan.']);
                } else {
                    // 6. Panggil logika penjadwalan ulang dari Model
                    $catatan_untuk_jadwal_baru = "Dijadwalkan ulang otomatis atas permintaan Dosen (ID: {$dosen_id}). Jadwal lama ID: " . $original_jadwal_id;
                    // SETELAH DIUBAH
                    // Kirim seluruh objek $original_jadwal agar model tahu detail slot yang harus dihindari
                    $hasil_reschedule = $this->Penjadwalan_model->attempt_reschedule_logic($pengajuan_data, $original_jadwal, $catatan_untuk_jadwal_baru);
                    // 7. Update history berdasarkan hasil reschedule
                    if ($hasil_reschedule['success']) {
                        $this->db->where('id', $reschedule_history_id)->update('jadwal_reschedule_history', ['new_jadwal_id' => $hasil_reschedule['new_jadwal_id'], 'reschedule_status' => 'success']);
                        log_message('info', "Penjadwalan ulang sukses. Jadwal baru ID: {$hasil_reschedule['new_jadwal_id']}.");
                    } else {
                        $this->db->where('id', $reschedule_history_id)->update('jadwal_reschedule_history', ['reschedule_status' => 'failed', 'kabag_notes' => 'Gagal mencari jadwal baru: ' . ($hasil_reschedule['error'] ?? 'Tidak ada detail error.')]);
                        log_message('warn', "Penjadwalan ulang GAGAL. Error: " . ($hasil_reschedule['error'] ?? 'Tidak ada detail error.'));
                    }
                }
            }
        }
    }

    // Menyelesaikan transaction. CodeIgniter akan cek jika ada error, lalu commit atau rollback.
    $this->db->trans_complete();

    // Cek status akhir dari transaction
    if ($this->db->trans_status() === FALSE) {
        // Jika ada yang gagal, transaction sudah otomatis di-rollback
        log_message('error', 'Transaction untuk reschedule jadwal ID ' . $original_jadwal_id . ' gagal dan semua query di-rollback.');
        $this->session->set_flashdata('error', 'Terjadi kesalahan fatal pada database saat memproses permintaan Anda. Semua perubahan telah dibatalkan.');
    } else {
        // Transaction berhasil dieksekusi, sekarang tampilkan pesan berdasarkan hasil logikanya
        if ($hasil_reschedule['success']) {
            $this->session->set_flashdata('success', 'Permintaan penjadwalan ulang berhasil. Jadwal baru (ID: ' . $hasil_reschedule['new_jadwal_id'] . ') telah dibuat dan menunggu konfirmasi Kabag.');
        } else {
            // Cek apakah ada pesan error spesifik yang sudah di-set sebelumnya
            if (!$this->session->flashdata('error')) {
                $this->session->set_flashdata('error', 'Permintaan penjadwalan ulang GAGAL: ' . ($hasil_reschedule['error'] ?? 'Tidak ada slot yang cocok.') . ". Jadwal lama telah ditandai untuk reschedule, silakan koordinasi dengan Kabag.");
            }
        }
    }

    redirect('dosen/jadwal_ujian');
}
        
}