<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Expenses extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('expenses_model');
    }
    public function index($id = '', $clientid = '')
    {
        $this->list_expenses($id, $clientid);
    }
    public function list_expenses($id = '', $clientid = '')
    {
        if (!has_permission('expenses', '', 'view') && !has_permission('expenses', '', 'view_own')) {
            access_denied('expenses');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('expenses', array(
                'id' => $id,
                'clientid' => $clientid
            ));
        }
        $data['expenseid'] = '';
        if (is_numeric($id)) {
            $data['expenseid'] = $id;
        }
        $data['categories'] = $this->expenses_model->get_category();
        $data['years']      = $this->expenses_model->get_expenses_years();
        $data['title']      = _l('expenses');

        $this->load->view('admin/expenses/manage', $data);
    }
    public function expense($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('expenses', '', 'create')) {
                    set_alert('danger', _l('access_denied'));
                    echo json_encode(array(
                        'url' => admin_url('expenses/expense')
                    ));
                    die;
                }
                $id = $this->expenses_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('expense')));
                    echo json_encode(array(
                        'url' => admin_url('expenses/list_expenses/' . $id),
                        'expenseid' => $id
                    ));
                    die;
                }
                echo json_encode(array(
                    'url' => admin_url('expenses/expense')
                ));
                die;
            } else {
                if (!has_permission('expenses', '', 'edit')) {
                    set_alert('danger', _l('access_denied'));
                    echo json_encode(array(
                        'url' => admin_url('expenses/expense/' . $id)
                    ));
                    die;
                }
                $success = $this->expenses_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('expense')));
                }
                echo json_encode(array(
                    'url' => admin_url('expenses/list_expenses/' . $id),
                    'expenseid' => $id
                ));
                die;
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('expense_lowercase'));
        } else {
            $data['expense'] = $this->expenses_model->get($id);

            if (!$data['expense'] || (!has_permission('expenses', '', 'view') && $data['expense']->addedfrom != get_staff_user_id())) {
                blank_page(_l('expense_not_found'));
            }

            $title = _l('edit', _l('expense_lowercase'));
        }


        if ($this->input->get('customer_id')) {
            $data['customer_id']        = $this->input->get('customer_id');
            $data['do_not_auto_toggle'] = true;
        }

        $this->load->model('taxes_model');
        $this->load->model('payment_modes_model');
        $this->load->model('currencies_model');
        $this->load->model('projects_model');

        $where_clients = 'tblclients.active=1';

        if (!has_permission('customers', '', 'view')) {
            $where_clients .= ' AND tblclients.userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id=' . get_staff_user_id() . ')';
        }

        $data['customers'] = $this->clients_model->get('', $where_clients);
        if ($id != '' && $data['expense']->clientid != 0) {
            if (total_rows('tblclients', array(
                'active' => 0,
                'userid' => $data['expense']->clientid
            )) > 0 || (total_rows('tblcustomeradmins', array(
                'staff_id' => get_staff_user_id(),
                'customer_id' => $data['expense']->clientid
            )) == 0 && !has_permission('customers', '', 'view'))) {
                $data['customers'][] = $this->clients_model->get($data['expense']->clientid, array(), 'row_array');
            }
        }


        $data['projects'] = array();
        if (isset($data['expense']) && $data['expense']->clientid != 0 || isset($data['customer_id'])) {
            $where             = '';
            $where_customer_id = (isset($data['customer_id']) ? $data['customer_id'] : $data['expense']->clientid);
            $where .= 'clientid=' . $where_customer_id;
            if (!has_permission('projects', '', 'view')) {
                $where .= ' AND id IN(SELECT project_id from tblprojectmembers WHERE staff_id=' . get_staff_user_id() . ')';
            }

            $data['projects'] = $this->projects_model->get('', $where);

            if ($id != '' && $data['expense']->project_id != 0) {
                if (total_rows('tblprojectmembers', array(
                    'staff_id' => get_staff_user_id(),
                    'project_id' => $data['expense']->project_id
                )) == 0 && !has_permission('projects', '', 'view')) {
                    $this->db->where('id', $data['expense']->project_id);
                    $data['projects'][] = $this->db->get('tblprojects')->row_array();
                }
            }
        }

        $data['taxes']         = $this->taxes_model->get();
        $data['categories']    = $this->expenses_model->get_category();
        $data['payment_modes'] = $this->payment_modes_model->get('', array(
            'invoices_only !=' => 1
        ));
        $data['currencies']    = $this->currencies_model->get();
        $data['title']         = $title;
        $this->load->view('admin/expenses/expense', $data);
    }
    function get_expenses_total()
    {
        if ($this->input->post()) {
            $data['totals'] = $this->expenses_model->get_expenses_total($this->input->post());
            if ($data['totals']['currency_switcher'] == true) {
                $this->load->model('currencies_model');
                $data['currencies'] = $this->currencies_model->get();
            }

            $data['_currency'] = $data['totals']['currencyid'];
            $this->load->view('admin/expenses/expenses_total_template', $data);
        }
    }
    public function delete($id)
    {
        if (!has_permission('expenses', '', 'delete')) {
            access_denied('expenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/list_expenses'));
        }
        $response = $this->expenses_model->delete($id);
        if ($response === true) {
            set_alert('success', _l('deleted', _l('expense')));
        } else {
            if (is_array($response) && $response['invoiced'] == true) {
                set_alert('warning', _l('expense_invoice_delete_not_allowed'));
            } else {
                set_alert('warning', _l('problem_deleting', _l('expense_lowercase')));
            }
        }
        redirect(admin_url('expenses/list_expenses'));
    }
    public function copy($id)
    {
        if (!has_permission('expenses', '', 'create')) {
            access_denied('expenses');
        }
        $new_expense_id = $this->expenses_model->copy($id);
        if ($new_expense_id) {
            set_alert('success', _l('expense_copy_success'));
            redirect(admin_url('expenses/expense/' . $new_expense_id));
        } else {
            set_alert('warning', _l('expense_copy_fail'));
        }
        redirect(admin_url('expenses/list_expenses/' . $id));
    }
    public function convert_to_invoice($id)
    {
        if (!has_permission('invoices', '', 'create')) {
            access_denied('Convert Expense to Invoice');
        }
        if (!$id) {
            redirect(admin_url('expenses/list_expenses'));
        }
        $draft_invoice = false;
        if ($this->input->get('save_as_draft')) {
            $draft_invoice = true;
        }

        $params = array();
        if ($this->input->get('include_note') == 'true') {
            $params['include_note'] = true;
        }

        if ($this->input->get('include_name') == 'true') {
            $params['include_name'] = true;
        }

        $invoiceid = $this->expenses_model->convert_to_invoice($id, $draft_invoice, $params);
        if ($invoiceid) {
            set_alert('success', _l('expense_converted_to_invoice'));
            redirect(admin_url('invoices/invoice/' . $invoiceid));
        } else {
            set_alert('warning', _l('expense_converted_to_invoice_fail'));
        }
        redirect(admin_url('expenses/list_expenses/' . $id));
    }
    public function get_expense_data_ajax($id)
    {
        if (!has_permission('expenses', '', 'view') && !has_permission('expenses', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }
        $expense = $this->expenses_model->get($id);

        if (!$expense || (!has_permission('expenses', '', 'view') && $expense->addedfrom != get_staff_user_id())) {
            echo _l('expense_not_found');
            die;
        }

        $data['expense'] = $expense;
        if ($expense->billable == 1) {
            if ($expense->invoiceid !== NULL) {
                $this->load->model('invoices_model');
                $data['invoice'] = $this->invoices_model->get($expense->invoiceid);
            }
        }

        $data['child_expenses'] = $this->expenses_model->get_child_expenses($id);
        $this->load->model('staff_model');
        $data['members'] = $this->staff_model->get('', 1);
        $this->load->view('admin/expenses/expense_preview_template', $data);
    }
    public function get_customer_change_data($customer_id = '')
    {
        $this->load->model('projects_model');

        $where = '';
        if ($customer_id != '') {
            $where = 'clientid=' . $customer_id;
        }

        if (!has_permission('projects', '', 'view') && $customer_id != '') {
            $where .= ' AND id IN(SELECT project_id from tblprojectmembers WHERE staff_id=' . get_staff_user_id() . ')';
        }

        if ($where == '') {
            $where = array();
        }

        echo json_encode(array(
            'projects' => $this->projects_model->get('', $where),
            'client_currency' => $this->clients_model->get_customer_default_currency($customer_id)
        ));
    }
    public function categories()
    {
        if (!is_admin()) {
            access_denied('expenses');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('expenses_categories');
        }
        $data['title'] = _l('expense_categories');
        $this->load->view('admin/expenses/manage_categories', $data);
    }
    public function category()
    {
        if (!is_admin()) {
            access_denied('expenses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->expenses_model->add_category($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('expense_category')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->expenses_model->update_category($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('expense_category')));
                }
            }
        }
    }
    public function delete_category($id)
    {
        if (!is_admin()) {
            access_denied('expenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/categories'));
        }
        $response = $this->expenses_model->delete_category($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('expense_category_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('expense_category')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expense_category_lowercase')));
        }
        redirect(admin_url('expenses/categories'));
    }
    public function add_expense_attachment($id)
    {
        handle_expense_attachments($id);
        echo json_encode(array(
            'url' => admin_url('expenses/list_expenses/' . $id)
        ));
    }
    public function delete_expense_attachment($id, $preview = '')
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'expense');
        $file = $this->db->get('tblfiles')->row();

        if ($file->staffid == get_staff_user_id() || is_admin()) {

            $success = $this->expenses_model->delete_expense_attachment($id);
            if ($success) {
                set_alert('success', _l('deleted', _l('expense_receipt')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('expense_receipt_lowercase')));
            }
            if ($preview == '') {
                redirect(admin_url('expenses/expense/' . $id));
            } else {
                redirect(admin_url('expenses/list_expenses/' . $id));
            }
        } else {
            access_denied('expenses');
        }
    }
}
