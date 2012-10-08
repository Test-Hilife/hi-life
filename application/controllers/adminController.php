<?php

class AdminController extends CI_Controller{
    
    function AdminController(){
        parent::__construct();
        $this->load->model('adminModel');
        $this->lang->load('modules/admin', $this->config->item('default_language'));
    }
    
    private function head(){
        $this->load->model('adminModel');
        $this->loginIn();
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
    
    public function users($act = '', $id = 0){
        $this->head();
        
        switch($act){
            
            case 'edit':
                $user = $this->adminModel->p_users($id);
                if( (int) $id > 0 && count($user) > 0 )
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
            break;
            /*
             * Просмотр всех пользователей
             */
            default:
                $this->load->view( 'admin/users' , $this->adminModel->p_users());
            break;
            
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
        }
    }
}
?>
