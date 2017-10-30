<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'tblproposals.id',
    'subject',
    'proposal_to',
    'total',
    'date',
    'open_till',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tblproposals.id and rel_type="proposal" ORDER by tag_order ASC) as tags',
    'datecreated',
    'status',
    );
$sIndexColumn = "id";
$sTable       = 'tblproposals';

$where = array();
$filter = array();
if($this->_instance->input->post('leads_related')){
    array_push($filter,'OR rel_type="lead"');
}
if($this->_instance->input->post('customers_related')){
    array_push($filter,'OR rel_type="customer"');
}
if($this->_instance->input->post('expired')){
    array_push($filter,'OR open_till IS NOT NULL AND open_till <"'.date('Y-m-d').'" AND status NOT IN(2,3)');
}
$statuses = $this->_instance->proposals_model->get_statuses();
$_statuses = array();

foreach($statuses as $status){
    if($this->_instance->input->post('proposals_'.$status)){
        array_push($_statuses,$status);
    }
}
if(count($_statuses) > 0){
     array_push($filter, 'AND status IN (' . implode(', ',$_statuses) . ')');
}

$agents = $this->_instance->proposals_model->get_sale_agents();
$_agents = array();
foreach($agents as $agent){
    if($this->_instance->input->post('sale_agent_'.$agent['sale_agent'])){
        array_push($_agents,$agent['sale_agent']);
    }
}
if(count($_agents) > 0){
     array_push($filter, 'AND assigned IN (' . implode(', ',$_agents) . ')');
}

$years = $this->_instance->proposals_model->get_proposals_years();
$_years = array();
foreach($years as $year){
    if($this->_instance->input->post('year_'.$year['year'])){
        array_push($_years,$year['year']);
    }
}
if(count($_years) > 0){
    array_push($filter,'AND YEAR(date) IN ('.implode(', ',$_years).')');
}

if(count($filter) > 0){
    array_push($where,'AND ('.prepare_dt_filter($filter).')');
}


if(!has_permission('proposals','','view')){
  array_push($where,'AND addedfrom='.get_staff_user_id());
}

$join = array();
$custom_fields = get_custom_fields('proposal',array('show_on_table'=>1));

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

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'currency',
    'rel_id',
    'rel_type',
    ));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {

        if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
            $_data = $aRow[ strafter($aColumns[$i],'as ')];
        } else {
            $_data = $aRow[ $aColumns[$i] ];
        }
        if($aColumns[$i] == 'tblproposals.id'){
            $_data = '<a href="' . admin_url('proposals/list_proposals/' . $aRow['tblproposals.id']) . '" onclick="init_proposal(' . $aRow['tblproposals.id'] . '); return false;">' . format_proposal_number($aRow['tblproposals.id']) . '</a>';
        } else if ($aColumns[$i] == 'subject') {
            $_data = '<a href="' . admin_url('proposals/list_proposals/' . $aRow['tblproposals.id']) . '" onclick="init_proposal(' . $aRow['tblproposals.id'] . '); return false;">' . $_data . '</a>';
        } else if ($aColumns[$i] == 'status') {
            $_data = format_proposal_status($aRow['status']);
        } else if($aColumns[$i] == 'total'){
            if($aRow['currency'] != 0){
                $_data = format_money($_data,$this->_instance->currencies_model->get_currency_symbol($aRow['currency']));
            } else {
                $_data = format_money($_data,$this->_instance->currencies_model->get_base_currency($aRow['currency'])->symbol);
            }

        } else if($aColumns[$i] == 'open_till' || $aColumns[$i] == 'datecreated' || $aColumns[$i] == 'date'){
            $_data = _d($_data);
        } else if($aColumns[$i] == 'proposal_to'){
           if(!empty($_data)){
              if(!empty($aRow['rel_id']) && $aRow['rel_id'] != 0){
                if($aRow['rel_type'] == 'lead'){
                  $_data = '<a href="#" onclick="init_lead('.$aRow['rel_id'].');return false;" target="_blank" data-toggle="tooltip" data-title="'._l('lead').'">'.$_data.'</a>';
              } else if($aRow['rel_type'] == 'customer'){
                  $_data = '<a href="'.admin_url('clients/client/'.$aRow['rel_id']).'" target="_blank" data-toggle="tooltip" data-title="'._l('client').'">'.$_data.'</a>';
              }
          }
      }
  } else if($i == 6){
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
