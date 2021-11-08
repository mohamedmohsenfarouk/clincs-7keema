<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Register extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->session->keep_flashdata('message');
        $this->load->library('form_validation');
        $this->load->model('register_model');
    }

    public function index()
    {
        $this->load->view('customer/register');
    }

    public function validation()
    {

        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('user_email', 'Email Address', 'required|trim|valid_email|is_unique[ea_customers.email]');
        $this->form_validation->set_rules('user_password', 'Password', 'required');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|min_length[11]');
        if ($this->form_validation->run()) {
            $verification_key = md5(rand());
            $encrypted_password = password_hash($this->input->post('user_password'), PASSWORD_BCRYPT);
            $data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email' => $this->input->post('user_email'),
                'phone_number' => $this->input->post('phone_number'),
                'token' => NULL,
                'password' => $encrypted_password,
                'verification_key' => $verification_key,
            );

            $id = $this->register_model->insert($data);
            if ($id > 0) {
                echo json_encode('success');
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
