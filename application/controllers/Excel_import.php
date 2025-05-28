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
        if (isset($_FILES["file"]["name"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
            $path = $_FILES["file"]["tmp_name"];
            
            try {
                $spreadsheet = IOFactory::load($path);
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                $this->session->set_flashdata('error', 'Gagal membaca file Excel. Pastikan format file benar. Error: ' . $e->getMessage());
                redirect('excel_import'); // Or your desired redirect path
                return;
            }

            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
    
            $existingEmailsInExcel = [];
            $existingNimsInExcel = [];
            $duplicateEmailsInExcel = [];
            $duplicateNimsInExcel = [];
    
            // Cek data duplikat dalam Excel (Email dan NIM)
            for ($row = 2; $row <= $highestRow; $row++) { // Start from row 2 assuming row 1 is header
                $emailCell = 'B' . $row;
                $nimCell = 'D' . $row;

                if ($worksheet->cellExists($emailCell)) {
                    $email = strtolower(trim((string) $worksheet->getCell($emailCell)->getValue())); 
                    if (!empty($email)) { 
                        if (in_array($email, $existingEmailsInExcel)) {
                            $duplicateEmailsInExcel[] = $email . " (baris " . $row . ")";
                        } else {
                            $existingEmailsInExcel[] = $email;
                        }
                    }
                }

                if ($worksheet->cellExists($nimCell)) {
                    $nim = trim((string) $worksheet->getCell($nimCell)->getValue()); 
                    if (!empty($nim)) { 
                        if (in_array($nim, $existingNimsInExcel)) {
                            $duplicateNimsInExcel[] = $nim . " (baris " . $row . ")";
                        } else {
                            $existingNimsInExcel[] = $nim;
                        }
                    }
                }
            }
    
            if (!empty($duplicateEmailsInExcel) || !empty($duplicateNimsInExcel)) {
                $errorMessage = 'Ditemukan data duplikat di dalam file Excel: ';
                if (!empty($duplicateEmailsInExcel)) {
                    $errorMessage .= 'Email duplikat: ' . implode(', ', array_unique($duplicateEmailsInExcel)) . '. ';
                }
                if (!empty($duplicateNimsInExcel)) {
                    $errorMessage .= 'NIM duplikat: ' . implode(', ', array_unique($duplicateNimsInExcel)) . '.';
                }
                $this->session->set_flashdata('error', $errorMessage);
                redirect('excel_import'); // Or your desired redirect path
                return; 
            }
    
            $importedCount = 0;
            $skippedCount = 0;
            $errors = [];

            for ($row = 2; $row <= $highestRow; $row++) { // Start from row 2
                $nama = trim((string) $worksheet->getCell('A' . $row)->getValue());
                $email = strtolower(trim((string) $worksheet->getCell('B' . $row)->getValue()));
                $password_val = (string) $worksheet->getCell('C' . $row)->getValue();
                $nim = trim((string) $worksheet->getCell('D' . $row)->getValue());
                $fakultas = trim((string) $worksheet->getCell('E' . $row)->getValue()); // New field: Fakultas
                $prodi = trim((string) $worksheet->getCell('F' . $row)->getValue());    // New field: Prodi
                
                // Adjust column letters for subsequent fields
                $pembimbing_id_val = trim((string) $worksheet->getCell('G' . $row)->getValue()); 
                $penguji1_id_val = trim((string) $worksheet->getCell('H' . $row)->getValue());  
                $penguji2_id_val = trim((string) $worksheet->getCell('I' . $row)->getValue());  

                // Check if the essential row is entirely empty (or just whitespace)
                if (empty($nama) && empty($email) && empty($nim) && empty($password_val) && empty($fakultas) && empty($prodi)) {
                    continue; // Skip truly empty rows
                }

                // Check for mandatory fields (adjust as per your requirements)
                if (empty($nama) || empty($email) || empty($nim) || empty($password_val) || empty($fakultas) || empty($prodi)) {
                    $skippedCount++;
                    $errors[] = "Data pada baris {$row} tidak lengkap (Nama, Email, Password, NIM, Fakultas, atau Prodi kosong) dan dilewati.";
                    continue; 
                }
    
                // Optional: Validate if Dosen IDs exist
                if (!empty($pembimbing_id_val) && !$this->db->where('id', $pembimbing_id_val)->get('dosen')->row()) {
                    $skippedCount++;
                    $errors[] = "Pembimbing ID '{$pembimbing_id_val}' pada baris {$row} tidak ditemukan dan dilewati.";
                    continue;
                }
                if (!empty($penguji1_id_val) && !$this->db->where('id', $penguji1_id_val)->get('dosen')->row()) {
                    $skippedCount++;
                    $errors[] = "Penguji 1 ID '{$penguji1_id_val}' pada baris {$row} tidak ditemukan dan dilewati.";
                    continue;
                }
                if (!empty($penguji2_id_val) && !$this->db->where('id', $penguji2_id_val)->get('dosen')->row()) {
                    $skippedCount++;
                    $errors[] = "Penguji 2 ID '{$penguji2_id_val}' pada baris {$row} tidak ditemukan dan dilewati.";
                    continue;
                }

                $this->db->group_start();
                $this->db->where('email', $email);
                $this->db->or_where('nim', $nim);
                $this->db->group_end();
                $cekDatabase = $this->db->get('mahasiswa')->row();
    
                if (!$cekDatabase) {
                    $data = array(
                        'nama' => $nama,
                        'email' => $email,
                        'password' => password_hash($password_val, PASSWORD_DEFAULT),
                        'nim' => $nim,
                        'fakultas' => $fakultas, // Added fakultas
                        'prodi' => $prodi,       // Added prodi
                        'pembimbing_id' => !empty($pembimbing_id_val) ? (int)$pembimbing_id_val : NULL,
                        'penguji1_id' => !empty($penguji1_id_val) ? (int)$penguji1_id_val : NULL,
                        'penguji2_id' => !empty($penguji2_id_val) ? (int)$penguji2_id_val : NULL,
                        'role_id' => 3, // Default role_id for mahasiswa
                        'is_active' => 1 // Default is_active status
                        // Add other fields like 'judul_skripsi', 'status_sempro', etc. if they are in your Excel and table
                    );
    
                    if ($this->db->insert('mahasiswa', $data)) {
                        $importedCount++;
                    } else {
                        $skippedCount++;
                        $db_error = $this->db->error();
                        $errors[] = "Gagal menyimpan data untuk NIM {$nim} / Email {$email} ke database (Error DB: ".$db_error['message'].").";
                    }
                } else {
                    $skippedCount++;
                    $reason = "";
                    if (strtolower($cekDatabase->email) == strtolower($email)) $reason .= "Email '{$email}' sudah ada di database. ";
                    if ($cekDatabase->nim == $nim) $reason .= "NIM '{$nim}' sudah ada di database.";
                    $errors[] = "Data pada baris {$row} dilewati: {$reason}";
                }
            }
    
            $flashSuccess = "";
            $flashError = "";

            if ($importedCount > 0) {
                $flashSuccess .= "{$importedCount} data mahasiswa berhasil diimport. ";
            }
            
            if ($skippedCount > 0) {
                $flashError .= "{$skippedCount} data dilewati.";
                if(!empty($errors)){
                     $flashError .= " Rincian: <br> - " . implode("<br> - ", $errors); // Using <br> for better readability in flash message
                }
            } elseif ($importedCount == 0 && $highestRow < 2) {
                 $flashError .= 'Tidak ada data untuk diimport dalam file Excel (file kosong atau hanya header).';
            } elseif ($importedCount == 0 && $skippedCount == 0 && $highestRow >=2) {
                 $flashError .= 'Tidak ada data baru yang diimport (kemungkinan semua data sudah ada, format tidak sesuai, atau tidak ada baris data yang valid).';
            }

            if(!empty($flashSuccess)){
                $this->session->set_flashdata('success', $flashSuccess);
            }
            if(!empty($flashError)){
                // If there's also a success message, append error, otherwise set it.
                $existing_error = $this->session->flashdata('error');
                $this->session->set_flashdata('error', ($existing_error ? $existing_error . "<br>" : "") . $flashError);
            }
    
            redirect('admin/data_mahasiswa'); // Redirect to your data display page
        } else {
            $errorMessage = 'Tidak ada file yang diupload atau terjadi kesalahan saat upload.';
            if(isset($_FILES["file"]["error"]) && $_FILES["file"]["error"] != UPLOAD_ERR_OK) {
                switch ($_FILES["file"]["error"]) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $errorMessage .= " Ukuran file melebihi batas.";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errorMessage .= " File hanya terupload sebagian.";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $errorMessage .= " Tidak ada file yang diupload.";
                        break;
                    default:
                        $errorMessage .= " Error upload tidak diketahui (Kode: ".$_FILES["file"]["error"].").";
                        break;
                }
            }
            $this->session->set_flashdata('error', $errorMessage);
            redirect('excel_import'); // Or your desired redirect path for upload form
        }
    }
    
}
?>
