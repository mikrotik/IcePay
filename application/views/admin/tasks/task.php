<?php if(isset($task)){
  echo form_hidden('task_is_edit',$task->id);
}
?>
<?php echo form_open(admin_url('tasks/task/'.$id),array('id'=>'task-form')); ?>
<div class="modal fade" id="_task_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo $title; ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <?php
            $rel_type = '';
            $rel_id = '';
            if(isset($task) || ($this->input->get('rel_id') && $this->input->get('rel_type'))){
              if($this->input->get('rel_id')){
                $rel_id = $this->input->get('rel_id');
                $rel_type = $this->input->get('rel_type');
              } else {
                $rel_id = $task->rel_id;
                $rel_type = $task->rel_type;
              }
              ?>
              <div class="clearfix"></div>
              <?php } ?>
              <?php
              if(isset($task) && $task->billed == 1){
               echo '<p class="text-success no-margin">'._l('task_is_billed','<a href="'.admin_url('invoices/list_invoices/'.$task->invoice_id).'" target="_blank">'.format_invoice_number($task->invoice_id)). '</a></p><br />';
             }
             ?>
             <div class="checkbox checkbox-primary no-mtop checkbox-inline">
              <input type="checkbox" id="task_is_public" name="is_public" <?php if(isset($task)){if($task->is_public == 1){echo 'checked';}}; ?>>
              <label for="task_is_public" data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('task_public_help'); ?>"><?php echo _l('task_public'); ?></label>
            </div>
            <div class="checkbox checkbox-primary checkbox-inline">
              <input type="checkbox" id="task_is_billable" name="billable" <?php if(isset($task)){if($task->billable == 1){echo 'checked';}} else {echo 'checked';}; ?>>
              <label for="task_is_billable" data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('task_billable_help'); ?>"><?php echo _l('task_billable'); ?></label>
            </div>
            <div class="task-visible-to-customer checkbox checkbox-inline checkbox-primary<?php if((isset($task) && $task->rel_type != 'project') || !isset($task) || (isset($task) && $task->rel_type == 'project' && total_rows('tblprojectsettings',array('project_id'=>$task->rel_id,'name'=>'view_tasks','value'=>0)) > 0)){echo ' hide';} ?>">
              <input type="checkbox" id="task_visible_to_client" name="visible_to_client" <?php if(isset($task)){if($task->visible_to_client == 1){echo 'checked';}} ?>>
              <label for="task_visible_to_client"><?php echo _l('task_visible_to_client'); ?></label>
            </div>
            <hr />
            <?php $value = (isset($task) ? $task->name : ''); ?>
            <?php echo render_input('name','task_add_edit_subject',$value); ?>
            <div class="task-hours<?php if(isset($task) && $task->rel_type == 'project' && total_rows('tblprojects',array('id'=>$task->rel_id,'billing_type'=>3)) == 0){echo ' hide';} ?>">
              <?php $value = (isset($task) ? $task->hourly_rate : 0); ?>
              <?php echo render_input('hourly_rate','task_hourly_rate',$value); ?>
            </div>
            <div class="project-details<?php if($rel_type != 'project'){echo ' hide';} ?>">
              <div class="form-group">
                <label for="milestone"><?php echo _l('task_milestone'); ?></label>
                <select name="milestone" id="milestone" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                  <option value=""></option>
                  <?php foreach($milestones as $milestone){ ?>
                  <option value="<?php echo $milestone['id']; ?>" <?php if(isset($task) && $task->milestone == $milestone['id']){echo 'selected'; } ?>><?php echo $milestone['name']; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <?php if(isset($task)){
                  $value = _d($task->startdate);
                } else if(isset($start_date)){
                  $value = $start_date;
                } else {
                  $value = _d(date('Y-m-d'));
                }
                ?>
                <?php echo render_date_input('startdate','task_add_edit_start_date',$value); ?>
              </div>
              <div class="col-md-6">
                <?php $value = (isset($task) ? _d($task->duedate) : ''); ?>
                <?php echo render_date_input('duedate','task_add_edit_due_date',$value,$project_end_date_attrs); ?>
              </div>
              <div class="col-md-6">
               <div class="form-group">
                <label for="priority" class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
                <select name="priority" class="selectpicker" id="priority" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                  <option value="1" <?php if(isset($task) && $task->priority == 1 || !isset($task) && get_option('default_task_priority') == 1){echo 'selected';} ?>><?php echo _l('task_priority_low'); ?></option>
                  <option value="2" <?php if(isset($task) && $task->priority == 2 || !isset($task) && get_option('default_task_priority') == 2){echo 'selected';} ?>><?php echo _l('task_priority_medium'); ?></option>
                  <option value="3" <?php if(isset($task) && $task->priority == 3 || !isset($task) && get_option('default_task_priority') == 3){echo 'selected';} ?>><?php echo _l('task_priority_high'); ?></option>
                  <option value="4" <?php if(isset($task) && $task->priority == 4 || !isset($task) && get_option('default_task_priority') == 4){echo 'selected';} ?>><?php echo _l('task_priority_urgent'); ?></option>
                  <?php do_action('task_priorities_select',(isset($task)?$task:0)); ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
               <label for="repeat_every" class="control-label"><?php echo _l('task_repeat_every'); ?></label>
               <select name="repeat_every" id="repeat_every" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <option value=""></option>
                <option value="1-week" <?php if(isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'week'){echo 'selected';} ?>><?php echo _l('week'); ?></option>
                <option value="2-week" <?php if(isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'week'){echo 'selected';} ?>>2 <?php echo _l('weeks'); ?></option>
                <option value="1-month" <?php if(isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'month'){echo 'selected';} ?>>1 <?php echo _l('month'); ?></option>
                <option value="2-month" <?php if(isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'month'){echo 'selected';} ?>>2 <?php echo _l('months'); ?></option>
                <option value="3-month" <?php if(isset($task) && $task->repeat_every == 3 && $task->recurring_type == 'month'){echo 'selected';} ?>>3 <?php echo _l('months'); ?></option>
                <option value="6-month" <?php if(isset($task) && $task->repeat_every == 6 && $task->recurring_type == 'month'){echo 'selected';} ?>>6 <?php echo _l('months'); ?></option>
                <option value="1-year" <?php if(isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'year'){echo 'selected';} ?>>1 <?php echo _l('year'); ?></option>
                <option value="custom" <?php if(isset($task) && $task->custom_recurring == 1){echo 'selected';} ?>><?php echo _l('recurring_custom'); ?></option>
              </select>
            </div>
          </div>
        </div>

        <div class="recurring_custom <?php if((isset($task) && $task->custom_recurring != 1) || (!isset($task))){echo 'hide';} ?>">
         <div class="row">
          <div class="col-md-6">
           <?php $value = (isset($task) && $task->custom_recurring == 1 ? $task->repeat_every : ''); ?>
           <?php echo render_input('repeat_every_custom','',$value,'number'); ?>
         </div>
         <div class="col-md-6">
           <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
            <option value="day" <?php if(isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'day'){echo 'selected';} ?>><?php echo _l('task_recuring_days'); ?></option>
            <option value="week" <?php if(isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'week'){echo 'selected';} ?>><?php echo _l('task_recuring_weeks'); ?></option>
            <option value="month" <?php if(isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'month'){echo 'selected';} ?>><?php echo _l('task_recuring_months'); ?></option>
            <option value="year" <?php if(isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'year'){echo 'selected';} ?>><?php echo _l('task_recuring_years'); ?></option>
          </select>
        </div>
      </div>
    </div>
    <div id="recurring_ends_on" class="<?php if(!isset($task) || (isset($task) && $task->recurring == 0)){echo 'hide';}?>">
      <?php $value = (isset($task) ? _d($task->recurring_ends_on) : ''); ?>
      <?php echo render_date_input('recurring_ends_on','recurring_ends_on',$value); ?>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
          <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
            <option value=""></option>
            <option value="project"
            <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'project'){echo 'selected';}} ?>><?php echo _l('project'); ?></option>
            <option value="invoice" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'invoice'){echo 'selected';}} ?>>
              <?php echo _l('invoice'); ?>
            </option>
            <option value="customer"
            <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'customer'){echo 'selected';}} ?>> <?php echo _l('client'); ?></option>
            <option value="estimate" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'estimate'){echo 'selected';}} ?>><?php echo _l('estimate'); ?></option>
            <option value="contract" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'contract'){echo 'selected';}} ?>><?php echo _l('contract'); ?></option>
            <option value="ticket" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'ticket'){echo 'selected';}} ?>><?php echo _l('ticket'); ?></option>
            <option value="expense" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'expense'){echo 'selected';}} ?>><?php echo _l('expense'); ?></option>
            <option value="lead" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'lead'){echo 'selected';}} ?>><?php echo _l('lead'); ?></option>
            <option value="proposal" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'proposal'){echo 'selected';}} ?>><?php echo _l('proposal'); ?></option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group<?php if($rel_id == ''){echo ' hide';} ?>" id="rel_id_wrapper">
          <label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
          <div id="rel_id_select">
            <select name="rel_id" id="rel_id" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
              <?php if($rel_id != '' && $rel_type != ''){
                $rel_data = get_relation_data($rel_type,$rel_id);
                $rel_val = get_relation_values($rel_data,$rel_type);
                echo '<option value="'.$rel_val['id'].'">'.$rel_val['name'].'</option>';
              } ?>
            </select>
          </div>
        </div>
      </div>
    </div>
    <?php if(isset($task) && $task->status == 5 && (has_permission('create') || has_permission('edit'))){
      echo render_datetime_input('datefinished','task_finished',_dt($task->datefinished));
    }
    ?>
    <div class="form-group">
      <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
      <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($task) ? prep_tags_input(get_tags_in($task->id,'task')) : ''); ?>" data-role="tagsinput">
    </div>
    <?php $rel_id_custom_field = (isset($task) ? $task->id : false); ?>
    <?php echo render_custom_fields('tasks',$rel_id_custom_field); ?>
    <hr />
    <p class="bold"><?php echo _l('task_add_edit_description'); ?></p>
    <?php $contents = ''; if(isset($task)){$contents = $task->description;} ?>
    <?php echo render_textarea('description','',$contents,array('data-task-ae-editor'=>true),array(),'','tinymce-task'); ?>
  </div>
</div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
  <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
</div>
</div>
</div>
<?php echo form_close(); ?>
<script>
 var _rel_id = $('#rel_id'),
 _rel_type = $('#rel_type'),
 _rel_id_wrapper = $('#rel_id_wrapper'),
 data = {};

 var _milestone_selected_data;
 _milestone_selected_data = undefined;

 $(function(){

  $( "body" ).off( "change", "#rel_id" );

  custom_fields_hyperlink();
  init_tags_inputs();

  _validate_form($('#task-form'), {
    name: 'required',
    startdate: 'required'
  },task_form_handler);

  $('.rel_id_label').html(_rel_type.find('option:selected').text());
  _rel_type.on('change', function() {
   var clonedSelect = _rel_id.html('').clone();
   _rel_id.selectpicker('destroy').remove();
   _rel_id = clonedSelect;
   $('#rel_id_select').append(clonedSelect);
   $('.rel_id_label').html(_rel_type.find('option:selected').text());

   task_rel_select();
   if($(this).val() != ''){
    _rel_id_wrapper.removeClass('hide');
  } else {
    _rel_id_wrapper.addClass('hide');
  }
  init_project_details(_rel_type.val());
});

  init_datepicker();
  init_color_pickers();
  init_selectpicker();
  task_rel_select();
  init_editor('.tinymce-task',{height:200});

  $('body').on('change','#rel_id',function(){
   if($(this).val() != ''){
     if(_rel_type.val() == 'project'){
       $.get(admin_url + 'projects/get_rel_project_data/'+$(this).val()+'/'+taskid,function(project){
         $("select[name='milestone']").html(project.milestones);
         if(typeof(_milestone_selected_data) != 'undefined'){
          $("select[name='milestone']").val(_milestone_selected_data.id);
          $('input[name="duedate"]').val(_milestone_selected_data.due_date)
        }
        $("select[name='milestone']").selectpicker('refresh');
        if(project.billing_type == 3){
         $('.task-hours').addClass('project-task-hours');
       } else {
         $('.task-hours').removeClass('project-task-hours');
       }
       init_project_details(_rel_type.val(),project.allow_to_view_tasks);
     },'json');
     }
   }
 });

  <?php if(!isset($task) && $rel_id != ''){ ?>
    _rel_id.change();
    <?php } ?>

  });

 <?php if(isset($_milestone_selected_data)){ ?>
  _milestone_selected_data = '<?php echo json_encode($_milestone_selected_data); ?>';
  _milestone_selected_data = JSON.parse(_milestone_selected_data);
  <?php } ?>

  function task_rel_select(){
    clearInterval(autocheck_notifications_timer_id);
    var options = {
      ajax: {
        url: admin_url + 'misc/get_relation_data',
        data: function () {
          data.q = '{{{q}}}';
          data.type = _rel_type.val();
          data.rel_id = _rel_id.val();
          <?php if(isset($task)){ ?>
           data.connection_type = 'task';
           data.connection_id = '<?php echo $task->id; ?>';
           <?php } ?>
           return data;
         }
       },
       locale: {
        emptyTitle: "<?php echo _l('search_ajax_empty'); ?>",
        statusInitialized: "<?php echo _l('search_ajax_initialized'); ?>",
        statusSearching:"<?php echo _l('search_ajax_searching'); ?>",
        statusNoResults:"<?php echo _l('not_results_found'); ?>",
        searchPlaceholder:"<?php echo _l('search_ajax_placeholder'); ?>",
        currentlySelected:"<?php echo _l('currently_selected'); ?>",
      },
      requestDelay:500,
      cache:false,
      preprocessData: function(processData){
        var bs_data = [];
        var len = processData.length;
        for(var i = 0; i < len; i++){
          var curr = processData[i];
          var _temp = {
            'value': curr.id,
            'text': curr.name,
          };
          bs_data.push(_temp);
        }
        return bs_data;
      },
      preserveSelectedPosition:'after',
      preserveSelected:true
    }
    _rel_id
    .selectpicker()
    .ajaxSelectPicker(options)
  }

  function init_project_details(type,tasks_visible_to_customer){
    var wrap = $('.non-project-details');
    var wrap_task_hours = $('.task-hours');
    if(type == 'project'){
      if(wrap_task_hours.hasClass('project-task-hours') == true){
        wrap_task_hours.removeClass('hide');
      } else {
        wrap_task_hours.addClass('hide');
      }
      wrap.addClass('hide');
      $('.project-details').removeClass('hide');
    } else {
      wrap_task_hours.removeClass('hide');
      wrap.removeClass('hide');
      $('.project-details').addClass('hide');
      $('.task-visible-to-customer').addClass('hide').prop('checked',false);
    }
    if(typeof(tasks_visible_to_customer) != 'undefined'){
      if(tasks_visible_to_customer == 1){
        $('.task-visible-to-customer').removeClass('hide');
        $('.task-visible-to-customer input').prop('checked',true);
      } else {
        $('.task-visible-to-customer').addClass('hide')
        $('.task-visible-to-customer input').prop('checked',false);
      }
    }
  }
</script>
