<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns = array(
    'tblinvoicepaymentrecords.id',
    'invoiceid',
    'paymentmode',
    'transactionid',
    'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
    'amount',
    'tblinvoicepaymentrecords.date'
    );

$join = array(
    'LEFT JOIN tblinvoices ON tblinvoices.id = tblinvoicepaymentrecords.invoiceid',
    'LEFT JOIN tblclients ON tblclients.userid = tblinvoices.clientid',
    'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblinvoices.currency',
    'LEFT JOIN tblinvoicepaymentsmodes ON tblinvoicepaymentsmodes.id = tblinvoicepaymentrecords.paymentmode'
    );

$where = array();
if(is_numeric($clientid)){
    array_push($where,'AND tblclients.userid='.$clientid);
}

if(!has_permission('payments','','view')){
    array_push($where,'AND invoiceid IN (SELECT id FROM tblinvoices WHERE addedfrom='.get_staff_user_id().')');
}

$sIndexColumn = "id";
$sTable       = 'tblinvoicepaymentrecords';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblinvoices.id',
    'tblinvoicepaymentrecords.date',
    'clientid',
    'symbol',
    'total',
    'number',
    'tblinvoicepaymentsmodes.name',
    'tblinvoicepaymentsmodes.id as paymentmodeid',
    'paymentmethod',
    ));

$output       = $result['output'];
$rResult      = $result['rResult'];

$this->_instance->load->model('payment_modes_model');
$online_modes = $this->_instance->payment_modes_model->get_online_payment_modes(true);

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
       if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
        $_data = $aRow[ strafter($aColumns[$i],'as ')];
    } else {
        $_data = $aRow[ $aColumns[$i] ];
    }
    if ($aColumns[$i] == 'paymentmode') {
     $_data = $aRow['name'];
           // Since version 1.0.1
     if (is_null($aRow['paymentmodeid'])) {
        foreach ($online_modes as $online_mode) {
            if ($aRow['paymentmode'] == $online_mode['id']) {
                $_data = $online_mode['name'];
            }
        }
    }
    if(!empty($aRow['paymentmethod'])){
        $_data .= ' - ' . $aRow['paymentmethod'];
    }
} else if ($aColumns[$i] == 'tblinvoicepaymentrecords.id') {
    $_data = '<a href="' . admin_url('payments/payment/' . $_data) . '">' . $_data . '</a>';
} else if ($aColumns[$i] == 'tblinvoicepaymentrecords.date') {
    $_data = _d($_data);
} else if ($aColumns[$i] == 'invoiceid') {
    $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow[$aColumns[$i]]) . '">' . format_invoice_number($aRow['id']) . '</a>';
} else if ($i == 4) {
    $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
} else if ($aColumns[$i] == 'amount') {
    $_data = format_money($_data,$aRow['symbol']);
}

$row[] = $_data;
}

$options            = icon_btn('payments/payment/' . $aRow['tblinvoicepaymentrecords.id'], 'pencil-square-o');
if(has_permission('payments','','delete')){
    $options .= icon_btn('payments/delete/' . $aRow['tblinvoicepaymentrecords.id'], 'remove', 'btn-danger _delete');
}
$row[] = $options;
$output['aaData'][] = $row;
}
