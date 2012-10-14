<?php

class ReviewModel extends CI_Model{
    
    public $info;
    public $exists = false;
    
    function __construct(){
        parent::__construct();
        $this->load->library('form_validation');
    }
    
    public function review_info($id = 0){
        $query = $this->db->where('id', $id)->get('reviews');
        $this->info = $query->result();
        if($query->num_rows() > 0)
            $this->exists = true;
    }
    
    public function add_review($opts){

        $this->form_validation->set_rules('rating', '', 'integer|required');
        $this->form_validation->set_rules('text', '', 'trim|required');
        
        if($this->form_validation->run() == FALSE)
            if($opts['act'] == 'edit')
            {
                $this->review_info($opts['id']);
                $this->load->view('tovar/add_review', $this->info);
            }
            else
                $this->load->view('tovar/add_review');
        else
        {
            $array = array(
                'user_id' => $this->siteModel->user->id,
                'text' => $this->input->post('text'),
                'rating' => $this->input->post('rating'),
                'type' => $opts['type']
            );
            if($opts['act'] == 'edit')
            {
                $this->db->where('id', $opts['id']);
                $this->db->update('reviews', $array);
            }
            else
                $this->db->insert('reviews', $array);
            
        }
    }
    
    public function del_review($id = 0, $redir = 'tovar/view/'){
        if( $this->db->delete('reviews', array('id' => $id)) )
            redirect( $this->config->item('site_url') . $redir . $this->info[0]->tovar_id );
        else
            redirect( $this->config->item('site_url') );
    }
    
}
?>
