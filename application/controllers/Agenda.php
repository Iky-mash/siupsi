<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agenda extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Agenda_model');
        $this->load->model('JadwalPeriode_model');
        $this->load->library('form_validation');
    }

    public function index() {

  $id_dosen = $this->session->userdata('id_dosen'); // Ambil ID dosen dari sesi

    if (!$id_dosen) {
        redirect('auth'); // Redirect kalau belum login
    }

    $data['agenda'] = $this->Agenda_model->get_all_agenda($id_dosen); 
   

       
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('agenda/index', $data);
        $this->load->view('templates/footer');
    }

   
    public function edit($id) {
        $id_dosen = $this->session->userdata('id_dosen'); 
        $agenda = $this->Agenda_model->get_by_id($id, $id_dosen);
    
        if (!$agenda) {
            show_error('Agenda tidak ditemukan atau bukan milik Anda.', 403);
        }
    
        // Ambil semua slot waktu untuk tanggal & dosen yang sama
        $agenda['slot_waktu'] = $this->Agenda_model->get_slots_by_date_and_dosen($agenda['tanggal'], $id_dosen);
    
    
        $data['agenda'] = $agenda;
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('agenda/edit', $data);
        $this->load->view('templates/footer');
    }
    
    public function edit_by_date($tanggal) {
        $id_dosen = $_SESSION['id_dosen']; // Pastikan id_dosen sudah benar
    
        // Perbaiki pemanggilan agar parameter sesuai dengan fungsi model
        $agenda = $this->Agenda_model->get_by_date($tanggal, $id_dosen);
    
        if (!$agenda || empty($agenda['slot_waktu'])) {
            $agenda = [
                'id_agenda' => null,
                'id_dosen' => $id_dosen,
                'tanggal' => $tanggal,
                'slot_waktu' => ''
            ];
        }
    
        $data['agenda'] = $agenda;
        $this->load->view('agenda/agenda_edit_form', $data);
    }
    public function store_by_date() {
        $this->load->model('Agenda_model');
    
        $id_dosen = $this->session->userdata('id_dosen'); // Ambil ID dosen dari sesi
        if (!$id_dosen) {
            redirect('auth'); // Jika belum login, redirect ke halaman login
        }
    
        $tanggal = $this->input->post('tanggal');
        $slot_waktu = implode(',', $this->input->post('slot_waktu') ?? []);
    
        $data = [
            'id_dosen' => $id_dosen, // Gunakan ID dosen dari sesi
            'tanggal' => $tanggal,
            'slot_waktu' => $slot_waktu
        ];
    
        $insert = $this->Agenda_model->insert_agenda($data);
    
        if ($insert) {
            redirect('agenda');
        } else {
            show_error('Gagal menyimpan data', 500);
        }
    }
    
    
    public function update($id_agenda)
    {
        $this->load->model('Agenda_model'); // Pastikan model dimuat
    
        // Ambil data dari form
        $tanggal = $this->input->post('tanggal');
        $slot_waktu = $this->input->post('slot_waktu'); // Array dari checkbox
    
        // Jika slot waktu kosong, simpan string kosong agar tidak error
        $slot_waktu_string = !empty($slot_waktu) ? implode(',', $slot_waktu) : '';
    
        // Data yang akan diupdate
        $data = [
            'tanggal' => $tanggal,
            'slot_waktu' => $slot_waktu_string
        ];
    
        // Update data di database
        $this->Agenda_model->update_agenda($id_agenda, $data);
    
        // Redirect kembali
        redirect('agenda');
    }
    
    
    
    
        
}
?>
