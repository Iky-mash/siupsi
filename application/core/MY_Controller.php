<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

  

    public $global_data = []; // Variabel untuk menyimpan semua data global

    public function __construct() {
        
        parent::__construct();

        // Load library dan helper yang umum digunakan
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database(); // Pastikan database selalu ter-load

        // Load model yang akan sering digunakan untuk notifikasi atau data global lainnya
        // Anda bisa juga melakukan autoload model ini di config/autoload.php
        $this->load->model('Riwayat_ujian_model');
        // Jika ada model lain untuk dosen/akademik, load di sini juga atau autoload
        // $this->load->model('Notifikasi_dosen_model');
        // $this->load->model('Notifikasi_akademik_model');

        // Memuat data pengguna global
        $this->_load_global_user_data();

        // Memuat notifikasi global berdasarkan peran
        $this->_load_global_notifications();

        // Membuat semua data dalam $this->global_data tersedia untuk semua view
        // yang di-load oleh controller turunan MY_Controller
        if (!empty($this->global_data)) {
            $this->load->vars($this->global_data);
        }
    }

    private function _load_global_user_data() {
        
        $user_session = $this->session->userdata();
        $this->global_data['user_id_global']     = isset($user_session['id']) ? $user_session['id'] : null;
        $this->global_data['user_nama_global']   = isset($user_session['nama']) ? $user_session['nama'] : 'Guest';
        $this->global_data['user_role_global']   = isset($user_session['role']) ? $user_session['role'] : null; // 'mahasiswa', 'dosen', 'akademik'
    }

    private function _load_global_notifications() {
        $user_id   = $this->global_data['user_id_global'];
        $user_role = $this->global_data['user_role_global'];

     
        
        $all_notifications = [];

        if ($user_id && $user_role) {
            switch ($user_role) {
                case 'Mahasiswa':

                    
                    $all_notifications = $this->Riwayat_ujian_model->get_riwayat_by_mahasiswa($user_id);
                    // Menambahkan link default untuk notifikasi mahasiswa jika belum ada
                    foreach ($all_notifications as &$notif) { // Gunakan pass by reference
                        if (!isset($notif['link'])) {
                            // Link bisa lebih spesifik jika model Anda sudah menyiapkannya
                            $notif['link'] = site_url('mahasiswa/riwayat_pengajuan');
                            // Tambahkan anchor jika ada timestamp untuk navigasi langsung di halaman riwayat
                            if (isset($notif['timestamp'])) {
                               $notif['link'] .= '#event-' . $notif['timestamp'];
                            }
                        }
                    }
                    unset($notif); // Hapus referensi setelah loop selesai
                    break;

                case 'Dosen':
                    // Ganti dengan pemanggilan model yang sesuai untuk dosen
                    // Contoh: $all_notifications = $this->Notifikasi_dosen_model->get_notifikasi_untuk_dosen($user_id);
                    // Untuk sekarang, kita bisa buat placeholder atau data dummy
                    if (method_exists($this->Riwayat_ujian_model, 'get_notifications_for_dosen')) { // Anda perlu membuat method ini
                         $all_notifications = $this->Riwayat_ujian_model->get_notifications_for_dosen($user_id);
                    } else {
                         $all_notifications = [ /* Data notifikasi dummy untuk dosen */ ];
                    }
                    break;

                case 'Akademik': // Atau 'kabag', sesuaikan dengan role Anda
                    // Ganti dengan pemanggilan model yang sesuai untuk akademik
                    // Contoh: $all_notifications = $this->Notifikasi_akademik_model->get_notifikasi_untuk_akademik($user_id);
                    if (method_exists($this->Riwayat_ujian_model, 'get_notifications_for_akademik')) { // Anda perlu membuat method ini
                         $all_notifications = $this->Riwayat_ujian_model->get_notifications_for_akademik($user_id);
                    } else {
                        $all_notifications = [ /* Data notifikasi dummy untuk akademik */ ];
                    }
                    break;
                
                default:
                    $all_notifications = [];
                    break;
            }
        }

        if (!empty($all_notifications)) {
            // Urutkan notifikasi: terbaru dulu
            usort($all_notifications, function($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });
        }
        
        // Ambil 5 notifikasi teratas untuk ditampilkan di navbar
        $this->global_data['navbar_notifications'] = array_slice($all_notifications, 0, 5);
        // Hitung total notifikasi untuk badge
        $this->global_data['navbar_notification_count'] = count($all_notifications);
    }
}