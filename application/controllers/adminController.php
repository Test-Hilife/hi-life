<?php

class AdminController extends CI_Controller{
    
    public function index(){
        
        $this->load->model('adminModel');
        $this->loginIn();
        
        $this->load->view($this->config->item('admin_template_dir') . 'head');
        
        $this->load->view($this->config->item('admin_template_dir') . 'foot');
        
    }
    
    private function loginIn(){
        if( ! $this->adminModel->adminExists() )
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', '', 'required|trim|xss_clean');
            $this->form_validation->set_rules('password', '', 'required|trim');

            if($this->form_validation->run() == FALSE)
            {
                $this->load->view('user/auth');
            }
            else
            {
                if($this->adminModel->adminExists())
                    $this->adminModel->login();
                else
                    $this->load->view('user/auth');
            }
        }
    }
}
?>
