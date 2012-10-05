<?php

class AdminModel extends CI_Model{
    
    private $email;
    private $password;
    
    function AdminModel(){
        parent::__construct();
    }
    
    private function admin_exists(){
        $this->db->select('id')
                    ->from('users')
                    ->where( array('email' => $this->email, 'pass' => $this->password, 'type' => 'admin') )
                    ->limit('1');
        $num = $this->db->count_all_results(); 
        return $num;
    }
    
    public function adminExists(){
        
        if($this->input->post('email') != '' && $this->input->post('password') != '')
        {
            $this->email = $this->input->post('email');
            $this->password = md5_hash( $this->input->post('password') );
        }
        else
        {
            $this->email = $this->session->userdata('admin_email');
            $this->password = $this->session->userdata('admin_passhash');
        }
        
        if( $this->email != '' && $this->password != '' )
            $num = $this->admin_exists();
        else
            $num = 1;
        
        if($num)
            return TRUE;
        else
            return FALSE;
    }
    
    public function login(){
        $this->session->set_userdata('admin_email', $this->email);
        $this->session->set_userdata('admin_passhash', $this->password);
        
        $this->load->helper('url');
        redirect('admin/index');
    }
    
    public function logout(){
        $this->session->set_userdata( array('admin_email' => '', 'admin_passhash' => '') );
        
        $this->load->helper('url');
        redirect('http://localhost/admin/index');
    }
    
}
?>
