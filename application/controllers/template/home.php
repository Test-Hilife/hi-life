<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index()
    {
        $pageInfo = array(
            'title' => $this->lang->line('home_page')
        );
        $this->siteModel->setPageInfo($pageInfo);
        

        $this->load->view($this->config->item('template_dir') . 'head');

        $this->load->view('index');

        $this->load->view($this->config->item('template_dir') . 'foot');

    }
}
