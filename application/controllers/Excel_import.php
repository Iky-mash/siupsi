<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel_import extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Excel_model');
    }

    public function index() {
        $this->load->view('admin/data_mahasiswa');
    }

    public function import()
    {
        $this->load->library('upload');
    
        if (isset($_FILES["file"]["name"])) {
            $path = $_FILES["file"]["tmp_name"];
            
            // Gunakan PhpSpreadsheet untuk membaca file Excel
            $spreadsheet = IOFactory::load($path);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
    
            $existingEmails = [];
            $existingNims = [];
            $duplicateEmails = [];
            $duplicateNims = [];
    
            // Cek data duplikat dalam Excel
            for ($row = 2; $row <= $highestRow; $row++) {
                $email = strtolower(trim($worksheet->getCell('B' . $row)->getValue())); // Email
                $nim = trim($worksheet->getCell('D' . $row)->getValue()); // NIM
    
                if (in_array($email, $existingEmails)) {
                    $duplicateEmails[] = $email;
                } else {
                    $existingEmails[] = $email;
                }
    
                if (in_array($nim, $existingNims)) {
                    $duplicateNims[] = $nim;
                } else {
                    $existingNims[] = $nim;
                }
            }
    
            // Jika ada duplikat di dalam Excel, tampilkan peringatan dan hentikan proses
            if (!empty($duplicateEmails) || !empty($duplicateNims)) {
                $errorMessage = '';
    
                if (!empty($duplicateEmails)) {
                    $errorMessage .= 'Email duplikat dalam file Excel: ' . implode(', ', $duplicateEmails) . '. ';
                }
                if (!empty($duplicateNims)) {
                    $errorMessage .= 'NIM duplikat dalam file Excel: ' . implode(', ', $duplicateNims) . '.';
                }
    
                $this->session->set_flashdata('error', $errorMessage);
                redirect('admin/data_mahasiswa'); // Ganti dengan URL halaman import
            }
    
            // Lanjutkan proses import jika tidak ada duplikat di Excel
            $importedEmails = [];
            for ($row = 2; $row <= $highestRow; $row++) {
                $nama = $worksheet->getCell('A' . $row)->getValue();
                $email = strtolower(trim($worksheet->getCell('B' . $row)->getValue()));
                $password = $worksheet->getCell('C' . $row)->getValue();
                $nim = trim($worksheet->getCell('D' . $row)->getValue());
    
                // Cek apakah email atau NIM sudah ada di database
                $cekDatabase = $this->db->where('email', $email)->or_where('nim', $nim)->get('mahasiswa')->row();
    
                if (!$cekDatabase) {
                    $data = array(
                        'nama' => $nama,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'nim' => $nim,
                        'role_id' => 3  // Sesuaikan dengan ID role mahasiswa
                    );
    
                    $this->db->insert('mahasiswa', $data);
                    $importedEmails[] = $email;
                }
            }
    
            if (!empty($importedEmails)) {
                $this->session->set_flashdata('success', 'Data berhasil diimport: ' . implode(', ', $importedEmails));
            } else {
                $this->session->set_flashdata('error', 'Tidak ada data baru yang diimport.');
            }
    
            redirect('admin/data_mahasiswa');
        }
    }
    
    
}
?>
