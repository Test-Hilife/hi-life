<?php
class UserController extends CI_Controller {
    
    public function index()
    {
        $this->auth();
    }
    
    public function signup(){
        $this->load->model('head');
        $this->load->model('userModel');
        $this->load->view($this->config->item('template_dir') . 'head', $this->head->array);        
        if($this->session->userdata('email'))
        {
            $data["text"] = $this->lang->line('signup');
            $this->load->view($this->config->item('template_dir') . 'div_error', $data);
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean|callback_email_exists');
            $this->form_validation->set_rules('password', '', 'trim|min_length[6]|max_legth[15]|required|matches[passconf]');
            $this->form_validation->set_rules('passconf', '', 'trim|required');            
            $this->form_validation->set_rules('name', '', 'trim|required|xss_clean');            
            $this->form_validation->set_rules('phone', '', 'trim|required|integer|min_legth[5]|max_legth[12]');

            if ($this->form_validation->run() == FALSE)
                $this->load->view('user/signup');
            else
            {
                $array = array(
                    'email' => $this->input->post('email'),
                    'pass'  => md5_hash( $this->input->post('password') ),
                    'username'  => $this->input->post('name'),
                    'phone' => $this->input->post('phone'),
                    'added' => date('Y-m-d H:i:s'),
                );
                $this->db->insert('users', $array);

                $data = array(
                    'email' => $array['email'],
                    'pass' => $array['pass'],
                    'last_vizit' => $array['added']
                    );
                $this->session->set_userdata($data);
            }
            
        }
        
        $this->load->view($this->config->item('template_dir') . 'foot', $this->head->array);        
    }
    
    public function auth(){
        $data = array(
            'title' => $this->lang->line('auth_title'),
            'copyright' => '&copy HiLife'
        );
        $this->load->model('userModel');
        $this->load->view($this->config->item('template_dir') . 'head', $data);
        if($this->userLogin())
        {
            $array["text"] = $this->lang->line('auth');
            $this->load->view($this->config->item('template_dir') . 'div_error', $array);
            echo '<a href="logout">Выйти</a>';
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|email_valid');
            $this->form_validation->set_rules('password', '', 'trim|required');
            
            if($this->form_validation->run() == FALSE)
                $this->load->view('user/auth');
            else
            {
                $array = array(
                    'email' => $this->input->post('email'),
                    'password' => md5_hash( $this->input->post('password') )
                );

                if( $this->userModel->auth($array) ){
                    $array = array('text' => $this->lang->line('success_auth'));
                    $this->load->view($this->config->item('template_dir') . 'success', $array);
                }
                else
                {
                    $array = array(
                        'text' => $this->lang->line('error_auth')
                    );
                    $this->load->view('user/auth', $array);
                }
                
            }
        }
        $this->load->view($this->config->item('template_dir') . 'foot', $data);
    }
    
    public function logout(){
        $this->load->model('userModel');
        $this->userModel->logout();
    }
    
    public function request_admin(){
        $data = array(
            'title' => $this->lang->line('request_admin_title'),
            'copyright' => '&copy HiLife'
        );
        $this->load->library('form_validation');
        
        $this->load->view($this->config->item('template_dir') . 'head', $data);
        
        $this->form_validation->set_rules('subject', '', 'trim|required|xss_clean|min_length[4]|max_length[150]');
        $this->form_validation->set_rules('descr', '', 'trim|required|min_length[5]');
        $this->form_validation->set_rules('phone', '', 'trim|integer|min_length[6]');
        $this->form_validation->set_rules('name', '', 'trim|xss_clean');
        $this->form_validation->set_rules('email', '', 'trim|xss_clean');
        
        if($this->form_validation->run() == FALSE)
            $this->load->view('user/request_admin');
        else 
        {
            $opts = array(
                'subject' => $this->input->post('subject'),
                'descr' => $this->input->post('descr'),
                'phone' => $this->input->post('phone'),
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email')
            );
            if( $this->db->insert('request_admin', $opts) ){
                $array = array('text' => $this->lang->line('success_request'));
                $this->load->view($this->config->item('template_dir') . 'success', $array);
            }else
                $array = array('text' => $this->lang->line('error_request'));
                $this->load->view($this->config->item('template_dir') . 'div_error', $array);
        }
        
        $this->load->view($this->config->item('template_dir') . 'foot', $data);
    }
    
    private function userLogin(){
        return $this->userModel->userLogin();
    }
    
    public function email_exists($email){
        return $this->userModel->email_exists($email);
    }
}
?>
