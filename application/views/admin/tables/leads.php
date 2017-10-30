<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$custom_fields = get_custom_fields('leads', array(
    'show_on_table' => 1
    ));

$is_admin = is_admin();

$aColumns     = array(
    '1',
    'tblleads.id',
    'tblleads.name',
    'tblleads.email',
    'tblleads.phonenumber',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tblleads.id and rel_type="lead" ORDER by tag_order ASC) as tags',
    'CONCAT(firstname, \' \', lastname)',
    'tblleadsstatus.name',
    'tblleadssources.name',
    'lastcontact',
    'dateadded'
    );

$aColumns = do_action('leads_table_sql_columns',$aColumns);

$sIndexColumn = "id";
$sTable       = 'tblleads';

$join = array(
    'LEFT JOIN tblstaff ON tblstaff.staffid = tblleads.assigned',
    'LEFT JOIN tblleadsstatus ON tblleadsstatus.id = tblleads.status',
    'LEFT JOIN tblleadssources ON tblleadssources.id = tblleads.source',
    );

$i = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_'.$i;
    if($field['type'] == 'date_picker') {
      $select_as = 'date_picker_cvalue_'.$i;
    }
    array_push($aColumns,'ctable_'.$i.'.value as '.$select_as);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $i . ' ON tblleads.id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);

    $i++;
}

$where = array();
$filter = false;

if ($this->_instance->input->post('custom_view')) {
    $filter = $this->_instance->input->post('custom_view');
    if ($filter == 'lost') {
        array_push($where, 'AND lost = 1');
    } else if ($filter == 'junk') {
        array_push($where, 'AND junk = 1');
    } else if ($filter == 'not_assigned') {
        array_push($where, 'AND assigned = 0');
    } else if($filter == 'contacted_today'){
        array_push($where,'AND lastcontact LIKE "'.date('Y-m-d').'%"');
    } else if($filter == 'created_today'){
        array_push($where,'AND dateadded LIKE "'.date('Y-m-d').'%"');
    } else if($filter == 'public'){
        array_push($where,'AND is_public = 1');
    }
}

if(!$filter || ($filter && $filter != 'lost' && $filter != 'junk')){
    array_push($where,'AND lost = 0 AND junk = 0');
}

if(is_admin()){
    if ($this->_instance->input->post('assigned')) {
        $by_assigned = $this->_instance->input->post('assigned');
        array_push($where, 'AND assigned =' . $by_assigned);
    }
}
if ($this->_instance->input->post('status')) {
    $by_assigned = $this->_instance->input->post('status');
    array_push($where, 'AND status =' . $by_assigned);
}

if ($this->_instance->input->post('source')) {
    $by_assigned = $this->_instance->input->post('source');
    array_push($where, 'AND source =' . $by_assigned);
}

if (!is_admin()) {
    array_push($where, 'AND (assigned =' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR is_public = 1)');
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

$where = do_action('leads_table_sql_where',$where);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblleads.id',
    'junk',
    'lost',
    'isdefault',
    'color',
    'assigned'
    ));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    for ($i = 0; $i < count($aColumns); $i++) {

        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if($aColumns[$i] == '1'){
            $_data = '<div class="checkbox"><input type="checkbox" value="'.$aRow['id'].'"><label></label></div>';
        } else if ($aColumns[$i] == 'tblleads.name' || $aColumns[$i] == 'tblleads.id') {
            $_data = '<a href="'.admin_url('leads/index/'.$aRow['id']).'" onclick="init_lead('.$aRow['id'].');return false;">'. $_data . '</a>';
        } else if ($aColumns[$i] == 'lastcontact' || $aColumns[$i] == 'dateadded') {
            if ($_data == '0000-00-00 00:00:00' || !is_date($_data)) {
                $_data = '';
            } else {
                $_data = '<span data-toggle="tooltip" data-title="'._dt($_data).'">'.time_ago($_data).'</span>';
            }
        } else if ($i == 6) {
            // Assigned user
            if ($aRow['assigned'] != 0) {
                $_data = '<a data-toggle="tooltip" data-title="'.$_data.'" href="'.admin_url('profile/'.$aRow['assigned']).'">'.staff_profile_image($aRow['assigned'], array(
                    'staff-profile-image-small'
                    )) . '</a>';
            } else {
                $_data = '';
            }
        } else if($i == 5){
            $_data = render_tags($_data);
        } else if ($aColumns[$i] == 'tblleadsstatus.name') {
            if ($aRow['tblleadsstatus.name'] == null) {
                if ($aRow['lost'] == 1) {
                    $_data = '<span class="label label-danger inline-block">' . _l('lead_lost') . '</span>';
                } else if ($aRow['junk'] == 1) {
                    $_data = '<span class="label label-warning inline-block">' . _l('lead_junk') . '</span>';
                }
            } else {

                $_label_class = '';
                if (empty($aRow['color'])) {
                    $_label_class = 'default';
                }

                $_data = '<span class="inline-block label label-' . $_label_class . '" style="color:' . $aRow['color'] . ';border:1px solid ' . $aRow['color'] . '">' . $_data . '</span>';
            }
        } else if ($aColumns[$i] == 'tblleads.phonenumber') {
            $_data = '<a href="tel:' . $_data . '">' . $_data . '</a>';
        } else if ($aColumns[$i] == 'tblleads.email') {
            $_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
        } else {
            if(strpos($aColumns[$i],'date_picker_') !== false){
                 $_data = _d($_data);
            }
        }

        $hook_data = do_action('leads_tr_data_output',array('output'=>$_data,'column'=>$aColumns[$i],'id'=>$aRow['id']));
        $_data = $hook_data['output'];
        $row[] = $_data;
    }

    $options = icon_btn('#', 'eye','btn-default',array('onclick'=>'init_lead('.$aRow['id'].');return false;'));
    if (is_lead_creator($aRow['id']) || $is_admin) {
        $options .= icon_btn('leads/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    }

    $row[] = $options;

    if ($aRow['assigned'] == get_staff_user_id()) {
        $row['DT_RowClass'] = 'alert-info';
    }
    $output['aaData'][] = $row;
}
