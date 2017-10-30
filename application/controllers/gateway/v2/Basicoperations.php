<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Basicoperations extends Gateway_controller
{

    private $_request = array();
    private $_processor = array();
    private $_merchant_processor = array();
    private $_object;

    function __construct()
    {
        parent::__construct();

    }

    public function init()
    {

        // Check if method is exist
        if (!method_exists($this,$this->uri->segment(4))){

            $this->_api->process(array(),406,406,true,$this->_content_type);

        }
    }
}