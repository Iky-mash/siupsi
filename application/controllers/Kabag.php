<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kabag extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Pengajuan_model'); 
        $this->load->model('Mahasiswa_model');
        $this->load->model('Penjadwalan_model'); 
        $this->load->model('Dosen_model');
        $this->load->model('Agenda_model');
         $this->load->model('Kabag_model');
        $this->load->library('session'); 
        $this->load->helper('url');  
        $this->load->model('Pekan_model');
        $this->load->model('Ruangan_model');
        if_logged_in();
        check_role(['Kabag', 'Admin']);

    }
    public function index() {
        $data['title'] = 'Dashboard';
    
          // 1. Hitung jumlah pengajuan ruangan yang menunggu
        $data['ruangan_menunggu_total'] = $this->Penjadwalan_model->hitung_ruangan_menunggu();

        // 2. Hitung jumlah pengajuan ujian yang menunggu
        $data['pengajuan_menunggu_total'] = $this->Penjadwalan_model->hitung_pengajuan_ujian_menunggu();
         // 3. (BARU) Hitung total mahasiswa Sempro
    $data['total_sempro'] = $this->Penjadwalan_model->hitung_berdasarkan_tipe_ujian('sempro');

    // 4. (BARU) Hitung total mahasiswa Semhas
    $data['total_semhas'] = $this->Penjadwalan_model->hitung_berdasarkan_tipe_ujian('semhas');
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_kabag', $data);
        $this->load->view('templates/navbar', $data);
       $this->load->view('kabag/index', $data);
        $this->load->view('templates/footer');
    }

    public function berkas() {
         $data['title'] = 'Berkas Pengajuan';
        $pengajuan_list = $this->Pengajuan_model->get_all_pengajuan_for_kabag_review();
         $mahasiswa_id = $this->session->userdata('id');
         $data['pengajuan'] = $this->Pengajuan_model->get_pengajuan_by_mahasiswa($mahasiswa_id);
        $data['jadwal'] = $this->Penjadwalan_model->get_jadwal_by_mahasiswa($mahasiswa_id);
        
        // Untuk setiap pengajuan, ambil detail berkasnya
        // Ini bisa membuat query N+1 jika banyak. Pertimbangkan optimasi jika perlu.
        foreach ($pengajuan_list as $key => $pengajuan) {
            $pengajuan_list[$key]->detail_berkas = $this->Pengajuan_model->get_detail_berkas_by_pengajuan_id($pengajuan->pengajuan_id, $pengajuan->tipe_ujian);
        }
        $data['pengajuan_list'] = $pengajuan_list;
         $data['pending_count'] = $this->Pengajuan_model->get_pending_application_count();


        // Definisikan juga konfigurasi file agar view bisa membuat link yang benar
        // Ini duplikat dari controller Pengajuan, mungkin bisa ditaruh di helper atau config
        $data['sempro_files_config'] = [
            ['name' => 'file_ktm', 'label' => 'KTM'], ['name' => 'file_krs', 'label' => 'KRS'], ['name' => 'form_snack', 'label' => 'Formulir Snack'],
            ['name' => 'file_surat_pengesahan_proposal', 'label' => 'Surat Pengesahan Proposal'],
            ['name' => 'file_bukti_pembayaran', 'label' => 'Bukti Pembayaran'], ['name' => 'file_transkrip_nilai', 'label' => 'Transkrip Nilai'],
            ['name' => 'file_nota_dinas_pembimbing', 'label' => 'Nota Dinas Pembimbing'], ['name' => 'file_proposal_skripsi', 'label' => 'File Proposal']
        ];
        $data['semhas_files_config'] = [
            ['name' => 'file_ktm', 'label' => 'KTM'], ['name' => 'file_krs', 'label' => 'KRS'], ['name' => 'form_snack', 'label' => 'Formulir Snack'],
            ['name' => 'file_lembar_pengesahan_skripsi', 'label' => 'Lembar Pengesahan Skripsi'], ['name' => 'file_sertifikat_ALTC', 'label' => 'Sertifikat ALTC'],
            ['name' => 'file_sertifikat_LPBA', 'label' => 'Sertifikat LPBA'],['name' => 'lembar_SPM', 'label' => 'Lembar SPM'],['name' => 'lembar_EC', 'label' => 'Lembar EC'],
            ['name' => 'file_transkrip_nilai', 'label' => 'Transkrip Nilai'], ['name' => 'file_nota_dinas_pembimbing', 'label' => 'Nota Dinas Pembimbing'],
            ['name' => 'lembar_bimbingan_skripsi', 'label' => 'Lembar Bimbingan Skripsi (14x Bimbingan)'], ['name' => 'lembar_keikutsertaan_sempro', 'label' => 'Lembar Keikutsertaan Sempro'],
            ['name' => 'file_draft_skripsi_final', 'label' => 'File Skripsi Final']
        ];



        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_kabag', $data);
        $this->load->view('templates/navbar', $data);
       $this->load->view('kabag/berkas_pengajuan', $data);
        $this->load->view('templates/footer');
    }

// Memproses penolakan pengajuan
    public function tolak_pengajuan() {
        $this->form_validation->set_rules('pengajuan_id', 'ID Pengajuan', 'required|integer');
        $this->form_validation->set_rules('alasan_penolakan', 'Alasan Penolakan', 'required|trim|min_length[10]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_modal_tolak_' . $this->input->post('pengajuan_id'), validation_errors('<p class="text-red-500 text-xs italic">', '</p>'));
            // Sebaiknya redirect kembali ke halaman berkas atau handle error agar modal bisa ditampilkan lagi dengan error
        } else {
            $pengajuan_id = $this->input->post('pengajuan_id');
            $alasan = $this->input->post('alasan_penolakan');

            if ($this->Pengajuan_model->update_pengajuan_status($pengajuan_id, 'ditolak', $alasan)) {
                $this->session->set_flashdata('success', 'Pengajuan berhasil ditolak.');
                // TODO: Kirim notifikasi email ke mahasiswa jika perlu
            } else {
                $this->session->set_flashdata('error', 'Gagal memproses penolakan pengajuan.');
            }
        }
        redirect('kabag/berkas');
    }

    // Memproses konfirmasi pengajuan (opsional, jika Kabag juga bisa konfirmasi)
public function konfirmasi_pengajuan($pengajuan_id) {
    if (!is_numeric($pengajuan_id)) {
        show_404();
    }

    $pengajuan = $this->Pengajuan_model->get_pengajuan_by_id($pengajuan_id);

    if (!$pengajuan) {
        $this->session->set_flashdata('error', 'Data pengajuan tidak ditemukan.');
        redirect('kabag/berkas');
        return;
    }

    // 1. Ubah status pengajuan menjadi 'dikonfirmasi'
    if ($this->Pengajuan_model->update_pengajuan_status($pengajuan_id, 'dikonfirmasi')) {
        
        // 2. Catat riwayat konfirmasi (opsional, tapi bagus untuk tracking)
        $data_konfirmasi = [
            'pengajuan_id'      => $pengajuan_id,
            'mahasiswa_id'      => $pengajuan['mahasiswa_id'],
            'tanggal_konfirmasi'=> date('Y-m-d H:i:s'),
            'status_konfirmasi' => 'Terkonfirmasi'
        ];
        $this->db->insert('konfirmasi_pengajuan', $data_konfirmasi);
        
        $this->session->set_flashdata('success', 'Pengajuan berhasil dikonfirmasi.');

    } else {
        $this->session->set_flashdata('error', 'Gagal memproses konfirmasi pengajuan.');
    }
    
    // 3. HAPUS PANGGILAN PENJADWALAN OTOMATIS DARI SINI
    // $this->penjadwalan_otomatis($pengajuan, $pengajuan_id); // Baris ini dihapus

    redirect('kabag/berkas');
}


// GANTI JUGA FUNGSI LAMA ANDA DENGAN YANG INI
private function penjadwalan_otomatis($pengajuan, $id_pengajuan_asli) {
    // Fungsi ini sekarang menerima parameter kedua: $id_pengajuan_asli
    // Ini adalah ID pengajuan yang DIJAMIN BENAR.

    $mhs_id = $pengajuan['mahasiswa_id'];
    $tipe   = $pengajuan['tipe_ujian'];
    
    // Kita tidak lagi menggunakan $pengajuan['id'] karena rawan salah.
    // Kita gunakan $id_pengajuan_asli yang kita bawa dari fungsi sebelumnya.

    $mhs    = $this->Penjadwalan_model->get_mahasiswa($mhs_id);
    $dosen1 = $mhs['penguji1_id'];
    $dosen2 = $mhs['penguji2_id'];
    $pemb   = $mhs['pembimbing_id'];

    $prd       = $this->Penjadwalan_model->get_periode($tipe);
    $dates     = $this->Penjadwalan_model->generate_date_range($prd['tanggal_mulai'], $prd['tanggal_selesai']);
    $timeSlots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25'];

    foreach ($dates as $tgl) {
        $hari = date('N', strtotime($tgl));
        if ($hari >= 6) {
            continue;
        }

        foreach ($timeSlots as $slot) {
            $is_dosen_tersedia = $this->Penjadwalan_model->cek_ketersediaan_dosen($tgl, $slot, [$dosen1, $dosen2, $pemb]);
            if (!$is_dosen_tersedia) {
                continue;
            }
            
            // Anggap saja skor selalu lolos untuk penyederhanaan
            $ruang = $this->Penjadwalan_model->get_ruangan_available($tipe, $tgl, $slot);
            if ($ruang) {
                // =============================================================
                // PERUBAHAN PENTING DI SINI:
                // Siapkan data untuk disimpan ke tabel jadwal_ujian
                // Pastikan 'pengajuan_id' diisi dengan $id_pengajuan_asli
                // =============================================================
                $data_jadwal_baru = [
                    'mahasiswa_id'      => $mhs_id,
                    'pengajuan_id'      => $id_pengajuan_asli, // MENGGUNAKAN ID YANG DIJAMIN BENAR
                    'judul_skripsi'     => $pengajuan['judul_skripsi'],
                    'tipe_ujian'        => $tipe,
                    'tanggal'           => $tgl,
                    'slot_waktu'        => $slot,
                    'ruangan_id'        => $ruang['id'],
                    'pembimbing_id'     => $pemb,
                    'penguji1_id'       => $dosen1,
                    'penguji2_id'       => $dosen2,
                    'status_konfirmasi' => 'Menunggu'
                ];
                
                // Panggil fungsi model untuk menyimpan data ini
                $jadwal_id = $this->Penjadwalan_model->simpan_jadwal_baru($data_jadwal_baru);
                
                // Update tabel pengajuan dengan jadwal_id yang baru dibuat
                $this->Penjadwalan_model->update_jadwal_pengajuan($id_pengajuan_asli, $jadwal_id);
                
                return; // Stop setelah jadwal berhasil disimpan
            }
        }
    }

    log_message('error', "Penjadwalan gagal: Tidak ada slot cocok untuk pengajuan ID $id_pengajuan_asli");
}

 public function pengajuan_ruangan() {
        $data['title'] = 'Jadwal Ujian';
      $data['jadwal'] = $this->Penjadwalan_model->get_all_jadwal();
      
    
        // Tampilkan ke view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_kabag', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('kabag/pengajuan_ruangan', $data);
        $this->load->view('templates/footer');
    }
 public function reschedule() {
    

    $this->load->model('Penjadwalan_model'); // Pastikan model sudah di-load
    $data['title'] = 'Riwayat Reschedule';
    $data['riwayat_list'] = $this->Penjadwalan_model->get_all_reschedule_history();

    // Sesuaikan path template dengan struktur Anda
    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar_kabag', $data);
    $this->load->view('templates/navbar', $data);
    $this->load->view('kabag/reschedule', $data); // View baru yang akan kita buat
    $this->load->view('templates/footer', $data);
}
public function cari_jadwal_alternatif()
{
    if (!$this->input->is_ajax_request()) {
        show_404(); return;
    }
    header('Content-Type: application/json');

    // Ambil semua parameter yang dibutuhkan
    $jadwal_id = $this->input->get('jadwal_id');
    $tipe_ujian = $this->input->get('tipe_ujian');
    $start_date_str = $this->input->get('tanggal_mulai');
    $original_slot = $this->input->get('slot_waktu_original');

    if (empty($jadwal_id)) {
        echo json_encode(['success' => false, 'message' => 'Error: ID Jadwal tidak valid.']);
        return;
    }

    // === PERUBAHAN UTAMA DIMULAI DI SINI ===

    // 1. Ambil periode pekan seminar yang aktif untuk tipe ujian ini
    $periode_ujian = $this->Penjadwalan_model->get_periode($tipe_ujian);
    if (!$periode_ujian) {
        echo json_encode(['success' => false, 'message' => 'Error: Pekan ujian untuk ' . $tipe_ujian . ' tidak ditemukan/aktif.']);
        return;
    }
    $akhir_pekan_ujian = new DateTime($periode_ujian['tanggal_selesai']);

    // 2. Ambil detail dosen yang terlibat
    $jadwal = $this->Penjadwalan_model->get_jadwal_by_id($jadwal_id);
    if (!$jadwal) {
        echo json_encode(['success' => false, 'message' => 'Error: Data jadwal tidak ditemukan.']);
        return;
    }
    $dosen_terlibat = array_filter([$jadwal->pembimbing_id, $jadwal->penguji1_id, $jadwal->penguji2_id]);

    // 3. Logika pencarian yang sudah dibatasi oleh periode
    $valid_slots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25'];
    $current_date = new DateTime($start_date_str);
    $start_date_obj = new DateTime($start_date_str);

    // Loop akan berjalan dari tanggal mulai hingga akhir pekan ujian
    while ($current_date <= $akhir_pekan_ujian) {
        $day_of_week = $current_date->format('N');
        if ($day_of_week >= 6) { // Lewati Sabtu & Minggu
            $current_date->modify('+1 day');
            continue;
        }

        $tanggal_cari = $current_date->format('Y-m-d');

        foreach ($valid_slots as $slot) {
            if ($current_date == $start_date_obj && $slot == $original_slot) {
                continue;
            }

            // Validasi ganda: Dosen DAN Ruangan
             $dosen_tersedia = $this->Penjadwalan_model->cek_ketersediaan_dosen($tanggal_cari, $slot, $dosen_terlibat, $jadwal_id);
    if (!$dosen_tersedia) {
        continue;
    }
            
    // Panggil fungsi model dengan menyertakan ID jadwal yang akan diabaikan
    $ruangan_tersedia = $this->Penjadwalan_model->get_ruangan_available($tipe_ujian, $tanggal_cari, $slot, $jadwal_id);
    if ($ruangan_tersedia) {
                // Slot valid ditemukan!
                echo json_encode([
                    'success' => true,
                    'message' => 'Jadwal alternatif yang valid ditemukan.',
                    'data' => [
                        'tanggal' => $tanggal_cari,
                        'slot_waktu' => $slot,
                        'formatted_date' => $current_date->format('l, d F Y')
                    ]
                ]);
                return; // Hentikan dan kirim hasil
            }
        }
        $current_date->modify('+1 day'); // Lanjut ke hari berikutnya
    }

    // Jika loop selesai tanpa menemukan jadwal yang cocok
    echo json_encode(['success' => false, 'message' => 'Tidak ditemukan slot jadwal yang cocok dalam periode pekan seminar yang ditentukan.']);
}
public function update_status() {
    $id_jadwal = $this->input->post('id');
    $status_baru = $this->input->post('status_konfirmasi');
    
    // Ambil data dari hidden input yang diisi oleh JavaScript
    $new_ruangan_id = $this->input->post('new_ruangan_id');
    $new_ruangan_name = $this->input->post('new_ruangan_name');
    $new_tanggal = $this->input->post('new_tanggal');
    $new_slot_waktu = $this->input->post('new_slot_waktu');
    $alasan_penolakan = $this->input->post('rejection_reason');

    // ALUR 1: PENJADWALAN ULANG OTOMATIS
    // Ini berjalan jika `cari_jadwal_alternatif` berhasil dan mengisi `new_tanggal` & `new_slot_waktu`.
    if ($status_baru == 'Dikonfirmasi' && !empty($new_tanggal) && !empty($new_slot_waktu)) {
        
        $jadwal_lama = $this->db->get_where('jadwal_ujian', ['id' => $id_jadwal])->row();
        $tipe_ujian = $jadwal_lama->tipe_ujian;

        // Cari satu ruangan yang tersedia di jadwal baru tersebut (sebagai final check)
        $ruangan_baru = $this->Penjadwalan_model->get_ruangan_available($tipe_ujian, $new_tanggal, $new_slot_waktu);

        if ($ruangan_baru) {
            // **PROSES INTI**: Mengubah data jadwal lama dengan data baru
            $this->db->where('id', $id_jadwal)->update('jadwal_ujian', [
                'tanggal'           => $new_tanggal,
                'slot_waktu'        => $new_slot_waktu,
                'ruangan_id'        => $ruangan_baru['id'],
                'status_konfirmasi' => 'Dikonfirmasi',
                'catatan_kabag'     => 'Dijadwalkan ulang otomatis ke ' . $new_tanggal . ' karena slot sebelumnya penuh.'
            ]);
            $this->session->set_flashdata('success', 'Jadwal berhasil dijadwalkan ulang secara otomatis.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menjadwalkan ulang. Slot yang direkomendasikan sudah tidak tersedia. Silakan coba lagi.');
        }

    // ALUR 2: Ganti Ruangan Saja
    } elseif ($status_baru == 'Dikonfirmasi' && !empty($new_ruangan_id)) {
        // ... (logika ini sudah benar)
        $this->db->where('id', $id_jadwal)->update('jadwal_ujian', [
            'ruangan_id'        => $new_ruangan_id,
            'status_konfirmasi' => 'Dikonfirmasi',
            'catatan_kabag'     => 'Ruangan diubah ke ' . htmlspecialchars($new_ruangan_name)
        ]);
        $this->session->set_flashdata('success', 'Ruangan untuk jadwal berhasil diperbarui.');

    // ALUR 3: Update Status Biasa (termasuk penolakan murni)
    } else {
        // ... (logika ini sudah benar)
        $data_update = ['status_konfirmasi' => $status_baru];
        if ($status_baru == 'Ditolak') {
            $data_update['catatan_kabag'] = 'Ditolak dengan alasan: ' . $alasan_penolakan;
        }
        $this->db->where('id', $id_jadwal)->update('jadwal_ujian', $data_update);
        $this->session->set_flashdata('success', 'Status jadwal berhasil diperbarui.');
    }

    redirect('kabag/pengajuan_ruangan');
}
// public function update_status() {
//     $id_jadwal = $this->input->post('id');
//     $status_baru = $this->input->post('status_konfirmasi');
//     $alasan_penolakan = $this->input->post('rejection_reason');
//     $new_ruangan_id = $this->input->post('new_ruangan_id');
//     $new_ruangan_name = $this->input->post('new_ruangan_name');

//     // ALUR 1: Jika memilih ruangan rekomendasi dari daftar (sudah benar)
//     if (!empty($new_ruangan_id)) {
//         $this->db->where('id', $id_jadwal)->update('jadwal_ujian', [
//             'status_konfirmasi' => 'Dikonfirmasi',
//             'ruangan_id'        => $new_ruangan_id,
//             'catatan_kabag'     => 'Ruangan diubah ke ' . htmlspecialchars($new_ruangan_name)
//         ]);
//         $this->session->set_flashdata('success', 'Jadwal berhasil diperbarui dengan ruangan ' . htmlspecialchars($new_ruangan_name) . '.');
//         redirect('kabag/pengajuan_ruangan');
//         return;
//     }

//     // ALUR 2: Jika status yang dipilih adalah "Ditolak"
//     if ($status_baru == 'Ditolak') {
//         // KASUS KHUSUS: Reschedule karena ruangan tidak tersedia ATAU semua penuh
//         if ($alasan_penolakan == 'room_unavailable' || $alasan_penolakan == 'all_rooms_full') {
            
//             // Panggil fungsi reschedule & bump yang baru di model
//             $hasil = $this->Penjadwalan_model->reschedule_with_bumping($id_jadwal);

//             if ($hasil['success']) {
//                 $this->session->set_flashdata('success', 'Proses penjadwalan ulang berantai berhasil. ' . ($hasil['message'] ?? ''));
//             } else {
//                 $this->session->set_flashdata('error', 'Penjadwalan ulang GAGAL: ' . ($hasil['error'] ?? 'Terjadi kesalahan tidak diketahui.'));
//             }
//             redirect('kabag/pengajuan_ruangan');
//             return;
//         }
        
//         // Logika penolakan biasa (tanpa reschedule)
//         $this->db->where('id', $id_jadwal)->update('jadwal_ujian', ['status_konfirmasi' => 'Ditolak', 'catatan_kabag' => 'Ditolak manual oleh Kabag.']);
//         $this->session->set_flashdata('success', 'Jadwal berhasil ditolak.');
    
//     // ALUR 3: Konfirmasi biasa
//     } else {
//         $this->db->where('id', $id_jadwal)->update('jadwal_ujian', ['status_konfirmasi' => 'Dikonfirmasi', 'catatan_kabag' => NULL]);
//         $this->session->set_flashdata('success', 'Jadwal berhasil dikonfirmasi.');
//     }

//     redirect('kabag/pengajuan_ruangan');
// }
// application/controllers/Kabag.php

public function jadwalkan_semua_terkonfirmasi() {
    // 1. Ambil semua pengajuan yang terkonfirmasi, sudah terurut berdasarkan prioritas
    $antrean_mahasiswa = $this->Penjadwalan_model->get_all_confirmed_applications_sorted();

    if (empty($antrean_mahasiswa)) {
        $this->session->set_flashdata('info', 'Tidak ada pengajuan yang perlu dijadwalkan saat ini.');
        redirect('kabag/berkas');
        return;
    }

    $sukses_count = 0;
    $gagal_count = 0;

    // 2. Loop melalui setiap mahasiswa di antrean
    foreach ($antrean_mahasiswa as $pengajuan) {
        // Panggil fungsi penjadwalan privat untuk setiap pengajuan
        $hasil = $this->_find_and_assign_schedule($pengajuan);
        if ($hasil) {
            $sukses_count++;
        } else {
            $gagal_count++;
        }
    }

    // 3. Berikan laporan hasil penjadwalan
    $this->session->set_flashdata('success', "Proses penjadwalan selesai. Berhasil menjadwalkan {$sukses_count} mahasiswa.");
    if ($gagal_count > 0) {
        $this->session->set_flashdata('error', "{$gagal_count} mahasiswa gagal dijadwalkan karena tidak ditemukan slot yang cocok.");
    }

    redirect('kabag/berkas');
}


private function _find_and_assign_schedule($pengajuan) {
    // Fungsi ini sama seperti penjadwalan_otomatis Anda yang lama,
    // tapi dibuat lebih modular.

    $mhs_id = $pengajuan['mahasiswa_id'];
    $tipe = $pengajuan['tipe_ujian'];
    $id_pengajuan_asli = $pengajuan['id'];
    
    // Ambil data dosen dari hasil query sebelumnya
    $dosen1 = $pengajuan['penguji1_id'];
    $dosen2 = $pengajuan['penguji2_id'];
    $pemb = $pengajuan['pembimbing_id'];

    if (empty($dosen1) || empty($dosen2) || empty($pemb)) {
        log_message('error', "Penjadwalan gagal: Dosen penguji/pembimbing belum di-set untuk pengajuan ID $id_pengajuan_asli");
        return false;
    }

    $prd = $this->Penjadwalan_model->get_periode($tipe);
    $dates = $this->Penjadwalan_model->generate_date_range($prd['tanggal_mulai'], $prd['tanggal_selesai']);
    $timeSlots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25'];

    foreach ($dates as $tgl) {
        // Abaikan hari Sabtu dan Minggu
        if (date('N', strtotime($tgl)) >= 6) continue;

        foreach ($timeSlots as $slot) {
            // Cek ketersediaan semua dosen yang terlibat
            if (!$this->Penjadwalan_model->cek_ketersediaan_dosen($tgl, $slot, [$dosen1, $dosen2, $pemb])) {
                continue;
            }
            
            // Cari ruangan yang tersedia
            $ruang = $this->Penjadwalan_model->get_ruangan_available($tipe, $tgl, $slot);
            if ($ruang) {
                // Jika semua cocok, simpan jadwal
                $data_jadwal_baru = [
                    'mahasiswa_id'  => $mhs_id,
                    'pengajuan_id'  => $id_pengajuan_asli,
                    'judul_skripsi' => $pengajuan['judul_skripsi'],
                    'tipe_ujian'    => $tipe,
                    'tanggal'       => $tgl,
                    'slot_waktu'    => $slot,
                    'ruangan_id'    => $ruang['id'],
                    'pembimbing_id' => $pemb,
                    'penguji1_id'   => $dosen1,
                    'penguji2_id'   => $dosen2,
                    'status_konfirmasi' => 'Menunggu', // Dosen perlu konfirmasi
                    'priority_score' => $pengajuan['priority_score']
                ];
                
                $jadwal_id = $this->Penjadwalan_model->simpan_jadwal_baru($data_jadwal_baru);
                
                // Update tabel pengajuan dengan ID jadwal yang baru
                $this->Penjadwalan_model->update_jadwal_pengajuan($id_pengajuan_asli, $jadwal_id);
                
                return true; // Berhasil, hentikan pencarian untuk mahasiswa ini
            }
        }
    }

    log_message('error', "Penjadwalan gagal: Tidak ada slot cocok untuk pengajuan ID $id_pengajuan_asli");
    return false; // Gagal menemukan jadwal untuk mahasiswa ini
}
public function get_recommended_rooms() {
    header('Content-Type: application/json'); // Set ini di paling awal

    // Cek apakah ini request AJAX
    if (!$this->input->is_ajax_request()) {
        http_response_code(403); // Forbidden
        log_message('error', 'AJAX Check Failed for get_recommended_rooms. X-Requested-With: ' . $this->input->server('HTTP_X_REQUESTED_WITH'));
        echo json_encode(['error' => 'Forbidden: Endpoint ini hanya menerima request AJAX.']);
        return;
    }

    $raw_tipe_ujian_from_get = $this->input->get('tipe_ujian');
    $tanggal_from_get = $this->input->get('tanggal');
    $slot_waktu_from_get = $this->input->get('slot_waktu');

    $tipe_ujian_input = strtolower(trim($raw_tipe_ujian_from_get));

    // =============== LOGGING DETAIL DIMULAI ===============
    log_message('debug', '------------------- get_recommended_rooms INVOCATION START -------------------');
    log_message('debug', '[INPUT] Raw GET param "tipe_ujian": \'' . $raw_tipe_ujian_from_get . '\'');
    log_message('debug', '[INPUT] Raw GET param "tanggal": \'' . $tanggal_from_get . '\'');
    log_message('debug', '[INPUT] Raw GET param "slot_waktu": \'' . $slot_waktu_from_get . '\'');
    log_message('debug', '[PROCESSED_INPUT] Trimmed and lowercased "tipe_ujian_input": \'' . $tipe_ujian_input . '\'');

    if (empty($tipe_ujian_input) || empty($tanggal_from_get) || empty($slot_waktu_from_get)) {
        log_message('error', '[CONDITION] Satu atau lebih parameter GET (tipe_ujian, tanggal, slot_waktu) KOSONG. Mengembalikan JSON array kosong.');
        echo json_encode([]);
        log_message('debug', '------------------- get_recommended_rooms INVOCATION END (empty input) -------------------');
        return;
    }
    
    // Default ke input yang sudah diproses jika tidak ada kondisi pemetaan spesifik yang terpenuhi
    $mapped_tipe_ujian = $tipe_ujian_input;
    $mapping_applied_info = 'Tidak ada aturan pemetaan spesifik yang cocok, menggunakan input yang sudah diproses.';

    // Logika pemetaan
    if (str_contains($tipe_ujian_input, 'proposal') || $tipe_ujian_input === 'sempro') {
        $mapped_tipe_ujian = 'sempro';
        $mapping_applied_info = 'Dipetakan ke "sempro" berdasarkan input: \'' . $tipe_ujian_input . '\'';
    } elseif (str_contains($tipe_ujian_input, 'hasil') || $tipe_ujian_input === 'semhas' ||
              str_contains($tipe_ujian_input, 'skripsi') || str_contains($tipe_ujian_input, 'sidang')) {
        $mapped_tipe_ujian = 'semhas';
        $mapping_applied_info = 'Dipetakan ke "semhas" berdasarkan input: \'' . $tipe_ujian_input . '\'';
    }

    log_message('debug', '[MAPPING_LOGIC] ' . $mapping_applied_info);
    log_message('debug', '[FINAL_MAPPED_TYPE] "mapped_tipe_ujian" untuk query DB: \'' . $mapped_tipe_ujian . '\'');

    $available_rooms = []; // Inisialisasi sebagai array kosong
    try {
        // 1. Ambil semua ruangan dengan tipe_seminar yang sesuai
        $this->db->select('id, nama_ruangan, kapasitas, tipe_seminar');
        $this->db->from('ruangan');
        $this->db->where('LOWER(tipe_seminar)', $mapped_tipe_ujian); // Pastikan nama kolom 'tipe_seminar' sesuai
        $query_all_rooms_of_type = $this->db->get();
        $all_rooms_of_type = $query_all_rooms_of_type->result_array();
        
        log_message('debug', '[DB_QUERY_ALL_ROOMS] SQL: ' . $this->db->last_query());
        log_message('debug', '[DB_RESULT_ALL_ROOMS] Ditemukan ' . count($all_rooms_of_type) . ' ruangan tipe ' . $mapped_tipe_ujian);

        if (!empty($all_rooms_of_type)) {
            // 2. Ambil ID ruangan yang sudah terpakai pada tanggal dan slot waktu tersebut
            $this->db->select('ruangan_id');
            $this->db->from('jadwal_ujian');
            $this->db->where('tanggal', $tanggal_from_get);
            $this->db->where('slot_waktu', $slot_waktu_from_get);
            // Abaikan jadwal yang statusnya 'Ditolak', karena ruangan tersebut dianggap tersedia
            $this->db->where_not_in('LOWER(status_konfirmasi)', ['ditolak']); 
            $query_booked_rooms = $this->db->get();
            $booked_room_rows = $query_booked_rooms->result_array();
            $booked_room_ids = array_column($booked_room_rows, 'ruangan_id');

            log_message('debug', '[DB_QUERY_BOOKED_ROOMS] SQL: ' . $this->db->last_query());
            log_message('debug', '[DB_RESULT_BOOKED_ROOMS] Ditemukan ' . count($booked_room_ids) . ' ID ruangan yang terpakai: ' . json_encode($booked_room_ids));

            // 3. Saring ruangan yang tersedia
            foreach ($all_rooms_of_type as $room) {
                if (!in_array($room['id'], $booked_room_ids)) {
                    $available_rooms[] = $room;
                }
            }
            log_message('debug', '[FILTERING] Ditemukan ' . count($available_rooms) . ' ruangan tersedia setelah penyaringan.');
        }

    } catch (Exception $e) {
        log_message('error', '[EXCEPTION] Exception terjadi di get_recommended_rooms: ' . $e->getMessage());
    }

    echo json_encode($available_rooms); // Ini akan menghasilkan '[]' jika $available_rooms kosong.
    log_message('debug', '------------------- get_recommended_rooms INVOCATION END -------------------');
}
    /**
     * Fungsi untuk menolak jadwal dan memicu penjadwalan ulang otomatis.
     */
    public function reschedule_and_reject() {
        header('Content-Type: application/json');

        $jadwal_id_lama = $this->input->post('jadwal_id'); 
        $pengajuan_id_asli = $this->input->post('pengajuan_id'); 
        
        // Data tambahan yang mungkin dibutuhkan oleh penjadwalan_otomatis
        // Jika tidak ada di $pengajuan_data, bisa diambil dari POST jika dikirim dari JS
        $mahasiswa_id_post = $this->input->post('mahasiswa_id');
        $tipe_ujian_post = $this->input->post('tipe_ujian');
        $judul_skripsi_post = $this->input->post('judul_skripsi');


        if (empty($jadwal_id_lama) || empty($pengajuan_id_asli)) {
            echo json_encode(['success' => false, 'error' => 'ID Jadwal atau ID Pengajuan asli tidak valid.']);
            return;
        }

        $this->db->where('id', $jadwal_id_lama);
        $this->db->update('jadwal_ujian', [
            'status_konfirmasi' => 'Ditolak',
            'catatan_kabag' => 'Ditolak - Semua ruangan penuh, proses penjadwalan ulang otomatis dipicu.' // Sesuaikan nama kolom catatan
        ]);

        if ($this->db->affected_rows() <= 0) {
             log_message('warning', "Gagal menandai jadwal ID {$jadwal_id_lama} sebagai Ditolak sebelum penjadwalan ulang (mungkin sudah Ditolak).");
        }

        // Ambil data pengajuan asli yang lengkap
        $pengajuan_data = $this->Pengajuan_model->get_pengajuan_by_id($pengajuan_id_asli);

        if (!$pengajuan_data) {
            // Jika $pengajuan_data tidak ditemukan berdasarkan $pengajuan_id_asli (misal jika $pengajuan_id_asli kosong/salah)
            // kita coba buat array $pengajuan_data minimal dari data POST
            // Ini adalah fallback, idealnya $pengajuan_id_asli valid dan $pengajuan_data ditemukan.
            if(!empty($mahasiswa_id_post) && !empty($tipe_ujian_post) && !empty($judul_skripsi_post)) {
                log_message('warning', "Data pengajuan asli (ID: {$pengajuan_id_asli}) tidak ditemukan, menggunakan data POST sebagai fallback untuk reschedule.");
                $pengajuan_data = [
                    'id' => $pengajuan_id_asli, // Bisa jadi ini tidak valid jika tidak ditemukan
                    'mahasiswa_id' => $mahasiswa_id_post,
                    'tipe_ujian' => $tipe_ujian_post,
                    'judul_skripsi' => $judul_skripsi_post,
                    // Anda mungkin perlu mengambil data dosen dari model mahasiswa di sini
                    // 'pembimbing_id' => ..., 'penguji1_id' => ..., 'penguji2_id' => ...
                ];
            } else {
                log_message('error', "Reschedule failed: Data pengajuan asli (ID: {$pengajuan_id_asli}) tidak ditemukan DAN data POST tidak lengkap.");
                echo json_encode(['success' => false, 'error' => 'Gagal mengambil data pengajuan asli untuk penjadwalan ulang.']);
                return;
            }
        }
        
        // PENTING: Pastikan $pengajuan_data memiliki semua field yang dibutuhkan oleh penjadwalan_otomatis_untuk_reschedule
        // Termasuk mahasiswa_id, tipe_ujian, judul_skripsi, pembimbing_id, penguji1_id, penguji2_id.
        // Jika tidak ada di $pengajuan_data, coba lengkapi dari data mahasiswa.
        if (empty($pengajuan_data['mahasiswa_id']) && !empty($mahasiswa_id_post)) {
            $pengajuan_data['mahasiswa_id'] = $mahasiswa_id_post;
        }
        if (empty($pengajuan_data['tipe_ujian']) && !empty($tipe_ujian_post)) {
            $pengajuan_data['tipe_ujian'] = $tipe_ujian_post;
        }
         if (empty($pengajuan_data['judul_skripsi']) && !empty($judul_skripsi_post)) {
            $pengajuan_data['judul_skripsi'] = $judul_skripsi_post;
        }

        // Jika data dosen belum ada di $pengajuan_data, coba ambil dari model mahasiswa
        if (empty($pengajuan_data['pembimbing_id']) || empty($pengajuan_data['penguji1_id']) || empty($pengajuan_data['penguji2_id'])) {
            if (!empty($pengajuan_data['mahasiswa_id'])) {
                $mhs_detail = $this->Penjadwalan_model->get_mahasiswa($pengajuan_data['mahasiswa_id']);
                if ($mhs_detail) {
                    $pengajuan_data['pembimbing_id'] = $pengajuan_data['pembimbing_id'] ?? $mhs_detail['pembimbing_id'] ?? null;
                    $pengajuan_data['penguji1_id'] = $pengajuan_data['penguji1_id'] ?? $mhs_detail['penguji1_id'] ?? null;
                    $pengajuan_data['penguji2_id'] = $pengajuan_data['penguji2_id'] ?? $mhs_detail['penguji2_id'] ?? null;
                }
            }
        }
        
        try {
            log_message('info', "Memulai penjadwalan ulang otomatis untuk pengajuan ID: {$pengajuan_id_asli} (dari jadwal lama ID: {$jadwal_id_lama})");
            $hasil_penjadwalan = $this->penjadwalan_otomatis_untuk_reschedule($pengajuan_data, $jadwal_id_lama);

            if ($hasil_penjadwalan['success']) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Jadwal lama (ID: '.$jadwal_id_lama.') telah ditolak. ' . $hasil_penjadwalan['message']
                ]);
            } else {
                 echo json_encode([
                    'success' => false, 
                    'error' => 'Jadwal lama (ID: '.$jadwal_id_lama.') telah ditolak, namun penjadwalan ulang otomatis gagal: ' . $hasil_penjadwalan['error']
                ]);
            }

        } catch (Exception $e) {
            log_message('error', "Error saat penjadwalan ulang otomatis untuk pengajuan ID {$pengajuan_id_asli}: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Terjadi kesalahan sistem saat penjadwalan ulang: ' . $e->getMessage()]);
        }
    }

    /**
     * Fungsi penjadwalan otomatis yang diadaptasi untuk kasus reschedule.
     * Bertujuan membuat jadwal BARU.
     * * @param array $pengajuan Data pengajuan yang lengkap (termasuk id, mahasiswa_id, tipe_ujian, judul_skripsi, dosen_ids).
     * @param int|null $id_jadwal_lama ID jadwal lama yang ditolak (untuk catatan).
     * @return array ['success' => bool, 'message'/'error' => string, 'new_jadwal_id' => int|null]
     */
    private function penjadwalan_otomatis_untuk_reschedule($pengajuan, $id_jadwal_lama = null) {
        // Validasi data $pengajuan yang esensial
        if (empty($pengajuan['id']) || empty($pengajuan['mahasiswa_id']) || empty($pengajuan['tipe_ujian']) || 
            empty($pengajuan['judul_skripsi']) || empty($pengajuan['pembimbing_id']) || 
            empty($pengajuan['penguji1_id']) || empty($pengajuan['penguji2_id'])) {
            
            $missing_fields = [];
            if (empty($pengajuan['id'])) $missing_fields[] = 'ID Pengajuan';
            if (empty($pengajuan['mahasiswa_id'])) $missing_fields[] = 'ID Mahasiswa';
            if (empty($pengajuan['tipe_ujian'])) $missing_fields[] = 'Tipe Ujian';
            // ... (tambahkan field lain yang wajib)
            $error_msg_detail = "Data pengajuan tidak lengkap untuk penjadwalan ulang. Field yang kurang: " . implode(", ", $missing_fields);
            log_message('error', $error_msg_detail . ". Data Pengajuan: " . json_encode($pengajuan));
            return ['success' => false, 'error' => $error_msg_detail];
        }

        $mhs_id  = $pengajuan['mahasiswa_id'];
        $tipe    = $pengajuan['tipe_ujian'];
        $peng_id = $pengajuan['id']; 
        $judul_skripsi = $pengajuan['judul_skripsi'];
        $dosen_pembimbing = $pengajuan['pembimbing_id'];
        $dosen_penguji1 = $pengajuan['penguji1_id'];
        $dosen_penguji2 = $pengajuan['penguji2_id'];


        log_message('info', "Reschedule - Otomatis dimulai. Pengajuan ID: {$peng_id}, Mhs ID: {$mhs_id}, Tipe: {$tipe}. Jadwal lama: {$id_jadwal_lama}");

        $prd = $this->Penjadwalan_model->get_periode($tipe);
        if (!$prd) {
            $error_msg = "Reschedule - Periode ujian aktif '{$tipe}' tidak ditemukan.";
            log_message('error', "{$error_msg} Pengajuan ID: {$peng_id}");
            $this->Pengajuan_model->update_status_pengajuan($peng_id, 'Gagal Jadwal Ulang', $error_msg); // Update status di tabel pengajuan
            return ['success' => false, 'error' => $error_msg];
        }

        $dates = $this->Penjadwalan_model->generate_date_range($prd['tanggal_mulai'], $prd['tanggal_selesai']);
        $timeSlots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25']; 

        $dosen_ids_terlibat = array_filter([$dosen_pembimbing, $dosen_penguji1, $dosen_penguji2]);

        foreach ($dates as $tgl) {
            $hari = date('N', strtotime($tgl)); 
            if ($hari >= 6) continue; 

            foreach ($timeSlots as $slot) {
                if (empty($dosen_ids_terlibat)){
                    log_message('debug', "Reschedule - Dosen kosong. Pengajuan {$peng_id}, Tgl {$tgl}, Slot {$slot}. Skip.");
                    continue;
                }

                $is_dosen_tersedia = $this->Penjadwalan_model->cek_ketersediaan_dosen($tgl, $slot, $dosen_ids_terlibat);
                if (!$is_dosen_tersedia) {
                    log_message('debug', "Reschedule - Dosen tidak tersedia. Pengajuan {$peng_id}, Tgl {$tgl}, Slot {$slot}. Skip.");
                    continue; 
                }
                
                $ruang = $this->Penjadwalan_model->get_ruangan_available($tipe, $tgl, $slot);
                if ($ruang) {
                    log_message('info', "Reschedule - Ruangan ditemukan. Pengajuan {$peng_id}, Ruang ID {$ruang['id']}, Tgl {$tgl}, Slot {$slot}");
                    
                    $data_jadwal_baru = [
                        'mahasiswa_id'  => $mhs_id,
                        'pengajuan_id'  => $peng_id, 
                        'judul_skripsi' => $judul_skripsi,
                        'tipe_ujian'    => $tipe,
                        'tanggal'       => $tgl,
                        'slot_waktu'    => $slot, 
                        'ruangan_id'    => $ruang['id'],
                        'pembimbing_id' => $dosen_pembimbing,
                        'penguji1_id'   => $dosen_penguji1,
                        'penguji2_id'   => $dosen_penguji2,
                        'status_konfirmasi' => 'Menunggu', 
                        'catatan_kabag' => "Dijadwalkan ulang otomatis dari jadwal ID " . ($id_jadwal_lama ?? 'N/A') // Sesuaikan nama kolom
                    ];

                    $new_jadwal_id = $this->Penjadwalan_model->simpan_jadwal_baru($data_jadwal_baru);

                    if ($new_jadwal_id) {
                        $this->Pengajuan_model->update_status_pengajuan($peng_id, 'Terjadwal Ulang', "Jadwal baru ID: {$new_jadwal_id}", $new_jadwal_id);
                        $success_msg = "Penjadwalan ulang otomatis berhasil. Jadwal baru (ID: {$new_jadwal_id}) dibuat (status Menunggu).";
                        log_message('info', $success_msg . " Pengajuan ID: {$peng_id}");
                        return ['success' => true, 'message' => $success_msg, 'new_jadwal_id' => $new_jadwal_id];
                    } else {
                        log_message('error', "Reschedule - Gagal simpan jadwal baru. Pengajuan ID {$peng_id}. Tgl {$tgl}, Slot {$slot}");
                    }
                } else {
                    log_message('debug', "Reschedule - Ruangan tidak tersedia. Pengajuan {$peng_id}, Tipe {$tipe}, Tgl {$tgl}, Slot {$slot}.");
                }
            }
        }

        $final_error_msg = "Reschedule - Tidak ada slot/ruangan yang cocok ditemukan.";
        log_message('warn', "{$final_error_msg} Pengajuan ID {$peng_id}");
        $this->Pengajuan_model->update_status_pengajuan($peng_id, 'Gagal Jadwal Ulang', $final_error_msg);
        return ['success' => false, 'error' => $final_error_msg];
    }
 public function kelola() {

    $data['title'] = 'Kelola Ruangan';
        $data['sempro'] = $this->Pekan_model->get_jadwal('sempro');
        $data['semhas'] = $this->Pekan_model->get_jadwal('semhas');
        $data['ruangan_sempro'] = $this->Ruangan_model->get_by_tipe('sempro');
        $data['ruangan_semhas'] = $this->Ruangan_model->get_by_tipe('semhas');
    
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_kabag', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('kabag/kelola_ruangan', $data);
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
        
        redirect('kabag/kelola');
    }
 public function profil() {

      $data['title'] = 'Profil saya';
        // $user_id = $this->session->userdata('user_id');
        // $data['profil'] = $this->Kabag_model->get_profile_by_id($user_id);
        
        // Ambil data mahasiswa berdasarkan user (email atau id)
        $data['kabag'] = $this->db->get_where('kabag', ['email' => $this->session->userdata('email')])->row_array();

   

         $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_kabag', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('kabag/profil', $data);
        $this->load->view('templates/footer');
    }
}