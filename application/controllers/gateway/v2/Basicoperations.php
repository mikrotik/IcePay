<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Basicoperations extends Gateway_controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        echo __METHOD__;
    }
}