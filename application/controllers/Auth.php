<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
{
    parent::__construct();
    $this->load->library('form_validation'); // Validasi Form
    $this->load->library('session');         // Session
    $this->load->database();                 // Database
    $this->load->helper('url');              // Helper URL untuk fungsi redirect
    $this->load->helper('form');             // Helper Form jika diperlukan
}


    public function index(){
        if ($this->session->userdata('email')) {
            redirect($this->session->userdata('role'));
        }
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email', [
            'required' => 'Email tidak boleh kosong',
        ]);
        $this->form_validation->set_rules('password', 'Password', 'trim|required', [
            'required' => 'Password tidak boleh kosong',
        ]);
       
        if ($this->form_validation->run() == false) {
            $this->load->view('auth/login');
        } else {
            $this->_validate_login();
        }

    }

private function _validate_login() {
    $email = $this->input->post('email');
    $password = $this->input->post('password');

    // Cek admin
    $user = $this->db->select('id, nama, email, password, role_id')
        ->from('admin')
        ->where('email', $email)
        ->get()
        ->row_array();

    // Jika tidak ditemukan di tabel admin, cek dosen
    if (!$user) {

        $user = $this->db->select('id, nama, email, password, role_id')
            ->from('dosen')
            ->where('email', $email)
            ->get()
            ->row_array();
    }

    // Jika tidak ditemukan di tabel dosen, cek mahasiswa
    if (!$user) {
        $user = $this->db->select('id, nama, email, password, role_id, nim, fakultas, prodi, is_active, date_created')
            ->from('mahasiswa')
            ->where('email', $email)
            ->get()
            ->row_array();
    }

    // Jika tidak ditemukan di tabel mahasiswa, cek kabag
    if (!$user) {
    $user = $this->db->select('id, nama, email, password, role_id')
        ->from('kabag')
        ->where('email', $email)
        ->get()
        ->row_array();

    
    // if (!$user) {
    //     echo "Email tidak ditemukan di tabel kabag";
    // } else {
    //     echo "Data user kabag ketemu: ";
    //     print_r($user);
    // }
    // die(); 
}



    if ($user) {
        if (password_verify($password, $user['password'])) {
            $role = $this->db->get_where('roles', ['id' => $user['role_id']])->row_array();

            $data = [
                'id' => $user['id'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'role_id' => $user['role_id'],
                'role' => $role['role_name'],
                'date_created' => $user['date_created']
            ];

            // Tambahkan atribut khusus sesuai role
            if ($role['role_name'] == 'Mahasiswa') {
                $data['nim'] = $user['nim'];
                $data['fakultas'] = $user['fakultas'];
                $data['prodi'] = $user['prodi'];
                $data['is_active'] = $user['is_active'];
            } elseif ($role['role_name'] == 'Dosen') {
                $data['id_dosen'] = $user['id'];
            } elseif ($role['role_name'] == 'Kabag') {
                $data['id_kabag'] = $user['id'];  
            }

            $this->session->set_userdata($data);

            // Redirect berdasarkan role
            if ($role['role_name'] == 'Admin') {
                redirect('admin');
            } elseif ($role['role_name'] == 'Dosen') {
                redirect('dosen');
            } elseif ($role['role_name'] == 'Mahasiswa') {
                redirect('mahasiswa');
            } elseif ($role['role_name'] == 'Kabag') {
                redirect('kabag');  
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Password salah!</div>');
            redirect('auth');
        }
    } else {
        $this->session->set_flashdata('message', '<div class="alert alert-danger">Email tidak terdaftar!</div>');
        redirect('auth');
    }
}

    
    

    public function register() {
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim', [
            'required' => 'Nama tidak boleh kosong',
        ]);
        $this->form_validation->set_rules('nim', 'Nim', 'required|trim', [
            'required' => 'Nim tidak boleh kosong',
        ]);
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[mahasiswa.email]', [
            'required' => 'Email tidak boleh kosong',
        ]);
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password tidak sama',
            'min_length' => 'Password terlalu pendek',
            'required' => 'Password tidak boleh kosong',
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == false) {
            $this->load->view('auth/register');
        } else {
            $data = [
                'nama' => htmlspecialchars($this->input->post('nama', true)),
                'nim' => htmlspecialchars($this->input->post('nim', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 3, // Mahasiswa
                'is_active' => 1,
                'date_created' => time(),
            ];
            $this->db->insert('mahasiswa', $data);
            $this->session->set_flashdata('message', 'Akun berhasil dibuat');
            redirect('auth');
        }
    }

    public function logout() {
        $this->session->sess_destroy(); // Menghapus semua session
        $this->session->set_flashdata('message', 'Logout berhasil');
        redirect('auth');
    }
    
}
