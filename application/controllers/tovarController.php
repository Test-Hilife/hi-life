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
    public function delete($id = 0){

        $this->siteModel->redirLogin();
        if( ! $this->tovarModel->tovar_exists($id) )
            die;
        $row = $this->tovarModel->tovar;
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
     * Удаление категории товара
     */
    public function del_cat($tovar = 0, $cat = 0){
        if( ! $this->tovarModel->exists_tovar($tovar) || ! (int)$cat)
            die;
        $row = $this->tovarModel->tovar;
        $array = explode(', ', $row[0]->cats);
        if( ! in_array($cat, $array) )
            die($this->lang->line('cat_not_exists'));
        echo count($array);
        if( count($array) != 1 ){
            if($array[0] != $cat)
                $cats = str_replace(', ' . $cat, '', $row[0]->cats);
            else 
                $cats = str_replace($cat . ', ', '', $row[0]->cats);
        }else
            $cats = '';
        $this->db->where('id', $tovar);
        $this->db->update('tovars', array('cats' => $cats));
        //redirect($this->config->item('site_url'));
    }
    
    /*
     * Добавление отзыва
     */
    public function add_review($tovar = 0){
        
        if( ! $this->tovarModel->exists_tovar($tovar) )
            die;   
        $this->siteModel->redirLogin();
        $this->load->model('reviewModel');
        if($this->reviewModel->add_review(array('type' => 'tovar')))
            redirect($this->config->item('site_url') . 'tovar/view/' . $tovar);
        else
            die($this->lang->line('unknown_error'));
        
    }
    
    /*
     * Удаление отзыва
     */
    public function del_review($id = 0){
        $this->load->model('reviewModel');
        $this->reviewModel->review_info($id);
        $this->siteModel->redirLogin();
        if( ! $this->reviewModel->exists 
                || $this->reviewModel->info[0]->user_id != $this->siteModel->user->id 
                && $this->siteModel->user->type < UC_MODERATOR )
            die;
        $this->reviewMode->del_review($id);
    }
    
    /*
     * Редактирование отзыва
     */
    public function edit_review($id = 0){
        $this->load->model('reviewModel');
        $this->reviewModel->review_info($id);
        $this->siteModel->redirLogin();
        if( ! $this->reviewModel->exists 
               || $this->reviewModel->info[0]->user_id != $this->siteModel->user->id 
               && $this->siteModel->user->type < UC_MODERATOR )
           die;  
        if($this->reviewModel->add_review(array('type' => 'tovar', 'act' => 'edit', 'id' => $id)))
            redirect($this->config->item('site_url') . 'tovar/view/' . $tovar);
        else
            die($this->lang->line('unknown_error'));
    }
    
    /*
     * Добавление в корзину
     */
    public function new_order($tovar = 0){
        
        if( !(int) $tovar )
            die;
        
        $this->tovarModel->new_order($tovar, 'yes');
        
    }
    
    /*
     * Открытие сделки
     */
    public function open_order($tovar = 0){
        
        if( !(int) $tovar )
            die;
        
        $this->tovarModel->new_order($tovar, 'no');
        
    }
    
    /*
     * Удаление сделки
     */
    public function del_order($tovar = 0){
        
        if( !(int) $tovar)
            die;
        
        if( $this->tovarModel->del_order($tovar) )
            redirect($this->config->item('site_url') . 'tovar/view/' . $tovar);
        else
        {
            $array['text'] = $this->lang->line('error_del_order');
            $this->load->view($this->config->item('template_dir') . 'div_error', $array);
        }
        
    }
    
    /*
     * Просмотр статистики товара поставщиком
     */
    public function stat($tovar = 0){
        
        if( !(int) $tovar )
            die;
        
        $this->siteModel->redirLogin();
        
        if( ! $this->tovarModel->exists_tovar($tovar) || $this->tovarModel->tovar[0]->postav_id != $this->siteModel->user->postav_id )
            die;
        
        $this->load->view( $this->config->item('template_dir') . 'head');
        
        /*Отывы*/
        $this->db->where('tovar_id', $tovar);
        $query = $this->db->get('reviews');
        $rowReviews = $query->result();
        
        /*Сделки*/
        $this->db->where(array('tovarid' => $tovar, 'payment' => 'yes'));
        $query = $this->db->get('orders');
        $ordersNum = $query->num_rows();
        
        $this->load->view('tovar/stat', array($rowReviews, $ordersNum));
        
        $this->load->view( $this->config->item('template_dir') . 'foot');
    }
    
}

?>
