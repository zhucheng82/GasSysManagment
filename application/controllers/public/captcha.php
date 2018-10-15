<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Captcha extends CI_Controller 
{

   public function index()
   {
        $this->load->helper('captcha');
        create_captcha(4,90,26,'verify');
   }

}