<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns = array('firstname', 'lastname', 'email', 'title', 'phonenumber', 'active', 'last_login');
$sIndexColumn = "id";
$sTable = 'tblcontacts';
$join = array();

$custom_fields = get_custom_fields('contacts',array('show_on_table'=>1));

$i = 0;
foreach($custom_fields as $field){
    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblcontacts.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
    $i++;
}

$where = array();
$where = array('WHERE userid='.$client_id);

// Fix for big queries. Some hosting have max_join_limit
if(count($custom_fields) > 4){
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns,$sIndexColumn,$sTable,$join,$where,array('tblcontacts.id as id','userid','is_primary'));

$output = $result['output'];
$rResult = $result['rResult'];
$total_client_contacts = total_rows('tblcontacts',array('userid'=>$client_id));
foreach ( $rResult as $aRow )
{
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
            $_data = $aRow[ strafter($aColumns[$i],'as ')];
        } else {
            $_data = $aRow[ $aColumns[$i] ];
        }
        if ($aColumns[$i] == 'active') {
            $checked = '';
            if ($aRow['active'] == 1) {
                $checked = 'checked';
            }

            $_data = '<div class="onoffswitch">
                <input type="checkbox" data-switch-url="'.admin_url().'clients/change_contact_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_'.$aRow['id'].'" data-id="'.$aRow['id'].'" ' . $checked . '>
                <label class="onoffswitch-label" for="c_'.$aRow['id'].'"></label>
            </div>';
            // For exporting
            $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
        } else if($aColumns[$i] == 'last_login'){
            if(!empty($_data)){
                $_data = time_ago($_data);
            }
        } else if($aColumns[$i] == 'firstname'){
            $_data = '<img src="'.contact_profile_image_url($aRow['id']).'" class="client-profile-image-small mright5"><a href="#" onclick="contact('.$aRow['userid'].','.$aRow['id'].');return false;">'.$_data.'</a>';
        } else {
            if(_startsWith($aColumns[$i],'ctable_') && is_date($_data)){
                 $_data = _d($_data);
            }
        }

        $row[] = $_data;
    }
    $options = '';
    $options .= icon_btn('#','pencil-square-o','btn-default',array('onclick'=>'contact('.$aRow['userid'].','.$aRow['id'].');return false;'));
    if(has_permission('customers','','delete') || is_customer_admin($aRow['userid'])){
         if($aRow['is_primary'] == 0 || ($aRow['is_primary'] == 1 && $total_client_contacts == 1)){
            $options .= icon_btn('clients/delete_contact/'.$aRow['userid'].'/'.$aRow['id'],'remove','btn-danger _delete');
        }
    }

    $row[] = $options;
    $output['aaData'][] = $row;
}
