<?php

class TovarModel extends CI_Model{
    
    public $tovar;
    
    function TovarModel(){
        parent::__construct();
        $this->load->model('userModel');
        $this->load->library('form_validation');
    }
    
    public function exists_tovar($id){
        if(!$id) return false;
        $this->db->where('id', $id);
        $query = $this->db->get('tovars');
        $row = $query->result();
        if($row){
            $this->tovar = $row;
            return TRUE;
        }else{
            $this->tovar = 0;
            return FALSE;
        }
            
    }
    
    public function add($add = true, $id = 0){
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
                'field' => 'cena',
                'rules' => 'trim|integer|required'
            ),
            array(
                'field' => 'skidka',
                'rules' => 'trim|integer'
            ),
            array(
                'field' => 'condition',
                'rules' => 'trim'
            ),
            array(
                'field' => 'cena_review',
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
            $this->load->view('tovar/new');
        else
        {
            $array = array(
                'name' => $this->input->post('name'),
                'small_text' => $this->input->post('small_text'),
                'text' => $this->input->post('text'),
                'cena' => $this->input->post('cena'),
                'skidka' => $this->input->post('skidka'),
                'condition' => $this->input->post('condition'),
                'cena_review' => $this->input->post('cena_review'),
                'added' => date('Y-m-d H:i:s'),
                'postav_name' => $this->userModel->userLogin('postav_name')
            );
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
                $this->db->insert('tovars', $array);
            else{
                $this->db->where('id', $id);
                $this->db->update('tovars', $array);
            }
        }
    }
}
?>
