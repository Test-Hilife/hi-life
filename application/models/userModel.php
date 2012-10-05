<?php

class UserModel extends CI_Model{
    
    function UserModel(){
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
            return TRUE;
        }
        else
            return FALSE;
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
    
    public function userLogin(){
        if($this->session->userdata('email') != '' && $this->session->userdata('pass') != '')
        {
            return TRUE;
        }
        else
            return FALSE;
    }    
    
}
?>
