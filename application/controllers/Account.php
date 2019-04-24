<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class Account extends CI_Controller {

    public function __construct()
    {
      parent::__construct();
      // is_logged_in();
      $this->load->model('M_menu');
      $uid = $this->session->userdata('id');
      $data['menu'] = $this->M_menu->sysmenu($uid);
      $this->load->view('layout/feheader', $data);
    }
    
    public function index()
    {
      $id_user =  $this->session->userdata('id');
      $data['divisi'] = $this->db->get('divisi')->result();
      $data['user']   = $this->db->get_where('user', ['id_user' => $id_user])->result_array();

      $this->form_validation->set_rules('current_pass', 'Current Password', 'trim|required');
      $this->form_validation->set_rules('new_pass1', 'New Password', 'trim|required|min_length[3]|matches[new_pass2]');
      $this->form_validation->set_rules('new_pass2', 'Confirm Password', 'trim|required|min_length[3]|matches[new_pass1]');

      if($this->form_validation->run() == false) {
        $this->load->view('user/v_account', $data);
        $this->load->view('layout/fefooter');
      }else{
        $id_user      = $this->session->userdata('id');
        $get_pass     = $this->db->get_where('user', ['id_user' => $id_user])->row_array();
        $current_pass = $this->input->post('current_pass');
        $new_pass     = $this->input->post('new_pass1');
        
        if ($get_pass['password'] != $current_pass) {
          $this->session->set_flashdata('message', 
          '<div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <center>Wrong current password</center>
          </div>');
          redirect('account');
        }else{
          if ($get_pass['password'] == $new_pass) {
            $this->session->set_flashdata('message', 
              '<div class="alert alert-warning">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <center>New pass cannot be the same as current</center>
              </div>');
              redirect('account');
          }else{
            $this->db->set('password', $new_pass);
            $this->db->where('id_user', $this->session->userdata('id'));
            $this->db->update('user');

            $this->session->set_flashdata('message', 
              '<div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <center>Password has been changed</center>
              </div>');
              redirect('account');
          }
        }
        $this->load->view('layout/fefooter');
        }

    }
  
  }
  
  /* End of file Controllername.php */
  