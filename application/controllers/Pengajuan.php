<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan extends CI_Controller {

     // Definisikan konfigurasi file di sini agar bisa diakses di beberapa method
    private $sempro_files_config = [
        ['name' => 'file_ktm', 'label' => 'KTM (Kartu Tanda Mahasiswa)'],
        ['name' => 'file_krs', 'label' => 'KRS (Kartu Rencana Studi)'],
        ['name' => 'form_snack', 'label' => 'Formulir Snack'],
        ['name' => 'file_surat_pengesahan_proposal', 'label' => 'Surat Pengesahan Proposal'],
        ['name' => 'file_bukti_pembayaran', 'label' => 'Bukti Pembayaran Sempro'],
        ['name' => 'file_transkrip_nilai', 'label' => 'Transkrip Nilai Terbaru'],
        ['name' => 'file_nota_dinas_pembimbing', 'label' => 'Nota Dinas Pembimbing'],
        ['name' => 'file_proposal_skripsi', 'label' => 'File Proposal Skripsi']
    ];

    private $semhas_files_config = [
        ['name' => 'file_ktm', 'label' => 'KTM (Kartu Tanda Mahasiswa)'],
        ['name' => 'file_krs', 'label' => 'KRS (Kartu Rencana Studi)'],
        ['name' => 'form_snack', 'label' => 'Formulir Snack'],
        ['name' => 'file_lembar_pengesahan_skripsi', 'label' => 'Lembar Pengesahan Skripsi'],
        ['name' => 'file_sertifikat_ALTC', 'label' => 'Sertifikat ALTC'],
        ['name' => 'file_sertifikat_LPBA', 'label' => 'Sertifikat LPBA'],
        ['name' => 'lembar_SPM', 'label' => 'Lembar SPM'],
        ['name' => 'lembar_EC', 'label' => 'Lembar EC'],
        ['name' => 'file_transkrip_nilai', 'label' => 'Transkrip Nilai Terbaru'],
        ['name' => 'file_nota_dinas_pembimbing', 'label' => 'Nota Dinas Pembimbing untuk Semhas'],
        ['name' => 'lembar_bimbingan_skripsi', 'label' => 'Lembar Bimbingan Skripsi (14x Bimbingan)'],
        ['name' => 'lembar_keikutsertaan_sempro', 'label' => 'Lembar Keikutsertaan Ujian Proposal'],
        ['name' => 'file_draft_skripsi_final', 'label' => 'File Draft Skripsi Final']
    ];

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

  
    public function index() {
        $data['title'] = 'Ajukan Jadwal Ujian';
        
        $mahasiswa_id = $this->session->userdata('id');
        if (!$mahasiswa_id) {
            $this->session->set_flashdata('error', 'Sesi tidak valid. Silakan login kembali.');
            redirect('auth/login_mahasiswa');
            return;
        }

        $mahasiswa = $this->Mahasiswa_model->get_mahasiswa_by_id($mahasiswa_id);
        
        // **LOGIKA BARU YANG DISEMPURNAKAN**

        // 1. Tentukan hak akses berdasarkan status_sempro
        $status_sempro = $mahasiswa->status_sempro;
        $can_access_semhas = ($status_sempro === 'ACC');
        $can_access_sempro = ($status_sempro !== 'ACC'); // Mahasiswa hanya bisa akses sempro jika status BUKAN ACC

        // Kirim hak akses ini ke view untuk menonaktifkan tombol
        $data['can_access_sempro'] = $can_access_sempro;
        $data['can_access_semhas'] = $can_access_semhas;

        // 2. Tentukan form mana yang harus menjadi default
        if ($can_access_semhas) {
            // Jika sudah ACC sempro, defaultnya adalah Semhas
            $data['selected_type'] = 'Semhas';
        } else {
            // Jika belum ACC sempro, defaultnya adalah Sempro
            $data['selected_type'] = 'Sempro';
        }

        // 3. (Opsional) Beri pesan jika pengguna mencoba mengakses form yang tidak diizinkan via URL
        $tipe_yang_diminta = $this->input->get('tipe');
        if ($tipe_yang_diminta === 'Sempro' && !$can_access_sempro) {
            $this->session->set_flashdata('error', 'Anda sudah lulus Seminar Proposal. Silakan lanjutkan ke pengajuan Seminar Hasil.');
        }
        if ($tipe_yang_diminta === 'Semhas' && !$can_access_semhas) {
             $this->session->set_flashdata('error', 'Anda harus lulus Seminar Proposal (status ACC) terlebih dahulu.');
        }

        // Pastikan config file selalu ada
        $data['sempro_files_config'] = $this->sempro_files_config;
        $data['semhas_files_config'] = $this->semhas_files_config;
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_mahasiswa', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('pengajuan/form', $data);
        $this->load->view('templates/footer');
    }
   public function store() {
        $tipe_ujian = $this->input->post('tipe_ujian');
        $mahasiswa_id = $this->session->userdata('id');

        if (empty($mahasiswa_id)) {
            $this->session->set_flashdata('error', 'Sesi tidak valid. Silakan login kembali.');
            redirect('auth/login_mahasiswa'); // Arahkan ke login
            return;
        }

        $this->form_validation->set_rules('judul_skripsi', 'Judul Skripsi', 'required|trim');
        $this->form_validation->set_rules('tipe_ujian', 'Tipe Ujian', 'required|in_list[Sempro,Semhas]');

        // Tentukan file mana yang wajib berdasarkan tipe_ujian
        $required_file_fields_config = ($tipe_ujian == 'Sempro') ? $this->sempro_files_config : $this->semhas_files_config;

        foreach ($required_file_fields_config as $file_config) {
            // Menggunakan callback untuk validasi file
            // Key untuk set_rules adalah nama input file, label adalah untuk pesan error
            $this->form_validation->set_rules($file_config['name'], $file_config['label'], 'callback_validate_file_upload[' . $file_config['name'] . ']');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_validation', validation_errors('<div class="alert alert-danger p-2 mb-2">', '</div>'));
            $this->index(); // Kembali ke form jika validasi gagal
            return;
        }

        // --- Lanjutkan dengan Proses Upload File jika validasi lolos ---
           // --- Handle File Uploads ---
       $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'pdf|doc|docx';
        $config['max_size'] = 51200;
        $config['encrypt_name'] = TRUE;
        $this->upload->initialize($config); // Load library di sini atau di __construct

        $file_fields_to_upload = array(); // Hanya file yang akan diupload (dari $required_file_fields_config)
         foreach ($required_file_fields_config as $file_conf) {
            $file_fields_to_upload[] = $file_conf['name'];
        }


        foreach ($file_fields_to_upload as $field) {
            // Re-initialize config untuk setiap upload agar tidak ada konflik
            $this->upload->initialize($config);

            if (!empty($_FILES[$field]['name'])) {
                if ($this->upload->do_upload($field)) {
                    $upload_data = $this->upload->data();
                    $uploaded_files[$field] = $upload_data['file_name'];
                } else {
                    $this->session->set_flashdata('error', 'Gagal mengunggah file ' . $field . ': ' . $this->upload->display_errors());
                    $this->index();
                    return;
                }
            } else {
                // Ini seharusnya sudah ditangani oleh callback validate_file_upload
                // Tapi sebagai fallback, bisa diisi null jika kolom DB mengizinkan (meskipun kita buat wajib)
                $uploaded_files[$field] = null;
            }
        }
        
        // --- Prepare data for pengajuan_ujian_prioritas table ---
        $data_pengajuan = array(
            'mahasiswa_id' => $mahasiswa_id,
            'judul_skripsi' => $this->input->post('judul_skripsi'),
            'tipe_ujian' => $tipe_ujian,
            'tanggal_pengajuan' => date('Y-m-d H:i:s'),
            'status' => 'draft'
        );

        // --- Save to Database ---
        $pengajuan_id = $this->Pengajuan_model->insert_pengajuan($data_pengajuan, $tipe_ujian, $uploaded_files);

        if ($pengajuan_id) {
            $this->session->set_flashdata('success', 'Pengajuan ujian berhasil disimpan sebagai draft.');
            redirect('mahasiswa/jadwal_ujian'); // Ganti ke halaman riwayat yang sesuai
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan pengajuan. Coba lagi atau hubungi administrator.');
            $this->index();
        }
    }

    // Callback function untuk validasi file upload
    public function validate_file_upload($value, $field_name) { // $value tidak terlalu berguna untuk file
        if (empty($_FILES[$field_name]['name'])) {
            // Ambil label yang benar untuk pesan error
            $tipe_ujian = $this->input->post('tipe_ujian'); // Perlu tahu tipe ujian untuk ambil config yang benar
            $file_configs = ($tipe_ujian == 'Sempro') ? $this->sempro_files_config : $this->semhas_files_config;
            $label = $field_name; // default
            foreach ($file_configs as $conf) {
                if ($conf['name'] == $field_name) {
                    $label = $conf['label'];
                    break;
                }
            }
            $this->form_validation->set_message('validate_file_upload', "Berkas '{$label}' wajib diunggah.");
            return FALSE;
        }
        return TRUE;
    }

    public function riwayat() {
        $data['title'] = 'Dashboard Mahasiswa';
        $mahasiswa_id = $this->session->userdata('id');
        $data['pengajuan'] = $this->Pengajuan_model->get_pengajuan_by_mahasiswa($mahasiswa_id);
        $data['jadwal'] = $this->Penjadwalan_model->get_jadwal_by_mahasiswa($mahasiswa_id);
      
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_mahasiswa', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('pengajuan/riwayat', $data);
        $this->load->view('templates/footer');
    }
    
}
