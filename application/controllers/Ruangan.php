<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ruangan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ruangan_model');
    }

    public function index()
    {
        $data['ruangan_sempro'] = $this->Ruangan_model->get_by_tipe('sempro');
        $data['ruangan_semhas'] = $this->Ruangan_model->get_by_tipe('semhas');
        $this->load->view('ruangan/index', $data);
      
    }
    

    public function tambah()
    {
        $this->load->view('ruangan/tambah');
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

    public function edit($id)
    {
        $data['ruangan'] = $this->Ruangan_model->get_by_id($id);
        $this->load->view('ruangan/edit', $data);
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

    public function hapus($id)
    {
        $this->Ruangan_model->delete($id);
        redirect('admin/jadwal');
    }
}
