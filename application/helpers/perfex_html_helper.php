<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Remove <br /> html tags from string to show in textarea with new linke
 * @param  string $text
 * @return string formated text
 */
function clear_textarea_breaks($text)
{
    $_text  = '';
    $_text  = $text;
    $breaks = array(
        "<br />",
        "<br>",
        "<br/>"
    );
    $_text  = str_ireplace($breaks, "", $_text);
    $_text  = trim($_text);
    return $_text;
}
/**
 * Equivalent function to nl2br php function but keeps the html if found and do not ruin the formatting
 * @param  string $string
 * @return string
 */
function nl2br_save_html($string)
{
    if(! preg_match("#</.*>#", $string)) // avoid looping if no tags in the string.
        return nl2br($string);

    $string = str_replace(array("\r\n", "\r", "\n"), "\n", $string);

    $lines=explode("\n", $string);
    $output='';
    foreach($lines as $line)
    {
        $line = rtrim($line);
        if(! preg_match("#</?[^/<>]*>$#", $line)) // See if the line finished with has an html opening or closing tag
            $line .= '<br />';
        $output .= $line . "\n";
    }

    return $output;
}
/**
 * Coma separated tags for input
 * @param  array $tag_names
 * @return string
 */
function prep_tags_input($tag_names){
    $tag_names = array_filter($tag_names, function($value) { return $value !== ''; });
    return implode(',',$tag_names);
}
/**
 * Function will render tags as html version to show to the user
 * @param  string $tags
 * @return string
 */
function render_tags($tags){

    $tags_html = '';
    if(!is_array($tags)){
        $tags = explode(',',$tags);
    }
    $tags = array_filter($tags, function($value) { return $value !== ''; });
    if(count($tags) > 0){
        $CI = &get_instance();
        $tags_html .= '<div class="tags-labels">';
        $i = 0;
        $len = count($tags);
        foreach($tags as $tag){
            $tag_id = 0;
            $CI->db->select('id')->where('name',$tag);
            $tag_row = $CI->db->get('tbltags')->row();
            if($tag_row){
                $tag_id = $tag_row->id;
            }
            $tags_html .= '<span class="label label-tag tag-id-'.$tag_id.'"><span class="tag">'.$tag.'</span><span class="hide">'.($i != $len - 1 ? ', ' : '') .'</span></span>';
            $i++;
        }
        $tags_html .= '</div>';
    }

    return $tags_html;
}
/**
 * Load app stylesheet based on option
 * Can load minified stylesheet and non minified
 *
 * This function also check if there is my_ prefix stylesheet to load them.
 * If in options is set to load minified files and the filename that is passed do not contain minified version the
 * original file will be used.
 *
 * @param  string $path
 * @param  string $filename
 * @return string
 */
function app_stylesheet($path,$filename){

    if(file_exists(FCPATH.$path.'/my_'.$filename)){
        $filename = 'my_'.$filename;
    }

    if(get_option('use_minified_files') == 1){
        $original_file_name = $filename;
        $_temp = explode('.',$filename);
        $last = count($_temp) -1;
        $extension = $_temp[$last];
        unset($_temp[$last]);
        $filename = '';
        foreach($_temp as $t){
            $filename .= $t.'.';
        }
        $filename.= 'min.'.$extension;

        if(!file_exists(FCPATH.$path.'/'.$filename)){
            $filename = $original_file_name;
        }
    }
    if(file_exists(FCPATH.$path.'my_'.$filename)){
        $filename = 'my_'.$filename;
    }
    return '<link href="'.base_url($path.'/'.$filename).'" rel="stylesheet">'.PHP_EOL;
}
/**
 * Load app script based on option
 * Can load minified stylesheet and non minified
 *
 * This function also check if there is my_ prefix stylesheet to load them.
 * If in options is set to load minified files and the filename that is passed do not contain minified version the
 * original file will be used.
 *
 * @param  string $path
 * @param  string $filename
 * @return string
 */
function app_script($path,$filename){

    if(file_exists(FCPATH.$path.'/my_'.$filename)){
        $filename = 'my_'.$filename;
    }

    if(get_option('use_minified_files') == 1){
        $original_file_name = $filename;
        $_temp = explode('.',$filename);
        $last = count($_temp) -1;
        $extension = $_temp[$last];
        unset($_temp[$last]);
        $filename = '';
        foreach($_temp as $t){
            $filename .= $t.'.';
        }
        $filename.= 'min.'.$extension;
        if(!file_exists($path.'/'.$filename)){
            $filename = $original_file_name;
        }
    }
    return '<script src="'.base_url($path.'/'.$filename).'"></script>'.PHP_EOL;
}
/**
 * For more readable code created this function to render only yes or not values for settings
 * @param  string $option_value option from database to compare
 * @param  string $label        input label
 * @param  string $tooltip      tooltip
 */
function render_yes_no_option($option_value, $label, $tooltip = '')
{
    ob_start();
    if ($tooltip != '') {
        $tooltip = ' data-toggle="tooltip" title="' . _l($tooltip) . '"';
    }
?>
    <div class="form-group"<?php
    echo $tooltip;
?>>
    <label for="<?php
    echo $option_value;
?>" class="control-label clearfix"><?php
    echo _l($label);
?></label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_<?php
    echo $label;
?>" name="settings[<?php
    echo $option_value;
?>]" value="1" <?php
    if (get_option($option_value) == '1') {
        echo 'checked';
    }
?>>
        <label for="y_opt_1_<?php
    echo $label;
?>"><?php
    echo _l('settings_yes');
?></label>
        </div>
        <div class="radio radio-primary radio-inline">
            <input type="radio" id="y_opt_2_<?php
    echo $label;
?>" name="settings[<?php
    echo $option_value;
?>]" value="0" <?php
    if (get_option($option_value) == '0') {
        echo 'checked';
    }
?>>
            <label for="y_opt_2_<?php
    echo $label;
?>"><?php
    echo _l('settings_no');
?></label>
            </div>
        </div>
        <?php
    $settings = ob_get_contents();
    ob_end_clean();
    echo $settings;
}
/**
 * Tasks html table used all over the application for relation tasks
 * This table is not used for the main tasks table
 * @param  array  $table_attributes
 * @return string
 */
function init_relation_tasks_table($table_attributes = array())
{
    $table_data = array(
        _l('tasks_dt_name'),
        _l('tasks_dt_datestart'),
        _l('task_duedate'),
        _l('tags'),
        _l('task_assigned'),
        _l('tasks_list_priority'),
        _l('task_status')
    );

    $custom_fields = get_custom_fields('tasks', array(
        'show_on_table' => 1
    ));

    foreach ($custom_fields as $field) {
        array_push($table_data, $field['name']);
    }

    $table_data = do_action('tasks_related_table_columns',$table_data);

    array_push($table_data, _l('options'));
    $name = 'rel-tasks';
    if ($table_attributes['data-new-rel-type'] == 'lead') {
        $name = 'rel-tasks-leads';
    }

    $table = '';
    $CI =& get_instance();
    $table_name = '.table-' . $name;
    $CI->load->view('admin/tasks/tasks_filter_by', array(
        'view_table_name' => $table_name
    ));
    if (has_permission('tasks', '', 'create')) {
        $disabled   = '';
        $table_name = addslashes($table_name);
        if ($table_attributes['data-new-rel-type'] == 'customer' && is_numeric($table_attributes['data-new-rel-id'])) {
            if (total_rows('tblclients', array(
                'active' => 0,
                'userid' => $table_attributes['data-new-rel-id']
            )) > 0) {
                $disabled = ' disabled';
            }
        }
        echo "<a href='#' class='btn btn-info pull-left mbot25 mright5" . $disabled . "' onclick=\"new_task_from_relation('$table_name'); return false;\">" . _l('new_task') . "</a>";


    }

    if ($table_attributes['data-new-rel-type'] == 'project') {
            echo "<a href='" . admin_url('tasks/list_tasks?project_id=' . $table_attributes['data-new-rel-id'] . '&kanban=true') . "' class='btn btn-default pull-left mbot25'>" . _l('view_kanban') . "</a>";

    }
    echo "<div class='clearfix'></div>";


    $table .= render_datatable($table_data, $name, array(), $table_attributes);

    return $table;
}
/**
 * Function used to render <option> for relation
 * This function will do all the necessary checking and return the options
 * @param  mixed $data
 * @param  string $type   rel_type
 * @param  string $rel_id rel_id
 * @return string
 */
function init_relation_options($data, $type, $rel_id = '')
{
    $_data = array();

    $has_permission_projects_view  = has_permission('projects', '', 'view');
    $has_permission_customers_view = has_permission('customers', '', 'view');
    $has_permission_contracts_view = has_permission('contracts', '', 'view');
    $has_permission_invoices_view  = has_permission('invoices', '', 'view');
    $has_permission_estimates_view = has_permission('estimates', '', 'view');
    $has_permission_expenses_view  = has_permission('expenses', '', 'view');
    $has_permission_proposals_view = has_permission('proposals', '', 'view');
    $is_admin                      = is_admin();
    $CI =& get_instance();
    $CI->load->model('projects_model');

    foreach ($data as $relation) {
        $relation_values = get_relation_values($relation, $type);
        if ($type == 'project') {
            if (!$has_permission_projects_view) {
                if (!$CI->projects_model->is_member($relation_values['id']) && $rel_id != $relation_values['id']) {
                    continue;
                }
            }
        } else if ($type == 'lead') {
            if(!$is_admin){
                if ($relation['assigned'] != get_staff_user_id() && $relation['addedfrom'] != get_staff_user_id() && $relation['is_public'] != 1 && $rel_id != $relation_values['id']) {
                    continue;
                }
            }
        } else if ($type == 'customer') {
            if (!$has_permission_customers_view && !have_assigned_customers() && $rel_id != $relation_values['id']) {
                continue;
            } else if (have_assigned_customers() && $rel_id != $relation_values['id'] && !$has_permission_customers_view) {
                if (!is_customer_admin($relation_values['id'])) {
                    continue;
                }
            }
        } else if ($type == 'contract') {
            if (!$has_permission_contracts_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        } else if ($type == 'invoice') {
            if (!$has_permission_invoices_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        } else if ($type == 'estimate') {
            if (!$has_permission_estimates_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        } else if ($type == 'expense') {
            if (!$has_permission_expenses_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        } else if ($type == 'proposal') {
            if (!$has_permission_proposals_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        }

        $_data[] = $relation_values;
      //  echo '<option value="' . $relation_values['id'] . '"' . $selected . '>' . $relation_values['name'] . '</option>';
    }

    return $_data;
}
/**
 * Function to translate ticket priority
 * The apps offers ability to translate ticket priority no matter if they are stored in database
 * @param  mixed $id
 * @return string
 */
function ticket_priority_translate($id)
{
    if ($id == '' || is_null($id)) {
        return '';
    }
    $line = _l('ticket_priority_db_' . $id,'',false);
    if ($line == 'db_translate_not_found') {
        $CI =& get_instance();
        $CI->db->where('priorityid', $id);
        $priority = $CI->db->get('tblpriorities')->row();
        if (!$priority) {
            return '';
        }
        return $priority->name;
    }
    return $line;
}
/**
 * Function to translate ticket status
 * The apps offers ability to translate ticket status no matter if they are stored in database
 * @param  mixed $id
 * @return string
 */
function ticket_status_translate($id)
{
    if ($id == '' || is_null($id)) {
        return '';
    }
    $line = _l('ticket_status_db_' . $id,'',false);
    if ($line == 'db_translate_not_found') {
        $CI =& get_instance();
        $CI->db->where('ticketstatusid', $id);
        $status = $CI->db->get('tblticketstatus')->row();
        if (!$status) {
            return '';
        }
        return $status->name;
    }
    return $line;
}
/**
 * Format task priority based on passed priority id
 * @param  mixed $id
 * @return string
 */
function task_priority($id)
{
    if ($id == '1') {
        $priority = _l('task_priority_low');
    } else if ($id == '2') {
        $priority = _l('task_priority_medium');
    } else if ($id == '3') {
        $priority = _l('task_priority_high');
    } else if ($id == '4') {
        $priority = _l('task_priority_urgent');
    } else {
        $priority = $id;
    }
    return $priority;
}
/**
 * Return class based on task priority id
 * @param  mixed $id
 * @return string
 */
function get_task_priority_class($id)
{
    if ($id == 1) {
        $class = 'muted';
    } else if ($id == 2) {
        $class = 'info';
    } else if ($id == 3) {
        $class = 'warning';
    } else {
        $class = 'danger';
    }
    return $class;
}
/**
 * @deprecated
 * @param  mixed  $id
 * @param  boolean $replace_default_by_muted
 * @return string
 */
function get_project_label($id, $replace_default_by_muted = false)
{
    return project_status_color_class($id,$replace_default_by_muted);
}
/**
 * All projects tasks have their own styling and this function will return the class based
 * on bootstrap framework like defualt,warning,info
 * @param  mixed  $id
 * @param  boolean $replace_default_by_muted
 * @return string
 */
function project_status_color_class($id,$replace_default_by_muted = false){
   if ($id == 1 || $id == 5) {
        $class = 'default';
        if($replace_default_by_muted == true){
            $class = 'muted';
        }
    } else if ($id == 2) {
        $class = 'info';
    } else if ($id == 3) {
        $class = 'warning';
    } else {
        // ID == 4 finished
        $class = 'success';
    }

    $hook_data = do_action('project_status_color_class', array(
        'id' => $id,
        'class' => $class
    ));

    $class     = $hook_data['class'];
    return $class;
}
/**
 * Project status translate
 * @param  mixed  $id
 * @return string
 */
function project_status_by_id($id){
    $label = _l('project_status_'.$id);
    $hook_data = do_action('project_status_label',array('id'=>$id,'label'=>$label));
    $label = $hook_data['label'];
    return $label;
}
/**
 * Function that renders input for admin area based on passed arguments
 * @param  string $name             input name
 * @param  string $label            label name
 * @param  string $value            default value
 * @param  string $type             input type eq text,number
 * @param  array  $input_attrs      attributes on <input
 * @param  array  $form_group_attr  <div class="form-group"> html attributes
 * @param  string $form_group_class additional form group class
 * @param  string $input_class      additional class on input
 * @return string
 */
function render_input($name, $label = '', $value = '', $type = 'text', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }
    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $input .= '<label for="' . $name . '" class="control-label">' . _l($label,'',false) . '</label>';
    }
    $input .= '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" class="form-control' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '">';
    $input .= '</div>';
    return $input;
}
/**
 * Render color picker input
 * @param  string $name        input name
 * @param  string $label       field name
 * @param  string $value       default value
 * @param  array  $input_attrs <input sttributes
 * @return string
 */
function render_color_picker($name, $label = '', $value = '', $input_attrs = array())
{

    $_input_attrs = '';
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }

    $picker = '';
    $picker .= '<div class="form-group">';
    $picker .= '<label for="' . $name . '" class="control-label">' . $label . '</label>';
    $picker .= '<div class="input-group mbot15 colorpicker-input">
    <input type="text" value="' . set_value($name, $value) . '" name="' . $name . '" id="' . $name . '" class="form-control" ' . $_input_attrs . ' />
    <span class="input-group-addon"><i></i></span>
</div>';
    $picker .= '</div>';
    return $picker;
}
/**
 * Render date picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
function render_date_input($name, $label = '', $value = '', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }
    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $input .= '<label for="' . $name . '" class="control-label">' . _l($label,'',false) . '</label>';
    }
    $input .= '<div class="input-group date">';
    $input .= '<input type="text" id="' . $name . '" name="' . $name . '" class="form-control datepicker' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name,$value) . '">';
    $input .= '<div class="input-group-addon">
    <i class="fa fa-calendar calendar-icon"></i>
</div>';
    $input .= '</div>';
    $input .= '</div>';
    return $input;
}
/**
 * Render date time picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
function render_datetime_input($name, $label = '', $value = '', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $html = render_date_input($name, $label, $value, $input_attrs, $form_group_attr, $form_group_class, $input_class);
    $html = str_replace('datepicker', 'datetimepicker', $html);
    return $html;
}
/**
 * Render textarea for admin area
 * @param  [type] $name             textarea name
 * @param  string $label            textarea label
 * @param  string $value            default value
 * @param  array  $textarea_attrs      textarea attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $textarea_class      <textarea> additional class
 * @return string
 */
function render_textarea($name, $label = '', $value = '', $textarea_attrs = array(), $form_group_attr = array(), $form_group_class = '', $textarea_class = '')
{

    $textarea         = '';
    $_form_group_attr = '';
    $_textarea_attrs  = '';
    if (!isset($textarea_attrs['rows'])) {
        $textarea_attrs['rows'] = 4;
    }

    foreach ($textarea_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_textarea_attrs .= $key . '=' . '"' . $val . '"';
    }
    foreach ($form_group_attr as $key => $val) {
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }
    if (!empty($textarea_class)) {
        $textarea_class = ' ' . $textarea_class;
    }
    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    $textarea .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $textarea .= '<label for="' . $name . '" class="control-label">' . _l($label,'',false) . '</label>';
    }

    $v = clear_textarea_breaks($value);
    if (strpos($textarea_class, 'tinymce') !== false) {
        $v = $value;
    }
    $textarea .= '<textarea id="' . $name . '" name="' . $name . '" class="form-control' . $textarea_class . '" ' . $_textarea_attrs . '>' . set_value($name, $v) . '</textarea>';

    $textarea .= '</div>';
    return $textarea;
}
/**
 * Render <select> field optimized for admin area and bootstrap-select plugin
 * @param  string  $name             select name
 * @param  array  $options          option to include
 * @param  array   $option_attrs     additional options attributes to include, attributes accepted based on the bootstrap-selectp lugin
 * @param  string  $label            select label
 * @param  string  $selected         default selected value
 * @param  array   $select_attrs     <select> additional attributes
 * @param  array   $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string  $form_group_class <div class="form-group"> additional class
 * @param  string  $select_class     additional <select> class
 * @param  boolean $include_blank    do you want to include the first <option> to be empty
 * @return string
 */
function render_select($name, $options, $option_attrs = array(), $label = '', $selected = '', $select_attrs = array(), $form_group_attr = array(), $form_group_class = '', $select_class = '', $include_blank = true)
{

    $callback_translate = '';
    if (isset($options['callback_translate'])) {
        $callback_translate = $options['callback_translate'];
        unset($options['callback_translate']);
    }
    $select           = '';
    $_form_group_attr = '';
    $_select_attrs    = '';
    if (!isset($select_attrs['data-width'])) {
        $select_attrs['data-width'] = '100%';
    }
    if (!isset($select_attrs['data-none-selected-text'])) {
        $select_attrs['data-none-selected-text'] = _l('dropdown_non_selected_tex');
    }
    foreach ($select_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_select_attrs .= $key . '=' . '"' . $val . '"';
    }
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }
    if (!empty($select_class)) {
        $select_class = ' ' . $select_class;
    }
    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    $select .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $select .= '<label for="' . $name . '" class="control-label">' . _l($label,'',false) . '</label>';
    }
    $select .= '<select id="' . $name . '" name="' . $name . '" class="selectpicker' . $select_class . '" ' . $_select_attrs . ' data-live-search="true">';
    if ($include_blank == true) {
        $select .= '<option value=""></option>';
    }
    foreach ($options as $option) {
        $val       = '';
        $_selected = '';
        $key       = '';
        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }
        if (!is_array($option_attrs[1])) {
            $val = $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {
                $val .= $option[$_val] . ' ';
            }
        }
        $val = trim($val);
        if ($callback_translate != '') {
            if (function_exists($callback_translate) && is_callable($callback_translate)) {
                $val = call_user_func($callback_translate, $key);
            }
        }
        $data_sub_text = '';
        if (!is_array($selected)) {
            if ($selected != '') {
                if ($selected == $key) {
                    $_selected = ' selected';
                }
            }
        } else {
            foreach ($selected as $id) {
                if ($key == $id) {
                    $_selected = ' selected';
                }
            }
        }
        if (isset($option_attrs[2])) {

            if (strpos($option_attrs[2], ',') !== false) {
                $sub_text = '';
                $_temp    = explode(',', $option_attrs[2]);
                foreach ($_temp as $t) {
                    if (isset($option[$t])) {
                        $sub_text .= $option[$t] . ' ';
                    }
                }
            } else {
                if (isset($option[$option_attrs[2]])) {
                    $sub_text = $option[$option_attrs[2]];
                } else {
                    $sub_text = $option_attrs[2];
                }
            }
            $data_sub_text = ' data-subtext=' . '"' . $sub_text . '"';
        }
        $data_content = '';
        if (isset($option['option_attributes'])) {
            foreach ($option['option_attributes'] as $_opt_attr_key => $_opt_attr_val) {
                $data_content .= $_opt_attr_key . '=' . '"' . $_opt_attr_val . '"';
            }
        }
        $select .= '<option value="' . $key . '"' . $_selected . $data_content . '' . $data_sub_text . '>' . $val . '</option>';
    }
    $select .= '</select>';
    $select .= '</div>';
    return $select;
}
/**
 * Init admin head
 * @param  boolean $aside should include aside
 */
function init_head($aside = true)
{
    $CI =& get_instance();
    $CI->load->view('admin/includes/head');
    $CI->load->view('admin/includes/header');
    $CI->load->view('admin/includes/setup_menu');
    if ($aside == true) {
        $CI->load->view('admin/includes/aside');
    }
}
/**
 * Init admin footer/tails
 */
function init_tail()
{
    $CI =& get_instance();
    $CI->load->view('admin/includes/scripts');
}
/**
 * Render table used for datatables
 * @param  array  $headings           [description]
 * @param  string $class              table class / added prefix table-$class
 * @param  array  $additional_classes
 * @return string                     formated table
 */
/**
 * Render table used for datatables
 * @param  array   $headings
 * @param  string  $class              table class / add prefix eq.table-$class
 * @param  array   $additional_classes additional table classes
 * @param  array   $table_attributes   table attributes
 * @param  boolean $tfoot              includes blank tfoot
 * @return string
 */
function render_datatable($headings = array(), $class = '', $additional_classes = array(''), $table_attributes = array(), $tfoot = false)
{
    $_additional_classes = '';
    $_table_attributes   = ' ';
    if (count($additional_classes) > 0) {
        $_additional_classes = ' ' . implode(' ', $additional_classes);
    }
    $CI =& get_instance();
    $browser = $CI->agent->browser();
    $IEfix   = '';
    if ($browser == 'Internet Explorer') {
        $IEfix = 'ie-dt-fix';
    }
    foreach ($table_attributes as $key => $val) {
        $_table_attributes .= $key . '=' . '"' . $val . '" ';
    }

    $table = '<div class="' . $IEfix . '"><table' . $_table_attributes . 'class="table table-striped table-' . $class . '' . $_additional_classes . '">';
    $table .= '<thead>';
    $table .= '<tr>';
    foreach ($headings as $heading) {
        $table .= '<th>' . $heading . '</th>';
    }
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    if ($tfoot == true) {
        $table .= '<tfoot>';
        $table .= '<tr>';
        for ($i = 0; $i < count($headings); $i++) {
            $table .= '<td></td>';
        }
        $table .= '</tr>';
        $table .= '</tfoot>';
    }
    $table .= '</table></div>';
    echo $table;
}
/**
 * Get company logo from company uploads folder
 * @param  string $url     href url of image
 * @param  string $href_class Additional classes on href
 */
function get_company_logo($url = '', $href_class = '')
{
    $company_logo = get_option('company_logo');
    $company_name = get_option('companyname');
    if ($url == '') {
        $url = site_url();
    } else {
        $url = site_url($url);
    }
    if ($company_logo != '') {
        echo '<a href="' . $url . '" class="' . $href_class . ' logo img-responsive"><img src="' . base_url('uploads/company/' . $company_logo) . '" class="img-responsive" alt="' . $company_name . '"></a>';
    } else if ($company_name != '') {
        echo '<a href="' . $url . '" class="' . $href_class . ' logo">' . $company_name . '</a>';
    } else {
        echo '';
    }
}
/**
 * Return staff profile image url
 * @param  mixed $staff_id
 * @param  string $type
 * @return string
 */
function staff_profile_image_url($staff_id, $type = 'small')
{
    $url = base_url('assets/images/user-placeholder.jpg');
    $CI =& get_instance();
    $CI->db->select('profile_image');
    $CI->db->from('tblstaff');
    $CI->db->where('staffid', $staff_id);
    $staff = $CI->db->get()->row();
    if ($staff) {
        if (!is_null($staff->profile_image)) {
            $url = base_url('uploads/staff_profile_images/' . $staff_id . '/' . $type . '_' . $staff->profile_image);
        }
    }
    return $url;
}
/**
 * Return contact profile image url
 * @param  mixed $contact_id
 * @param  string $type
 * @return string
 */
function contact_profile_image_url($contact_id, $type = 'small')
{
    $url = base_url('assets/images/user-placeholder.jpg');
    $CI =& get_instance();
    $CI->db->select('profile_image');
    $CI->db->from('tblcontacts');
    $CI->db->where('id', $contact_id);
    $contact = $CI->db->get()->row();
    if ($contact) {
        if (!is_null($contact->profile_image)) {
            $url = base_url('uploads/client_profile_images/' . $contact_id . '/' . $type . '_' . $contact->profile_image);
        }
    }
    return $url;
}
/**
 * Staff profile image with href
 * @param  boolean $id        staff id
 * @param  array   $classes   image classes
 * @param  string  $type
 * @param  array   $img_attrs additional <img /> attributes
 * @return string
 */
function staff_profile_image($id = false, $classes = array('staff-profile-image'), $type = 'small', $img_attrs = array())
{
    $url = base_url('assets/images/user-placeholder.jpg');
    $CI =& get_instance();
    $CI->db->select('profile_image,firstname,lastname');
    $CI->db->where('staffid', $id);
    $result = $CI->db->get('tblstaff')->row();

    $_attributes = '';
    foreach ($img_attrs as $key => $val) {
        $_attributes .= $key . '=' . '"' . $val . '" ';
    }

    if (!$result) {
        return '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';
    }

    if ($result && $result->profile_image !== null) {
        $profile_image = '<img ' . $_attributes . ' src="' . base_url('uploads/staff_profile_images/' . $id . '/' . $type . '_' . $result->profile_image) . '" class="' . implode(' ', $classes) . '" alt="' . $result->firstname . ' ' . $result->lastname . '" />';
    } else {
        $profile_image = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" alt="' . $result->firstname . ' ' . $result->lastname . '" />';
    }
    return $profile_image;
}
/**
 * Generate small icon button / font awesome
 * @param  string $url        href url
 * @param  string $type       icon type
 * @param  string $class      button class
 * @param  array  $attributes additional attributes
 * @return string
 */
function icon_btn($url = '', $type = '', $class = 'btn-default', $attributes = array())
{
    $_attributes = '';
    foreach ($attributes as $key => $val) {
        $_attributes .= $key . '=' . '"' . $val . '" ';
    }
    $_url = '#';
    if (_startsWith($url, 'http')) {
        $_url = $url;
    } else if ($url !== '#') {
        $_url = admin_url($url);
    }
    return '<a href="' . $_url . '" class="btn ' . $class . ' btn-icon" ' . $_attributes . '><i class="fa fa-' . $type . '"></i></a>';
}
/**
 * Render admin tickets table
 * @param string  $name        table name
 * @param boolean $bulk_action include checkboxes on the left side for bulk actions
 */
function AdminTicketsTableStructure($name = '', $bulk_action = false)
{
    if ($name == '') {
        $name = 'tickets-table';
    }
    ob_start();
?>
<table class="table <?php
    echo $name;
?> table-tickets">
<thead>
<tr>
<?php
    if ($bulk_action == true) {
?>
<th>
    <span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="tickets"><label></label></div>
</th>
<?php
    }
?>
<th>#</th>
<th><?php
    echo _l('ticket_dt_subject');
?></th>
<th><?php
    echo _l('tags');
?></th>
<th><?php
    echo _l('ticket_dt_department');
?></th>
<th<?php if(get_option('services') == 0){echo ' class="not_visible"'; }?>><?php
        echo _l('ticket_dt_service');
?></th>
<th><?php
    echo _l('ticket_dt_submitter');
?></th>
<th><?php
    echo _l('ticket_dt_status');
?></th>
<th><?php
    echo _l('ticket_dt_priority');
?></th>
<th><?php
    echo _l('ticket_dt_last_reply');
?></th>
<th class="ticket_created_column">
<?php
    echo _l('ticket_date_created');
?></th>
<?php
    $custom_fields = get_custom_fields('tickets', array(
        'show_on_table' => 1
    ));
    foreach ($custom_fields as $field) {
?>
    <th><?php
        echo $field['name'];
?></th>
<?php
    }
?>
<th><?php
    echo _l('options');
?></th>
</tr>
</thead>
<tbody>
</tbody>
</table>

<?php
    $table = ob_get_contents();
    ob_end_clean();
    return $table;
}
/**
 * Get status label class for task
 * @param  mixed $id
 * @return string
 */
function get_status_label($id)
{
    $label = 'default';

    if ($id == 2) {
        $label = 'light-green';
    } else if ($id == 3) {
        $label = 'default';
    } else if ($id == 4) {
        $label = 'info';
    } else if ($id == 5) {
        $label = 'success';
    } else if ($id == 6) {
        $label = 'warning';
    }

    $hook_data = do_action('task_status_label',array('label'=>$label,'status_id'=>$id));
    $label = $hook_data['label'];
    return $label;
}
/**
 * Function that format task status for the final user
 * @param  string  $id    status id
 * @param  boolean $text
 * @param  boolean $clean
 * @return string
 */
function format_task_status($id, $text = false, $clean = false)
{
    $status_name = _l('task_status_' . $id);
    $hook_data = do_action('task_status_name',array('current'=>$status_name,'status_id'=>$id));
    $status_name = $hook_data['current'];

    if ($clean == true) {
        return $status_name;
    }

    $label = get_status_label($id);

    if ($text == false) {
        $class = 'label label-' . $label;
    } else {
        $class = 'text-' . $label;
    }

    return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
}
if(!function_exists('get_table_items_and_taxes')) {
/**
 * Pluggable function for all table items HTML and PDF
 * @param  array  $items         all items
 * @param  [type]  $type          where do items come form, eq invoice,estimate,proposal etc..
 * @param  boolean $admin_preview in admin preview add additional sortable classes
 * @return array
 */
    function get_table_items_and_taxes($items, $type, $admin_preview = false)
    {
        $result['html']    = '';
        $result['taxes']   = array();
        $_calculated_taxes = array();
        $i                 = 1;
        foreach ($items as $item) {
            $_item             = '';
            $tr_attrs       = '';
            $td_first_sortable = '';
            if ($admin_preview == true) {
                $tr_attrs       = ' class="sortable" data-item-id="' . $item['id'] . '"';
                $td_first_sortable = ' class="dragger item_no"';
            }

            if(class_exists('pdf')){
                $font_size = get_option('pdf_font_size');
                if($font_size == ''){
                    $font_size = 10;
                }

                $tr_attrs .= ' style="font-size:'.($font_size+4).'px;"';
            }

            $_item .= '<tr' . $tr_attrs . '>';
            $_item .= '<td' . $td_first_sortable . ' align="center">' . $i . '</td>';
            $_item .= '<td class="description" align="left;"><span class="bold">' . $item['description'] . '</span><br /><span style="color:#777;">' . $item['long_description'] . '</span></td>';
            $_item .= '<td align="right">' . floatVal($item['qty']);
            if ($item['unit']) {
                $_item .= ' ' . $item['unit'];
            }
            $_item .= '</td>';
            $_item .= '<td align="right">' . _format_number($item['rate']) . '</td>';
            if (get_option('show_tax_per_item') == 1) {
                $_item .= '<td align="right">';
            }
            $item_taxes = array();
            if ($type == 'proposal') {
                $item_taxes = get_proposal_item_taxes($item['id']);
            } else if ($type == 'estimate') {
                $item_taxes = get_estimate_item_taxes($item['id']);
            } else if ($type == 'invoice') {
                $item_taxes = get_invoice_item_taxes($item['id']);
            }
            if (count($item_taxes) > 0) {
                foreach ($item_taxes as $tax) {
                    $calc_tax     = 0;
                    $tax_not_calc = false;
                    if (!in_array($tax['taxname'], $_calculated_taxes)) {
                        array_push($_calculated_taxes, $tax['taxname']);
                        $tax_not_calc = true;
                    }
                    if ($tax_not_calc == true) {
                        $result['taxes'][$tax['taxname']]          = array();
                        $result['taxes'][$tax['taxname']]['total'] = array();
                        array_push($result['taxes'][$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
                        $result['taxes'][$tax['taxname']]['tax_name'] = $tax['taxname'];
                        $result['taxes'][$tax['taxname']]['taxrate']  = $tax['taxrate'];
                    } else {
                        array_push($result['taxes'][$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
                    }
                    if (get_option('show_tax_per_item') == 1) {

                        $item_tax = '';

                        if ((count($item_taxes) > 1 && get_option('remove_tax_name_from_item_table') == false) || get_option('remove_tax_name_from_item_table') == false || mutiple_taxes_found_for_item($item_taxes)) {
                            $item_tax = str_replace('|', ' ', $tax['taxname']) . '%<br />';
                        } else {
                            $item_tax .= $tax['taxrate'] . '%';
                        }

                        $hook_data = array('final_tax_html'=>$item_tax,'item_taxes'=>$item_taxes,'item_id'=>$item['id']);
                        $hook_data = do_action('item_tax_table_row',$hook_data);
                        $item_tax = $hook_data['final_tax_html'];
                        $_item .= $item_tax;

                    }
                }
            } else {
                if (get_option('show_tax_per_item') == 1) {
                    $hook_data = array('final_tax_html'=>'0%','item_taxes'=>$item_taxes,'item_id'=>$item['id']);
                    $hook_data = do_action('item_tax_table_row',$hook_data);
                    $_item .= $hook_data['final_tax_html'];
                }
            }
            if (get_option('show_tax_per_item') == 1) {
                $_item .= '</td>';
            }
            $_item .= '<td class="amount" align="right">' . _format_number(($item['qty'] * $item['rate'])) . '</td>';
            $_item .= '</tr>';
            $result['html'] .= $_item;
            $i++;
        }
       return do_action('before_return_table_items_html_and_taxes',$result);
    }

}
/**
 * @deprecated
 */
function get_table_items_html_and_taxes($items, $type, $admin_preview = false)
{
    return get_table_items_and_taxes($items, $type, $admin_preview);
}
/**
 * @deprecated
 */
function get_table_items_pdf_and_taxes($items, $type)
{
    return get_table_items_and_taxes($items, $type);
}
function protected_file_url_by_path($path){
    return str_replace(FCPATH, '', $path);
}
/**
 * Callback for check_for_links
 */
function _make_url_clickable_cb($matches)
{
    $ret = '';
    $url = $matches[2];
    if (empty($url))
        return $matches[0];
    // removed trailing [.,;:] from URL
    if (in_array(substr($url, -1), array(
        '.',
        ',',
        ';',
        ':'
    )) === true) {
        $ret = substr($url, -1);
        $url = substr($url, 0, strlen($url) - 1);
    }
    return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target='_blank'>$url</a>" . $ret;
}
/**
 * Callback for check_for_links
 */
function _make_web_ftp_clickable_cb($matches)
{
    $ret  = '';
    $dest = $matches[2];
    $dest = 'http://' . $dest;
    if (empty($dest))
        return $matches[0];
    // removed trailing [,;:] from URL
    if (in_array(substr($dest, -1), array(
        '.',
        ',',
        ';',
        ':'
    )) === true) {
        $ret  = substr($dest, -1);
        $dest = substr($dest, 0, strlen($dest) - 1);
    }
    return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\" target='_blank'>$dest</a>" . $ret;
}
/**
 * Callback for check_for_links
 */
function _make_email_clickable_cb($matches)
{
    $email = $matches[2] . '@' . $matches[3];
    return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}
/**
 * Check for links/emails/ftp in string to wrap in href
 * @param  string $ret
 * @return string      formatted string with href in any found
 */
function check_for_links($ret)
{
    $ret = ' ' . $ret;
    // in testing, using arrays here was found to be faster
    $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
    // this one is not in an array because we need it to run last, for cleanup of accidental links within links
    $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
    $ret = trim($ret);
    return $ret;
}
/**
 * Strip tags
 * @param  string $html string to strip tags
 * @return string
 */
function _strip_tags($html)
{
    return strip_tags($html, '<br>,<em>,<p>,<ul>,<ol>,<li>,<h4><h3><h2><h1>,<pre>,<code>,<a>,<img>,<strong>,<blockquote>,<table>,<thead>,<th>,<tr>,<td>,<tbody>,<tfoot>');
}
/**
 * Adjust color brightness
 * @param  string $hex   hex color to adjust from
 * @param  mixed $steps eq -20 or 20
 * @return string
 */
function adjust_color_brightness($hex, $steps)
{
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));
    // Normalize into a six character long hex string
    $hex   = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return      = '#';
    foreach ($color_parts as $color) {
        $color = hexdec($color); // Convert to decimal
        $color = max(0, min(255, $color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }
    return $return;
}
/**
 * Convert hex color to rgb
 * @param  string $color color hex code
 * @return string
 */
function hex2rgb($color)
{
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6) {
        return array(
            0,
            0,
            0
        );
    }
    $rgb = array();
    for ($x = 0; $x < 3; $x++) {
        $rgb[$x] = hexdec(substr($color, (2 * $x), 2));
    }
    return $rgb;
}
/**
 * Function that strip all html tags from string/text/html
 * @param  string $str
 * @param  string $allowed prevent specific tags to be stripped
 * @return string
 */
function strip_html_tags($str, $allowed = '')
{
    $str = preg_replace('/(<|>)\1{2}/is', '', $str);
    $str = preg_replace(array(
        // Remove invisible content
        '@<head[^>]*?>.*?</head>@siu',
        '@<style[^>]*?>.*?</style>@siu',
        '@<script[^>]*?.*?</script>@siu',
        '@<object[^>]*?.*?</object>@siu',
        '@<embed[^>]*?.*?</embed>@siu',
        '@<applet[^>]*?.*?</applet>@siu',
        '@<noframes[^>]*?.*?</noframes>@siu',
        '@<noscript[^>]*?.*?</noscript>@siu',
        '@<noembed[^>]*?.*?</noembed>@siu',
        // Add line breaks before and after blocks
        '@</?((address)|(blockquote)|(center)|(del))@iu',
        '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
        '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
        '@</?((table)|(th)|(td)|(caption))@iu',
        '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
        '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
        '@</?((frameset)|(frame)|(iframe))@iu'
    ), array(
        ' ',
        ' ',
        ' ',
        ' ',
        ' ',
        ' ',
        ' ',
        ' ',
        ' ',
        "\n\$0",
        "\n\$0",
        "\n\$0",
        "\n\$0",
        "\n\$0",
        "\n\$0",
        "\n\$0",
        "\n\$0"
    ), $str);
    return strip_tags($str, $allowed);
    return $str;
} //function strip_html_tags ENDS
?>
