<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('form');
    }

    public function index()
    {
        // Jika pengguna sudah login, redirect ke halaman sesuai role
        if ($this->session->userdata('email')) {
            // Pastikan 'role' yang disimpan di session adalah nama controller tujuan
            // contoh: 'admin', 'mahasiswa', dll.
            redirect($this->session->userdata('role'));
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email', [
            'required' => 'Email tidak boleh kosong.', // Pesan dalam Bahasa Indonesia
            'valid_email' => 'Format email tidak valid.'
        ]);
        $this->form_validation->set_rules('password', 'Password', 'trim|required', [
            'required' => 'Password tidak boleh kosong.' // Pesan dalam Bahasa Indonesia
        ]);
       
        if ($this->form_validation->run() == false) {
            // Jika validasi form dasar gagal, tampilkan kembali halaman login
            // Pesan error dari form_validation akan ditampilkan oleh form_error() di view
            $this->load->view('auth/login');
        } else {
            // Jika validasi form dasar berhasil, lanjutkan ke proses validasi login
            $this->_validate_login();
        }
    }

    private function _validate_login()
    {
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
            $user = $this->db->select('id, nama, email, password, role_id, nim, fakultas, prodi, is_active')
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
        }

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $role = $this->db->get_where('roles', ['id' => $user['role_id']])->row_array();

                $data_session = [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'email' => $user['email'],
                    'role_id' => $user['role_id'],
                    'role' => $role['role_name'], // Ini akan digunakan untuk redirect
                ];

                // Tambahkan atribut khusus sesuai role
                if ($role['role_name'] == 'Mahasiswa') {
                    $data_session['nim'] = $user['nim'];
                    $data_session['fakultas'] = $user['fakultas'];
                    $data_session['prodi'] = $user['prodi'];
                    $data_session['is_active'] = $user['is_active'];
                } elseif ($role['role_name'] == 'Dosen') {
                    $data_session['id_dosen'] = $user['id']; // atau atribut lain yang relevan
                } elseif ($role['role_name'] == 'Kabag') {
                    $data_session['id_kabag'] = $user['id']; // atau atribut lain yang relevan
                }

                $this->session->set_userdata($data_session);

                // Redirect berdasarkan role (pastikan nama controller sesuai)
                if ($role['role_name'] == 'Admin') {
                    redirect('admin'); // ke controller Admin
                } elseif ($role['role_name'] == 'Dosen') {
                    redirect('dosen'); // ke controller Dosen
                } elseif ($role['role_name'] == 'Mahasiswa') {
                    redirect('mahasiswa'); // ke controller Mahasiswa
                } elseif ($role['role_name'] == 'Kabag') {
                    redirect('kabag'); // ke controller Kabag
                } else {
                    // Fallback jika role tidak dikenal
                    $this->session->set_flashdata('login_error_message', 'Role pengguna tidak valid.');
                    redirect('auth');
                }
            } else {
                // Password salah
                $this->session->set_flashdata('login_error_message', 'Password salah!');
                redirect('auth');
            }
        } else {
            // Email tidak terdaftar
            $this->session->set_flashdata('login_error_message', 'Email tidak terdaftar!');
            redirect('auth');
        }
    }
    
    public function register()
    {
        // Aturan validasi (gunakan pesan dalam Bahasa Indonesia)
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim', [
            'required' => 'Nama tidak boleh kosong.',
        ]);
        $this->form_validation->set_rules('nim', 'NIM', 'required|trim|is_unique[mahasiswa.nim]', [
            'required' => 'NIM tidak boleh kosong.',
            'is_unique' => 'NIM ini sudah terdaftar.'
        ]);
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[mahasiswa.email]', [
            'required' => 'Email tidak boleh kosong.',
            'valid_email' => 'Format email tidak valid.',
            'is_unique' => 'Email ini sudah terdaftar.'
        ]);
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password tidak sama dengan konfirmasi password.',
            'min_length' => 'Password terlalu pendek (minimal 3 karakter).',
            'required' => 'Password tidak boleh kosong.',
        ]);
        $this->form_validation->set_rules('password2', 'Konfirmasi Password', 'required|trim|matches[password]', [
            'required' => 'Konfirmasi password tidak boleh kosong.',
            'matches' => 'Konfirmasi password tidak sama dengan password.'
        ]);

        if ($this->form_validation->run() == false) {
            $this->load->view('auth/register'); // Pastikan view register.php ada
        } else {
            $data = [
                'nama' => htmlspecialchars($this->input->post('nama', true)),
                'nim' => htmlspecialchars($this->input->post('nim', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT), // Ambil dari 'password' bukan 'password1'
                'role_id' => 3, // Asumsi role_id 3 untuk Mahasiswa
                'is_active' => 1, // Default aktif
            ];
            $this->db->insert('mahasiswa', $data);
            $this->session->set_flashdata('login_success_message', 'Akun berhasil dibuat. Silakan login.');
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        $this->session->set_flashdata('login_success_message', 'Anda telah berhasil logout.');
        redirect('auth');
    }
}