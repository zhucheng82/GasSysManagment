<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Macx
 * Date: 2018/12/12
 * Time: 上午11:42
 */



class Data_analysis extends MY_Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     *
     */
    public function index()
    {
        $itemSelect = array('index'=>2);
        $this->load->view('admin/top', $itemSelect);

        $data = array();
        $this->load->view('admin/meter_data_analysis_view', $data);
    }

}