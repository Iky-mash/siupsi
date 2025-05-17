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

    public function get_all_jadwal() {
        $this->db->select('
            jadwal_ujian.id, 
            jadwal_ujian.mahasiswa_id,
             mahasiswa.nama AS mahasiswa_nama,
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
            penguji2.nama AS penguji2_nama
        ');
        $this->db->from('jadwal_ujian');
        $this->db->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left');
        $this->db->join('dosen AS pembimbing', 'pembimbing.id = jadwal_ujian.pembimbing_id', 'left');
        $this->db->join('dosen AS penguji1', 'penguji1.id = jadwal_ujian.penguji1_id', 'left');
        $this->db->join('dosen AS penguji2', 'penguji2.id = jadwal_ujian.penguji2_id', 'left');
        $this->db->join('mahasiswa', 'mahasiswa.id = jadwal_ujian.mahasiswa_id', 'left'); 
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
    $this->db->order_by('tanggal, slot_waktu', 'ASC');
    return $this->db->get()->result();
}
// Model: application/models/Penjadwalan_model.php
public function get_jadwal_by_id($id)
{
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
    $this->db->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left');
    $this->db->join('dosen AS pembimbing', 'pembimbing.id = jadwal_ujian.pembimbing_id', 'left');
    $this->db->join('dosen AS penguji1', 'penguji1.id = jadwal_ujian.penguji1_id', 'left');
    $this->db->join('dosen AS penguji2', 'penguji2.id = jadwal_ujian.penguji2_id', 'left');
    $this->db->where('jadwal_ujian.id', $id);
    return $this->db->get()->row(); // satu objek, bukan array
}

public function get_jadwal_by_dosen($id_dosen)
{
    $this->db->select('jadwal_ujian.*, mahasiswa.nama as nama_mahasiswa, ruangan.nama_ruangan');
    $this->db->from('jadwal_ujian');
    $this->db->join('mahasiswa', 'jadwal_ujian.mahasiswa_id = mahasiswa.id');
    $this->db->join('ruangan', 'jadwal_ujian.ruangan_id = ruangan.id', 'left');
    $this->db->where('jadwal_ujian.pembimbing_id', $id_dosen);
    $this->db->or_where('jadwal_ujian.penguji1_id', $id_dosen);
    $this->db->or_where('jadwal_ujian.penguji2_id', $id_dosen);
    
    return $this->db->get()->result_array();
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

    // Cek ketersediaan dosen pada slot tertentu
    // public function cek_ketersediaan_dosen($dosen_id, $tanggal, $slot) {
    //     $agenda = $this->db->get_where('agenda_dosen', ['id_dosen' => $dosen_id, 'tanggal' => $tanggal])->row_array();
    //     if (!$agenda) {
    //         return true; 
    //     }
    //     $slots_busy = explode(',', $agenda['slot_waktu']);
    //     return !in_array($slot, $slots_busy);
    // }

    public function cek_ketersediaan_dosen($tanggal, $slot, $dosen_ids) {
        // Pastikan $tanggal berada dalam format yang benar
        $tanggal = date('Y-m-d', strtotime($tanggal)); // Formatkan tanggal jika perlu
    
        // Pastikan $dosen_ids adalah array, jika tidak, ubah jadi array
        if (!is_array($dosen_ids)) {
            $dosen_ids = [$dosen_ids];
        }
    
        // Validasi jika tanggal dan slot waktu tidak kosong
        if (empty($tanggal) || empty($slot)) {
            return false; // Jika tanggal atau slot kosong, kembalikan false (tidak valid)
        }
    
        $this->db->where('tanggal', $tanggal);
        $this->db->where('slot_waktu', $slot);
    
        // Jika tidak ada dosen yang dikirim, langsung kembalikan true
        if (empty($dosen_ids)) {
            return true;
        }
    
        $this->db->group_start();
        foreach ($dosen_ids as $id) {
            $this->db->or_where('pembimbing_id', $id);
            $this->db->or_where('penguji1_id', $id);
            $this->db->or_where('penguji2_id', $id);
        }
        $this->db->group_end();
    
        $query = $this->db->get('jadwal_ujian');
    
        // TRUE = dosen tersedia, FALSE = bentrok
        return $query->num_rows() === 0;
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

    // Update pengajuan dengan jadwal_id
    public function update_jadwal_pengajuan($pengajuan_id, $jadwal_id) {
        $this->db->where('id', $pengajuan_id)
                 ->update('pengajuan_ujian_prioritas', ['jadwal_id' => $jadwal_id]);
    }

    // Simpan konfirmasi tambahan jika diperlukan
    public function simpan_konfirmasi($data) {
        $this->db->insert('konfirmasi_pengajuan', $data);
    }
}
