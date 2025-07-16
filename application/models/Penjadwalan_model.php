<?php
/**
 * Penjadwalan_model.php
 * Model untuk menangani penjadwalan ujian skripsi prioritas
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjadwalan_model extends CI_Model {
    // Ambil data pengajuan berdasarkan ID
    public function get_pengajuan_by_id($id) {
        return $this->db->get_where('pengajuan_ujian_prioritas', ['id' => $id])->row_array();
    }

    // Ambil data mahasiswa beserta relasi dosen
    public function get_mahasiswa($mahasiswa_id) {
        return $this->db->get_where('mahasiswa', ['id' => $mahasiswa_id])->row_array();
    }

    // Ambil periode ujian berdasarkan tipe (sempro/semhas)
    public function get_periode($tipe_ujian) {
        return $this->db->get_where('jadwal_periode', ['jenis' => strtolower($tipe_ujian)])->row_array();
    }

    // Generate array tanggal dari tanggal mulai sampai tanggal selesai (Senin-Jumat)
 public function hitung_ruangan_menunggu()
    {
        $this->db->from('jadwal_ujian');
        $this->db->where('status_konfirmasi', 'menunggu');
        return $this->db->count_all_results();
    }

    public function hitung_pengajuan_ujian_menunggu()
    {
        // Mengasumsikan nama tabel adalah 'pengajuan_ujian_prioritas'
        // sesuai dengan deskripsi Anda.
        $this->db->from('pengajuan_ujian_prioritas'); 
        $this->db->where('status', 'draft');
        return $this->db->count_all_results();
    }

public function get_all_confirmed_applications_sorted() {
    $current_year = date('Y');
    
    // Logika SELECT dan priority_score sama persis dengan yang sudah kita buat
    $this->db->select("
        pup.*, 
        m.nama AS nama_mahasiswa, m.nim AS nim_mahasiswa, m.tahun_masuk,
        m.pembimbing_id, m.penguji1_id, m.penguji2_id,
        (
            (CASE
                WHEN ($current_year - m.tahun_masuk) >= 7 THEN 5
                WHEN ($current_year - m.tahun_masuk) = 6 THEN 4
                WHEN ($current_year - m.tahun_masuk) = 5 THEN 3
                WHEN ($current_year - m.tahun_masuk) = 4 THEN 2
                ELSE 1
            END) * 5 + 
            (CASE
                WHEN pup.tipe_ujian = 'Semhas' THEN 3
                ELSE 1
            END) * 3
        ) AS priority_score
    ");

    $this->db->from('pengajuan_ujian_prioritas pup');
    $this->db->join('mahasiswa m', 'pup.mahasiswa_id = m.id', 'left');

    // Filter HANYA yang dikonfirmasi DAN belum punya jadwal
    $this->db->where('pup.status', 'dikonfirmasi');
    $this->db->where('pup.jadwal_id IS NULL', null, false);

    // Urutkan berdasarkan prioritas tertinggi
    $this->db->order_by('priority_score', 'DESC');
    $this->db->order_by('pup.tanggal_pengajuan', 'ASC');

    $query = $this->db->get();
    return $query->result_array(); // Kembalikan sebagai array untuk kemudahan
}

    public function penjadwalan_otomatis($pengajuan) {
    // Your existing penjadwalan_otomatis logic here
    // Make sure $this calls refer to Penjadwalan_model methods or loaded models
    // e.g., $mhs = $this->get_mahasiswa($mhs_id);
    // e.g., $prd = $this->get_periode($tipe);

    $mhs_id  = $pengajuan['mahasiswa_id'];
    $tipe    = $pengajuan['tipe_ujian'];
    // Ensure $pengajuan['id'] is the ID from 'pengajuan_ujian_prioritas' table
    $peng_id = $pengajuan['id']; 

    $mhs    = $this->get_mahasiswa($mhs_id); // Assumes get_mahasiswa is in this model
    if (!$mhs) {
        log_message('error', "Penjadwalan otomatis gagal: Mahasiswa ID $mhs_id tidak ditemukan.");
        return false;
    }
    $dosen1 = $mhs['penguji1_id'];
    $dosen2 = $mhs['penguji2_id'];
    $pemb   = $mhs['pembimbing_id'];

    $prd     = $this->get_periode($tipe); // Assumes get_periode is in this model
    if (!$prd) {
        log_message('error', "Penjadwalan otomatis gagal: Periode untuk tipe ujian $tipe tidak ditemukan.");
        return false;
    }
    $dates     = $this->generate_date_range($prd['tanggal_mulai'], $prd['tanggal_selesai']);
    $timeSlots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25'];

    foreach ($dates as $tgl) {
        $hari = date('N', strtotime($tgl));
        if ($hari >= 6) { // Skip Sabtu & Minggu
            continue;
        }

        foreach ($timeSlots as $slot) {
            $is_dosen_tersedia = $this->cek_ketersediaan_dosen(
                $tgl, $slot, [$dosen1, $dosen2, $pemb]
            );

            if (!$is_dosen_tersedia) {
                continue;
            }

            $skor = $this->hitung_prioritas([ // Assumes hitung_prioritas is in this model
                'pembimbing' => $pemb,
                'dosen1'     => $dosen1,
                'dosen2'     => $dosen2,
                'tanggal'    => $tgl,
                'slot'       => $slot,
                'tipe_ujian' => $tipe
            ]);

            if ($skor >= 0.8) { // Threshold for scheduling
                $ruang = $this->get_ruangan_available($tipe, $tgl, $slot); // Assumes this is in model
                if ($ruang) {
                    $jadwal_id = $this->simpan_jadwal([ // Assumes this is in model
                        'mahasiswa_id' => $mhs_id,
                        'pengajuan_id' => $peng_id, 
                        'judul_skripsi'=> $pengajuan['judul_skripsi'],
                        'tipe_ujian'   => $tipe,
                        'tanggal'      => $tgl,
                        'slot'         => $slot,
                        'ruangan_id'   => $ruang['id'],
                        'pembimbing'   => $pemb,
                        'dosen1'       => $dosen1,
                        'dosen2'       => $dosen2
                        // status_konfirmasi will be 'menunggu' by default in simpan_jadwal
                    ]);
                    
                    // Link this new schedule back to the original pengajuan
                    $this->update_jadwal_pengajuan($peng_id, $jadwal_id);
                    log_message('info', "Penjadwalan otomatis berhasil untuk pengajuan ID $peng_id. Jadwal ID baru: $jadwal_id");
                    return true; // Successfully scheduled
                }
            }
        }
    }
    log_message('error', "Penjadwalan otomatis gagal: Tidak ada slot & ruangan yang cocok untuk pengajuan ID $peng_id");
    return false; // Scheduling failed
}

public function reschedule_with_bumping($id_jadwal_lama) {
    $jadwal_gagal = $this->db->get_where('jadwal_ujian', ['id' => $id_jadwal_lama])->row_array();
    if (!$jadwal_gagal) {
        return ['success' => false, 'error' => 'Data jadwal lama tidak ditemukan.'];
    }

    // Hapus jadwal lama dari database agar slotnya bisa dievaluasi kembali
    $this->db->where('id', $id_jadwal_lama)->delete('jadwal_ujian');
    log_message('debug', "Bumping System: Jadwal lama ID {$id_jadwal_lama} dihapus untuk memulai proses reschedule.");
    
    // Panggil fungsi inti yang akan mencari slot baru secara berantai
    return $this->_find_and_assign_slot_cascading($jadwal_gagal);
}

/**
 * FUNGSI PRIVAT INTI: Mencari slot baru, bisa menggeser jadwal lain jika perlu (rekursif).
 * @param array $data_mahasiswa_untuk_dijadwalkan Data lengkap dari jadwal yang sedang dicarikan slot.
 * @return array Hasil proses.
 */
private function _find_and_assign_slot_cascading($data_mahasiswa_untuk_dijadwalkan) {
    $tipe_ujian = $data_mahasiswa_untuk_dijadwalkan['tipe_ujian'];
    $skor_prioritas_saya = $data_mahasiswa_untuk_dijadwalkan['priority_score'];
    $dosen_terlibat = array_filter([
        $data_mahasiswa_untuk_dijadwalkan['pembimbing_id'],
        $data_mahasiswa_untuk_dijadwalkan['penguji1_id'],
        $data_mahasiswa_untuk_dijadwalkan['penguji2_id']
    ]);

    $prd = $this->get_periode($tipe_ujian);
    $dates = $this->generate_date_range($prd['tanggal_mulai'], $prd['tanggal_selesai']);
    $timeSlots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25'];

    foreach ($dates as $tgl) {
        if (date('N', strtotime($tgl)) >= 6) continue;

        foreach ($timeSlots as $slot) {
            if (!$this->cek_ketersediaan_dosen($tgl, $slot, $dosen_terlibat)) continue;
            
            $semua_ruangan = $this->get_all_ruangan_for_type($tipe_ujian);
            foreach ($semua_ruangan as $ruang) {
                $jadwal_yang_menempati = $this->get_jadwal_by_slot($tgl, $slot, $ruang['id']);

                // KASUS 1: SLOT KOSONG - langsung ambil
                if (!$jadwal_yang_menempati) {
                    unset($data_mahasiswa_untuk_dijadwalkan['id']);
                    $data_mahasiswa_untuk_dijadwalkan['tanggal'] = $tgl;
                    $data_mahasiswa_untuk_dijadwalkan['slot_waktu'] = $slot;
                    $data_mahasiswa_untuk_dijadwalkan['ruangan_id'] = $ruang['id'];
                    $data_mahasiswa_untuk_dijadwalkan['status_konfirmasi'] = 'Menunggu';
                    $data_mahasiswa_untuk_dijadwalkan['catatan_kabag'] = 'Hasil reschedule otomatis.';
                    $this->simpan_jadwal_baru($data_mahasiswa_untuk_dijadwalkan);
                    return ['success' => true, 'message' => "Jadwal baru berhasil dibuat pada {$tgl} {$slot}."];
                }

                // KASUS 2: SLOT TERISI - LOGIKA BUMPING
                $skor_prioritas_mereka = $jadwal_yang_menempati['priority_score'];
                if ($jadwal_yang_menempati['status_konfirmasi'] == 'Menunggu' && $skor_prioritas_saya > $skor_prioritas_mereka) {
                    
                    log_message('debug', "BUMPING: Mahasiswa ID {$data_mahasiswa_untuk_dijadwalkan['mahasiswa_id']} (skor {$skor_prioritas_saya}) menggeser jadwal ID {$jadwal_yang_menempati['id']} (skor {$skor_prioritas_mereka}).");

                    $jadwal_yang_digeser = $jadwal_yang_menempati;
                    $this->db->where('id', $jadwal_yang_digeser['id'])->delete('jadwal_ujian');
                    
                    unset($data_mahasiswa_untuk_dijadwalkan['id']);
                    $data_mahasiswa_untuk_dijadwalkan['tanggal'] = $tgl;
                    $data_mahasiswa_untuk_dijadwalkan['slot_waktu'] = $slot;
                    $data_mahasiswa_untuk_dijadwalkan['ruangan_id'] = $ruang['id'];
                    $data_mahasiswa_untuk_dijadwalkan['status_konfirmasi'] = 'Menunggu';
                    $data_mahasiswa_untuk_dijadwalkan['catatan_kabag'] = 'Hasil reschedule (menggeser jadwal lain).';
                    $this->simpan_jadwal_baru($data_mahasiswa_untuk_dijadwalkan);

                    // REKURSIF: Carikan jadwal baru untuk mahasiswa yang tadi digeser
                    return $this->_find_and_assign_slot_cascading($jadwal_yang_digeser);
                }
            }
        }
    }
    
    return ['success' => false, 'error' => "Tidak ditemukan slot kosong atau yang bisa digeser untuk Mahasiswa ID {$data_mahasiswa_untuk_dijadwalkan['mahasiswa_id']}."];
}
public function get_all_ruangan_for_type($tipe_ujian) {
    $this->db->from('ruangan');
    
    // Ruangan bisa digunakan jika tipenya cocok ATAU tipenya 'Semua'
    $this->db->group_start();
    $this->db->where('tipe_seminar', $tipe_ujian);
    $this->db->or_where('tipe_seminar', 'Semua');
    $this->db->group_end();
    
    $query = $this->db->get();
    return $query->result_array();
}
/**
 * FUNGSI PEMBANTU: Mengambil jadwal di slot spesifik.
 * @return array|null
 */
public function get_jadwal_by_slot($tanggal, $slot_waktu, $ruangan_id) {
    return $this->db->get_where('jadwal_ujian', [
        'tanggal' => $tanggal,
        'slot_waktu' => $slot_waktu,
        'ruangan_id' => $ruangan_id
    ])->row_array();
}
// Make sure your get_all_jadwal function selects the pengajuan_id
// For example, if 'jadwal_ujian' table has a column 'pengajuan_ujian_id':
public function get_all_jadwal() {
    $current_year = date('Y');

    $this->db->select("
        jadwal_ujian.id,
        jadwal_ujian.mahasiswa_id,
        mahasiswa.nama AS mahasiswa_nama,
        mahasiswa.email AS mahasiswa_email,
        jadwal_ujian.judul_skripsi,
        jadwal_ujian.tipe_ujian,
        jadwal_ujian.tanggal,
        jadwal_ujian.slot_waktu,
        jadwal_ujian.ruangan_id,
        jadwal_ujian.pembimbing_id,
        jadwal_ujian.penguji1_id,
        jadwal_ujian.penguji2_id,
        jadwal_ujian.status_konfirmasi,
        jadwal_ujian.created_at,
        jadwal_ujian.updated_at,
        ruangan.nama_ruangan,
        pembimbing.nama AS pembimbing_nama,
        penguji1.nama AS penguji1_nama,
        penguji2.nama AS penguji2_nama,
        jadwal_ujian.pengajuan_id,
        (CASE
            WHEN ({$current_year} - mahasiswa.tahun_masuk) >= 7 THEN 5
            WHEN ({$current_year} - mahasiswa.tahun_masuk) = 6 THEN 4
            WHEN ({$current_year} - mahasiswa.tahun_masuk) = 5 THEN 3
            WHEN ({$current_year} - mahasiswa.tahun_masuk) = 4 THEN 2
            ELSE 1
        END) AS nilai_ms
    ");

    $this->db->from('jadwal_ujian');
    $this->db->join('mahasiswa', 'mahasiswa.id = jadwal_ujian.mahasiswa_id', 'left');
    $this->db->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left');
    $this->db->join('dosen AS pembimbing', 'pembimbing.id = jadwal_ujian.pembimbing_id', 'left');
    $this->db->join('dosen AS penguji1', 'penguji1.id = jadwal_ujian.penguji1_id', 'left');
    $this->db->join('dosen AS penguji2', 'penguji2.id = jadwal_ujian.penguji2_id', 'left');

    // ▼▼▼ PERUBAHAN DI SINI ▼▼▼
    // Urutkan berdasarkan tanggal (dari yang paling awal), lalu berdasarkan slot waktu.
    $this->db->order_by('jadwal_ujian.tanggal', 'ASC');
    $this->db->order_by('jadwal_ujian.slot_waktu', 'ASC');
    // ▲▲▲ AKHIR PERUBAHAN ▲▲▲

    $query = $this->db->get();
    return $query->result();
}
    // application/models/Penjadwalan_model.php
 public function hitung_berdasarkan_tipe_ujian($tipe) {
        $this->db->from('jadwal_ujian');
        $this->db->where('tipe_ujian', $tipe);
        return $this->db->count_all_results();
    }
public function get_jadwal_by_mahasiswa($mahasiswa_id) {
    $this->db->select('
        jadwal_ujian.id, 
          mahasiswa.nama AS mahasiswa_nama,
    mahasiswa.nim AS mahasiswa_nim,
        jadwal_ujian.judul_skripsi,
        jadwal_ujian.tipe_ujian,
        jadwal_ujian.tanggal,
        jadwal_ujian.slot_waktu,
        ruangan.nama_ruangan,
        pembimbing.nama AS pembimbing_nama,
        pembimbing.nip AS pembimbing_nip,
        penguji1.nama AS penguji1_nama,
        penguji2.nama AS penguji2_nama,
        jadwal_ujian.status_konfirmasi,
        jadwal_ujian.created_at,
        jadwal_ujian.updated_at
    ');
    $this->db->from('jadwal_ujian');
    $this->db->join('mahasiswa', 'mahasiswa.id = jadwal_ujian.mahasiswa_id', 'left');
    $this->db->join('ruangan',   'ruangan.id   = jadwal_ujian.ruangan_id',   'left');
    $this->db->join('dosen AS pembimbing', 'pembimbing.id = jadwal_ujian.pembimbing_id', 'left');
    $this->db->join('dosen AS penguji1',    'penguji1.id    = jadwal_ujian.penguji1_id',    'left');
    $this->db->join('dosen AS penguji2',    'penguji2.id    = jadwal_ujian.penguji2_id',    'left');
    $this->db->where('jadwal_ujian.mahasiswa_id', $mahasiswa_id);
    // TAMBAHKAN BARIS INI: Filter hanya jadwal yang status_konfirmasinya 'Disetujui'
    $this->db->where('jadwal_ujian.status_konfirmasi', 'Dikonfirmasi'); 
    $this->db->order_by('jadwal_ujian.tanggal, jadwal_ujian.slot_waktu', 'ASC');
    return $this->db->get()->result();
}
// Model: application/models/Penjadwalan_model.php
public function get_jadwal_by_id($id)
{
    $this->db->select('
        jadwal_ujian.id,
         jadwal_ujian.pengajuan_id, 
        mahasiswa.nama AS mahasiswa_nama,
        mahasiswa.nim AS mahasiswa_nim,
        jadwal_ujian.judul_skripsi,
        jadwal_ujian.tipe_ujian,
        jadwal_ujian.tanggal,
        jadwal_ujian.slot_waktu,
        ruangan.nama_ruangan,
        jadwal_ujian.pembimbing_id,
        jadwal_ujian.penguji1_id,
        jadwal_ujian.penguji2_id,
        pembimbing.nama AS pembimbing_nama,
        pembimbing.nip AS pembimbing_nip,
        penguji1.nama AS penguji1_nama,
        penguji2.nama AS penguji2_nama,
        jadwal_ujian.status_konfirmasi,
        jadwal_ujian.created_at,
        jadwal_ujian.updated_at
    ');
    $this->db->from('jadwal_ujian');
    $this->db->join('mahasiswa', 'mahasiswa.id = jadwal_ujian.mahasiswa_id', 'left');
    $this->db->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left');
    $this->db->join('dosen AS pembimbing', 'pembimbing.id = jadwal_ujian.pembimbing_id', 'left');
    $this->db->join('dosen AS penguji1', 'penguji1.id = jadwal_ujian.penguji1_id', 'left');
    $this->db->join('dosen AS penguji2', 'penguji2.id = jadwal_ujian.penguji2_id', 'left');
    
    $this->db->where('jadwal_ujian.id', $id);

    
    
    return $this->db->get()->row(); // satu objek, bukan array
}
// application/models/Penjadwalan_model.php
public function get_jadwal_id($id_jadwal_ujian)
{
    $this->db->select('
        ju.id,
        ju.pengajuan_id, 
        m.nama AS mahasiswa_nama, 
        m.nim AS mahasiswa_nim,
        ju.judul_skripsi,
        ju.tipe_ujian,
        ju.tanggal,
        ju.slot_waktu,
        r.nama_ruangan,
        pemb.nama AS pembimbing_nama,
        pemb.id AS pembimbing_id,
        p1.nama AS penguji1_nama,
        p2.nama AS penguji2_nama,
        ju.status_konfirmasi
    ');
    $this->db->from('jadwal_ujian ju');
    // Join yang lain tetap dibutuhkan untuk mendapatkan detail nama, ruangan, dll.
    $this->db->join('mahasiswa m', 'm.id = ju.mahasiswa_id', 'left');
    $this->db->join('ruangan r', 'r.id = ju.ruangan_id', 'left');
    $this->db->join('dosen pemb', 'pemb.id = ju.pembimbing_id', 'left');
    $this->db->join('dosen p1', 'p1.id = ju.penguji1_id', 'left');
    $this->db->join('dosen p2', 'p2.id = ju.penguji2_id', 'left');
    $this->db->where('ju.id', $id_jadwal_ujian);

    return $this->db->get()->row();
}
public function get_all_reschedule_history() {
    $this->db->select('
        jrh.id AS history_id,
        jrh.original_jadwal_id,
        jrh.new_jadwal_id,
        jrh.requested_by_user_type,
        jrh.requested_by_user_id,
        jrh.request_timestamp,
        jrh.reason_for_reschedule,
        jrh.reschedule_status,
        jrh.kabag_action_timestamp,
        jrh.kabag_notes,
        d.nama AS requester_nama,  -- Nama dosen yang meminta (jika tipe user adalah dosen)
        m_orig.nama AS mahasiswa_nama_orig,
        m_orig.nim AS mahasiswa_nim_orig,
        ju_orig.tipe_ujian AS tipe_ujian_orig,
        ju_orig.tanggal AS tanggal_orig,
        ju_orig.slot_waktu AS slot_waktu_orig,
        r_orig.nama_ruangan AS ruangan_orig,
        ju_new.tanggal AS tanggal_new,
        ju_new.slot_waktu AS slot_waktu_new,
        r_new.nama_ruangan AS ruangan_new
    ');
    $this->db->from('jadwal_reschedule_history jrh');

    // Join untuk mendapatkan nama dosen yang meminta (jika tipe = dosen)
    $this->db->join('dosen d', 'd.id = jrh.requested_by_user_id AND jrh.requested_by_user_type = "dosen"', 'left');

    // Join untuk mendapatkan detail jadwal ujian asli
    $this->db->join('jadwal_ujian ju_orig', 'ju_orig.id = jrh.original_jadwal_id', 'left');
    $this->db->join('mahasiswa m_orig', 'm_orig.id = ju_orig.mahasiswa_id', 'left');
    $this->db->join('ruangan r_orig', 'r_orig.id = ju_orig.ruangan_id', 'left');

    // Join untuk mendapatkan detail jadwal ujian baru (jika new_jadwal_id ada)
    $this->db->join('jadwal_ujian ju_new', 'ju_new.id = jrh.new_jadwal_id', 'left');
    $this->db->join('ruangan r_new', 'r_new.id = ju_new.ruangan_id', 'left');

    $this->db->order_by('jrh.request_timestamp', 'DESC'); // Tampilkan yang terbaru dulu
    $query = $this->db->get();
    return $query->result_array(); // Mengembalikan array of array
}
  public function get_jadwal_by_dosen($id_dosen)
{
    $this->db->select('
        jadwal_ujian.*, 
        mahasiswa.nama as nama_mahasiswa, 
        mahasiswa.id as mahasiswa_id_fk, 
        mahasiswa.status_sempro, 
        mahasiswa.status_semhas, 
        ruangan.nama_ruangan
    ');
    $this->db->from('jadwal_ujian');
    $this->db->join('mahasiswa', 'jadwal_ujian.mahasiswa_id = mahasiswa.id');
    $this->db->join('ruangan', 'jadwal_ujian.ruangan_id = ruangan.id', 'left');

    // Filter umum: hanya jadwal yang sudah dikonfirmasi
    $this->db->where('jadwal_ujian.status_konfirmasi', 'Dikonfirmasi');

    // Filter umum: hanya tampilkan jika hasil ujian belum diinput oleh mahasiswa
    // Ini menjadi filter utama agar jadwal hilang setelah hasil akhir di-submit.
    $this->db->group_start();
        // Kondisi untuk tipe ujian proposal/sempro
        $this->db->group_start();
            $this->db->where_in('LOWER(jadwal_ujian.tipe_ujian)', ['proposal', 'sempro']);
            $this->db->where('mahasiswa.status_sempro', 'Belum Mengajukan');
        $this->db->group_end();
        // ATAU
        // Kondisi untuk tipe ujian hasil/sidang
        $this->db->or_group_start();
            $this->db->where_in('LOWER(jadwal_ujian.tipe_ujian)', ['hasil', 'semhas', 'skripsi', 'sidang']);
            $this->db->where('mahasiswa.status_semhas', 'Belum Mengajukan');
        $this->db->group_end();
    $this->db->group_end();

    // Dapatkan tanggal hari ini untuk perbandingan
    $today = date('Y-m-d');

    // KONDISI UTAMA: Terapkan aturan berbeda untuk Pembimbing dan Penguji
    $this->db->group_start();

        // ATURAN 1: Jika dosen adalah PEMBIMBING, jadwal SELALU tampil (selama kondisi status di atas terpenuhi)
        $this->db->where('jadwal_ujian.pembimbing_id', $id_dosen);

        // ATAU

        // ATURAN 2: Jika dosen adalah PENGUJI, jadwal HANYA tampil jika tanggal ujian >= hari ini
        $this->db->or_group_start();
            // Kondisi peran sebagai penguji
            $this->db->group_start();
                $this->db->where('jadwal_ujian.penguji1_id', $id_dosen);
                $this->db->or_where('jadwal_ujian.penguji2_id', $id_dosen);
            $this->db->group_end();
            
            // DAN kondisi tanggal belum terlewat
            $this->db->where('jadwal_ujian.tanggal >=', $today);
        $this->db->group_end();

    $this->db->group_end();


    $this->db->order_by('jadwal_ujian.tanggal', 'ASC');
    return $this->db->get()->result_array();
}

    public function update_status_mahasiswa($mahasiswa_id, $tipe_ujian, $status_hasil)
    {
        $data = [];
        // Sesuaikan 'proposal'/'sempro' dan 'hasil'/'semhas' dengan value di `tipe_ujian` Anda
        $tipe_ujian_lower = strtolower($tipe_ujian);

        if ($tipe_ujian_lower == 'proposal' || $tipe_ujian_lower == 'sempro') {
            $data['status_sempro'] = $status_hasil;
        } elseif ($tipe_ujian_lower == 'hasil' || $tipe_ujian_lower == 'semhas' || $tipe_ujian_lower == 'skripsi' || $tipe_ujian_lower == 'sidang') {
            $data['status_semhas'] = $status_hasil;
        } else {
            // Tipe ujian tidak dikenali untuk update status ini
            return false; 
        }

        if (!empty($data)) {
            $this->db->where('id', $mahasiswa_id);
            return $this->db->update('mahasiswa', $data);
        }
        return false;
    }
public function updateStatus($id, $status)
{
    $this->db->where('id', $id);
    $this->db->update('jadwal_ujian', ['status_konfirmasi' => $status]);

    if ($this->db->affected_rows() > 0) {
        return true;
    } else {
        log_message('error', 'Update gagal untuk ID: ' . $id);
        return false;
    }
}

    public function generate_date_range($start_date, $end_date) {
        $dates = [];
        $current = strtotime($start_date);
        $end     = strtotime($end_date);
        while ($current <= $end) {
            $dow = date('N', $current); // 1 (Mon) ... 7 (Sun)
            if ($dow >= 1 && $dow <= 5) {
                $dates[] = date('Y-m-d', $current);
            }
            $current = strtotime('+1 day', $current);
        }
        return $dates;
    }

   

public function cek_ketersediaan_dosen($tanggal, $slot, $dosen_ids, $ignore_jadwal_id = null) {
        if (empty($dosen_ids) || !is_array($dosen_ids)) {
            return true;
        }
        
        // 1. Cek di agenda pribadi (tidak berubah)
        $this->db->where('tanggal', $tanggal)->where_in('id_dosen', $dosen_ids)->like('slot_waktu', $slot);
        if ($this->db->get('agenda_dosen')->num_rows() > 0) {
            return false;
        }

        // 2. Cek di tabel jadwal ujian dengan logika baru yang lebih ketat
        $this->db->from('jadwal_ujian');
        $this->db->where('tanggal', $tanggal);
        $this->db->where('slot_waktu', $slot);
        
        // ===== PERUBAHAN KUNCI #1 =====
        // Abaikan jadwal spesifik yang sedang kita coba pindahkan agar slot aslinya bisa "kosong"
        if ($ignore_jadwal_id) {
            $this->db->where('id !=', $ignore_jadwal_id);
        }
        // ==============================

        // Cek apakah salah satu dosen terlibat di jadwal LAIN (apapun statusnya)
        $this->db->group_start();
        $this->db->where_in('pembimbing_id', $dosen_ids);
        $this->db->or_where_in('penguji1_id', $dosen_ids);
        $this->db->or_where_in('penguji2_id', $dosen_ids);
        $this->db->group_end();
        
     
        $bentrok_di_jadwal = $this->db->count_all_results();

        // Dosen hanya tersedia jika tidak ada bentrok sama sekali di jadwal lain.
        return ($bentrok_di_jadwal == 0);
    }

    /**
     * Cek dan ambil satu ruangan kosong dengan logika yang ketat.
     * Ruangan dianggap terpakai jika ada jadwal LAIN (apapun statusnya) di slot tersebut.
     *
     * @param string $tipe_ujian
     * @param string $tanggal
     * @param string $slot
     * @param int|null $ignore_jadwal_id ID jadwal yang sedang diproses (untuk diabaikan dalam pengecekan)
     * @return array|null
     */
    public function get_ruangan_available($tipe_ujian, $tanggal, $slot, $ignore_jadwal_id = null) {
        // Ambil semua ID ruangan yang sudah terisi oleh jadwal LAIN (apapun statusnya)
        $this->db->select('ruangan_id')
                 ->from('jadwal_ujian')
                 ->where('tanggal', $tanggal)
                 ->where('slot_waktu', $slot);

        // ===== PERUBAHAN KUNCI #3 =====
        // Abaikan jadwal spesifik yang sedang kita coba pindahkan
        if ($ignore_jadwal_id) {
            $this->db->where('id !=', $ignore_jadwal_id);
        }
        
        $used_ids_query = $this->db->get();
        $used_ids = array_column($used_ids_query->result_array(), 'ruangan_id');

        // Cari ruangan yang sesuai tipe DAN TIDAK ADA di daftar yang terpakai
        $this->db->select('*')->from('ruangan')->where('tipe_seminar', strtolower($tipe_ujian));
        if (!empty($used_ids)) {
            $this->db->where_not_in('id', $used_ids);
        }
        
        return $this->db->limit(1)->get()->row_array();
    }

    
    

    // Hitung skor prioritas
    public function hitung_prioritas($params) {
        $km = 1; // mahasiswa sudah konfirmasi
        $db = $this->cek_ketersediaan_dosen($params['pembimbing'], $params['tanggal'], $params['slot']) ? 1 : 0;
        $dp = ($this->cek_ketersediaan_dosen($params['dosen1'], $params['tanggal'], $params['slot'])
             && $this->cek_ketersediaan_dosen($params['dosen2'], $params['tanggal'], $params['slot'])) ? 1 : 0;
        $r  = $this->is_ruangan_available($params['tipe_ujian'], $params['tanggal'], $params['slot']) ? 1 : 0;
        $w  = ($db && $dp) ? 1 : 0;
        return 0.4*$km + 0.2*$db + 0.2*$dp + 0.1*$r + 0.1*$w;
    }

    // Cek dan ambil satu ruangan kosong sesuai tipe, tanggal, slot
   

    private function is_ruangan_available($tipe_ujian, $tanggal, $slot) {
        return (bool) $this->get_ruangan_available($tipe_ujian, $tanggal, $slot);
    }

    // Simpan jadwal ujian
    public function simpan_jadwal($data) {
        $ins = [
            'mahasiswa_id'   => $data['mahasiswa_id'],
            'judul_skripsi'  => $data['judul_skripsi'],
            'tipe_ujian'     => $data['tipe_ujian'],
            'tanggal'        => $data['tanggal'],
            'slot_waktu'     => $data['slot'],
            'ruangan_id'     => $data['ruangan_id'],
            'pembimbing_id'  => $data['pembimbing'],
            'penguji1_id'    => $data['dosen1'],
            'penguji2_id'    => $data['dosen2'],
            'status_konfirmasi' => 'menunggu',
            'created_at'     => date('Y-m-d H:i:s')
        ];
        $this->db->insert('jadwal_ujian', $ins);
        return $this->db->insert_id();
    }
public function simpan_jadwal_baru($data) {
    // Fungsi ini sangat sederhana: hanya menerima array data dan memasukkannya ke tabel.
    // Ini memastikan tidak ada kolom yang tertinggal atau nama yang salah.
    $this->db->insert('jadwal_ujian', $data);
    
    // Mengembalikan ID dari baris yang baru saja dibuat
    return $this->db->insert_id();
}
    // Update pengajuan dengan jadwal_id
    public function update_jadwal_pengajuan($pengajuan_id, $jadwal_id) {
        $this->db->where('id', $pengajuan_id)
                 ->update('pengajuan_ujian_prioritas', ['jadwal_id' => $jadwal_id]);
    }

    // Simpan konfirmasi tambahan jika diperlukan
    public function simpan_konfirmasi($data) {
        $this->db->insert('konfirmasi_pengajuan', $data);
    }

public function attempt_reschedule_logic($pengajuan, $objek_jadwal_lama = null, $catatan_untuk_jadwal_baru = "Dijadwalkan ulang otomatis")
{
    // Validasi data pengajuan awal (tetap sama)
    if (empty($pengajuan['id'])) {
        $error_msg_detail = "Data pengajuan tidak lengkap untuk penjadwalan ulang.";
        log_message('error', $error_msg_detail . ". Data Pengajuan: " . json_encode($pengajuan));
        return ['success' => false, 'error' => $error_msg_detail, 'new_jadwal_id' => null];
    }

    // Ekstrak data yang diperlukan (tetap sama)
    $mhs_id = $pengajuan['mahasiswa_id'];
    $tipe = $pengajuan['tipe_ujian'];
    $peng_id = $pengajuan['id'];
    $judul_skripsi = $pengajuan['judul_skripsi'];
    $dosen_pembimbing = $pengajuan['pembimbing_id'];
    $dosen_penguji1 = $pengajuan['penguji1_id'];
    $dosen_penguji2 = $pengajuan['penguji2_id'];

    log_message('info', "Mencoba Penjadwalan Ulang - Otomatis. Pengajuan ID: {$peng_id}, Mhs ID: {$mhs_id}, Tipe: {$tipe}. Jadwal lama ID: " . ($objek_jadwal_lama->id ?? 'N/A'));

    // Ambil periode ujian yang aktif (tetap sama)
    $prd = $this->get_periode($tipe);
    if (!$prd) {
        return ['success' => false, 'error' => "Periode ujian aktif '{$tipe}' tidak ditemukan.", 'new_jadwal_id' => null];
    }

    // Siapkan data untuk perulangan (tetap sama)
    $dates = $this->generate_date_range($prd['tanggal_mulai'], $prd['tanggal_selesai']);
    $timeSlots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25'];
    $dosen_ids_terlibat = array_filter([$dosen_pembimbing, $dosen_penguji1, $dosen_penguji2]);

    if (count($dosen_ids_terlibat) < 3) {
        return ['success' => false, 'error' => 'Data dosen penguji/pembimbing tidak lengkap.', 'new_jadwal_id' => null];
    }

    // <-- [PERUBAHAN] Dapatkan index dari slot waktu yang lama untuk perbandingan
    $start_date = $objek_jadwal_lama->tanggal ?? null;
    $start_slot_index = -1; // Default jika tidak ada jadwal lama
    if ($start_date) {
        $start_slot_index = array_search($objek_jadwal_lama->slot_waktu, $timeSlots);
        if ($start_slot_index === false) $start_slot_index = -1; // Handle jika slot tidak ditemukan
    }
    
    // Mulai perulangan untuk mencari slot yang cocok
    foreach ($dates as $tgl) {
        $hari = date('N', strtotime($tgl));
        if ($hari >= 6) continue; // Lewati hari Sabtu & Minggu

        // <-- [PERUBAHAN] Lewati semua tanggal SEBELUM tanggal reschedule
        if ($start_date && $tgl < $start_date) {
            continue;
        }

        foreach ($timeSlots as $index => $slot) {

            // <-- [PERUBAHAN] Jika di tanggal yang sama, lewati semua slot SEBELUM atau SAMA DENGAN slot yang di-reschedule
            if ($start_date && $tgl == $start_date && $index <= $start_slot_index) {
                log_message('debug', "Reschedule - Melewati slot lama atau sebelumnya. Tgl: {$tgl}, Slot: {$slot}");
                continue;
            }

            // --- Logika selanjutnya tetap sama ---

            // 1. Cek ketersediaan semua dosen yang terlibat (tidak ada bentrok)
            $is_dosen_tersedia = $this->cek_ketersediaan_dosen($tgl, $slot, $dosen_ids_terlibat);
            if (!$is_dosen_tersedia) {
                log_message('debug', "Reschedule - Dosen tidak tersedia. Pengajuan {$peng_id}, Tgl {$tgl}, Slot {$slot}. Lewati.");
                continue;
            }

            // 2. Cek ketersediaan ruangan
            $ruang = $this->get_ruangan_available($tipe, $tgl, $slot);
            if ($ruang) {
                log_message('info', "Reschedule - Ruangan ditemukan. Pengajuan {$peng_id}, Ruang ID {$ruang['id']}, Tgl {$tgl}, Slot {$slot}");
                
                // Siapkan data untuk disimpan ke database
                $data_jadwal_baru = [
                    'mahasiswa_id'      => $mhs_id,
                    'pengajuan_id'      => $peng_id,
                    'judul_skripsi'     => $judul_skripsi,
                    'tipe_ujian'        => $tipe,
                    'tanggal'           => $tgl,
                    'slot_waktu'        => $slot,
                    'ruangan_id'        => $ruang['id'],
                    'pembimbing_id'     => $dosen_pembimbing,
                    'penguji1_id'       => $dosen_penguji1,
                    'penguji2_id'       => $dosen_penguji2,
                    'status_konfirmasi' => 'Menunggu',
                    'catatan_kabag'     => $catatan_untuk_jadwal_baru
                ];

                // Simpan jadwal baru dan dapatkan ID-nya
                $new_jadwal_id = $this->simpan_jadwal_baru($data_jadwal_baru);

                if ($new_jadwal_id) {
                    // Update status di tabel pengajuan
                    $this->load->model('Pengajuan_model');
                    $this->Pengajuan_model->update_status_pengajuan(
                        $peng_id,
                        'Terjadwal Ulang Menunggu Kabag',
                        "Jadwal baru ID: {$new_jadwal_id}",
                        $new_jadwal_id
                    );
                    
                    $success_msg = "Penjadwalan ulang otomatis berhasil. Jadwal baru (ID: {$new_jadwal_id}) dibuat.";
                    log_message('info', $success_msg . " Pengajuan ID: {$peng_id}");

                    // Jadwal berhasil dibuat, hentikan proses dan kembalikan hasil
                    return ['success' => true, 'message' => $success_msg, 'new_jadwal_id' => $new_jadwal_id];
                } else {
                    log_message('error', "Reschedule - Gagal simpan jadwal baru. Pengajuan ID {$peng_id}. Tgl {$tgl}, Slot {$slot}");
                }
            } else {
                log_message('debug', "Reschedule - Ruangan tidak tersedia. Pengajuan {$peng_id}, Tipe {$tipe}, Tgl {$tgl}, Slot {$slot}.");
            }
        } // Akhir loop slot
    } // Akhir loop tanggal

    // Jika loop selesai tanpa menemukan jadwal
    $final_error_msg = "Reschedule - Tidak ada slot atau ruangan yang cocok ditemukan setelah mencari di seluruh periode.";
    log_message('warn', "{$final_error_msg} Pengajuan ID {$peng_id}");
    
    return ['success' => false, 'error' => $final_error_msg, 'new_jadwal_id' => null];
}
}
