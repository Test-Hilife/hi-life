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
    
    public function errorTypeUser($type){
        if($this->user->type < $type)
            redirect( $this->config->item('site_url') );
    } 
    
    /*
     * Запись в лог
     */
    public function log_write($text = ''){
        $str = explode('/', $_SERVER['REQUEST_URI']);
        $array = array(
            'class' => $str[1],
            'text' => $text
        );
        if(count($str) >= 3)
            $array['method'] = $str[2];
        $this->db->insert('logs', $array);
        if( $this->db->affected_rows() )
            return TRUE;
        else
            return FALSE;
    }
    
    /*
     * Чтение лога
     */
    public function log_read($opts){
        if(isset($opts['method']))
            $this->db->where('method', $opts['method']);
        if(isset($opts['class']))
            $this->db->where('class', $opts['class']);
        if(isset($opts['id']))
            $this->db->where('id', $opts['id']);
        $query = $this->db->get('logs');
        $row = $query->result();
        return $row;
    }
    
}

?>
