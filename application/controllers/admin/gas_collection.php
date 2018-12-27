<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Macx
 * Date: 2018/12/12
 * Time: 下午3:22
 */
class Gas_collection extends MY_Admin_Controller
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
        $itemSelect = array('index'=>3);
        $this->load->view('admin/top', $itemSelect);

        $data = array();
        $this->load->view('admin/meter_gas_collection_view', $data);
    }

}