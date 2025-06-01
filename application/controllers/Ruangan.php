<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ruangan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ruangan_model');
          if_logged_in();
        check_role(['Kabag', 'Admin']);
    }

    public function index()
    {
        $data['ruangan_sempro'] = $this->Ruangan_model->get_by_tipe('sempro');
        $data['ruangan_semhas'] = $this->Ruangan_model->get_by_tipe('semhas');
        $this->load->view('ruangan/index', $data);
      
    }
    

    public function tambah()
    {

        $data['title'] = 'Tambah Ruangan';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data); 
        $this->load->view('templates/navbar', $data);
        $this->load->view('ruangan/tambah'); 
        $this->load->view('templates/footer');
    }
     public function tambah_kabag()
    {

        $data['title'] = 'Tambah Ruangan';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_kabag', $data); 
        $this->load->view('templates/navbar', $data);
        $this->load->view('ruangan/tambah_kabag'); 
        $this->load->view('templates/footer');
    }

    public function simpan()
    {
        $data = [
            'nama_ruangan' => $this->input->post('nama_ruangan'),
            'kapasitas' => $this->input->post('kapasitas'),
            'tipe_seminar' => $this->input->post('tipe_seminar')
        ];
        $this->Ruangan_model->insert($data);
        redirect('admin/jadwal');
    }
    public function simpan_kabag()
    {
        $data = [
            'nama_ruangan' => $this->input->post('nama_ruangan'),
            'kapasitas' => $this->input->post('kapasitas'),
            'tipe_seminar' => $this->input->post('tipe_seminar')
        ];
        $this->Ruangan_model->insert($data);
        redirect('kabag/kelola');
    }

    public function edit($id)
    {
         $data['title'] = 'Edit Ruangan';
        $data['ruangan'] = $this->Ruangan_model->get_by_id($id);


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_admin', $data); 
        $this->load->view('templates/navbar', $data);
        $this->load->view('ruangan/edit', $data);
        $this->load->view('templates/footer');
    }
       public function edit_kabag($id)
    {
         $data['title'] = 'Edit Ruangan Ujian';
        $data['ruangan'] = $this->Ruangan_model->get_by_id($id);


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_kabag', $data); 
        $this->load->view('templates/navbar', $data);
        $this->load->view('ruangan/edit_kabag', $data);
        $this->load->view('templates/footer');
    }

    public function update($id)
    {
        $data = [
            'nama_ruangan' => $this->input->post('nama_ruangan'),
            'kapasitas' => $this->input->post('kapasitas'),
            'tipe_seminar' => $this->input->post('tipe_seminar')
        ];
        $this->Ruangan_model->update($id, $data);
        redirect('admin/jadwal');
    }
       public function update_kabag($id)
    {
        $data = [
            'nama_ruangan' => $this->input->post('nama_ruangan'),
            'kapasitas' => $this->input->post('kapasitas'),
            'tipe_seminar' => $this->input->post('tipe_seminar')
        ];
        $this->Ruangan_model->update($id, $data);
        redirect('kabag/kelola');
    }

    public function hapus($id)
    {
        $this->Ruangan_model->delete($id);
        redirect('admin/jadwal');
    }
public function hapus_kabag($id)
    {
        $this->Ruangan_model->delete($id);
        redirect('kabag/kelola');
    }

}
