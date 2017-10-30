  <h4 class="no-mtop bold"><?php echo _l('projects'); ?></h4>
  <hr />
  <div class="row">
   <?php
   $_where = '';
   if(!has_permission('projects','','view')){
    $_where = 'id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id='.get_staff_user_id().')';
  }
  ?>
  <?php foreach($project_statuses as $status){ ?>
  <div class="col-md-5ths total-column">
    <div class="panel_s">
     <div class="panel-body">
      <h3 class="text-muted _total">
        <?php $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status. ' AND clientid='.$client->userid; ?>
        <?php echo total_rows('tblprojects',$where); ?>
      </h3>
      <span class="text-<?php echo project_status_color_class($status,true); ?>"><?php echo project_status_by_id($status); ?></span>
    </div>
  </div>
</div>
<?php } ?>
</div>
<?php if(isset($client)){ ?>
<?php if(has_permission('projects','','create')){ ?>
<a href="<?php echo admin_url('projects/project?customer_id='.$client->userid); ?>" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_project'); ?></a>
<?php }
$table_data = array(
  '#',
  _l('project_name'),
  _l('project_customer'),
  _l('tags'),
  _l('project_start_date'),
  _l('project_deadline'),
  _l('project_members'),
  _l('project_status'),
  );
if(has_permission('projects','','create') || has_permission('projects','','edit')){
 array_push($table_data,_l('project_billing_type'));
}
$custom_fields = get_custom_fields('projects',array('show_on_table'=>1));
foreach($custom_fields as $field){
  array_push($table_data,$field['name']);
}
array_push($table_data, _l('options'));

render_datatable($table_data,'projects-single-client');
}
?>
