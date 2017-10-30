<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'tblproposals.id',
    'subject',
    'total',
    'date',
    'open_till',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tblproposals.id and rel_type="proposal" ORDER by tag_order ASC) as tags',
    'datecreated',
    'status'
    );
$sIndexColumn = "id";
$sTable       = 'tblproposals';

$custom_fields = get_custom_fields('proposal',array('show_on_table'=>1));
$join = array();

$i = 0;
foreach($custom_fields as $field){
    $select_as = 'cvalue_'.$i;
    if($field['type'] == 'date_picker') {
      $select_as = 'date_picker_cvalue_'.$i;
    }
    array_push($aColumns,'ctable_'.$i.'.value as '.$select_as);
    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblproposals.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
    $i++;
}

$additional_select = array(
    'tblproposals.id',
    'currency',
    );

$where = 'AND rel_id = '.$rel_id. ' AND rel_type = "'.$rel_type.'"';

if($rel_type == 'customer'){
    $this->_instance->db->where('userid',$rel_id);
    $customer = $this->_instance->db->get('tblclients')->row();
    if($customer){
        if(!is_null($customer->leadid)){
            $where .= ' OR rel_type="lead" AND rel_id='.$customer->leadid;
        }
    }
}

$where = array($where);

if(!has_permission('proposals','','view')){
  array_push($where,'AND addedfrom='.get_staff_user_id());
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additional_select);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
            $_data = $aRow[ strafter($aColumns[$i],'as ')];
        } else {
            $_data = $aRow[ $aColumns[$i] ];
        } if($aColumns[$i] == 'tblproposals.id'){
             $_data = '<a href="'.admin_url('proposals/list_proposals/'.$aRow['id']).'">' . format_proposal_number($aRow['tblproposals.id']) . '</a>';
        } else if ($aColumns[$i] == 'subject') {
            $_data = '<a href="'.admin_url('proposals/list_proposals/'.$aRow['id']).'">' . $_data . '</a>';
        } else if ($aColumns[$i] == 'status') {
            $_data = format_proposal_status($aRow['status']);
        } else if($aColumns[$i] == 'open_till' || $aColumns[$i] == 'datecreated' || $aColumns[$i] == 'date'){
            $_data = _d($_data);
        } else if($aColumns[$i] == 'total'){
            if($aRow['currency'] != 0){
                $_data = format_money($_data,$this->_instance->currencies_model->get_currency_symbol($aRow['currency']));
            } else {
                $_data = format_money($_data,$this->_instance->currencies_model->get_base_currency($aRow['currency'])->symbol);
            }
        } else if($i == 5){
            $_data = render_tags($_data);
        } else {
             if(strpos($aColumns[$i],'date_picker_') !== false){
                 $_data = _d($_data);
            }
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
