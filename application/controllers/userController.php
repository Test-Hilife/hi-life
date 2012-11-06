<?php
class UserController extends CI_Controller {
    
    function UserController(){
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('userModel');
        $this->lang->load('modules/user', $this->config->item('default_language'));
    }
    
    public function index()
    {
        $this->auth();
    }
    
    public function signup(){
        $this->load->model('head');
        $this->load->view($this->config->item('template_dir') . 'head', $this->head->array);        
        if($this->session->userdata('email'))
        {
            $data["text"] = $this->lang->line('signup');
            $this->load->view($this->config->item('template_dir') . 'div_error', $data);
        }
        else
        {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean');
            $this->form_validation->set_rules('password', '', 'trim|min_length[6]|max_legth[15]|required|matches[passconf]');
            $this->form_validation->set_rules('passconf', '', 'trim|required');            
            $this->form_validation->set_rules('name', '', 'trim|required|xss_clean');            
            $this->form_validation->set_rules('phone', '', 'trim|required|integer|min_legth[5]|max_legth[12]');

            if ($this->form_validation->run() == FALSE)
                $this->load->view('user/signup');
            else
            {
                if( !$this->email_exists($this->input->post('email')) )
                {
                    $this->load->view($this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('email_exists')) );
                }
                else
                {
                    $hash_code = random_string('alnum', 32);
                    $array = array(
                        'email' => $this->input->post('email'),
                        'pass'  => md5_hash( $this->input->post('password') ),
                        'username'  => $this->input->post('name'),
                        'phone' => $this->input->post('phone'),
                        'hash' => $hash_code
                    );
                    $this->db->insert('users', $array);
                    $this->siteModel->log_write($this->lang->line('signup_new_user') . $array['username']);
                    
                    $this->load->helper('string');
                    $array_signup = array(
                        'name' => $array['username'],
                        'hash' => $hash_code
                    );
                    
                    $this->load->library('email');
                    $this->email->from($this->config->item('site_email'), $this->config->item('site_name'));
                    $this->email->to($this->input->post('email'));
                    $this->email->subject($this->lang->line('signup_in_site') . $this->config->item('site_name'));
                    $this->email->message( $this->load->view('user/signup_ok', $array_signup) );
                    if( $this->email->send() )
                    {
                        $this->load->view( $this->config->item('template_dir') . 'success', array('text' => $this->lang->line('signup_ok_in_email')) );
                    }
                    else
                    {
                        $this->load->view( $this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('signup_error_in_email')) );                       
                    }
                    /*
                    $data = array(
                        'email' => $array['email'],
                        'pass' => $array['pass'],
                        'last_vizit' => date('Y-m-d H:i:s')
                        );
                    $this->session->set_userdata($data);
                     */
                    
                }
            }
            
        }
        
        $this->load->view($this->config->item('template_dir') . 'foot', $this->head->array);        
    }
    
    public function confirm($hash = ''){
        if( !trim($hash) )
            die;
        $this->db->where('hash', $hash);
        $query = $this->db->get('users');
        if( ! $query->num_rows() )
            die;
        $this->db->where('hash', $hash);
        $this->db->update('users', array('status' => 'active'));
        redirect( $this->config->item('site_url') );
    }
    
    public function auth($str = true){
        if($str){
            $data = array(
                'title' => $this->lang->line('auth_title'),
                'copyright' => '&copy HiLife'
            );
            $this->load->view($this->config->item('template_dir') . 'head', $data);
        }
        if($this->siteModel->login)
        {
            $array["text"] = $this->lang->line('auth');
            $this->load->view($this->config->item('template_dir') . 'div_error', $array);
            echo '<a href="logout">Выйти</a>';
        }
        else
        {
            if( $str == FALSE )
            {
                $array = array(
                   'email' => $this->input->post('email'),
                   'password' => md5_hash( $this->input->post('password') )
                );
                $this->userModel->auth($array);               
            }
            else
            {
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
                    $this->userModel->auth($array);
                }
            }
        }
        if($str)
            $this->load->view($this->config->item('template_dir') . 'foot', $data);
    }
    
    public function logout(){
        $this->userModel->logout();
    }
    
    public function request_admin(){
        $data = array(
            'title' => $this->lang->line('request_admin_title'),
            'copyright' => '&copy HiLife'
        );
        
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
                'email' => $this->input->post('email'),
                'added' => date('Y-m-d H:i:s')
            );
            if( $this->db->insert('request_admin', $opts) ){
                $array = array('text' => $this->lang->line('success_request'));
                $this->load->view($this->config->item('template_dir') . 'success', $array);
            }else{
                $array = array('text' => $this->lang->line('error_request'));
                $this->load->view($this->config->item('template_dir') . 'div_error', $array);
            }
        }
        
        $this->load->view($this->config->item('template_dir') . 'foot', $data);
    }
    
    public function request_in_supplier(){
        $data = array(
            'title' => $this->lang->line('request_in_postav_title'),
            'copyright' => '&copy;'
        );
        $this->load->view($this->config->item('template_dir') . 'head', $data);
        
        $this->siteModel->redirLogin();
            $this->form_validation->set_rules('descr', '', 'trim');
            $this->form_validation->set_rules('name', '', 'trim|required');
            
            $info = $this->siteModel->user;
            
            if( $this->form_validation->run() == FALSE )
                $this->load->view('user/request_in_postav', $info);
            else
            {
                $this->load->helper('url');
                $array = array(
                    'added' => date('Y-m-d H:i:s'),
                    'name' => $info->username,
                    'userid' => $info->id,
                    'in_supplier' => 'yes',
                    'subject' => $this->input->post('name')
                );
                if($this->input->post('descr'))
                    $array['descr'] = $this->input->post('descr');
                $this->db->insert('request_admin', $array);
                redirect( $this->config->item('site_url') );
            }
            
        $this->load->view($this->config->item('template_dir') . 'foot', $data);
    }
    
    private function userLogin(){
        return $this->userModel->userLogin();
    }
    
    private function email_exists($email){
        return $this->userModel->email_exists($email);
    }
}
?>
