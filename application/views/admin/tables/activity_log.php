<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns         = array(
    'description',
    'date',
    'tblactivitylog.staffid'
    );
$sIndexColumn     = "id";
$sTable           = 'tblactivitylog';
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(), 'ORDER BY date ASC');
$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'date') {
            $_data = _dt($_data);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
