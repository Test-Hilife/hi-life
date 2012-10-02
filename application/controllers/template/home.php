<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index()
    {
        $this->load->model("head");
           
        //Шапка
        $this->load->view($this->config->item('template_dir') . 'head', $this->head->array);
        //Основа
        $this->load->view('index');
        //Футер
        $this->load->view($this->config->item('template_dir') . 'foot', $this->head->array);

    }
}
