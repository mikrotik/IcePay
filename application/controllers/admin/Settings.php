<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

class Settings extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('payment_modes_model');
        $this->load->model('settings_model');

    }
    /* View all settings */
    public function index()
    {
        if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }
        if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
            $logo_uploaded    = (handle_company_logo_upload() ? true : false);
            $favicon_uploaded = (handle_favicon_upload() ? true : false);

            $post_data = $this->input->post(NULL, FALSE);
            $success   = $this->settings_model->update($post_data);
            if ($success > 0) {
                set_alert('success', _l('settings_updated'));
            }

            if ($logo_uploaded || $favicon_uploaded) {
                set_debug_alert(_l('logo_favicon_changed_notice'));
            }

            // Do hard refresh on general for the logo
            if ($this->input->get('group') == 'general') {
                redirect(admin_url('settings?group=' . $this->input->get('group')), 'refresh');
            } else {
                redirect(admin_url('settings?group=' . $this->input->get('group')));
            }
        }
        $this->load->model('taxes_model');
        $this->load->model('tickets_model');
        $this->load->model('leads_model');
        $data['taxes']                                   = $this->taxes_model->get();
        $data['ticket_priorities']                       = $this->tickets_model->get_priority();
        $data['ticket_priorities']['callback_translate'] = 'ticket_priority_translate';
        $data['roles']                                   = $this->roles_model->get();
        $data['leads_sources']                           = $this->leads_model->get_source();
        $data['leads_statuses']                          = $this->leads_model->get_status();
        $data['title']                                   = _l('options');
        if (!$this->input->get('group') || ($this->input->get('group') == 'update' && !is_admin())) {
            $view = 'general';
        } else {
            $view = $this->input->get('group');
        }
        if ($view == 'update') {
            if (!extension_loaded('curl')) {
                $data['update_errors'][] = 'CURL Extension not enabled';
                $data['latest_version']  = 0;
                $data['update_info']     = json_decode("");
            } else {
                $data['update_info'] = $this->misc_model->get_update_info();
                if (strpos($data['update_info'], 'Curl Error -') !== FALSE) {
                    $data['update_errors'][] = $data['update_info'];
                    $data['latest_version']  = 0;
                    $data['update_info']     = json_decode("");
                } else {
                    $data['update_info']    = json_decode($data['update_info']);
                    $data['latest_version'] = $data['update_info']->latest_version;
                    $data['update_errors']  = array();
                }
            }

            if (!extension_loaded('zip')) {
                $data['update_errors'][] = 'ZIP Extension not enabled';
            }

            $data['current_version'] = $this->db->get('tblmigrations')->row()->version;
        }

        $data['contacts_permissions'] = $this->perfex_base->get_contact_permissions();
        $this->load->library('pdf');
        $data['payment_gateways'] = $this->payment_modes_model->get_online_payment_modes(true);
        $data['group']            = $this->input->get('group');
        $data['group_view']       = $this->load->view('admin/settings/includes/' . $view, $data, true);
        $this->load->view('admin/settings/all', $data);
    }
    /* Remove company logo from settings / ajax */
    public function remove_company_logo()
    {
        do_action('before_remove_company_logo');
        if (!has_permission('settings', '', 'delete')) {
            access_denied('settings');
        }
        if (file_exists(get_upload_path_by_type('company') . '/' . get_option('company_logo'))) {
            unlink(get_upload_path_by_type('company') . '/' . get_option('company_logo'));
        }
        update_option('company_logo', '');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function remove_favicon()
    {
        do_action('before_remove_favicon');
        if (!has_permission('settings', '', 'delete')) {
            access_denied('settings');
        }
        if (file_exists(get_upload_path_by_type('company') . '/' . get_option('favicon'))) {
            unlink(get_upload_path_by_type('company') . '/' . get_option('favicon'));
        }
        update_option('favicon', '');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function delete_option($id)
    {
        if (!has_permission('settings', '', 'delete')) {
            access_denied('settings');
        }
        echo json_encode(array(
            'success' => delete_option($id)
        ));
    }
}
