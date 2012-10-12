<?php

class AdminController extends CI_Controller{
    
    function AdminController(){
        parent::__construct();
        $this->load->model('adminModel');
        $this->lang->load('modules/admin', $this->config->item('default_language'));
        $this->loginIn();
    }
    
    private function head(){
        $this->load->model('adminModel');
        
        $this->load->view($this->config->item('admin_template_dir') . 'head');
    }
    
    private function foot(){
        $this->load->view($this->config->item('admin_template_dir') . 'foot');
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
        $this->head();
     
        $this->load->view( 'admin/users' , $this->adminModel->p_users());
        
        $this->foot();
    }
    
    /*
     * Редактирвание пользователя
     */
    public function user_edit($id = 0){
        
        $this->head();
        
        $user = $this->adminModel->p_users($id);
        if( $id > 0 && $user > 0 )
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', '', 'required|trim|xss_clean|max_length[100]');
            $this->form_validation->set_rules('email', '', 'required|trim|email_valid');
            $this->form_validation->set_rules('phone', '', 'trim|integer');
            $this->form_validation->set_rules('class', '', 'trim|required');
            $this->form_validation->set_rules('status', '', 'trim|required');
            $this->form_validation->set_rules('password', '', 'trim');

            if( $this->form_validation->run() == FALSE)
                $this->load->view( 'admin/users_edit' , $user );
            else
            {
                $this->adminModel->p_users_edit($id);
            }
        }
        else
            $this->load->view( $this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('not_id')) );

        $this->foot();
    }
    
    /*
     * Просмотр категорий
     */
    public function cats(){
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
        $this->db->delete('cats', array('id' => $id));
    }
    
    /*
     * Добавление категорий
     */
    public function add_cat(){
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
            redirect($this->config->item('site_url') . 'admin/cats');
        }
        
        $this->foot();
    }
    
    /*
     * Различные вопросы администрации от пользователей
     */
    public function requests($act = '', $id = 0){
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
                    if($array['result']->name){
                        $this->load->view( 'admin/requests_view', $array );
                    }else{
                        $view = $this->config->item('template_dir') . 'div_error';
                        $this->load->view($this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('not_request')));
                    }
                }
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
        echo 'sss';
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
