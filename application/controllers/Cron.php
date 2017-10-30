<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron extends CRM_Controller
{
    function __construct()
    {
        parent::__construct();
        update_option('cron_has_run_from_cli',1);
        $this->load->model('cron_model');
    }

    public function index()
    {
        $last_cron_run = get_option('last_cron_run');
        if(time() > ($last_cron_run + 300) || $last_cron_run == ''){
          do_action('cron_run');
          $this->cron_model->run();
        }
   }
}
