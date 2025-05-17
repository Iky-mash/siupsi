<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Pengajuan_model');
        $this->load->model('Penjadwalan_model');
        $this->load->library('session'); // Pastikan session sudah dimuat
        
    }

    // Fungsi untuk menyimpan pengajuan
    public function simpan() {
        // Pastikan mahasiswa_id tersedia di session (misalnya sudah login)
        $mahasiswa_id = $this->session->userdata('id');  // Menggunakan key 'id' yang diset saat login

        // Jika mahasiswa_id tidak ada, redirect ke halaman login
        if (!$mahasiswa_id) {
            redirect('auth/login'); // Ganti dengan rute login yang sesuai
        }
     

        // Ambil tanggal pengajuan (otomatis saat disimpan)
        $tanggal_pengajuan = date('Y-m-d'); // Format tanggal yang sesuai

        // Status otomatis 'draft'
        $status = 'draft';

        // Konfigurasi upload file
        $config['upload_path'] = './uploads/'; // pastikan folder uploads ada
        $config['allowed_types'] = 'pdf|doc|docx';
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        // Upload file_lembar_bimbingan
        if (!$this->upload->do_upload('file_lembar_bimbingan')) {
            echo $this->upload->display_errors();
            return;
        }
        $lembar_bimbingan = $this->upload->data('file_name');

        // Upload file_lembar_pengesahan
        if (!$this->upload->do_upload('file_lembar_pengesahan')) {
            echo $this->upload->display_errors();
            return;
        }
        $lembar_pengesahan = $this->upload->data('file_name');

        // Data yang akan disimpan
        $data = [
            'mahasiswa_id' => $mahasiswa_id,
            'judul_skripsi' => $this->input->post('judul_skripsi'),
            'tipe_ujian' => $this->input->post('tipe_ujian'),
            'file_lembar_bimbingan' => $lembar_bimbingan,
            'file_lembar_pengesahan' => $lembar_pengesahan,
            'tanggal_pengajuan' => $tanggal_pengajuan,
            'status' => $status,
        ];

        // Panggil model untuk menyimpan data
        $this->Pengajuan_model->insert($data);

        // Redirect ke halaman sukses
        redirect('Pengajuan/sukses');
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
    private function penjadwalan_otomatis($pengajuan) {
        $mhs_id    = $pengajuan['mahasiswa_id'];
        $tipe      = $pengajuan['tipe_ujian'];
        $peng_id   = $pengajuan['id'];
    
        $mhs     = $this->Penjadwalan_model->get_mahasiswa($mhs_id);
        $dosen1  = $mhs['penguji1_id'];
        $dosen2  = $mhs['penguji2_id'];
        $pemb    = $mhs['pembimbing_id'];
    
        $prd       = $this->Penjadwalan_model->get_periode($tipe);
        $dates     = $this->Penjadwalan_model->generate_date_range($prd['tanggal_mulai'], $prd['tanggal_selesai']);
        $timeSlots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25'];
    
        foreach ($dates as $tgl) {
            $hari = date('N', strtotime($tgl)); // 6=Sabtu, 7=Minggu
    
            // Abaikan weekend (bisa dikonfigurasi jika ingin pakai)
            if ($hari >= 6) {
                continue;
            }
    
            foreach ($timeSlots as $slot) {
               // ✅ Cek apakah dosen pembimbing atau penguji bentrok jadwal
            //    $dosen_list = [$dosen1, $dosen2, $pemb];
            //    $dosen_tidak_tersedia = false;
               
            //    foreach ($dosen_list as $dosen) {
            //        if (!$this->Penjadwalan_model->cek_ketersediaan_dosen($dosen, $tgl, $slot)) {
            //            $dosen_tidak_tersedia = true;
            //            break; 
            //        }
            //    }
               
            //    if ($dosen_tidak_tersedia) {
            //        continue; 
            //    }
            $is_dosen_tersedia = $this->Penjadwalan_model->cek_ketersediaan_dosen(
                $tgl, $slot, [$dosen1, $dosen2, $pemb]
            );
            
            // Jika dosen tidak tersedia (terjadi bentrok), lewati slot ini
            if (!$is_dosen_tersedia) {
                continue; // lewati slot ini
            }
            
            // Jika dosen tersedia, lanjutkan untuk menghitung prioritas
            $skor = $this->Penjadwalan_model->hitung_prioritas([
                'pembimbing' => $pemb,
                'dosen1'     => $dosen1,
                'dosen2'     => $dosen2,
                'tanggal'    => $tgl,
                'slot'       => $slot,
                'tipe_ujian' => $tipe
            ]);
            
            
                if ($skor >= 0.8) {
                    $ruang = $this->Penjadwalan_model->get_ruangan_available($tipe, $tgl, $slot);
                    if ($ruang) {
                        // Simpan jadwal
                        $jadwal_id = $this->Penjadwalan_model->simpan_jadwal([
                            'mahasiswa_id'=> $mhs_id,
                            'judul_skripsi'=> $pengajuan['judul_skripsi'],
                            'tipe_ujian'  => $tipe,
                            'tanggal'     => $tgl,
                            'slot'        => $slot,
                            'ruangan_id'  => $ruang['id'],
                            'pembimbing'  => $pemb,
                            'dosen1'      => $dosen1,
                            'dosen2'      => $dosen2
                        ]);
    
                        $this->Penjadwalan_model->update_jadwal_pengajuan($peng_id, $jadwal_id);
                        return; // Stop setelah jadwal berhasil disimpan
                    }
                }
            }
        }
    
        // Jika semua tanggal dan slot gagal
        log_message('error', "Penjadwalan gagal: Tidak ada slot cocok untuk pengajuan ID $peng_id");
    }
    
    public function form() {
        $data['title'] = 'Dashboard Mahasiswa';
        $mahasiswa_id = $this->session->userdata('id');
        $data['pengajuan'] = $this->Pengajuan_model->get_pengajuan_by_mahasiswa($mahasiswa_id);
     
      
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_mahasiswa', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('pengajuan/form', $data);
        $this->load->view('templates/footer');
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
