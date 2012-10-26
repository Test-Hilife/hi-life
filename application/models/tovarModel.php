<?php

class TovarModel extends CI_Model{
    
    public $tovar;
    
    function __construct(){
        parent::__construct();
        $this->load->model('userModel');
        $this->load->library('form_validation');
        $this->lang->load('modules/tovar', $this->config->item('default_language'));
    }
    
    public function exists_tovar($id = 0){
        if(!$id) return false;
        $this->db->where('id', $id);
        $query = $this->db->get('products');
        $row = $query->result();
        if($row){
            $this->tovar = $row;
            return TRUE;
        }else{
            $this->tovar = 0;
            return FALSE;
        }   
    }
    
    public function new_order($tovar = 0, $basket = 'yes'){
        if($this->siteModel->login)
            $user = $this->siteModel->user->id;
        else
        {
            $this->load->helper('string');
            if( trim($this->session->userdata('user')) )
                $user = $this->session->userdata('user');
            else
            {
                $user = random_string('alnum', 16);
                $this->session->set_userdata('user', $user);
            }
        }
        
        $query = $this->db->where(array('productid' => $tovar, 'user' => $user))
                            ->get('orders');
        if($query->num_rows() > 0 && $basket == 'yes')
            redirect($this->config->item('site_url') . 'tovar/view/' . $tovar);
        
        if($basket == 'yes')
            $this->db->insert('orders', array('user' => $user, 'productid' => $tovar, 'basket' => $basket));
        elseif($basket = 'no')
        {
            if($query->num_rows() > 0)
            {
                $this->db->where(array('productid' => $tovar, 'user' => $user));
                $this->db->update('orders', array('basket' => $basket));
            }
            else
            {
                $this->db->insert('orders', array('user' => $user, 'productid' => $tovar, 'basket' => 'no'));
            }
        }
        
        /*
         * Отправляем сообщение на почту
         */
        if( $this->siteModel->login )
        {
            $this->load->library('email');
            $this->email->from($this->config->item('site_email'), $this->config->item('site_name'));
            $this->email->to($this->siteModel->user->email);
            $this->email->subject($this->lang->line('new_order') . $this->config->item('site_name'));
            $array_order = array(
                'name' => $this->siteModel->user->username,
                'order_id' => $this->db->insert_id()
            );
            $this->email->message( $this->load->view('tovar/send_email_new_order', $array_order) );
            $this->email->send();       
        }
        redirect($this->config->item('site_url') . 'tovar/payment/' . $tovar);
    }
    
    public function del_order($tovar = 0){
        if($this->siteModel->login)
            $user = $this->siteModel->user->id;
        elseif( trim($this->session->userdata('user')) )
            $user = $this->session->userdata('user'); 
        else
            return FALSE;
        
        if( $this->db->delete('orders', array('user' => $user, 'productid' => $tovar)) )
            return TRUE;
        else
            return FALSE;
        
    }
    
    public function add($add = true, $id = 0){
        
        $this->siteModel->errorTypeUser(UC_SUPPLIER);
        
        $config = array(
            array(
                'field' => 'name',
                'rules' => 'trim|required|min_length[3]|max_length[200]'
            ),
            array(
                'field' => 'small_text',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'text',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'price',
                'rules' => 'trim|integer|required'
            ),
            array(
                'field' => 'discount',
                'rules' => 'trim|integer'
            ),
            array(
                'field' => 'condition',
                'rules' => 'trim'
            ),
            array(
                'field' => 'price_review',
                'rules' => 'trim|integer'
            ),
            array(
                'field' => 'image1',
                'rules' => 'required'
            ),
            array(
                'field' => 'image2'
            ),
            array(
                'field' => 'image3'
            ),
            array(
                'field' => 'image4'
            ),
            array(
                'field' => 'image5'
            )           
        );
        
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() == FALSE)
            $this->load->view('product/new');
        else
        {
            $array = array(
                'name' => $this->input->post('name'),
                'small_text' => $this->input->post('small_text'),
                'text' => $this->input->post('text'),
                'price' => $this->input->post('cena'),
                'discount' => $this->input->post('skidka'),
                'condition' => $this->input->post('condition'),
                'price_review' => $this->input->post('cena_review'),
                'added' => date('Y-m-d H:i:s'),
                'supplier_id' => $this->siteModel->user->supplier_id
            );
            /* Если редакирует - отправляем на обработку модератору.*/
            if( $add == FALSE )
                $array['moderated'] = 'no';
            
            $config_image = array(
                'upload_path' => './uploads/images/tovar/',
                'allowed_types' => 'gif|jpg|png',
                'max_size' => 2 * 1024,
                'overwrite' => false,
                'encrypt_name' => true,
                'remove_spaces' => true
            );
            $this->load->library('upload', $config_image);
            for($i = 1;$i<=5;$i++)
            {
                if($this->input->post('image' . $i))
                {
                    $this->upload->do_upload('image' . $i);
                    $data = $this->upload->data();
                    $array['image' . $i] = $data['file_name'];
                }
            }
            if( $add )
                $this->db->insert('products', $array);
            else{
                $this->db->where('id', $id);
                $this->db->update('products', $array);
            }
        }
    }
    

}
?>
