<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
		is_active();
		$this->load->model('M_mahasiswa');
		$this->load->model('M_fakultas');
		$this->load->model('M_jurusan');
    }
    
    public function index()
    {
        menu();
        $data['mahasiswa']  = $this->M_mahasiswa->getMhs();
		$this->load->view('mahasiswa/v_list_mahasiswa', $data);
        $this->load->view('layout/fefooter');
    }

    public function create()
    {
        menu();
        $data['kode']	= $this->M_mahasiswa->kode();
        $data['fak']	= $this->M_fakultas->getFakultas();
        $data['jur']	= $this->M_jurusan->getJurusan();
		$this->load->view('mahasiswa/v_create_mahasiswa', $data);
        $this->load->view('layout/fefooter');
    }

    public function getID()
    {
        $fakultas = $this->input->post('id_fakultas');
        $jurusan = $this->M_jurusan->getJurusanByFak($fakultas);
        
        $lists = "<option value=''>Pilih</option>";
        
        foreach($jurusan as $data){
        $lists .= "<option value='".$data->id_jurusan."'>".$data->nama_jurusan."</option>"; // Tambahkan tag option ke variabel $lists
        }
        
        $callback = array('list_kota'=>$lists); // Masukan variabel lists tadi ke dalam array $callback dengan index array : list_kota
        echo json_encode($lists); // konversi varibael $callback menjadi JSON
        
    }

    public function store()
    {
        $id_aksess  = $this->input->post('id_akses');
        $nama       = $this->input->post('nama_mhs');
        $email      = $this->input->post('email');
        $password   = md5($this->input->post('password'));
        $fakultas   = $this->input->post('fakultas');
        $jurusan    = $this->input->post('jurusan');
        $semester   = $this->input->post('semester');
        $role       = ['1', '31', '38', '46']; // statis role untuk mahasiswa

        $id_akses   = array('id_akses'  => $id_aksess);
        $user       = array(
            'nama_mahasiswa'    => $nama,
            'email'             => $email,
            'password'          => $password,
            'fakultas'          => $fakultas,
            'jurusan'           => $jurusan,
            'semester'          => $semester
        );
        $data   = array_merge($user);

        $this->M_mahasiswa->insertMhs($data,$id_akses);
        for ($i=0;$i < count($role); $i++) {
			$detail	= array(
					'id_akses'	=> $id_aksess,
					'id_menu' 	=> $role[$i]
                    );
            $this->db->insert('detailakses', $detail);
		}

        // $this->_kirimemail($token, 'verify');

        $this->session->set_flashdata('message', 
        '<div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <center>Mahasiswa berhasil ditambah</center>
        </div>');
        redirect('mahasiswa');
    }

    public function editrole()
    {
        menu();
        $nim = $this->uri->segment(3);
		$data['allmenu']    = $this->M_menu->allMenu();
		$data['role']	    = $this->M_menu->sysmenu_mhs($nim);
        $data['mahasiswa']  = $this->M_mahasiswa->getMhsByNim($nim);
		$this->load->view('mahasiswa/v_editrole_mahasiswa', $data);
        $this->load->view('layout/fefooter');
    }

    public function updaterole()
	{
		$id_akses	= $this->input->post('id_akses');
		$role		= $this->input->post('check_list');
		$delete 	= $this->M_user->hapusakses($id_akses);
        $cnt 		= count($role);
        // echo $cnt;
        // exit;
		for ($i=0;$i < $cnt; $i++) {
			$detail	= array(
					'id_akses'	=> $id_akses,
					'id_menu' 	=> $role[$i]
					);
			$this->M_user->tambahakses($detail);
			// $this->db->update('user', ['aktif' => 1]);
		}
		redirect('mahasiswa');
	}

}

/* End of file Controllername.php */