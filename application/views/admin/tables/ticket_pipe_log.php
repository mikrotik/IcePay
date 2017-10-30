<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name',
    'date',
    'email_to',
    'email',
    'subject',
    'message',
    'status'
    );
$sIndexColumn = "id";
$sTable       = 'tblticketpipelog';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(), 'ORDER BY date ASC');
$output       = $result['output'];
$rResult      = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'date') {
            $_data = _dt($_data);
        } else if ($aColumns[$i] == 'message') {
            $_data = mb_substr($_data, 0, 800);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
