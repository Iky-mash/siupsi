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
    
         $data['status_summary'] = $this->Penjadwalan_model->get_status_summary();


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

        // Definisikan juga konfigurasi file agar view bisa membuat link yang benar
        // Ini duplikat dari controller Pengajuan, mungkin bisa ditaruh di helper atau config
        $data['sempro_files_config'] = [
            ['name' => 'file_ktm', 'label' => 'KTM'], ['name' => 'file_krs', 'label' => 'KRS'],
            ['name' => 'file_surat_pengesahan_proposal', 'label' => 'Surat Pengesahan Proposal'],
            ['name' => 'file_bukti_pembayaran', 'label' => 'Bukti Pembayaran'], ['name' => 'file_transkrip_nilai', 'label' => 'Transkrip Nilai'],
            ['name' => 'file_nota_dinas_pembimbing', 'label' => 'Nota Dinas Pembimbing'], ['name' => 'file_proposal_skripsi', 'label' => 'File Proposal']
        ];
        $data['semhas_files_config'] = [
            ['name' => 'file_ktm', 'label' => 'KTM'], ['name' => 'file_krs', 'label' => 'KRS'],
            ['name' => 'file_lembar_pengesahan_skripsi', 'label' => 'Lembar Pengesahan Skripsi'], ['name' => 'file_lembar_ec', 'label' => 'Lembar EC'],
            ['name' => 'file_lembar_spm', 'label' => 'Lembar SPM'], ['name' => 'file_bukti_pembayaran', 'label' => 'Bukti Pembayaran'],
            ['name' => 'file_transkrip_nilai', 'label' => 'Transkrip Nilai'], ['name' => 'file_nota_dinas_pembimbing', 'label' => 'Nota Dinas Pembimbing'],
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

    // 1. Ambil data pengajuan terlebih dahulu untuk mendapatkan mahasiswa_id yang benar
    $pengajuan = $this->Pengajuan_model->get_pengajuan_by_id($pengajuan_id);

    if (!$pengajuan) {
        $this->session->set_flashdata('error', 'Data pengajuan tidak ditemukan.');
        redirect('kabag/berkas'); // Atau halaman error yang sesuai
        return; // Hentikan eksekusi jika pengajuan tidak ada
    }

    // Ambil mahasiswa_id dari data pengajuan
    $mahasiswa_id_pengaju = $pengajuan['mahasiswa_id']; // Ini adalah ID mahasiswa yang mengajukan

    if ($this->Pengajuan_model->update_pengajuan_status($pengajuan_id, 'dikonfirmasi')) {
        $this->session->set_flashdata('success', 'Pengajuan berhasil dikonfirmasi.');
        // TODO: Kirim notifikasi email ke mahasiswa jika perlu
    } else {
        $this->session->set_flashdata('error', 'Gagal memproses konfirmasi pengajuan.');
        redirect('kabag/berkas'); // Redirect jika update status gagal
        return;
    }

    // Data untuk tabel konfirmasi_pengajuan
    $data_konfirmasi = [
        'pengajuan_id'      => $pengajuan_id,
        'mahasiswa_id'      => $mahasiswa_id_pengaju, // Gunakan ID mahasiswa dari data pengajuan
        'tanggal_konfirmasi'=> date('Y-m-d'),
        'status_konfirmasi' => 'Terkonfirmasi'
    ];

    // Simpan ke tabel konfirmasi_pengajuan
    if (!$this->db->insert('konfirmasi_pengajuan', $data_konfirmasi)) {
        // Tangani jika insert ke konfirmasi_pengajuan gagal
        $db_error = $this->db->error();
        $this->session->set_flashdata('error', 'Gagal menyimpan data konfirmasi. DB Error: ' . $db_error['message']);
        // Mungkin perlu rollback update status sebelumnya jika insert ini gagal
        redirect('kabag/berkas');
        return;
    }

    // Update status di pengajuan_ujian_prioritas menjadi 'dikonfirmasi' (ini mungkin sudah dilakukan oleh update_pengajuan_status di atas, pastikan tidak duplikat)
    // Jika update_pengajuan_status sudah mengurus ini, baris berikut bisa jadi tidak perlu:
    // $this->db->where('id', $pengajuan_id);
    // $this->db->update('pengajuan_ujian_prioritas', ['status' => 'dikonfirmasi']);

    // Panggil fungsi penjadwalan otomatis
    // Data $pengajuan sudah kita dapatkan di awal, jadi bisa langsung digunakan
    $this->penjadwalan_otomatis($pengajuan);

    redirect('kabag/berkas');
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

public function update_status() {
    $id_jadwal_yang_diproses = $this->input->post('id'); // Ini adalah jadwal_ujian.id
    $status_baru = $this->input->post('status_konfirmasi');
    $alasan_penolakan = $this->input->post('rejection_reason'); // 'room_unavailable' atau 'all_rooms_full'

    log_message('debug', "Kabag->update_status: Memproses jadwal_ujian.id = {$id_jadwal_yang_diproses}, Status Baru = {$status_baru}, Alasan Penolakan = {$alasan_penolakan}");

    $data_update_untuk_jadwal_ujian = ['status_konfirmasi' => $status_baru];
    if ($status_baru == 'Ditolak') {
        if ($alasan_penolakan == 'room_unavailable') {
            $data_update_untuk_jadwal_ujian['catatan_kabag'] = 'Ditolak - Ruangan yang dipilih tidak tersedia.';
        } elseif ($alasan_penolakan == 'all_rooms_full') {
            $data_update_untuk_jadwal_ujian['catatan_kabag'] = 'Ditolak - Semua ruangan penuh, proses penjadwalan ulang otomatis akan dicoba.';
        }
    }

    // Update status di tabel jadwal_ujian
    $this->db->where('id', $id_jadwal_yang_diproses);
    $updated = $this->db->update('jadwal_ujian', $data_update_untuk_jadwal_ujian);

    if ($updated) {
        $this->session->set_flashdata('success', 'Status jadwal berhasil diperbarui.');

        // Jika status 'Ditolak' dan alasan 'all_rooms_full', coba lakukan penjadwalan ulang
        if ($status_baru == 'Ditolak' && $alasan_penolakan == 'all_rooms_full') {
            log_message('debug', "Kabag->update_status: Status DITOLAK karena semua ruangan penuh untuk jadwal_ujian.id = {$id_jadwal_yang_diproses}. Mencoba penjadwalan ulang.");

            // 1. Cari data pengajuan_ujian_prioritas yang terkait dengan jadwal_ujian yang ditolak ini
            // Asumsi: pengajuan_ujian_prioritas.jadwal_id merujuk ke jadwal_ujian.id
            $pengajuan_terkait = $this->db->get_where('pengajuan_ujian_prioritas', ['jadwal_id' => $id_jadwal_yang_diproses])->row_array();

            if ($pengajuan_terkait) {
                $id_pengajuan_asli = $pengajuan_terkait['id'];
                log_message('debug', "Kabag->update_status: Ditemukan pengajuan_ujian_prioritas terkait dengan id = {$id_pengajuan_asli}. Data: " . json_encode($pengajuan_terkait));

                // 2. Ambil detail dosen dari jadwal_ujian yang lama (yang baru saja ditolak)
                // karena $pengajuan_terkait mungkin tidak menyimpan ID dosen secara langsung.
                $detail_jadwal_lama = $this->db->get_where('jadwal_ujian', ['id' => $id_jadwal_yang_diproses])->row_array();

                if ($detail_jadwal_lama) {
                    // 3. Siapkan data lengkap untuk fungsi penjadwalan_otomatis_untuk_reschedule
                    $data_untuk_reschedule = [
                        'id'            => $id_pengajuan_asli, // ID dari tabel pengajuan_ujian_prioritas
                        'mahasiswa_id'  => $pengajuan_terkait['mahasiswa_id'],
                        'tipe_ujian'    => $pengajuan_terkait['tipe_ujian'],
                        'judul_skripsi' => $pengajuan_terkait['judul_skripsi'],
                        'pembimbing_id' => $detail_jadwal_lama['pembimbing_id'], // Ambil dari jadwal lama
                        'penguji1_id'   => $detail_jadwal_lama['penguji1_id'],   // Ambil dari jadwal lama
                        'penguji2_id'   => $detail_jadwal_lama['penguji2_id'],   // Ambil dari jadwal lama
                        // Tambahkan field lain jika diperlukan oleh penjadwalan_otomatis_untuk_reschedule
                    ];

                    log_message('debug', "Kabag->update_status: Data lengkap untuk reschedule: " . json_encode($data_untuk_reschedule));

                    // 4. Panggil fungsi penjadwalan_otomatis_untuk_reschedule dari controller Kabag
                    // Fungsi ini akan mencoba membuat jadwal BARU.
                    $hasil_reschedule = $this->penjadwalan_otomatis_untuk_reschedule($data_untuk_reschedule, $id_jadwal_yang_diproses);

                    if ($hasil_reschedule['success']) {
                        // Jika reschedule berhasil, jadwal_id di pengajuan_ujian_prioritas akan diupdate oleh penjadwalan_otomatis_untuk_reschedule
                        $this->session->set_flashdata('success', 'Status jadwal diperbarui menjadi Ditolak. ' . $hasil_reschedule['message']);
                    } else {
                        // Jika reschedule gagal, jadwal_id di pengajuan_ujian_prioritas mungkin perlu di-NULL-kan lagi
                        // atau status pengajuannya diubah menjadi 'Gagal Jadwal Ulang Total'
                        $this->db->where('id', $id_pengajuan_asli)->update('pengajuan_ujian_prioritas', ['status' => 'Gagal Jadwal Ulang (Kabag)', 'jadwal_id' => NULL]);
                        $this->session->set_flashdata('error', 'Status jadwal diperbarui menjadi Ditolak, namun penjadwalan ulang otomatis GAGAL: ' . $hasil_reschedule['error'] . ' Pengajuan dikembalikan ke status gagal terjadwal.');
                    }
                } else {
                    log_message('error', "Kabag->update_status: Gagal mendapatkan detail dari jadwal_ujian.id = {$id_jadwal_yang_diproses} untuk melengkapi data reschedule.");
                    $this->session->set_flashdata('error', 'Status jadwal diperbarui, namun gagal mendapatkan detail jadwal lama untuk proses penjadwalan ulang.');
                }
            } else {
                log_message('error', "Kabag->update_status: Tidak ditemukan pengajuan_ujian_prioritas yang tertaut (via kolom jadwal_id) dengan jadwal_ujian.id = {$id_jadwal_yang_diproses}. Tidak bisa melakukan reschedule otomatis.");
                $this->session->set_flashdata('error', 'Status jadwal diperbarui, namun gagal memicu penjadwalan ulang otomatis: Tidak ada data pengajuan asli yang tertaut dengan jadwal ini untuk dijadwalkan ulang.');
            }
        }
    } else {
        $this->session->set_flashdata('error', 'Gagal memperbarui status jadwal atau tidak ada perubahan status.');
    }

    redirect('kabag/pengajuan_ruangan');
}

    /**
     * Fungsi untuk mendapatkan rekomendasi ruangan alternatif melalui AJAX.
     */
  // In Kabag.php controller

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