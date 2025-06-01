<?php
use Dompdf\Dompdf;
use Dompdf\Options;
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Pengajuan_model'); 
        $this->load->model('Mahasiswa_model');
        $this->load->model('Penjadwalan_model'); 
        $this->load->model('Dosen_model');
        $this->load->model('Agenda_model');
        $this->load->library('session');  // Pastikan session di-load
        $this->load->helper('url');  
        $this->load->model('Pekan_model');
        $this->load->model('Ruangan_model');//ruangan
        if_logged_in();
        check_role(['Admin', 'Ruangan']); 
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
  
        $data['title'] = 'Data Mahasiswa';
        

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/data_mahasiswa', $data); 
        $this->load->view('templates/footer');
    }
    public function data_dosen() {
        $data['dosen'] = $this->Dosen_model->get_all_dosen(); // Mengambil data dosen
        $data['dosen'] = $this->db->get('dosen')->result();
        $data['title'] = 'Data Dosen';
        

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/data_dosen', $data); 
        $this->load->view('templates/footer');
    }
public function laporan_seminar() {
    $this->load->model('Mahasiswa_model');
    $data['mahasiswa_seminar'] = $this->Mahasiswa_model->get_mahasiswa_seminar_status();

    $data['title'] = 'Laporan Seminar';

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar_admin', $data);
    $this->load->view('templates/navbar', $data);
    $this->load->view('admin/laporan_seminar', $data);
    $this->load->view('templates/footer');
}

public function download_laporan_seminar() {
    $this->load->model('Mahasiswa_model');
    $data['mahasiswa_seminar'] = $this->Mahasiswa_model->get_mahasiswa_seminar_status();
    $data['title'] = 'Laporan Status Seminar Mahasiswa'; // Title for the PDF content

    // Load the view and pass data
    // The TRUE third parameter tells it to return the content as a string
    $html = $this->load->view('admin/laporan_seminar_pdf', $data, TRUE);

    // Configure Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true); // Important for loading external CSS/images if any (not used here)
    $options->set('defaultFont', 'Arial'); // Set a default font

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait'); // 'portrait' or 'landscape'

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    // The 'Attachment' => 1 will force download, 0 will try to show in browser
    $filename = 'laporan_seminar_mahasiswa_'.date('Ymd').'.pdf';
    $dompdf->stream($filename, array("Attachment" => 1));
    exit; // Important to prevent any further output from CodeIgniter
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
        $data['pengajuan'] = $this->Pengajuan_model->get_all_pengajuan();
        $data['title'] = 'Dashboard Admin';
        

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/pengajuan_ujian', $data);
        $this->load->view('templates/footer');
    }

    // Menyetuju atau Menolak Pengajuan Ujian
    public function verifikasi_pengajuan($pengajuan_id) {
      
        $this->load->model('Pengajuan_model');
        $this->load->model('JadwalUjian_model');
    
        // Ambil data pengajuan berdasarkan ID
        $pengajuan = $this->Pengajuan_model->get_pengajuan_by_id($pengajuan_id);
    
        if ($pengajuan) {
            // Update status pengajuan menjadi 'Disetujui'
            $this->Pengajuan_model->update_status($pengajuan_id, 'Disetujui');
    
            // Dapatkan rekomendasi jadwal ujian
            $jadwal_rekomendasi = $this->JadwalUjian_model->generateJadwalRekomendasi($pengajuan_id);
    
            if ($jadwal_rekomendasi) {
                // Simpan jadwal rekomendasi ke database
                $this->JadwalUjian_model->simpanJadwal($jadwal_rekomendasi);
    
                $this->session->set_flashdata('success', 'Pengajuan disetujui dan jadwal ujian berhasil dibuat.');
                redirect('admin/pengajuan_ujian');
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
    public function importdata_mhs() {
       
        $data['title'] = 'Import Data Mahasiswa';
        

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/importdata_mhs', $data);
        $this->load->view('templates/footer');
    }
      public function tambah_dosen() {
         $data['title'] = 'Tambah Data Dosen';
        // Aturan validasi
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
        // Validasi email: required, valid_email, dan is_unique (cek di tabel 'dosen' kolom 'email')
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[dosen.email]', [
            'is_unique' => 'Email ini sudah terdaftar.'
        ]);
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]'); // Contoh min_length
        // Validasi NIP: required dan is_unique (cek di tabel 'dosen' kolom 'nip')
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim|is_unique[dosen.nip]', [
            'is_unique' => 'NIP ini sudah terdaftar.'
        ]);
        $this->form_validation->set_rules('role_id', 'Role ID', 'required|integer');

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, tampilkan kembali form dengan pesan error
            $data['judul'] = 'Form Tambah Data Dosen';
            
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/tambah_dosen', $data);
        $this->load->view('templates/footer');
            // Ganti 'nama_view_form_dosen' dengan nama file view Anda
        } else {
            // Jika validasi berhasil, proses penyimpanan data
            $data_dosen = [
                'nama' => $this->input->post('nama', true),
                'email' => $this->input->post('email', true),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT), // Selalu hash password
                'nip' => $this->input->post('nip', true),
                'role_id' => $this->input->post('role_id', true)
            ];

            $this->Dosen_model->tambah_dosen($data_dosen);

            // Set flashdata untuk pesan sukses
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Data dosen berhasil ditambahkan!</div>');
            redirect('admin/tambah_dosen'); 
        }
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
    public function mahasiswa_disetujui() {
        $this->load->model('Pengajuan_model');
        $data['title'] = 'Mahsiswa Disetujui';
        
        // Ambil data mahasiswa yang pengajuannya disetujui
        $data['mahasiswa_disetujui'] = $this->Pengajuan_model->getMahasiswaDisetujui();
    
        // Load view dan kirim data ke halaman dashboard admin
      
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/mahasiswa_disetujui', $data);
        $this->load->view('templates/footer');
    }


    public function jadwalkan($mahasiswa_id) {
        log_message('debug', 'Mahasiswa ID yang diterima: ' . $mahasiswa_id);
    
        $this->load->model('Penjadwalan_model');
    
        // Ambil data mahasiswa
        $mahasiswa = $this->Penjadwalan_model->getMahasiswaById($mahasiswa_id);
        if (!$mahasiswa) {
            $this->session->set_flashdata('error', 'ID mahasiswa tidak valid.');
            redirect('admin/mahasiswa_disetujui');
            return;
        }
    
        // Periksa keberadaan dosen pembimbing dan penguji
        if (empty($mahasiswa['pembimbing_id']) || empty($mahasiswa['penguji1_id']) || empty($mahasiswa['penguji2_id'])) {
            $this->session->set_flashdata('error', 'Data dosen pembimbing atau penguji tidak lengkap.');
            redirect('admin/mahasiswa_disetujui');
            return;
        }
    
        // Tentukan rentang tanggal dan waktu sesi
        $tanggal_awal = date('Y-m-d', strtotime('+4 days'));
        $tanggal_akhir = date('Y-m-d', strtotime('+21 days'));
        $waktu_sesi = [
            ['waktu_mulai' => '08:00:00', 'waktu_selesai' => '08:40:00'],
            ['waktu_mulai' => '09:00:00', 'waktu_selesai' => '09:40:00'],
            ['waktu_mulai' => '10:00:00', 'waktu_selesai' => '10:40:00'],
            ['waktu_mulai' => '11:00:00', 'waktu_selesai' => '11:40:00'],
            ['waktu_mulai' => '13:00:00', 'waktu_selesai' => '13:40:00'],
            ['waktu_mulai' => '14:00:00', 'waktu_selesai' => '14:40:00'],
            ['waktu_mulai' => '15:00:00', 'waktu_selesai' => '15:40:00']
        ];
    
        // Gunakan fungsi `cariJadwalYangTersedia` untuk mendapatkan rekomendasi jadwal
        $rekomendasi_jadwal = $this->Penjadwalan_model->cariJadwalYangTersedia($tanggal_awal, $tanggal_akhir, $waktu_sesi, $mahasiswa);
    
        // Jika tidak ada rekomendasi jadwal, tampilkan pesan error
        if (empty($rekomendasi_jadwal)) {
            $this->session->set_flashdata('error', 'Tidak ada jadwal tersedia dalam rentang waktu yang ditentukan.');
            redirect('admin/mahasiswa_disetujui');
            return;
        }
    
        // Simpan rekomendasi jadwal ke database
        foreach ($rekomendasi_jadwal as $jadwal) {
            $this->Penjadwalan_model->buatJadwal(
                $mahasiswa_id,
                $jadwal['tanggal'],
                $jadwal['waktu_mulai'],
                $jadwal['waktu_selesai'],
                $mahasiswa['pembimbing_id'],
                $mahasiswa['penguji1_id'],
                $mahasiswa['penguji2_id']
            );
        }
    
        $this->session->set_flashdata('success', 'Jadwal berhasil dibuat.');
        redirect('admin/mahasiswa_disetujui');
    }

    public function jadwalAdmin() {
        $this->load->model('Penjadwalan_model');
    
        $data['jadwal_ujian'] = $this->Penjadwalan_model->get_all_jadwal();
    
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/rekomendasi', $data);
        $this->load->view('templates/footer');
    }
    

    //Pekan sempro dan semhas
    public function jadwal() {
      $data['title'] = 'Pekan Seminar';

        $data['sempro'] = $this->Pekan_model->get_jadwal('sempro');
        $data['semhas'] = $this->Pekan_model->get_jadwal('semhas');
        $data['ruangan_sempro'] = $this->Ruangan_model->get_by_tipe('sempro');
        $data['ruangan_semhas'] = $this->Ruangan_model->get_by_tipe('semhas');
    
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('admin/pekan', $data);
        $this->load->view('templates/footer');
    }

    public function update_jadwal() {
        $jenis = $this->input->post('jenis');
        $tanggal_mulai = $this->input->post('tanggal_mulai');
        $tanggal_selesai = $this->input->post('tanggal_selesai');

        if ($tanggal_mulai && $tanggal_selesai) {
            $data = [
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai
            ];
            $this->Pekan_model->update_jadwal($jenis, $data);
            $this->session->set_flashdata('success', 'Jadwal berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('error', 'Tanggal harus diisi!');
        }
        
        redirect('admin/jadwal');
    }

    //DOSEN
    public function edit_dosen($id)
    {
         $data['title'] = 'Edit Data Dosen';
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
    
    public function agenda($id_dosen) {
        echo "Step 1: ID Dosen = " . $id_dosen;
        $agenda = $this->Agenda_model->get_agenda_by_dosen($id_dosen);
        if (empty($agenda)) {
            echo "Step 2: Agenda not found";
        } else {
            print_r($agenda);
        }
        die();
    }
    
    
    
    
}