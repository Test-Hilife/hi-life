<?php

class TovarController extends CI_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->model('userModel');
        $this->load->model('tovarModel');
        $this->load->helper('url');
        $this->lang->load('modules/tovar', $this->config->item('default_language'));
    }
    
    /*
     * Добавление товара
     */
    public function add(){
        if( $this->siteModel->user->type < UC_POSTAV )
            die;
        $this->load->view( $this->config->item('template_dir') . 'head');
        
        if( $this->tovarModel->add() )
            redirect( $this->config->item('site_url') );
        else
            redirect( $this->config->item('site_url') . 'tovar/add');
        
        $this->load->view( $this->config->item('template_dir') . 'foot');
    }
    
    /*
     * Редактирование товара
     * Отличий от добавления нет, только в формы вставляем данные из базы
     */
    public function edit($id = 0){
        if( $this->siteModel->user->type < UC_POSTAV || ! (int) $id )
            die;
        $this->load->view( $this->config->item('template_dir') . 'head');
        
        $this->db->where('id', $id);
        $query = $this->db->get('tovars');
        $row = $query->result();
        if(!$row)
        {
            $data['text'] = $this->lang->line('not_id');
            $this->load->view($this->config->item('template_dir') . 'div_error', $data);
        }
        else
        {
    
            if( $this->tovarModel->add(false, $id) )
                redirect( $this->config->item('site_url') );
            else
                redirect( $this->config->item('site_url') . 'tovar/edit/' . $id );
        
        }
        $this->load->view( $this->config->item('template_dir') . 'foot');
    }
    
    /*
     * Удаление товара
     */
    public function delete($id){
        $this->db->select('postav_id')
                    ->where('id', $id);
        $query = $this->db->get('tovars');
        $row = $query->result();
        
        if(!$row)
            die;
        $this->siteModel->redirLogin();
        
        if( $this->siteModel->user->postav_id == $row[0]->postav_id || $this->siteModel->user->type >= UC_POSTAV)
        {
            $this->db->delete('tovars', array('id' => $id));
            redirect($this->config->item('site_url') . 'tovar/');
        }
        else
            die;
    }
    
    /*
     * Установление как топ-купона
     */
    public function top($id = 0, $act = 'yes'){
        if( $this->siteModel->login == false || $this->siteModel->user->type < UC_MODERATOR || ! $id || $act != 'yes' && $act != 'no' )
            die;
        $this->db->where('id', $id);
        $this->db->update('tovars', array('top' => $act));
        redirect($this->config->item('site_url') . 'admin/');
    }
    
    /*
     * Добавление категории к товару
     */
    public function add_cat($tovar = 0, $cat = 0){
        if( ! $this->tovarModel->exists_tovar($tovar) || ! (int)$cat)
            die;
        $row = $this->tovarModel->tovar;
        if($row[0]->cats)
            $cats_array = explode(', ', $row[0]->cats);
        else
            $cats_array = array();
        
        if(in_array($cat, $cats_array))
            die($this->lang->line('cat_exists'));

        if( count($cats_array) > 0 )
            $str = $row[0]->cats . ', ' . $cat;
        else
            $str = $cat;

        $this->db->where('id', $tovar);
        $this->db->update('tovars', array('cats' => $str));
    }
    
    /*
     * Редактирование категорий
     */
    public function edit_cats($tovarId = 0){
        
    }
    
}

?>
