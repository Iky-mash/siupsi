<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_model extends CI_Model {

      private $table = 'pengajuan_ujian_prioritas';
 public function __construct() {
        parent::__construct();
        // Jika Anda memiliki constructor, pastikan memanggil parent::__construct();
        // Namun, jika tidak ada __construct(), tidak masalah.
        // Error ini biasanya bukan karena __construct() yang hilang, tapi karena $table itu sendiri.
    }

    // Fungsi untuk menyimpan data pengajuan
    public function insert($data) {
        return $this->db->insert('pengajuan_ujian_prioritas', $data);
    }

    // Fungsi untuk mendapatkan pengajuan berdasarkan mahasiswa_id
     public function insert_pengajuan($data_pengajuan, $tipe_ujian, $uploaded_files) {
        $this->db->trans_start(); // Start transaction

        // 1. Insert into pengajuan_ujian_prioritas
        $this->db->insert('pengajuan_ujian_prioritas', $data_pengajuan);
        $pengajuan_id = $this->db->insert_id();

        if ($pengajuan_id) {
            // 2. Prepare and insert into specific berkas table
            $data_berkas = array('pengajuan_id' => $pengajuan_id);
            foreach ($uploaded_files as $db_column => $file_path) {
                $data_berkas[$db_column] = $file_path;
            }

            if ($tipe_ujian == 'Sempro') {
                $this->db->insert('berkas_sempro', $data_berkas);
            } elseif ($tipe_ujian == 'Semhas') {
                $this->db->insert('berkas_semhas', $data_berkas);
            }
        }

        $this->db->trans_complete(); // Complete transaction

        if ($this->db->trans_status() === FALSE) {
            // Transaction failed
            return false;
        } else {
            return $pengajuan_id;
        }
    }
   public function get_active_submissions($mahasiswa_id) {
        $this->db->select('id, judul_skripsi, tipe_ujian, tanggal_pengajuan, status, alasan_penolakan');
        $this->db->from('pengajuan_ujian_prioritas');
        $this->db->where('mahasiswa_id', $mahasiswa_id);
        // Anda bisa filter status tertentu, misal yang belum 'selesai' atau 'dijadwalkan ujian'
        // Contoh: $this->db->where_in('status', ['draft', 'dikonfirmasi', 'ditolak']);
        $this->db->order_by('tanggal_pengajuan', 'DESC');
        $query = $this->db->get();
        return $query->result(); // Mengembalikan semua hasil yang cocok
    }
     public function get_latest_submission_by_type($mahasiswa_id, $tipe_ujian) {
        $this->db->select('id, judul_skripsi, tipe_ujian, tanggal_pengajuan, status, alasan_penolakan'); // Pilih kolom yang dibutuhkan
        $this->db->from('pengajuan_ujian_prioritas');
        $this->db->where('mahasiswa_id', $mahasiswa_id);
        $this->db->where('tipe_ujian', $tipe_ujian);
        $this->db->order_by('tanggal_pengajuan', 'DESC'); // Ambil yang terbaru
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row(); // Mengembalikan satu baris data sebagai objek
    }
public function get_all_pengajuan_for_kabag_review() {
    // Ambil tahun saat ini
    $current_year = date('Y');

    // Query SELECT tetap sama
    $this->db->select("
        pup.id AS pengajuan_id, pup.judul_skripsi, pup.tipe_ujian, pup.tanggal_pengajuan, pup.status, pup.alasan_penolakan,
        m.nama AS nama_mahasiswa, m.nim AS nim_mahasiswa, m.tahun_masuk,
        (
            (CASE
                WHEN ($current_year - m.tahun_masuk) >= 7 THEN 5
                WHEN ($current_year - m.tahun_masuk) = 6 THEN 4
                WHEN ($current_year - m.tahun_masuk) = 5 THEN 3
                WHEN ($current_year - m.tahun_masuk) = 4 THEN 2
                ELSE 1
            END)
        ) AS nilai_ms,
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

    // ============================================
    // ==         PERUBAHAN FINAL DI SINI          ==
    // ============================================
    // 1. Hanya ambil status yang masih aktif dalam antrean review/jadwal
    $this->db->where_in('pup.status', ['draft', 'dikonfirmasi']);
    
    // 2. DAN pastikan pengajuan tersebut BELUM memiliki jadwal
    $this->db->where('pup.jadwal_id IS NULL', null, false);
    
    // Urutan tetap berdasarkan prioritas
    $this->db->order_by('priority_score', 'DESC');
    $this->db->order_by('pup.tanggal_pengajuan', 'ASC');

    $query = $this->db->get();
    return $query->result();
}
public function get_pending_application_count() {
    return $this->db->where('status', 'draft')->count_all_results('pengajuan_ujian_prioritas');
}

    /**
     * Mengambil detail berkas untuk satu pengajuan.
     * @param int $pengajuan_id
     * @param string $tipe_ujian ('Sempro' atau 'Semhas')
     * @return object|null Objek berisi path file-file atau null
     */
  public function get_detail_berkas_by_pengajuan_id($pengajuan_id, $tipe_ujian) {
        // Logika fungsi ini tidak perlu diubah
        $table = ($tipe_ujian == 'Sempro') ? 'berkas_sempro' : 'berkas_semhas';
        return $this->db->get_where($table, ['pengajuan_id' => $pengajuan_id])->row();
    }

    /**
     * Mengupdate status dan alasan penolakan pengajuan.
     * @param int $pengajuan_id
     * @param string $status ('dikonfirmasi' atau 'ditolak')
     * @param string|null $alasan_penolakan (null jika dikonfirmasi)
     * @return bool Berhasil atau tidak
     */
    public function update_pengajuan_status($pengajuan_id, $status, $alasan_penolakan = null) {
        $data = array(
            'status' => $status,
            'alasan_penolakan' => $alasan_penolakan
            // Anda mungkin ingin menambahkan 'tanggal_keputusan' jika ada kolomnya
        );
        $this->db->where('id', $pengajuan_id);
        return $this->db->update('pengajuan_ujian_prioritas', $data);
    }



























    public function get_pengajuan_by_mahasiswa($mahasiswa_id) {
        return $this->db->get_where('pengajuan_ujian_prioritas', ['mahasiswa_id' => $mahasiswa_id])->result();
    }
    
    public function insert_konfirmasi($data) {
        return $this->db->insert('konfirmasi_pengajuan', $data);
    }
    public function get_all_pengajuan()
    {
        $this->db->select('pengajuan_ujian_prioritas.*, mahasiswa.nama, mahasiswa.nim, mahasiswa.fakultas, mahasiswa.prodi');
        $this->db->from('pengajuan_ujian_prioritas');
        $this->db->join('mahasiswa', 'pengajuan_ujian_prioritas.mahasiswa_id = mahasiswa.id');
        return $this->db->get()->result_array();
    }
    public function cek_pengajuan_aktif($mahasiswa_id, $tipe_ujian)
{
    $this->db->where('mahasiswa_id', $mahasiswa_id);
    $this->db->where('tipe_ujian', $tipe_ujian);
    $this->db->where('status', 'draft');
    return $this->db->get('pengajuan_ujian_prioritas')->row();
}
public function get_pengajuan_by_id($id) {
    return $this->db->get_where('pengajuan_ujian_prioritas', ['id' => $id])->row_array();
}
 public function update_status_pengajuan($pengajuan_id, $status, $catatan = null, $jadwal_id = null) {
        $data_to_update = [
            'status' => $status
        ];

        // Argumen ke-4 adalah $jadwal_id.
        // Kita perlu cara untuk membedakan apakah $jadwal_id benar-benar dikirim atau tidak,
        // karena jika gagal reschedule, kita mungkin ingin mengesetnya jadi NULL.
        // func_num_args() akan menghitung jumlah argumen yang benar-benar dilewatkan ke fungsi.
        if (func_num_args() >= 4) {
            $data_to_update['jadwal_id'] = $jadwal_id; // $jadwal_id bisa berupa ID atau NULL
        }


        // Untuk $catatan: Tabel pengajuan_ujian_prioritas Anda tidak memiliki kolom 'catatan'.
        // Jadi, kita hanya akan log catatan ini untuk saat ini.
        // Jika Anda menambahkan kolom 'catatan' di tabel 'pengajuan_ujian_prioritas', Anda bisa uncomment bagian di bawah:
        /*
        if ($catatan !== null) {
            // Pastikan kolom 'catatan' ada di tabel Anda
            // if ($this->db->field_exists('catatan', 'pengajuan_ujian_prioritas')) {
            //     $data_to_update['catatan'] = $catatan;
            // } else {
            //     log_message('info', "Kolom 'catatan' tidak ditemukan di tabel pengajuan_ujian_prioritas. Catatan untuk Pengajuan ID {$pengajuan_id}: {$catatan}");
            // }
            log_message('info', "Catatan untuk Pengajuan ID {$pengajuan_id} (status: {$status}): {$catatan}");
        }
        */
        // Log catatan terlepas dari ada tidaknya kolom di DB
        if ($catatan !== null) {
            log_message('debug', "Catatan untuk Pengajuan ID {$pengajuan_id} (status baru: {$status}): {$catatan}");
        }

        $this->db->where('id', $pengajuan_id);
        $updated = $this->db->update('pengajuan_ujian_prioritas', $data_to_update);

        if ($updated) {
            log_message('info', "Status pengajuan ID {$pengajuan_id} berhasil diupdate menjadi '{$status}'" . (isset($data_to_update['jadwal_id']) ? " dengan jadwal_id = " . $data_to_update['jadwal_id'] : ""));
            return true;
        } else {
            log_message('error', "Gagal mengupdate status pengajuan ID {$pengajuan_id}.");
            return false;
        }
    }
     public function has_submitted($mahasiswa_id, $tipe_ujian) {
        $this->db->where('mahasiswa_id', $mahasiswa_id);
        $this->db->where('tipe_ujian', $tipe_ujian);
        // Line 84 di Pengajuan_model.php kemungkinan besar adalah baris di bawah ini
        $query = $this->db->get($this->table); // <-- Akses ke $this->table
        if ($query) { // Tambahkan pengecekan apakah query berhasil
            return $query->num_rows() > 0;
        }
        return false; // Jika query gagal, kembalikan false
    }

    /**
     * Cek apakah mahasiswa sudah pernah mengajukan Sempro.
     * Bisa juga menggunakan has_submitted($mahasiswa_id, 'Sempro')
     * Fungsi ini dibuat untuk kejelasan jika ada logika khusus nantinya.
     * @param int $mahasiswa_id
     * @return bool
     */
    public function has_submitted_sempro($mahasiswa_id) {
        return $this->has_submitted($mahasiswa_id, 'Sempro');
    }

    // Anda mungkin sudah punya fungsi ini atau serupa untuk menampilkan riwayat
    public function get_riwayat_by_mahasiswa($mahasiswa_id) {
        $this->db->where('mahasiswa_id', $mahasiswa_id);
        $this->db->order_by('tanggal_pengajuan', 'DESC');
        $query = $this->db->get($this->table);
        return $query->result_array(); // atau result()
    }
}
