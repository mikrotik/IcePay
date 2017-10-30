<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class Gateway_controller extends CRM_Controller
{

    public $_arraytoxml;
    public $_api;
    public $_content_type = 'json';

    function __construct()
    {
        parent::__construct();

        $content_type = $this->input->get('content_type');

        $this->load->library('arraytoxml');

        $this->load->library('gateway/rest');
        $this->load->library('gateway/api');

        $this->_api = new Api();

        $this->load->helper('perfex_xml_value_prep');
        $this->load->helper('gateway/api');
        $this->load->helper('perfex_uuid');

        if (isset($content_type)){
            $this->_content_type = $this->input->get('content_type');
        }
    }
}