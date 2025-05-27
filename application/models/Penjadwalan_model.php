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
public function get_status_summary()
{
    $this->db->select('status_konfirmasi, COUNT(*) as total');
    $this->db->group_by('status_konfirmasi');
    return $this->db->get('jadwal_ujian')->result();
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


// Make sure your get_all_jadwal function selects the pengajuan_id
// For example, if 'jadwal_ujian' table has a column 'pengajuan_ujian_id':
public function get_all_jadwal() {
    $this->db->select('
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
        jadwal_ujian.id AS pengajuan_id  
                                                    
    ');
    $this->db->from('jadwal_ujian');
    $this->db->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left');
    $this->db->join('dosen AS pembimbing', 'pembimbing.id = jadwal_ujian.pembimbing_id', 'left');
    $this->db->join('dosen AS penguji1', 'penguji1.id = jadwal_ujian.penguji1_id', 'left');
    $this->db->join('dosen AS penguji2', 'penguji2.id = jadwal_ujian.penguji2_id', 'left');
    $this->db->join('mahasiswa', 'mahasiswa.id = jadwal_ujian.mahasiswa_id', 'left'); 
    // If pengajuan_ujian_id is in a different table that needs joining, adjust accordingly.
    $query = $this->db->get();
    return $query->result();
}
    // application/models/Penjadwalan_model.php

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
        pup.id AS pengajuan_id,  
        m.nama AS mahasiswa_nama, 
        m.nim AS mahasiswa_nim,
        ju.judul_skripsi,
        ju.tipe_ujian,
        ju.tanggal,
        ju.slot_waktu,
        r.nama_ruangan,
        pemb.nama AS pembimbing_nama,
        pemb.id AS pembimbing_id,
        pemb.nip AS pembimbing_nip,
        p1.nama AS penguji1_nama,
        p2.nama AS penguji2_nama,
        ju.status_konfirmasi,
        ju.created_at,
        ju.updated_at
    ');
    $this->db->from('jadwal_ujian ju');
    // JOIN ke pengajuan_ujian_prioritas untuk mendapatkan pengajuan_id
    // Asumsi: pengajuan_ujian_prioritas memiliki kolom 'jadwal_id' yang merujuk ke 'jadwal_ujian.id'
    $this->db->join('pengajuan_ujian_prioritas pup', 'pup.jadwal_id = ju.id', 'left');
    $this->db->join('mahasiswa m', 'm.id = ju.mahasiswa_id', 'left'); // atau m.id = pup.mahasiswa_id jika lebih relevan
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

        // Kondisi dosen sebagai pembimbing atau penguji
        $this->db->group_start();
            $this->db->where('jadwal_ujian.pembimbing_id', $id_dosen);
            $this->db->or_where('jadwal_ujian.penguji1_id', $id_dosen);
            $this->db->or_where('jadwal_ujian.penguji2_id', $id_dosen);
        $this->db->group_end();

        // Filter hanya jadwal yang status_konfirmasinya 'Dikonfirmasi'
        $this->db->where('jadwal_ujian.status_konfirmasi', 'Dikonfirmasi');

        // Filter berdasarkan tipe ujian DAN status mahasiswa yang 'Belum Mengajukan'.
        // Ini akan membuat jadwal hilang dari daftar setelah dosen melakukan aksi (ACC/Mengulang),
        // dengan asumsi aksi tersebut mengubah status_sempro/status_semhas mahasiswa.
        $this->db->group_start(); // Grup utama untuk kondisi OR

            // Kondisi untuk tipe ujian proposal atau sempro
            $this->db->group_start(); // Grup untuk kondisi proposal/sempro
                $this->db->where_in('LOWER(jadwal_ujian.tipe_ujian)', ['proposal', 'sempro']);
                $this->db->where('mahasiswa.status_sempro', 'Belum Mengajukan');
            $this->db->group_end(); // Akhir grup untuk kondisi proposal/sempro

            // Kondisi untuk tipe ujian hasil, semhas, skripsi, atau sidang
            $this->db->or_group_start(); // Grup untuk kondisi hasil/semhas/dll.
                $this->db->where_in('LOWER(jadwal_ujian.tipe_ujian)', ['hasil', 'semhas', 'skripsi', 'sidang']);
                $this->db->where('mahasiswa.status_semhas', 'Belum Mengajukan');
            $this->db->group_end(); // Akhir grup untuk kondisi hasil/semhas/dll.

            // Catatan: Jika Anda menggunakan kolom `is_result_inputted` di `jadwal_ujian`
            // sebagai flag utama bahwa dosen telah menginput hasil, Anda bisa juga
            // menambahkan atau mengganti filter di atas dengan:
            // $this->db->where('jadwal_ujian.is_result_inputted', 0); // atau false
            // Namun, karena view Anda bergantung pada status_sempro/semhas untuk tombol,
            // memfilter berdasarkan status tersebut di model adalah yang paling konsisten.

        $this->db->group_end(); // Akhir grup utama untuk kondisi OR

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

   

public function cek_ketersediaan_dosen($tanggal, $slot, $dosen_ids) {
    $tanggal = date('Y-m-d', strtotime($tanggal));

    if (!is_array($dosen_ids)) {
        $dosen_ids = [$dosen_ids];
    }

    if (empty($tanggal) || empty($slot)) {
        return false;
    }

    if (empty($dosen_ids)) {
        return true;
    }

    $this->db->where('tanggal', $tanggal);
    $this->db->where_in('id_dosen', $dosen_ids);
    $this->db->like('slot_waktu', $slot); // Cek apakah slot ada dalam string

    $query1 = $this->db->get('agenda_dosen');

    if ($query1->num_rows() > 0) {
        return false; // Ada bentrok di agenda_dosen
    }

    // Cek juga jadwal_ujian yang sama seperti sebelumnya
    $this->db->where('tanggal', $tanggal);
    $this->db->where('slot_waktu', $slot);
    $this->db->group_start();
    foreach ($dosen_ids as $id) {
        $this->db->or_where('pembimbing_id', $id);
        $this->db->or_where('penguji1_id', $id);
        $this->db->or_where('penguji2_id', $id);
    }
    $this->db->group_end();

    $query2 = $this->db->get('jadwal_ujian');

    return $query2->num_rows() === 0;
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
    public function get_ruangan_available($tipe_ujian, $tanggal, $slot) {
        // Ambil semua ruangan yang dipakai pada tanggal & slot tersebut
        $used = $this->db->select('ruangan_id')
                         ->from('jadwal_ujian')
                         ->where('tanggal', $tanggal)
                         ->where('slot_waktu', $slot)
                         ->get()
                         ->result_array();
        $used_ids = array_column($used, 'ruangan_id');

        // Query ruangan sesuai tipe ujian
        $this->db->select('*')->from('ruangan')
                 ->where('tipe_seminar', strtolower($tipe_ujian));
        if (!empty($used_ids)) {
            $this->db->where_not_in('id', $used_ids);
        }
        $res = $this->db->get()->result_array();
        return !empty($res) ? $res[0] : null;
    }

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
    // Data yang akan dimasukkan ke tabel jadwal_ujian
    $ins = [
        'mahasiswa_id'    => $data['mahasiswa_id'],
        'judul_skripsi'   => $data['judul_skripsi'],
        'tipe_ujian'      => $data['tipe_ujian'],
        'tanggal'         => $data['tanggal'],
        'slot_waktu'      => $data['slot_waktu'],    // PASTIKAN MENGGUNAKAN KEY INI
        'ruangan_id'      => $data['ruangan_id'],
        'pembimbing_id'   => $data['pembimbing_id'], // PASTIKAN MENGGUNAKAN KEY INI
        'penguji1_id'     => $data['penguji1_id'],   // PASTIKAN MENGGUNAKAN KEY INI
        'penguji2_id'     => $data['penguji2_id'],   // PASTIKAN MENGGUNAKAN KEY INI
        'status_konfirmasi' => isset($data['status_konfirmasi']) ? $data['status_konfirmasi'] : 'Menunggu',
        'created_at'      => date('Y-m-d H:i:s')
    ];

    // Tambahkan 'catatan_kabag' jika ada di $data dan kolomnya ada di tabel
    if (isset($data['catatan_kabag'])) {
        $ins['catatan_kabag'] = $data['catatan_kabag'];
    }

    // (Opsional) Tambahkan 'pengajuan_id' jika memang ada kolomnya di tabel 'jadwal_ujian'
    // dan jika Anda mengirimkannya dari controller.
    // if (isset($data['pengajuan_id'])) {
    //     $ins['pengajuan_id'] = $data['pengajuan_id'];
    // }

    $this->db->insert('jadwal_ujian', $ins);
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

    public function attempt_reschedule_logic($pengajuan, $id_jadwal_lama = null, $catatan_untuk_jadwal_baru = "Dijadwalkan ulang otomatis") {
    // ... (Salin dan adaptasi logika dari Kabag::penjadwalan_otomatis_untuk_reschedule)
    // Pastikan $pengajuan memiliki: id (pengajuan_ujian_prioritas.id), mahasiswa_id, tipe_ujian,
    // judul_skripsi, pembimbing_id, penguji1_id, penguji2_id.

    // Validasi data $pengajuan yang esensial (seperti di method asli Kabag)
    if (empty($pengajuan['id']) ) {
        $error_msg_detail = "Data pengajuan tidak lengkap untuk penjadwalan ulang."; // Tambahkan detail lebih lanjut
        log_message('error', $error_msg_detail . ". Data Pengajuan: " . json_encode($pengajuan));
        return ['success' => false, 'error' => $error_msg_detail, 'new_jadwal_id' => null];
    }

    $mhs_id = $pengajuan['mahasiswa_id'];
    $tipe = $pengajuan['tipe_ujian'];
    $peng_id = $pengajuan['id']; // Ini adalah pengajuan_ujian_prioritas.id
    $judul_skripsi = $pengajuan['judul_skripsi'];
    $dosen_pembimbing = $pengajuan['pembimbing_id'];
    $dosen_penguji1 = $pengajuan['penguji1_id'];
    $dosen_penguji2 = $pengajuan['penguji2_id'];

    log_message('info', "Mencoba Penjadwalan Ulang - Otomatis. Pengajuan ID: {$peng_id}, Mhs ID: {$mhs_id}, Tipe: {$tipe}. Jadwal lama: {$id_jadwal_lama}");

    $prd = $this->get_periode($tipe);
    if (!$prd) {
        // ... penanganan error ...
        // Contoh: $this->load->model('Pengajuan_model');
        // $this->Pengajuan_model->update_status_pengajuan($peng_id, 'Gagal Reschedule', 'Periode tidak ditemukan');
        return ['success' => false, 'error' => "Periode ujian aktif '{$tipe}' tidak ditemukan.", 'new_jadwal_id' => null];
    }

    $dates = $this->generate_date_range($prd['tanggal_mulai'], $prd['tanggal_selesai']);
    $timeSlots = ['08:45-10:25', '10:30-12:10', '13:00-14:40', '14:45-16:25']; // Sesuaikan slot jika perlu
    $dosen_ids_terlibat = array_filter([$dosen_pembimbing, $dosen_penguji1, $dosen_penguji2]);
     if (empty($dosen_ids_terlibat)) {
        return ['success' => false, 'error' => 'Data dosen penguji/pembimbing tidak lengkap.', 'new_jadwal_id' => null];
    }


    foreach ($dates as $tgl) {
        $hari = date('N', strtotime($tgl)); 
        if ($hari >= 6) continue; // Lewati Sabtu & Minggu

        foreach ($timeSlots as $slot) {
            // Cek ketersediaan dosen
            $is_dosen_tersedia = $this->cek_ketersediaan_dosen($tgl, $slot, $dosen_ids_terlibat);
            if (!$is_dosen_tersedia) {
                log_message('debug', "Reschedule - Dosen tidak tersedia. Pengajuan {$peng_id}, Tgl {$tgl}, Slot {$slot}. Lewati.");
                continue; 
            }

            // Cek ketersediaan ruangan
            $ruang = $this->get_ruangan_available($tipe, $tgl, $slot);
            if ($ruang) {
                log_message('info', "Reschedule - Ruangan ditemukan. Pengajuan {$peng_id}, Ruang ID {$ruang['id']}, Tgl {$tgl}, Slot {$slot}");
                $data_jadwal_baru = [
                    'mahasiswa_id'    => $mhs_id,
                    'pengajuan_id'    => $peng_id, // Kritis: tautkan kembali ke pengajuan asli
                    'judul_skripsi'   => $judul_skripsi,
                    'tipe_ujian'      => $tipe,
                    'tanggal'         => $tgl,
                    'slot_waktu'      => $slot, // Pastikan key ini sesuai dengan yang diharapkan oleh simpan_jadwal_baru
                    'ruangan_id'      => $ruang['id'],
                    'pembimbing_id'   => $dosen_pembimbing, // Pastikan key ini sesuai
                    'penguji1_id'     => $dosen_penguji1,   // Pastikan key ini sesuai
                    'penguji2_id'     => $dosen_penguji2,   // Pastikan key ini sesuai
                    'status_konfirmasi' => 'Menunggu', // Kabag perlu konfirmasi jadwal baru ini
                    'catatan_kabag'   => $catatan_untuk_jadwal_baru
                ];

                $new_jadwal_id = $this->simpan_jadwal_baru($data_jadwal_baru); // Menggunakan method model yang sudah ada

                if ($new_jadwal_id) {
                    // Update status di tabel 'pengajuan_ujian_prioritas'
                    $this->load->model('Pengajuan_model'); // Load jika belum
                    $this->Pengajuan_model->update_status_pengajuan(
                        $peng_id,
                        'Terjadwal Ulang Menunggu Kabag', // Status baru untuk pengajuan
                        "Jadwal baru ID: {$new_jadwal_id}",
                        $new_jadwal_id // Update jadwal_id di pengajuan_ujian_prioritas
                    );
                    $success_msg = "Penjadwalan ulang otomatis berhasil. Jadwal baru (ID: {$new_jadwal_id}) dibuat (status Menunggu Konfirmasi Kabag).";
                    log_message('info', $success_msg . " Pengajuan ID: {$peng_id}");
                    return ['success' => true, 'message' => $success_msg, 'new_jadwal_id' => $new_jadwal_id];
                } else {
                    log_message('error', "Reschedule - Gagal simpan jadwal baru. Pengajuan ID {$peng_id}. Tgl {$tgl}, Slot {$slot}");
                    // Tidak perlu langsung return error, biarkan loop mencari slot lain jika ada
                }
            } else {
                 log_message('debug', "Reschedule - Ruangan tidak tersedia. Pengajuan {$peng_id}, Tipe {$tipe}, Tgl {$tgl}, Slot {$slot}.");
            }
        } // Akhir loop slot
    } // Akhir loop tanggal

    $final_error_msg = "Reschedule - Tidak ada slot/ruangan yang cocok ditemukan.";
    log_message('warn', "{$final_error_msg} Pengajuan ID {$peng_id}");
    // $this->load->model('Pengajuan_model');
    // $this->Pengajuan_model->update_status_pengajuan($peng_id, 'Gagal Reschedule Total', $final_error_msg);
    return ['success' => false, 'error' => $final_error_msg, 'new_jadwal_id' => null];
}
}
