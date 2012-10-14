<?php

class SiteModel extends CI_Model{
    
    public $login = false;
    public $user;
    
    function __construct(){
        parent::__construct();
        if( $this->session->userdata('pass') )
        {
            $this->db->where( array('email' => $this->session->userdata('email'), 'pass' => $this->session->userdata('pass')) )
                        ->limit(1);
            $query = $this->db->get('users');
            if($query->num_rows())
            {
                $row = $query->result();
                $this->user = $row[0];
                $this->login = true;
            }
        }
    }
    
    public function redirLogin(){
        if($this->login == FALSE)
            redirect($this->config->item('site_url') . 'user/auth');
    }
    
}

?>
