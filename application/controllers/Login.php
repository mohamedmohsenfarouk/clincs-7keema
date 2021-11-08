<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    if ($this->session->userdata('id')) {
      redirect('private_area');
    }
    $this->load->library('form_validation');
    $this->load->model('login_model');
  }

  function index()
  {
    $this->load->view('customer/login');
  }

  function validation()
  {
    $this->form_validation->set_rules('user_email', 'Email Address', 'required|trim|valid_email');
    $this->form_validation->set_rules('user_password', 'Password', 'required');
    if ($this->form_validation->run()) {
      $result = $this->login_model->can_login($this->input->post('user_email'), $this->input->post('user_password'));
      if ($result == '') {
        $user = $this->db->select('*')->from('ea_customers')->where('email', $this->input->post('user_email'))->get();
        $this->session->set_userdata('user_id', $user->result()[0]->id);
        $data = [
          'first_name' => $user->result()[0]->first_name,
          'last_name' => $user->result()[0]->last_name,
          'email' => $user->result()[0]->email,
          'phone_number' => $user->result()[0]->phone_number,

        ];
        echo json_encode([
          'msg'  => 'success',
          'data'  => $data,
        ]);
      } else {
        $this->session->set_flashdata('message', $result);
        redirect('login');
      }
    } else {
      $errors = $this->form_validation->error_array();
      $all_errors = [];
      foreach ($errors as $error) {
        array_push($all_errors, $error);
      }
      print_r($all_errors);
    }
  }
}
