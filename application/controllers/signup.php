<?php
class Signup extends CI_Controller {
    
    public function index()
    {

        $this->lang->load('signup', 'russian/modules');
        $this->load->model('head');
        $this->load->view($this->config->item('template_dir') . 'head', $this->head->array);        
        if($this->userLogin()){
            $data["text"] = $this->lang->line('signup');
            $this->load->view($this->config->item('template_dir') . 'div_error', $data);
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean|callback_email_exists');
            $this->form_validation->set_rules('password', '', 'trim|min_length[6]|max_legth[15]|required|matches[passconf]');
            $this->form_validation->set_rules('passconf', '', 'trim|required');            
            $this->form_validation->set_rules('name', '', 'trim|required|xss_clean');            
            $this->form_validation->set_rules('phone', '', 'trim|required|integer|min_legth[5]|max_legth[12]');

            if ($this->form_validation->run() == FALSE)
                $this->load->view('signup');
            else
            {
                $array = array(
                    'email' => $this->input->post('email'),
                    'pass'  => md5( md5('saltsalt') . $this->input->post('password') . 'saltsalt'),
                    'username'  => $this->input->post('name'),
                    'phone' => $this->input->post('phone'),
                    'added' => date('Y-m-d H:i:s'),
                );
                $this->db->insert('users', $array);

                $data = array(
                    'email' => $array['email'],
                    'pass' => $array['pass'],
                    'last_vizit' => $array['added']
                    );
                $this->session->set_userdata($data);
            }
            
        }
        
        $this->load->view($this->config->item('template_dir') . 'foot', $this->head->array);
    }
    
    private function userLogin(){
        if($this->session->userdata('email') != '' && $this->session->userdata('pass') != '')
        {
            return TRUE;
        }
        else
            return FALSE;
    }
    
    public function email_exists($email){
        $this->db->select('id')
                    ->from('users')
                    ->where('email', $email)
                    ->limit(1);
        $num = $this->db->count_all_results();
        if($num > 0){
            $this->form_validation->set_message('email', $this->lang->line('email_exists'));
            return false;
        }
        else
            return true;
    }
}
?>
