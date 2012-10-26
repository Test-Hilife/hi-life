<?php

class AdminController extends CI_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->model('adminModel');
        $this->lang->load('modules/admin', $this->config->item('default_language'));
        $this->loginIn();
    }
    
    private function head(){
        $this->load->view($this->config->item('admin_template_dir') . 'head');
    }
    
    private function foot(){
        $this->load->view($this->config->item('admin_template_dir') . 'foot');
    }
    
    /*
     * Функция для просмотра страниц только определенным классам администрации
     */
    private function errorTypeUser($type = 0){
        if( $this->siteModel->user->type < $type )
            redirect( $this->config->item('site_url') . 'admin/index' );
    }
    
    /*
     * Отправка Email сообщения
     */
    public function send_email($userid = 0){
        $this->errorTypeUser(UC_MODERATOR);
        $this->head();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('subject', '', 'trim|required');
        $this->form_validation->set_rules('descr', '', 'trim|required');
        $this->form_validation->set_rules('email', '', 'trim|required|valid_email');
        if( $this->form_validation->run() == FALSE )
            $this->load->view('send_email');
        else
        {
            $this->load->library('email');
            $this->email->from($this->config->item('site_email'), $this->config->item('site_name'));
            $this->email->to($this->input->post('email'));
            $this->email->subject($this->input->post('subject'));
            $this->email->message($this->input->post('descr'));
            if( $this->email->send() )
            {
                $this->siteModel->log_write( $this->siteModel->user->username . $this->lang->line('send_email_log') . $this->input->post('email') );
                redirect($this->config->item('site_url') . 'admin/');
            }else
                $this->load->view($this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('error_send_email')));
        }
        $this->foot();
    }
    
    /*
     * Просмотр товаров
     */
    public function products($act = 'all'){
        $this->head();
        if( $act == 'all' )
        {
            $this->db->order_by('added', 'DESC');   
        }
        elseif( $act == 'new' )
        {
            $this->db->where('moderated', 'no');
            $this->db->order_by('added', 'desc');
        }
        $query = $this->db->get('products');
        $row = $query->result();
        $this->load->view('admin/products', $row);
        $this->foot();
    }
    
    /*
     * Утверждение купона
     */
    public function product_moderated($tovar = 0, $act = 'yes'){
        $this->errorTypeUser(UC_MODERATOR);
        if ($act != 'yes' && $act != 'no' && (int) $tovar < 1)
            die;
        $this->load->model('tovarModel');
        if( $this->tovar_exists($tovar) )
        {
            $this->db->where('id', $tovar);
            $this->db->update('products', array('moderated', $act));
            $this->siteModel->log_write( $this->siteModel->user->username . $this->lang->line('tovar_moderated_log') .$tovar );
        }
        else
        {
            $this->load->view($this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('not_id')));
        }
    }
    
    public function index(){
        
        $this->head();

        $this->index_page();
        
        $this->foot();
        
    }
    
    /*
     * Список пользователей
     */
    public function users(){
        $this->errorTypeUser(UC_MODERATOR);
        $this->head();
     
        $this->load->view( 'admin/users' , $this->adminModel->p_users());
        
        $this->foot();
    }
    
    /*
     * Редактирвание пользователя
     */
    public function user_edit($id = 0){
        $this->errorTypeUser(UC_MODERATOR);
        $this->head();
        
        $user = $this->adminModel->p_users($id);
        if( $id > 0 && $user > 0 )
        {
                $this->adminModel->p_users_edit($id, $user);
        }
        else
            $this->load->view( $this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('not_id')) );

        $this->foot();
    }
    
    /*
     * Просмотр категорий
     */
    public function cats(){
        $this->errorTypeUser(UC_MODERATOR);
        $this->head();
        
        $query = $this->db->order_by('id', 'desc')->get('cats');
        if($query->num_rows() > 0)
        {
            $this->load->view('admin/cats', $query->result());
        }
        else
            $this->load->view($this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('not_cats')));
        
        $this->foot();
    }
    
    /*
     * Удаление категорий
     */
    public function del_cat($id = 0){
        if( !$id )
            die;
        $this->errorTypeUser(UC_MODERATOR);
        $this->db->delete('cats', array('id' => $id));
    }
    
    /*
     * Добавление категорий
     */
    public function add_cat(){
        $this->errorTypeUser(UC_MODERATOR);
        $this->head();
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', '', 'trim|required');
        $this->form_validation->set_rules('descr', '', 'trim|required');
        $this->form_validation->set_rules('parent_cat', '', 'integer|required');
        if($this->form_validation->run() == FALSE)
        {
            /*
             * Пока в отображение ничего не отправляю, т.е название категорий, для добавления
             * подгатегории. Будет потом, когда будет дизайн.
             */
            $this->load->view('admin/add_cat');
        }
        else
        {
            $array = array(
                'cat_name' => $this->input->post('name'),
                'descr' => $this->input->post('descr')
            );
            if($this->input->post('parent_cat') != 0)
                $array['parent_cat'] = $this->input->post('parent_cat');
            $this->db->insert('cats', $array);
            $this->siteModel->log_write( $this->siteModel->user->username . $this->lang->line('add_cat_log') . $array['cat_name'] );

            redirect($this->config->item('site_url') . 'admin/cats');
        }
        
        $this->foot();
    }
    
    /*
     * Различные вопросы администрации от пользователей
     */
    public function requests($act = '', $id = 0){
        $this->errorTypeUser(UC_MODERATOR);
        $this->head();
        
        switch($act){
            
            /*
             * Удаление запроса
             */
            case 'delete':
                if( ! (int) $id){
                    $array['text'] = $this->lang->line('not_id');
                    $this->load->view( $this->config->item('template_dir') . 'div_error', $array);
                }else{
                    $this->adminModel->p_requests_delete($id);
                }
            break;
            
            /*
             * Просмотр запроса
             */
            case 'view':
                if( !(int) $id )
                    $this->load->view($this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('not_request')));
                else
                {
                    $array = $this->adminModel->p_requests($id);
                    if($array['result']->name)
                    {
                        $this->load->view( 'admin/requests_view', $array );
                    }
                    else
                    {
                        $this->load->view($this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('not_request')));
                    }
                }
            break;
            
            /*
             * Принятие заявки в поставщики
             */
            case 'in_supplier':
                if( !(int) $id )
                    die;
                $this->db->where('id', $id);
                $query = $this->db->get('request_admin');
                $row = $query->result();
                if( !$query->num_rows() || $row[0]->in_supplier == 'no' )
                    die;
                $array = array(
                    'name' => $row[0]->subject,
                    'text' => $row[0]->descr
                );
                $this->db->insert('suppliers', $array);
                $this->db->where('id', $row[0]->userid);
                $this->db->update('users', array('supploer_id' => $this->db->insert_id()));
                $this->siteModel->log_write( $this->siteModel->user->username . $this->lang->line('supplier_add_log') . $this->db->insert_id() );
                redirect( $this->config->item('site_url') . 'admin/requests');
            break;
            
            /*
             * Просмотр всех запросов
             */
            default:
                $this->load->view( 'admin/requests', $this->adminModel->p_requests() );
            break;
        
        }
        
        $this->foot();
        
    }
    
    /*
     * Главная страница
     */
    private function index_page(){
        //$this->loginIn();
    }
    
    private function loginIn(){
        if( ! $this->adminModel->adminExists() )
        {
            $this->head();
            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', '', 'required|trim|xss_clean');
            $this->form_validation->set_rules('password', '', 'required|trim');

            if($this->form_validation->run() == FALSE)
            {
                $this->load->view('user/auth');
            }
            else
            {
                if($this->adminModel->adminExists())
                    $this->adminModel->login();
                else
                    $this->load->view('user/auth');
            }
            $this->foot();
        }
    }
}
?>
