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

            $this->_api->process(array(),406,417,406,true,$this->_content_type);

        }

        // grab the request
        $request = trim(file_get_contents('php://input'));

        // find out if the request is valid XML
        $xml = @simplexml_load_string($request);

        // if it is not valid XML...
        if (!$xml) {

            $this->_api->process(array(),412,510,412,true,$this->_content_type);

        } else {

            // Make an array out of the XML
            $this->_request = $this->arraytoxml->toArray($xml);

        }

        // Check if memberGuid has valid format
        if (!UUID::is_valid($this->_request['memberGuid'])){
            $this->_api->process(array(),200,511,0,true,$this->_content_type);
        }

    }

    public function Charge()
    {
        $this->_api->process(array(), 200,null,0, false, $this->_content_type);
    }

    public function Authorize()
    {
        $this->_api->process(array(), 200,null,0, false, $this->_content_type);
    }

    public function Refund()
    {
        $this->_api->process(array(), 200,null,0, false, $this->_content_type);
    }

    public function Capture()
    {
        $this->_api->process(array(), 200,null,0, false, $this->_content_type);
    }

    public function Void()
    {
        $this->_api->process(array(), 200,null,0, false, $this->_content_type);
    }
}