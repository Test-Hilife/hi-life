<?php

class UserModel extends CI_Model{
    
    function __construct(){
        parent::__construct();
    }
    
    public function auth($opts){
        $this->db->select('id')
                    ->from('users')
                    ->where(array('email' => $opts['email'], 'pass' => $opts['password']))
                    ->limit(1);
        $num = $this->db->count_all_results();
        if($num > 0)
        {
            $this->session->set_userdata(array('email' => $opts['email'], 'pass' => $opts['password']));
            $array = array('text' => $this->lang->line('success_auth'));
            $this->load->view($this->config->item('template_dir') . 'success', $array);
            return TRUE;
        }
        else
        {    
            $array = array('text' => $this->lang->line('error_auth'));
            $this->load->view('user/auth', $array);           
            return FALSE;
        }
        
    }
    
    public function logout(){
        $this->session->set_userdata('pass', '');
        return true;
    }
    
    public function email_exists($email){
        $this->db->select('id')
                    ->from('users')
                    ->where('email', $email)
                    ->limit(1);
        $num = $this->db->count_all_results();
        if($num > 0){
            $this->form_validation->set_message('email', $this->lang->line('email_exists'));
            return false;
        }
        else
            return true;
    }
    
    public function userInfo(){
        $array = array(
            'email' => $this->session->userdata('email'),
            'pass' => $this->session->userdata('pass')
        );
        $this->db->where( $array );
        $query = $this->db->get('users');
        if( ! $query->num_rows() )
            return FALSE;
        else
        {
            $array = $query->result();
            return $array;
        }
    }
    
    public function userLogin(){
        if( $this->session->userdata('email') != '' && $this->session->userdata('pass') != '')
        {
            $this->db->select('id')
                        ->where( array('email' => $this->session->userdata('email'), 'pass' => $this->session->userdata('pass')) )
                        ->limit('1');
            $query = $this->db->get('users');
            $num = $this->db->count_all_results(); 
            if( $num )
            {
                return TRUE;
            }
            else
                return FALSE;
        }
        else
            return FALSE;
    }   
    
}
?>
