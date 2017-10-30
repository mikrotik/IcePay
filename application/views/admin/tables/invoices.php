<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns = array(
    'number',
    'total',
    'total_tax',
    'YEAR(date)',
    'date',
    'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
    'project_id',
    'duedate',
    'tblinvoices.status',
    );

$join = array(
    'LEFT JOIN tblclients ON tblclients.userid = tblinvoices.clientid',
    'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblinvoices.currency',
    'LEFT JOIN tblprojects ON tblprojects.id = tblinvoices.project_id',
    );

$custom_fields = get_custom_fields('invoice',array('show_on_table'=>1));

$i = 0;
foreach($custom_fields as $field){
    $select_as = 'cvalue_'.$i;
    if($field['type'] == 'date_picker') {
      $select_as = 'date_picker_cvalue_'.$i;
    }
    array_push($aColumns,'ctable_'.$i.'.value as '.$select_as);
    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblinvoices.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
    $i++;
}

$where = array();
$filter = array();

if($this->_instance->input->post('not_sent')){
    array_push($filter, 'AND sent = 0 AND tblinvoices.status NOT IN(2,5)');
}
if($this->_instance->input->post('not_have_payment')){
    array_push($filter, 'AND tblinvoices.id NOT IN(SELECT invoiceid FROM tblinvoicepaymentrecords)');
}
if($this->_instance->input->post('recurring')){
    array_push($filter, 'AND recurring > 0');
}
$statuses = $this->_instance->invoices_model->get_statuses();
$_statuses = array();
foreach($statuses as $status){
    if($this->_instance->input->post('invoices_'.$status)){
        array_push($_statuses,$status);
    }
}
if(count($_statuses) > 0){
   array_push($filter, 'AND tblinvoices.status IN (' . implode(', ',$_statuses) . ')');
}
$agents = $this->_instance->invoices_model->get_sale_agents();
$_agents = array();
foreach($agents as $agent){
    if($this->_instance->input->post('sale_agent_'.$agent['sale_agent'])){
        array_push($_agents,$agent['sale_agent']);
    }
}
if(count($_agents) > 0){
   array_push($filter, 'AND sale_agent IN (' . implode(', ',$_agents) . ')');
}

$_modes = array();
foreach($data['payment_modes'] as $mode){
    if($this->_instance->input->post('invoice_payments_by_'.$mode['id'])){
       array_push($_modes,$mode['id']);
   }
}
if(count($_modes) > 0){
 array_push($where,'AND tblinvoices.id IN (SELECT invoiceid FROM tblinvoicepaymentrecords WHERE paymentmode IN ("'. implode('", "', $_modes) .'"))');
}

$years = $this->_instance->invoices_model->get_invoices_years();
$_years = array();
foreach($years as $year){
    if($this->_instance->input->post('year_'.$year['year'])){
        array_push($_years,$year['year']);
    }
}
if(count($_years) > 0){
    array_push($where,'AND YEAR(date) IN ('.implode(', ',$_years).')');
}

if(count($filter) > 0){
    array_push($where,'AND ('.prepare_dt_filter($filter).')');
}

if (is_numeric($clientid)) {
    array_push($where,'AND tblinvoices.clientid='.$clientid);
}

if($this->_instance->input->post('project_id')){
    array_push($where,'AND project_id='.$this->_instance->input->post('project_id'));
}

if(!has_permission('invoices','','view')){
    array_push($where,'AND tblinvoices.addedfrom='.get_staff_user_id());
}

$sIndexColumn = "id";
$sTable       = 'tblinvoices';

$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblinvoices.id',
    'tblinvoices.clientid',
    'symbol',
    'total',
    'tblprojects.name',
    ));
$output       = $result['output'];
$rResult      = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
            $_data = $aRow[ strafter($aColumns[$i],'as ')];
        } else {
            $_data = $aRow[ $aColumns[$i] ];
        }
        if ($aColumns[$i] == 'number') {
            // If is from client area table
            if (is_numeric($clientid) || $this->_instance->input->post('project_id')) {
                $__data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a><br />';
            } else {
               $__data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" onclick="init_invoice(' . $aRow['id'] . '); return false;">' . format_invoice_number($aRow['id']) . '</a><br />';
           }
       } else if ($aColumns[$i] == 'date') {
        $__data = _d($_data);
    } else if ($i == 5) {
        $__data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a><br />';
    } else if ($aColumns[$i] == 'duedate') {
        $__data = _d($_data);
    } else if ($aColumns[$i] == 'total' || $aColumns[$i] == 'total_tax') {
        $__data = format_money($_data, $aRow['symbol']);
    } else if($aColumns[$i] == 'tblinvoices.status') {
        $__data = format_invoice_status($aRow['tblinvoices.status']);
        // Status
    } else if($aColumns[$i] == 'project_id'){
        $__data = '<a href="'.admin_url('projects/view/'.$aRow['project_id']).'">'.$aRow['name'].'</a>';
    } else  {
       // check if field is date so can be converted, possible option is to be custom field with type of date
       if(strpos($aColumns[$i],'date_picker_') !== false){
         $_data = _d($_data);
       }
        $__data = $_data;
    }
    $row[] = $__data;
}

$output['aaData'][] = $row;
}
