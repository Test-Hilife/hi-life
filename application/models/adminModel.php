<?php

class AdminModel extends CI_Model{
    
    private $email;
    private $password;
    
    function AdminModel(){
        parent::__construct();
        $this->load->helper('url');
    }
    
    /*
     * Список пользователей
     */
    public function p_users($id = 0){
        if( $id > 0 )
        {
            $this->db->where('id', $id)->limit(1);
            $query = $this->db->get('users');
            if($query->num_rows() > 0)
            {
                $array['result'] = $query->result();
                $array['result'] = $array['result'][0];
            }else
                return false;
        }
        else
        {
            $this->db->order_by('id', 'desc');
            $query = $this->db->get('users');
            $array['result'] = $query->result();
        }
        return $array;
    }
    
    public function p_users_edit($id){
        $array = array(
            'username' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'type' => $this->input->post('class'),
            'status' => $this->input->post('status')
        );
        if( $this->input->post('password') )
            $array['pass'] = md5_hash($this->input->post('password'));
        $this->db->where('id', $id);
        $this->db->update('users', $array);
        redirect( $this->config->item('site_url') . 'admin/users');
    }
    
    /*
     * Запросы пользователей
     */
    public function p_requests($id = 0){
        if( (int) $id > 0)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('request_admin');
            
            $array['result'] = $query->result();
            $array['result'] = $array['result'][0]; 
        }
        else
        {
            $query = $this->db->get('request_admin');

            $array['result'] = $query->result();
        }
        return $array;
    }
    
    public function p_requests_delete($id){
            $this->db->where('id', $id);
            $this->db->delete('request_admin');
            redirect( $this->config->item('site_url') . 'admin/requests');
    }
    
    private function admin_exists(){
        $this->db->select('id')
                    ->from('users')
                    ->where( array('email' => $this->session->userdata['email'], 'pass' => $this->session->userdata['pass'], 'type' => 'admin') )
                    ->limit('1');
        return $this->db->count_all_results();
    }
    
    public function adminExists(){
        
        if($this->input->post('email') != '' && $this->input->post('password') != '')
        {
            $this->email = $this->input->post('email');
            $this->password = md5_hash( $this->input->post('password') );
        }
        else
        {
            $this->email = $this->session->userdata('admin_email');
            $this->password = $this->session->userdata('admin_passhash');
        }
        
        if( $this->email != '' && $this->password != '' )
            $num = $this->admin_exists();
        else
            $num = 1;
        
        if($num)
            return TRUE;
        else
            return FALSE;
    }
    
    public function login(){
        $this->session->set_userdata('admin_email', $this->email);
        $this->session->set_userdata('admin_passhash', $this->password);
        
        $this->load->helper('url');
        redirect('admin/index');
    }
    
    public function logout(){
        $this->session->set_userdata( array('admin_email' => '', 'admin_passhash' => '') );
        
        $this->load->helper('url');
        redirect('http://localhost/admin/index');
    }
    
}
?>
