<?php

class Request_admin extends CI_Controller{
    
    public function index(){
        
        $this->lang->load('request_admin', 'russian');
        $data = array(
            'title' => $this->lang->line('title'),
            'copyright' => '&copy HiLife'
        );
        
        $this->load->view($this->config->item('site_name') . 'head', $data);
        
        
        
        $this->load->view($this->config->item('site_name') . 'foot', $data);
        
    }
    
}
?>
