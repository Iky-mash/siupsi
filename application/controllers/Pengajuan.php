<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan extends CI_Controller {

     // Definisikan konfigurasi file di sini agar bisa diakses di beberapa method
    private $sempro_files_config = [
        ['name' => 'file_ktm', 'label' => 'KTM (Kartu Tanda Mahasiswa)'],
        ['name' => 'file_krs', 'label' => 'KRS (Kartu Rencana Studi)'],
        ['name' => 'file_surat_pengesahan_proposal', 'label' => 'Surat Pengesahan Proposal'],
        ['name' => 'file_bukti_pembayaran', 'label' => 'Bukti Pembayaran Sempro'],
        ['name' => 'file_transkrip_nilai', 'label' => 'Transkrip Nilai Terbaru'],
        ['name' => 'file_nota_dinas_pembimbing', 'label' => 'Nota Dinas Pembimbing'],
        ['name' => 'file_proposal_skripsi', 'label' => 'File Proposal Skripsi']
    ];

    private $semhas_files_config = [
        ['name' => 'file_ktm', 'label' => 'KTM (Kartu Tanda Mahasiswa)'],
        ['name' => 'file_krs', 'label' => 'KRS (Kartu Rencana Studi)'],
        ['name' => 'file_lembar_pengesahan_skripsi', 'label' => 'Lembar Pengesahan Skripsi'],
        ['name' => 'file_lembar_ec', 'label' => 'Lembar EC (Ethical Clearance)'],
        ['name' => 'file_lembar_spm', 'label' => 'Lembar SPM'],
        ['name' => 'file_bukti_pembayaran', 'label' => 'Bukti Pembayaran Semhas'],
        ['name' => 'file_transkrip_nilai', 'label' => 'Transkrip Nilai Terbaru'],
        ['name' => 'file_nota_dinas_pembimbing', 'label' => 'Nota Dinas Pembimbing untuk Semhas'],
        ['name' => 'file_draft_skripsi_final', 'label' => 'File Draft Skripsi Final']
    ];

    public function __construct() {
        parent::__construct();
        $this->load->model('Pengajuan_model');
        $this->load->model('Penjadwalan_model');
        $this->load->library('session'); // Pastikan session sudah dimuat
         $this->load->library('form_validation'); // <--- TAMBAHKAN BARIS INI
        $this->load->helper(array('form', 'url'));
         
    }

    public function index() {
     
        // This method will display the form
        $data['title'] = 'Form Pengajuan Ujian';
        // You can pass an initial type if needed, e.g., from a query parameter
   $tipe_from_get = $this->input->get('tipe');
$data['selected_type'] = !empty($tipe_from_get) ? $tipe_from_get : 'Sempro';

    // PASTIKAN BARIS-BARIS INI ADA SEBELUM VIEW DI-LOAD DARI BARIS 50:
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
        $config['max_size'] = 2048;
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
   public function form_pengajuan($data_view = array()) {
        if (!$this->session->userdata('id')) {
            $this->session->set_flashdata('error_message', 'Silakan login terlebih dahulu.');
            redirect('auth/login'); // Ganti dengan rute login Anda
            return;
        }
        // Baris ini akan memuat view form Anda, pastikan path-nya benar
        

          $this->load->view('templates/header', $data_view);
        $this->load->view('templates/sidebar_mahasiswa', $data_view);
        $this->load->view('templates/navbar', $data_view);
       $this->load->view('pengajuan/form', $data_view);
        $this->load->view('templates/footer');
    }
    // Fungsi untuk menyimpan pengajuan
 public function simpan() {
        $mahasiswa_id = $this->session->userdata('id');
        if (!$mahasiswa_id) {
            $this->session->set_flashdata('error_message', 'Sesi Anda telah berakhir. Silakan login kembali.');
            redirect('auth/login');
            return;
        }

        // Data untuk dikirim kembali ke view jika ada error
        $data_view = array();
        // Simpan input post untuk repopulate form jika error
        $data_view['form_data'] = $this->input->post();


        $judul_skripsi = $this->input->post('judul_skripsi');
        $tipe_ujian_input = $this->input->post('tipe_ujian');

        // Validasi Judul Skripsi (contoh sederhana, bisa ditambahkan di form_validation)
        if (empty($judul_skripsi)) {
            $data_view['error_popup_message'] = 'Judul Skripsi wajib diisi.';
            $this->form_pengajuan($data_view); // Panggil form_pengajuan dengan data error
            return;
        }


        if ($this->Pengajuan_model->has_submitted($mahasiswa_id, $tipe_ujian_input)) {
            $data_view['error_popup_message'] = 'Anda sudah pernah mengajukan untuk ' . htmlspecialchars($tipe_ujian_input) . '. Satu mahasiswa hanya bisa mengajukan satu kali per tipe ujian.';
            $this->form_pengajuan($data_view); // Panggil form_pengajuan dengan data error
            return;
        }

        if ($tipe_ujian_input == 'Semhas') {
            if (!$this->Pengajuan_model->has_submitted_sempro($mahasiswa_id)) {
                $data_view['error_popup_message'] = 'Anda harus melakukan pengajuan Seminar Proposal (Sempro) terlebih dahulu sebelum dapat mengajukan Seminar Hasil (Semhas).';
                $this->form_pengajuan($data_view); // Panggil form_pengajuan dengan data error
                return;
            }
        }

        $tanggal_pengajuan = date('Y-m-d');
        $status = 'draft';

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'pdf|doc|docx';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;
        $this->upload->initialize($config);

        $lembar_bimbingan_filename = null;
        $lembar_pengesahan_filename = null;

        if (empty($_FILES['file_lembar_bimbingan']['name'])) {
             $data_view['error_popup_message'] = 'File Lembar Bimbingan wajib diupload.';
             $this->form_pengajuan($data_view);
             return;
        }
        if (!$this->upload->do_upload('file_lembar_bimbingan')) {
            $error_msg = strip_tags($this->upload->display_errors());
            $data_view['error_popup_message'] = 'Gagal mengupload Lembar Bimbingan: ' . $error_msg;
            $this->form_pengajuan($data_view);
            return;
        }
        $lembar_bimbingan_filename = $this->upload->data('file_name');


        if (empty($_FILES['file_lembar_pengesahan']['name'])) {
            if ($lembar_bimbingan_filename && file_exists($config['upload_path'] . $lembar_bimbingan_filename)) {
                unlink($config['upload_path'] . $lembar_bimbingan_filename);
            }
            $data_view['error_popup_message'] = 'File Lembar Pengesahan wajib diupload.';
            $this->form_pengajuan($data_view);
            return;
        }
        if (!$this->upload->do_upload('file_lembar_pengesahan')) {
            if ($lembar_bimbingan_filename && file_exists($config['upload_path'] . $lembar_bimbingan_filename)) {
                unlink($config['upload_path'] . $lembar_bimbingan_filename);
            }
            $error_msg = strip_tags($this->upload->display_errors());
            $data_view['error_popup_message'] = 'Gagal mengupload Lembar Pengesahan: ' . $error_msg;
            $this->form_pengajuan($data_view);
            return;
        }
        $lembar_pengesahan_filename = $this->upload->data('file_name');
        
        $data_db = [
            'mahasiswa_id' => $mahasiswa_id,
            'judul_skripsi' => $judul_skripsi,
            'tipe_ujian' => $tipe_ujian_input,
            'file_lembar_bimbingan' => $lembar_bimbingan_filename,
            'file_lembar_pengesahan' => $lembar_pengesahan_filename,
            'tanggal_pengajuan' => $tanggal_pengajuan,
            'status' => $status,
        ];

        if ($this->Pengajuan_model->insert($data_db)) {
            $this->session->set_flashdata('success_message', 'Pengajuan ujian (' . htmlspecialchars($tipe_ujian_input) . ') berhasil disimpan.');
            redirect('Pengajuan/riwayat');
        } else {
            if ($lembar_bimbingan_filename && file_exists($config['upload_path'] . $lembar_bimbingan_filename)) {
                unlink($config['upload_path'] . $lembar_bimbingan_filename);
            }
            if ($lembar_pengesahan_filename && file_exists($config['upload_path'] . $lembar_pengesahan_filename)) {
                unlink($config['upload_path'] . $lembar_pengesahan_filename);
            }
            // Jika gagal insert DB, tampilkan error di form yang sama
            $data_view['error_popup_message'] = 'Terjadi kesalahan internal saat menyimpan data pengajuan. Silakan coba lagi.';
            $this->form_pengajuan($data_view);
        }
    }
    // Fungsi untuk menampilkan pesan sukses
    public function sukses() {
        echo "Pengajuan berhasil disimpan.";
    }
    public function konfirmasi()
    {
        $this->load->model('Pengajuan_model'); // Pastikan model dipanggil
        $this->load->model('Penjadwalan_model');
    
        $pengajuan_id = $this->input->post('pengajuan_id');
        $mahasiswa_id = $this->session->userdata('id'); // Mengambil 'id' dari session (bukan 'mahasiswa_id')
    
        if (!$mahasiswa_id) {
            show_error("Mahasiswa tidak ditemukan di session!", 500);
        }
    
        $data_konfirmasi = [
            'pengajuan_id' => $pengajuan_id,
            'mahasiswa_id' => $mahasiswa_id, // menggunakan 'id' yang sama seperti pada session
            'tanggal_konfirmasi' => date('Y-m-d'),
            'status_konfirmasi' => 'Terkonfirmasi'
        ];
    
        // Simpan ke tabel konfirmasi_pengajuan
        $this->db->insert('konfirmasi_pengajuan', $data_konfirmasi);
    
        // Update status di pengajuan_ujian_prioritas menjadi 'dikonfirmasi'
        $this->db->where('id', $pengajuan_id);
        $this->db->update('pengajuan_ujian_prioritas', ['status' => 'dikonfirmasi']);

          // 3. Ambil kembali data pengajuan
    $pengajuan = $this->Pengajuan_model->get_pengajuan_by_id($pengajuan_id);

    // 4. Panggil fungsi penjadwalan otomatis
    $this->penjadwalan_otomatis($pengajuan);
    
        // Redirect ke halaman jadwal ujian
        redirect('mahasiswa/jadwal_ujian');
    }
    // private function penjadwalan_otomatis($pengajuan) {
    //     $mhs_id    = $pengajuan['mahasiswa_id'];
    //     $tipe      = $pengajuan['tipe_ujian'];
    //     $peng_id   = $pengajuan['id'];
    
    //     $mhs     = $this->Penjadwalan_model->get_mahasiswa($mhs_id);
    //     $dosen1  = $mhs['penguji1_id'];
    //     $dosen2  = $mhs['penguji2_id'];
    //     $pemb    = $mhs['pembimbing_id'];
    
    //     $prd       = $this->Penjadwalan_model->get_periode($tipe);
    //     $dates     = $this->Penjadwalan_model->generate_date_range($prd['tanggal_mulai'], $prd['tanggal_selesai']);
    //     $timeSlots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25'];
    
    //     foreach ($dates as $tgl) {
    //         $hari = date('N', strtotime($tgl)); // 6=Sabtu, 7=Minggu
    
    //         // Abaikan weekend (bisa dikonfigurasi jika ingin pakai)
    //         if ($hari >= 6) {
    //             continue;
    //         }
    
    //         foreach ($timeSlots as $slot) {
    //            // ✅ Cek apakah dosen pembimbing atau penguji bentrok jadwal
    //         //    $dosen_list = [$dosen1, $dosen2, $pemb];
    //         //    $dosen_tidak_tersedia = false;
               
    //         //    foreach ($dosen_list as $dosen) {
    //         //        if (!$this->Penjadwalan_model->cek_ketersediaan_dosen($dosen, $tgl, $slot)) {
    //         //            $dosen_tidak_tersedia = true;
    //         //            break; 
    //         //        }
    //         //    }
               
    //         //    if ($dosen_tidak_tersedia) {
    //         //        continue; 
    //         //    }
    //         $is_dosen_tersedia = $this->Penjadwalan_model->cek_ketersediaan_dosen(
    //             $tgl, $slot, [$dosen1, $dosen2, $pemb]
    //         );
            
    //         // Jika dosen tidak tersedia (terjadi bentrok), lewati slot ini
    //         if (!$is_dosen_tersedia) {
    //             continue; // lewati slot ini
    //         }
            
    //         // Jika dosen tersedia, lanjutkan untuk menghitung prioritas
    //         $skor = $this->Penjadwalan_model->hitung_prioritas([
    //             'pembimbing' => $pemb,
    //             'dosen1'     => $dosen1,
    //             'dosen2'     => $dosen2,
    //             'tanggal'    => $tgl,
    //             'slot'       => $slot,
    //             'tipe_ujian' => $tipe
    //         ]);
            
            
    //             if ($skor >= 0.8) {
    //                 $ruang = $this->Penjadwalan_model->get_ruangan_available($tipe, $tgl, $slot);
    //                 if ($ruang) {
    //                     // Simpan jadwal
    //                     $jadwal_id = $this->Penjadwalan_model->simpan_jadwal([
    //                         'mahasiswa_id'=> $mhs_id,
    //                         'judul_skripsi'=> $pengajuan['judul_skripsi'],
    //                         'tipe_ujian'  => $tipe,
    //                         'tanggal'     => $tgl,
    //                         'slot'        => $slot,
    //                         'ruangan_id'  => $ruang['id'],
    //                         'pembimbing'  => $pemb,
    //                         'dosen1'      => $dosen1,
    //                         'dosen2'      => $dosen2
    //                     ]);
    
    //                     $this->Penjadwalan_model->update_jadwal_pengajuan($peng_id, $jadwal_id);
    //                     return; // Stop setelah jadwal berhasil disimpan
    //                 }
    //             }
    //         }
    //     }
    
    //     // Jika semua tanggal dan slot gagal
    //     log_message('error', "Penjadwalan gagal: Tidak ada slot cocok untuk pengajuan ID $peng_id");
    // }
    
    // public function form() {
    //     $data['title'] = 'Dashboard Mahasiswa';
    //     $mahasiswa_id = $this->session->userdata('id');
    //     $data['pengajuan'] = $this->Pengajuan_model->get_pengajuan_by_mahasiswa($mahasiswa_id);
     
      
    //     $this->load->view('templates/header', $data);
    //     $this->load->view('templates/sidebar_mahasiswa', $data);
    //     $this->load->view('templates/navbar', $data);
    //     $this->load->view('pengajuan/form', $data);
    //     $this->load->view('templates/footer');
    // }
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
