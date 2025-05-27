<?php
// application/models/Riwayat_ujian_model.php

class Riwayat_ujian_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_mahasiswa_bimbingan_ids($dosen_id) {
        $this->db->select('DISTINCT(ju.mahasiswa_id) as mahasiswa_id');
        $this->db->from('jadwal_ujian ju');
        $this->db->where('ju.pembimbing_id', $dosen_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Mengambil detail mahasiswa dari tabel mahasiswa.
     * Menggunakan struktur tabel mahasiswa yang telah Anda berikan.
     */
    public function get_mahasiswa_detail($mahasiswa_id) {
        // Tabel: mahasiswa, Kolom: id, nama, nim
        $this->db->select('id, nama, nim, fakultas, prodi'); // Ambil kolom yang relevan
        $query = $this->db->get_where('mahasiswa', array('id' => $mahasiswa_id)); 
        return $query->row_array();
    }
    
    public function get_riwayat_by_mahasiswa($mahasiswa_id) {
        $events = [];

        // 1. Ambil semua entri jadwal_ujian untuk mahasiswa ini
        // Asumsi ada tabel 'ruangan' dengan 'id' dan 'nama_ruangan'. Jika tidak, bagian nama_ruangan bisa error/kosong.
        $this->db->select('ju.*, r.nama_ruangan'); 
        $this->db->from('jadwal_ujian ju');
        $this->db->join('ruangan r', 'r.id = ju.ruangan_id', 'left'); // Sesuaikan jika tabel ruangan berbeda/tidak ada
        $this->db->where('ju.mahasiswa_id', $mahasiswa_id);
        $this->db->order_by('ju.created_at', 'ASC'); 
        $jadwal_ujian_entries = $this->db->get()->result_array();

        foreach ($jadwal_ujian_entries as $ujian) {
            $events[] = [
                'timestamp' => strtotime($ujian['created_at']),
                'datetime_str' => date('d M Y, H:i', strtotime($ujian['created_at'])),
                'type' => 'PENGAJUAN AWAL',
                'title' => 'Pengajuan Ujian ' . htmlspecialchars(ucfirst($ujian['tipe_ujian'])),
                'details' => 'Judul Skripsi: ' . htmlspecialchars($ujian['judul_skripsi']) . '<br>' .
                             'Tanggal Ujian Diusulkan: ' . date('d M Y', strtotime($ujian['tanggal'])) . '<br>' .
                             'Slot Waktu: ' . htmlspecialchars($ujian['slot_waktu']) . '<br>' .
                             'Ruangan: ' . htmlspecialchars($ujian['nama_ruangan'] ?? 'Belum Ditentukan'),
                'actor' => 'Mahasiswa', // Diajukan oleh mahasiswa
                'jadwal_id_terkait' => $ujian['id'],
                'status_saat_itu' => $ujian['status_konfirmasi'],
                'catatan_kabag_saat_itu' => $ujian['catatan_kabag']
            ];

            if ($ujian['status_konfirmasi'] == 'Dikonfirmasi' || $ujian['status_konfirmasi'] == 'Ditolak') {
                 $events[] = [
                    'timestamp' => strtotime($ujian['updated_at']),
                    'datetime_str' => date('d M Y, H:i', strtotime($ujian['updated_at'])),
                    'type' => 'STATUS PENGAJUAN UJIAN',
                    'title' => 'Status Pengajuan Ujian: ' . htmlspecialchars($ujian['status_konfirmasi']),
                    'details' => 'Catatan Kabag: ' . (!empty($ujian['catatan_kabag']) ? htmlspecialchars($ujian['catatan_kabag']) : '-'),
                    'actor' => 'Kabag',
                    'jadwal_id_terkait' => $ujian['id']
                ];
            }

            // 2. Ambil riwayat reschedule yang terkait dengan original_jadwal_id ini
            // Tidak perlu JOIN untuk nama peminta lagi
            $this->db->select('jrh.*'); 
            $this->db->from('jadwal_reschedule_history jrh');
            $this->db->where('jrh.original_jadwal_id', $ujian['id']);
            $this->db->order_by('jrh.request_timestamp', 'ASC');
            $reschedule_history_entries = $this->db->get()->result_array();

            foreach ($reschedule_history_entries as $history) {
                $actor_reschedule_request = ucfirst($history['requested_by_user_type']);
                if (!empty($history['requested_by_user_id'])) {
                    // Menambahkan ID jika ada, untuk identifikasi jika diperlukan
                    $actor_reschedule_request .= ' (ID: ' . $history['requested_by_user_id'] . ')';
                }

                $events[] = [
                    'timestamp' => strtotime($history['request_timestamp']),
                    'datetime_str' => date('d M Y, H:i', strtotime($history['request_timestamp'])),
                    'type' => 'PERMINTAAN RESCHEDULE',
                    'title' => 'Permintaan Reschedule Diajukan',
                    'details' => 'Alasan: ' . htmlspecialchars($history['reason_for_reschedule']),
                    'actor' => $actor_reschedule_request, // Aktor adalah tipe pengguna + ID
                    'original_jadwal_id' => $history['original_jadwal_id'],
                    'new_jadwal_id_proposed' => $history['new_jadwal_id']
                ];

                if (!empty($history['kabag_action_timestamp'])) {
                    $status_reschedule_text = 'Status Tidak Diketahui';
                    if ($history['reschedule_status'] == 'approved' || ($history['reschedule_status'] == 'requested' && !empty($history['new_jadwal_id']))) {
                        $status_reschedule_text = 'Reschedule Disetujui';
                    } elseif ($history['reschedule_status'] == 'rejected') {
                        $status_reschedule_text = 'Reschedule Ditolak';
                    } elseif ($history['reschedule_status'] == 'requested' && empty($history['new_jadwal_id'])) {
                         $status_reschedule_text = 'Menunggu Persetujuan Kabag';
                    }

                    $details_reschedule_action = 'Catatan Kabag: ' . (!empty($history['kabag_notes']) ? htmlspecialchars($history['kabag_notes']) : '-');
                    if (!empty($history['new_jadwal_id'])) {
                        $details_reschedule_action .= '<br>ID Jadwal Baru yang Dibuat: ' . $history['new_jadwal_id'];
                    }

                    $events[] = [
                        'timestamp' => strtotime($history['kabag_action_timestamp']),
                        'datetime_str' => date('d M Y, H:i', strtotime($history['kabag_action_timestamp'])),
                        'type' => 'STATUS RESCHEDULE',
                        'title' => $status_reschedule_text,
                        'details' => $details_reschedule_action,
                        'actor' => 'Kabag', // Tindakan reschedule oleh Kabag
                        'original_jadwal_id' => $history['original_jadwal_id'],
                        'new_jadwal_id_final' => $history['new_jadwal_id']
                    ];
                } else if ($history['reschedule_status'] == 'requested' && empty($history['kabag_action_timestamp'])) {
                     $events[] = [
                        'timestamp' => strtotime($history['request_timestamp']) + 1, 
                        'datetime_str' => date('d M Y, H:i', strtotime($history['request_timestamp'])),
                        'type' => 'STATUS RESCHEDULE', // Status dari permintaan reschedule itu sendiri
                        'title' => 'Menunggu Persetujuan Kabag',
                        'details' => 'Permintaan reschedule menunggu tindakan dari Kabag.',
                        'actor' => 'Sistem', // Atau bisa juga aktor dari peminta request awal
                        'original_jadwal_id' => $history['original_jadwal_id']
                    ];
                }
            }
        }

        if (!empty($events)) {
            usort($events, function($a, $b) {
                return $a['timestamp'] <=> $b['timestamp'];
            });
        }
        
        return $events;
    }
}