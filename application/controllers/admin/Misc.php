<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Misc extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('misc_model');
    }
    public function notifications_check()
    {
        $this->load->view('admin/includes/notifications');
    }

    public function fetch_address_info_gmaps(){
        include_once(APPPATH.'third_party/JD_Geocoder_Request.php');

        $data = $this->input->post();
        $address = '';

        $address.= $data['address'];
        if(!empty($data['city'])){
            $address .= ', '.$data['city'];
        }

        if(!empty($data['country'])){
            $address .= ', '.$data['country'];
        }

        $georequest = new JD_Geocoder_Request();
        $georequest->forwardSearch($address);
        echo json_encode($georequest);
    }

    public function get_taxes_dropdown_template()
    {
        $name    = $this->input->post('name');
        $taxname = $this->input->post('taxname');
        echo $this->misc_model->get_taxes_dropdown_template($name, $taxname);
    }
    public function dismiss_cron_setup_message()
    {
        update_option('hide_cron_is_required_message', 1);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function tinymce_file_browser()
    {
        $this->load->view('admin/includes/elfinder_tinymce');
    }
    public function get_relation_data()
    {
        if ($this->input->post()) {
            $type = $this->input->post('type');
            $data = get_relation_data($type, '', $this->input->post('connection_type'), $this->input->post('connection_id'));
            if ($this->input->post('rel_id')) {
                $rel_id = $this->input->post('rel_id');
            } else {
                $rel_id = '';
            }
            echo json_encode(init_relation_options($data, $type, $rel_id));
            die;
            // init_relation_options($data, $type, $rel_id);
        }
    }
    public function delete_sale_activity($id)
    {
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->delete('tblsalesactivity');
        }
    }
    public function upload_sales_file()
    {
        handle_sales_attachments($this->input->post('rel_id'), $this->input->post('type'));
    }
    public function add_sales_external_attachment()
    {
        if ($this->input->post()) {
            $file = $this->input->post('files');
            $this->misc_model->add_attachment_to_database($this->input->post('rel_id'), $this->input->post('type'), $file, $this->input->post('external'));
        }
    }
    public function toggle_file_visibility($id)
    {

        $this->db->where('id', $id);
        $row = $this->db->get('tblfiles')->row();
        if ($row->visible_to_customer == 1) {
            $v = 0;
        } else {
            $v = 1;
        }

        $this->db->where('id', $id);
        $this->db->update('tblfiles', array(
            'visible_to_customer' => $v
        ));
        echo $v;
    }
    public function format_date()
    {
        if ($this->input->post()) {
            $date = $this->input->post('date');
            $date = strtotime(current(explode("(", $date)));
            echo _d(date('Y-m-d', $date));
        }
    }
    public function send_file()
    {
        if ($this->input->post('send_file_email')) {
            if ($this->input->post('file_path')) {
                $this->load->model('emails_model');
                $this->emails_model->add_attachment(array(
                    'attachment' => $this->input->post('file_path'),
                    'filename' => $this->input->post('file_name'),
                    'type' => $this->input->post('filetype'),
                    'read' => true
                ));
                $message = $this->input->post('send_file_message');
                $success = $this->emails_model->send_simple_email($this->input->post('send_file_email'), $this->input->post('send_file_subject'), $message);
                if ($success) {
                    set_alert('success', _l('custom_file_success_send', $this->input->post('send_file_email')));
                } else {
                    set_alert('warning', _l('custom_file_fail_send'));
                }
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function update_ei_items_order($type)
    {
        $data = $this->input->post();
        foreach ($data['data'] as $order) {
            $this->db->where('id', $order[0]);
            $this->db->update('tblitems_in', array(
                'item_order' => $order[1]
            ));
        }
    }
    /* Since version 1.0.2 add client reminder */
    public function add_reminder($rel_id_id, $rel_type)
    {
        $message    = '';
        $alert_type = 'warning';
        if ($this->input->post()) {
            $success = $this->misc_model->add_reminder($this->input->post(), $rel_id_id);
            if ($success) {
                $alert_type = 'success';
                $message    = _l('reminder_added_successfuly');
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function get_reminders($id, $rel_type)
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('reminders', array(
                'id' => $id,
                'rel_type' => $rel_type
            ));
        }
    }
    /* Since version 1.0.2 delete client reminder */
    public function delete_reminder($rel_id, $id, $rel_type)
    {
        if (!$id && !$rel_id) {
            die('No reminder found');
        }
        $success    = $this->misc_model->delete_reminder($id);
        $alert_type = 'warning';
        $message    = _l('reminder_failed_to_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('reminder_deleted');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function run_cron_manually()
    {
        if (is_admin()) {
            $this->load->model('cron_model');
            $this->cron_model->run(true);
            redirect(admin_url('settings?group=cronjob'));
        }
    }
    /* Since Version 1.0.1 - General search */
    public function search()
    {
        $data['result'] = $this->misc_model->perform_search($this->input->post('q'));
        $this->load->view('admin/search', $data);
    }
    public function add_note($rel_id, $rel_type)
    {
        if ($this->input->post()) {
            $success = $this->misc_model->add_note($this->input->post(), $rel_type, $rel_id);
            if ($success) {
                set_alert('success', _l('added_successfuly', _l('note')));
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function edit_note($id)
    {
        if ($this->input->post()) {
            $success = $this->misc_model->edit_note($this->input->post(), $id);
            echo json_encode(array(
                'success' => $success,
                'message' => _l('note_updated_successfuly')
            ));
        }
    }
    public function delete_note($id)
    {
        $success = $this->misc_model->delete_note($id);
        if ($success) {
            set_alert('success', _l('deleted', _l('note')));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    /* Remove customizer open from database */
    public function set_setup_menu_closed()
    {
        if ($this->input->is_ajax_request()) {
            $this->session->set_userdata(array(
                'setup-menu-open' => ''
            ));
        }
    }
    /* Set session that user clicked on setup_menu menu link to stay open */
    public function set_setup_menu_open()
    {
        if ($this->input->is_ajax_request()) {
            $this->session->set_userdata(array(
                'setup-menu-open' => true
            ));
        }
    }
    /* User dismiss announcement */
    public function dismiss_announcement($id)
    {
        $this->misc_model->dismiss_announcement($id);
        redirect($_SERVER['HTTP_REFERER']);
    }
    /* Set notifications to read */
    public function set_notifications_read()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->misc_model->set_notifications_read()
            ));
        }
    }
    public function set_notification_read($id)
    {
        $this->misc_model->set_notification_read($id);
    }
    /* Check if staff email exists / ajax */
    public function staff_email_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $member_id = $this->input->post('memberid');
                if ($member_id != '') {
                    $this->db->where('staffid', $member_id);
                    $_current_email = $this->db->get('tblstaff')->row();
                    if ($_current_email->email == $this->input->post('email')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('email', $this->input->post('email'));
                $total_rows = $this->db->count_all_results('tblstaff');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }
    /* Check if client email exists/  ajax */
    public function contact_email_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $userid = $this->input->post('userid');
                if ($userid != '') {
                    $this->db->where('id', $userid);
                    $_current_email = $this->db->get('tblcontacts')->row();
                    if ($_current_email->email == $this->input->post('email')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('email', $this->input->post('email'));
                $total_rows = $this->db->count_all_results('tblcontacts');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }
    /* Goes blank page but with messagae access danied / message set from session flashdata */
    public function access_denied()
    {
        $this->load->view('admin/blank_page');
    }
    /* Goes to blank page with message page not found / message set from session flashdata */
    public function not_found()
    {
        $this->load->view('admin/blank_page');
    }
    /* Get role permission for specific role id / Function relocated here becuase the Roles Model have statement on top if has role permission */
    public function get_role_permissions_ajax($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->roles_model->get_role_permissions($id));
            die();
        }
    }
}
