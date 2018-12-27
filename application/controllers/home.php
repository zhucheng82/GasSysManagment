<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
//phpinfo();exit();
//        $this->load->view('home');
        redirect(ADMIN_SITE_URL);
    }

    public function test()
    {
        $this->load->service('message_service');
        $tpl_id = 5;
        //$sender_id = 0;
        $receiver = '68';
        $receiver_type = 6;
        $arrParam = array('{money}' => 10, '{order_id}' => 10111);

        $this->message_service->send_sys($tpl_id, $receiver, $receiver_type, $arrParam);

    }

}
