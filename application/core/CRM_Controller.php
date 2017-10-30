<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

class CRM_Controller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->check_installation();
        $this->db->reconnect();
        $timezone = get_option('default_timezone');
        date_default_timezone_set($timezone);
        do_action('perfex_init');
    }
    private function check_installation()
    {
        if (is_dir(FCPATH . 'install') && ENVIRONMENT != 'development') {
            echo '<h3>Delete the install folder</h3>';
            die;
        }
    }
}
