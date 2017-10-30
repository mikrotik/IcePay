<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns = array(
    'name',
    'startdate',
    'duedate',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tblstafftasks.id and rel_type="task" ORDER by tag_order ASC) as tags',
    '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM tblstafftaskassignees JOIN tblstaff ON tblstaff.staffid = tblstafftaskassignees.staffid WHERE taskid=tblstafftasks.id) as assignees',
    'priority',
    'status'
);



$where = array();
include_once(APPPATH.'views/admin/tables/includes/tasks_filter.php');

array_push($where, 'AND rel_id="' . $rel_id . '" AND rel_type="' . $rel_type . '"');
$join          = array();
$custom_fields = get_custom_fields('tasks', array(
    'show_on_table' => 1
));

$i             = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_'.$i;
    if($field['type'] == 'date_picker') {
      $select_as = 'date_picker_cvalue_'.$i;
    }
    array_push($aColumns,'ctable_'.$i.'.value as '.$select_as);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $i . ' ON tblstafftasks.id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}

$aColumns = do_action('tasks_related_table_sql_columns',$aColumns);

$sIndexColumn = "id";
$sTable       = 'tblstafftasks';
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblstafftasks.id',
    'dateadded',
    'priority',
    'invoice_id',
    '(SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM tblstafftaskassignees WHERE taskid=tblstafftasks.id) as assignees_ids'
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
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="'.admin_url('tasks/index/'.$aRow['id']).'" class="main-tasks-table-href-name" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $_data . '</a>';
        } else if ($aColumns[$i] == 'status') {
            $_data = '<span class="inline-block label label-'.get_status_label($aRow['status']).'" task-status-table="'.$aRow['status'].'">' . format_task_status($aRow['status'],false,true);
            if ($aRow['status'] == 5) {
                $_data .= '<a href="#" onclick="unmark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l('task_unmark_as_complete') . '"></i></a>';
            } else {
                $_data .= '<a href="#" onclick="mark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l('task_single_mark_as_complete') . '"></i></a>';
            }
            $_data .= '</span>';
        } else if ($aColumns[$i] == 'startdate' || $aColumns[$i] == 'duedate') {
            if ($aColumns[$i] == 'startdate') {
                $_data = _d($aRow['startdate']);
            } else {
                  $_data = _d($aRow['duedate']);
            }
        } else if ($aColumns[$i] == 'priority') {
            $_data = '<span class="text-' . get_task_priority_class($_data) . ' inline-block">' . task_priority($_data) . '</span>';
        } else if ($aColumns[$i] == 'billable') {
            if ($_data == 1) {
                $billable = _l("task_billable_yes");
            } else {
                $billable = _l("task_billable_no");
            }
            $_data = $billable;
        } else if ($aColumns[$i] == 'billed') {
            if ($aRow['billable'] == 1) {
                if ($_data == 1) {
                    $_data = '<span class="label label-success inline-block">' . _l('task_billed_yes') . '</span>';
                } else {
                    $_data = '<span class="label label-danger inline-block">' . _l('task_billed_no') . '</span>';
                }
            } else {
                $_data = '';
            }
        } else if($aColumns[$i] == $aColumns[4]) {
            $assignees        = explode(',', $_data);
            $assignee_ids        = explode(',', $aRow['assignees_ids']);
            $_data            = '';
            $export_assignees = '';
            $as = 0;
            foreach ($assignees as $assigned) {
                $assignee_id = $assignee_ids[$as];
                if ($assigned != '') {
                    $_data .= '<a href="' . admin_url('profile/' . $assignee_id) . '">' . staff_profile_image($assignee_id, array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $assigned
                    )) . '</a>';
                    // For exporting
                    $export_assignees .= $assigned . ', ';
                }

                $as++;
            }
            if ($export_assignees != '') {
                $_data .= '<span class="hide">' . mb_substr($export_assignees, 0, -2) . '</span>';
            }
        } else if($i == 3){
            $_data = render_tags($_data);
        } else {
             if(strpos($aColumns[$i],'date_picker_') !== false){
                 $_data = _d($_data);
            }
        }
        $hook_data = do_action('tasks_related_tr_data_output',array('output'=>$_data,'column'=>$aColumns[$i],'id'=>$aRow['id']));
        $_data = $hook_data['output'];
        $row[] = $_data;
    }

    $options = '';
    if (has_permission('tasks', '', 'edit')) {
        $options .= icon_btn('#', 'pencil-square-o', 'btn-default pull-right mleft5', array(
            'onclick' => 'edit_task(' . $aRow['id'] . '); return false'
        ));
    }

    $class = 'btn-success';
    $atts  = array(
        'onclick' => 'timer_action(this,' . $aRow['id'] . '); return false'
    );

    $tooltip        = '';
    $is_assigned    = $this->_instance->tasks_model->is_task_assignee(get_staff_user_id(), $aRow['id']);
    $is_task_billed = $this->_instance->tasks_model->is_task_billed($aRow['id']);
    if ($is_task_billed || !$is_assigned || $aRow['status'] == 5) {
        $class = 'btn-default disabled';
        if($aRow['status'] == 5){
            $tooltip = ' data-toggle="tooltip" data-title="' . format_task_status($aRow['status'],false,true) . '"';
        } else if ($is_task_billed) {
            $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_billed_cant_start_timer') . '"';
        } else if(!$is_assigned) {
            $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_start_timer_only_assignee') . '"';
        }
    }
    if (!$this->_instance->tasks_model->is_timer_started($aRow['id'])) {
        $options .= '<span' . $tooltip . ' class="pull-right">' . icon_btn('#', 'clock-o', $class . ' no-margin', $atts) . '</span>';
    } else {
        $options .= icon_btn('#', 'clock-o', 'btn-danger pull-right no-margin', array(
            'onclick' => 'timer_action(this,' . $aRow['id'] . ',' . $this->_instance->tasks_model->get_last_timer($aRow['id'])->id . '); return false'
        ));
    }
    $row[]              = $options;
    $rowClass = '';
    if ((!empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['status'] != 5) {
        $rowClass = 'text-danger bold ';
    }

    $row['DT_RowClass'] = $rowClass;
    $output['aaData'][] = $row;
}
