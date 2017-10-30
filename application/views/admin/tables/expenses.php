<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns      = array(
    'tblexpenses.id',
    'category',
    'amount',
    'expense_name',
    'file_name',
    'date',
    'project_id',
    'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
    'invoiceid',
    'reference_no',
    'paymentmode',
);
$join          = array(
    'LEFT JOIN tblclients ON tblclients.userid = tblexpenses.clientid',
    'LEFT JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category',
    'LEFT JOIN tblprojects ON tblprojects.id = tblexpenses.project_id',
    'LEFT JOIN tblfiles ON tblfiles.rel_id = tblexpenses.id AND rel_type="expense"',
);
$custom_fields = get_custom_fields('expenses', array(
    'show_on_table' => 1
));
$i             = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_'.$i;
    if($field['type'] == 'date_picker') {
      $select_as = 'date_picker_cvalue_'.$i;
    }
    array_push($aColumns,'ctable_'.$i.'.value as '.$select_as);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $i . ' ON tblexpenses.id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}
$where = array();
$filter = array();
include_once(APPPATH.'views/admin/tables/includes/expenses_filter.php');

if (is_numeric($clientid)) {
    array_push($where,'AND tblexpenses.clientid=' . $clientid);
}

if(!has_permission('expenses','','view')){
    array_push($where,'AND tblexpenses.addedfrom='.get_staff_user_id());
}

$aColumns = do_action('expenses_table_sql_columns',$aColumns);
$join = do_action('expenses_table_sql_join',$join);
$where = do_action('expenses_table_sql_where',$where);

$sIndexColumn = "id";
$sTable       = 'tblexpenses';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblexpensescategories.name as category_name',
    'billable',
    'tblexpenses.clientid',
    'currency',
    'tax',
    'tblprojects.name as project_name',
));
$output       = $result['output'];
$rResult      = $result['rResult'];
$this->_instance->load->model('currencies_model');
$this->_instance->load->model('payment_modes_model');
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if($aColumns[$i] == 'tblexpenses.id'){
            $_data = '<span class="label label-default inline-block">'.$_data.'</span>';
        } else if ($aColumns[$i] == 'category') {
            if (is_numeric($clientid)) {
                $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['tblexpenses.id']) . '">' . $aRow['category_name'] . '</a>';
            } else {
                $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['tblexpenses.id']) . '" onclick="init_expense(' . $aRow['tblexpenses.id'] . ');return false;">' . $aRow['category_name'] . '</a>';
            }
            if ($aRow['billable'] == 1) {
                if ($aRow['invoiceid'] == NULL) {
                    $_data .= '<p class="text-danger">' . _l('expense_list_unbilled') . '</p>';
                } else {
                    if (total_rows('tblinvoices', array(
                        'id' => $aRow['invoiceid'],
                        'status' => 2
                    )) > 0) {
                        $_data .= '<p class="text-success">' . _l('expense_list_billed') . '</p>';
                    } else {
                        $_data .= '<p class="text-success">' . _l('expense_list_invoice') . '</p>';
                    }
                }
            }
        } else if ($aColumns[$i] == 'amount') {
            $total = $_data;
            if ($aRow['tax'] != 0) {
                $_tax = get_tax_by_id($aRow['tax']);
                $total += ($total / 100 * $_tax->taxrate);
            }
            $_data = format_money($total, $this->_instance->currencies_model->get($aRow['currency'])->symbol);
        } else if ($i == 7) {
            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
        } else if ($aColumns[$i] == 'paymentmode') {
            $_data = '';
            if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                $payment_mode = $this->_instance->payment_modes_model->get($aRow['paymentmode'], array(), false,true);
                if($payment_mode){
                    $_data = $payment_mode->name;
                }
            }
        } else if($aColumns[$i] == 'project_id'){
            $_data = '<a href="'.admin_url('projects/view/'.$aRow['project_id']).'">'.$aRow['project_name'].'</a>';
        } else if($aColumns[$i] == 'file_name'){
            if(!empty($_data)){
                $_data=  '<a href="'.site_url('download/file/expense/'.$aRow['tblexpenses.id']).'">'.$_data.'</a>';
            }
        } else if($aColumns[$i] == 'date'){
            $_data = _d($_data);
        } else if($aColumns[$i] == 'invoiceid'){
            if($_data){
                $_data = '<a href="'.admin_url('invoices/list_invoices/'.$_data).'">'.format_invoice_number($_data).'</a>';
            } else {
                $_data = '';
            }

        } else {
            if(strpos($aColumns[$i],'date_picker_') !== false){
                 $_data = _d($_data);
            }
        }

        $hook_data = do_action('expenses_tr_data_output',array('output'=>$_data,'column'=>$aColumns[$i],'id'=>$aRow['tblexpenses.id']));
        $_data = $hook_data['output'];

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
