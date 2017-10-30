<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends Rest
{
    protected $_objDateTime;
    protected $_arraytoxml;
    protected $_CI;

    public function __construct(){

        parent::__construct();

        $this->_arraytoxml = new ArrayToXML();
        $this->_CI = &get_instance();

    }

    public function process($data,$status,$code = false,$hasError = false,$contentType = 'json')
    {
        $this->_objDateTime = new DateTime('NOW');

        $this->setCode($code);
        $this->setContentType($contentType);

        if ($hasError == false && $data['ResultState'] == true) {
            $output['Result'] = $code;
            $output['Message'] = $this->get_rest_status_message();
            $output['TrackingMemberCode'] = 'Test 1111-2222-3333-4444';
            $output['TransactionId'] = UUID::trxid(8);
            $output['TransactionGuid'] = UUID::v5('1546058f-5a25-4334-85ae-e68f2a44bbaf', UUID::trxid(8));
            $output['TransactionDateTime'] = $this->_objDateTime->format(DateTime::ISO8601);

            $this->response($this->$contentType($output), $status);
        } else {
            $error['Result'] = $code;
            $error['Message'] = $this->get_rest_status_message();
            $error['Reason'] = 'Error message from processor';
            $error['TrackingMemberCode'] = 'Test 5555-6666-7777-8888';
            $error['TransactionId'] = UUID::trxid(8);
            $error['TransactionGuid'] = UUID::v5('1546058f-5a25-4334-85ae-e68f2a44bbaf', UUID::trxid(8));
            $error['TransactionDateTime'] = $this->_objDateTime->format(DateTime::ISO8601);

            $this->response($this->$contentType($error), $status);
        }

    }

    private function json($data) {

        if (is_array($data)) {
            return json_encode($data);
        }
    }

    private function xml($data) {

        if (is_array($data)) {
            return $this->_arraytoxml->toXml($data);
        }
    }
}