<?php

class productController extends CI_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->model('userModel');
        $this->load->model('productModel');
        $this->load->helper('url');
        $this->lang->load('modules/product', $this->config->item('default_language'));
    }
    
    /*
     * Товары
     */
    public function all(){
        $this->db->where("moderated", "yes")
                ->order_by("id", "DESC");
        $query = $this->db->get("products");
        $row = $query->result();
        
        $pageInfo = array(
            "title" => $this->lang->line("products_title")
        );
        $this->siteModel->setPageInfo($pageInfo);
        
        $this->load->view( $this->config->item("template_dir") . "head");
        
        if( !$query->num_rows() )
        {
            $error = $this->lang->line("products_not_exists");
            $this->load->view( $this->config->item("template_dir") . "div_error", $error);
        }
        else
        {
            $array["row"] = $row;
            $this->load->view( "product/all" , $array );
        }
        $this->load->view( $this->config->item("template_dir") . "foot");
    }
    
    /*
     * Просмотр товара
     */
    public function view($id = 0){
        if( !(int) $id)
            die;
        $this->db->where("id", $id);
        $query = $this->db->get("products");
        $row = $query->result();
        $pageInfo = array(
            'title' => $row[0]->name,
            'keywords' => '',
            'descr' => $row[0]->small_text
        );
        $this->load->view( $this->config->item('template_dir') . 'head' );
        if( !$query->num_rows() )
        {
            $array["text"] = $this->lang->line("product_not_exists");
            $this->load->view($this->config->item('template_dir') . 'div_error', $array );
        }
        else
        {
            $this->load->view( 'product/view', $row[0] );
        }
        
        $this->load->view( $this->config->item('template_dir') . 'foot' );
    }
    
    /*
     * Поиск товаров по категориям
     */
    public function searchInCat($catId = 0){
        if(! (int) $catId)
            die;
        $this->load->view( $this->config->item('template_dir') . 'head');
        
        $query = $this->db->query('SELECT * FROM products WHERE (locate('.$catId.',cats)>0)');
        $row = $query->result();
        foreach($row as $product)
            echo $product->name."<br>";
        
        $this->load->view( $this->config->item('template_dir') . 'foot');
    }
    /*
     * Поиск товара
     */
    public function search($search = '', $order = ''){
        $this->load->view( $this->config->item('template_dir') . 'head');
        //В случае поиска через хеадер сайта
        if(!isset($search))
        {
            $search = $this->input->post("search");
        }
        if(isset($search) || isset($order))
            $this->productModel->search($search, $order);
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('search', '', 'trim|required|min_length[3]');
            $this->form_validation->set_rules('order', '', 'trim|required');
            if( $this->form_validation->run() == FALSE )
                $this->load->view('product/search');
            else
            {
                $this->productModel->search();
            }
        }
        $this->load->view( $this->config->item('template_dir') . 'foot');
    }
    
    /*
     * Добавление товара
     */
    public function add(){
        if( $this->siteModel->user->type < UC_SUPPLIER )
            die;
        $this->load->view( $this->config->item('template_dir') . 'head');
        
        if( $this->productModel->add() )
            redirect( $this->config->item('site_url') );
        else
            redirect( $this->config->item('site_url') . 'product/add');
        
        $this->load->view( $this->config->item('template_dir') . 'foot');
    }
    
    /*
     * Редактирование товара
     * Отличий от добавления нет, только в формы вставляем данные из базы
     */
    public function edit($id = 0){
        if( $this->siteModel->user->type < UC_SUPPLIER || ! (int) $id )
            die;
        $this->load->view( $this->config->item('template_dir') . 'head');
        
        $this->db->where('id', $id);
        $query = $this->db->get('products');
        $row = $query->result();
        if(!$row)
        {
            $data['text'] = $this->lang->line('not_id');
            $this->load->view($this->config->item('template_dir') . 'div_error', $data);
        }
        else
        {
    
            if( $this->productModel->add(false, $id) )
                redirect( $this->config->item('site_url') );
            else
                redirect( $this->config->item('site_url') . 'product/edit/' . $id );
        
        }
        $this->load->view( $this->config->item('template_dir') . 'foot');
    }
    
    /*
     * Удаление товара
     */
    public function delete($id = 0){

        $this->siteModel->redirLogin();
        if( ! $this->productModel->product_exists($id) )
            die;
        $row = $this->productModel->product;
        if( $this->siteModel->user->supplier_id == $row[0]->supplier_id || $this->siteModel->user->type >= UC_SUPPLIER)
        {
            $this->db->delete('products', array('id' => $id));
            $this->siteModel->log_write( $this->siteModel->user->username . $this->lang->line('product_delete_log') . $id );
            redirect($this->config->item('site_url') . 'product/');
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
        $this->db->update('products', array('top' => $act));
        $this->siteModel->log_write( $this->siteModel->user->username . $this->lang->line('product_top_log') . $id );
        redirect($this->config->item('site_url') . 'admin/');
    }
    
    /*
     * Добавление категории к товару
     */
    public function add_cat($product = 0, $cat = 0){
        if( ! $this->productModel->exists_product($product) || ! (int)$cat)
            die;
        $row = $this->productModel->product;
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

        $this->db->where('id', $product);
        $this->db->update('products', array('cats' => $str));
    }
    
    /*
     * Удаление категории товара
     */
    public function del_cat($product = 0, $cat = 0){
        if( ! $this->productModel->exists_product($product) || ! (int)$cat)
            die;
        $row = $this->productModel->product;
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
        $this->db->where('id', $product);
        $this->db->update('products', array('cats' => $cats));
        //redirect($this->config->item('site_url'));
    }
    
    /*
     * Добавление отзыва
     */
    public function add_review($product = 0){
        
        if( ! $this->productModel->exists_product($product) )
            die;   
        $this->siteModel->redirLogin();
        $this->load->model('reviewModel');
        if($this->reviewModel->add_review(array('type' => 'product')))
            redirect($this->config->item('site_url') . 'product/view/' . $product);
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
        if($this->reviewModel->add_review(array('type' => 'product', 'act' => 'edit', 'id' => $id)))
            redirect($this->config->item('site_url') . 'product/view/' . $product);
        else
            die($this->lang->line('unknown_error'));
    }
    
    /*
     * Добавление в корзину
     */
    public function new_order($product = 0){
        
        if( !(int) $product )
            die;
        
        $this->productModel->new_order($product, 'yes');
        
    }
    
    /*
     * Открытие сделки
     */
    public function open_order($product = 0){
        
        if( !(int) $product )
            die;
        
        $this->productModel->new_order($product, 'no');
        
    }
    
    /*
     * Удаление сделки
     */
    public function del_order($product = 0){
        
        if( !(int) $product)
            die;
        
        if( $this->productModel->del_order($product) )
            redirect($this->config->item('site_url') . 'product/view/' . $product);
        else
        {
            $array['text'] = $this->lang->line('error_del_order');
            $this->load->view($this->config->item('template_dir') . 'div_error', $array);
        }
        
    }
    
    /*
     * Просмотр статистики товара поставщиком
     */
    public function stat($product = 0){
        
        if( !(int) $product )
            die;
        
        $this->siteModel->redirLogin();
        
        if( ! $this->productModel->exists_product($product) || $this->productModel->product[0]->supplier_id != $this->siteModel->user->supplier_id )
            die;
        
        $this->load->view( $this->config->item('template_dir') . 'head');
        
        /*Отывы*/
        $this->db->where('product_id', $product);
        $query = $this->db->get('reviews');
        $rowReviews = $query->result();
        
        /*Сделки*/
        $this->db->where(array('productid' => $product, 'payment' => 'yes'));
        $query = $this->db->get('orders');
        $ordersNum = $query->num_rows();
        
        $this->load->view('product/stat', array($rowReviews, $ordersNum));
        
        $this->load->view( $this->config->item('template_dir') . 'foot');
    }
    
}

?>
