<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penjadwalan_model extends CI_Model {
    
    // Fungsi untuk mengambil data mahasiswa berdasarkan ID
    public function getMahasiswaById($mahasiswa_id) {
        log_message('debug', 'Data Mahasiswa ID yang dicari: ' . $mahasiswa_id);
        
        $this->db->select('mahasiswa.id AS mahasiswa_id, mahasiswa.nama, mahasiswa.judul_skripsi, 
        pembimbing.id AS pembimbing_id, 
        penguji1.id AS penguji1_id, 
        penguji2.id AS penguji2_id');
        $this->db->from('mahasiswa');
        $this->db->join('dosen pembimbing', 'mahasiswa.pembimbing_id = pembimbing.id', 'left');
        $this->db->join('dosen penguji1', 'mahasiswa.penguji1_id = penguji1.id', 'left');
        $this->db->join('dosen penguji2', 'mahasiswa.penguji2_id = penguji2.id', 'left');
        
        $this->db->where('mahasiswa.id', $mahasiswa_id);
    
        // Log query untuk debugging
        log_message('debug', 'SQL Query: ' . $this->db->last_query());
    
        return $this->db->get()->row_array();
    }

    // Fungsi untuk mengecek ketersediaan jadwal
    public function cekKetersediaan($tanggal, $waktu_mulai, $waktu_selesai, $dosen_ids) {
        $this->db->where('tanggal', $tanggal);
        $this->db->where_in('id_dosen', $dosen_ids);
        $query = $this->db->get('agenda_dosen');
    
        // Jika tidak ada agenda sama sekali pada tanggal tersebut
        if ($query->num_rows() == 0) {
            return true; // Waktu tersedia
        }
    
        // Periksa konflik waktu untuk setiap agenda
        foreach ($query->result_array() as $agenda) {
            // Jika waktu yang diajukan bertabrakan dengan agenda yang ada
            if (
                ($waktu_mulai >= $agenda['waktu_mulai'] && $waktu_mulai < $agenda['waktu_selesai']) ||
                ($waktu_selesai > $agenda['waktu_mulai'] && $waktu_selesai <= $agenda['waktu_selesai']) ||
                ($waktu_mulai <= $agenda['waktu_mulai'] && $waktu_selesai >= $agenda['waktu_selesai'])
            ) {
                return false; // Konflik, waktu tidak tersedia
            }
        }
    
        return true; // Tidak ada konflik, waktu tersedia
    }
    
    
    public function cekKetersediaanHarian($tanggal, $waktu_sesi, $dosen_ids) {
        foreach ($waktu_sesi as $waktu) {
            [$waktu_mulai, $waktu_selesai] = $waktu;
    
            // Gunakan fungsi cekKetersediaan untuk setiap sesi waktu
            if ($this->cekKetersediaan($tanggal, $waktu_mulai, $waktu_selesai, $dosen_ids)) {
                return true; // Ada waktu yang tersedia pada hari itu
            }
        }
    
        // Jika semua sesi pada hari itu tidak tersedia
        return false;
    }
    
    public function cariJadwalYangTersedia($tanggal_mulai, $tanggal_akhir, $waktu_sesi, $mahasiswa) {
        // Inisialisasi variabel untuk menyimpan rekomendasi jadwal
        $rekomendasi_jadwal = [];
    
        // Looping melalui rentang tanggal
        $current_date = $tanggal_mulai;
        while (strtotime($current_date) <= strtotime($tanggal_akhir)) {
            // Looping untuk setiap sesi waktu yang tersedia pada hari tersebut
            foreach ($waktu_sesi as $sesi) {
                $waktu_mulai = $sesi['waktu_mulai'];
                $waktu_selesai = $sesi['waktu_selesai'];
    
                // Cek ketersediaan waktu untuk pembimbing dan penguji
                if ($this->Penjadwalan_model->cekKetersediaan($current_date, $waktu_mulai, $waktu_selesai, [
                    $mahasiswa['pembimbing_id'],
                    $mahasiswa['penguji1_id'],
                    $mahasiswa['penguji2_id']
                ])) {
                    // Tambahkan jadwal ke dalam rekomendasi
                    $rekomendasi_jadwal[] = [
                        'tanggal' => $current_date,
                        'waktu_mulai' => $waktu_mulai,
                        'waktu_selesai' => $waktu_selesai
                    ];
    
                    // Hentikan pencarian jika sudah mencapai 3 rekomendasi
                    if (count($rekomendasi_jadwal) >= 3) {
                        return $rekomendasi_jadwal; // Return hasil
                    }
                }
            }
    
            // Lanjutkan ke tanggal berikutnya
            $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
        }
    
        // Kembalikan hasil rekomendasi jika selesai memeriksa semua tanggal
        return $rekomendasi_jadwal;
    }
    
    // Fungsi untuk membuat jadwal
    public function buatJadwal($mahasiswa_id, $tanggal, $waktu_mulai, $waktu_selesai, $pembimbing_id, $penguji1_id, $penguji2_id) {
        $data = [
            'mahasiswa_id' => $mahasiswa_id,
            'tanggal' => $tanggal,
            'waktu_mulai' => $waktu_mulai,
            'waktu_selesai' => $waktu_selesai,
            'pembimbing_id' => $pembimbing_id,
            'penguji1_id' => $penguji1_id,
            'penguji2_id' => $penguji2_id,
            'status' => 'Dijadwalkan',
        ];
        $this->db->insert('penjadwalan', $data);
    }

    public function simpanJadwalTerpilih($pengajuan_id, $tanggal, $waktu_mulai, $waktu_selesai) {
        $data = [
            'pengajuan_id' => $pengajuan_id,
            'tanggal' => $tanggal,
            'waktu_mulai' => $waktu_mulai,
            'waktu_selesai' => $waktu_selesai,
            'status' => 'Dijadwalkan'
        ];
        $this->db->insert('jadwal_ujian', $data);
    }
    
}