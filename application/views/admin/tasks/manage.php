<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body _buttons">
            <div class="row">
              <div class="col-md-8">
                <?php if(has_permission('tasks','','create')){ ?>
                <a href="#" onclick="new_task(); return false;" class="btn btn-info pull-left new"><?php echo _l('new_task'); ?></a>
                <?php } ?>
                <a href="<?php echo admin_url('tasks/switch_kanban/'.$switch_kanban); ?>" class="btn btn-default mleft10 pull-left">
                 <?php if($switch_kanban == 1){ echo _l('switch_to_list_view');}else{echo _l('leads_switch_to_kanban');}; ?>
               </a>
                <?php if($this->input->get('project_id')){ ?>
              <a href="<?php echo admin_url('projects/view/'.$this->input->get('project_id').'?group=project_tasks'); ?>" class="mtop5 pull-left mleft10"><?php echo _l('back_to_project'); ?></a>
            <?php } ?>
             </div>
             <div class="col-md-4">
               <?php if($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
               <div data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
               <?php echo render_input('search','','','search',array('data-name'=>'search','onkeyup'=>'tasks_kanban();','placeholder'=>_l('search_tasks')),array(),'no-margin') ?>
               </div>
               <?php } else { ?>
               <?php $this->load->view('admin/tasks/tasks_filter_by',array('view_table_name'=>'.table-tasks')); ?>
               <a href="<?php echo admin_url('tasks/detailed_overview'); ?>" class="btn btn-success pull-right mright5"><?php echo _l('detailed_overview'); ?></a>
               <?php } ?>

             </div>
           </div>
         </div>
       </div>
       <div class="panel_s mtop5">
        <div class="panel-body">
          <div class="clearfix"></div>
          <?php
          if($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
          <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
           <div class="row">

           <div id="kanban-params">
              <?php echo form_hidden('project_id',$this->input->get('project_id')); ?>
           </div>
            <div class="container-fluid">
             <div id="kan-ban"></div>
           </div>
         </div>
       </div>
       <?php } else { ?>
       <a href="#" data-toggle="modal" data-target="#tasks_bulk_actions" class="btn btn-info mbot15"><?php echo _l('bulk_actions'); ?></a>
       <?php $this->load->view('admin/tasks/_table',array('bulk_actions'=>true)); ?>

     </div>
     <div class="modal fade bulk_actions" id="tasks_bulk_actions" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
          </div>
          <div class="modal-body">
            <?php if(has_permission('tasks','','delete')){ ?>
            <div class="checkbox checkbox-danger">
              <input type="checkbox" name="mass_delete" id="mass_delete">
              <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
            </div>
            <hr class="mass_delete_separator" />
            <?php } ?>
            <div id="bulk_change">
            <div class="form-group">
              <label for="move_to_status_tasks_bulk_action"><?php echo _l('task_status'); ?></label>
              <select name="move_to_status_tasks_bulk_action" id="move_to_status_tasks_bulk_action" data-width="100%" class="selectpicker" data-none-selected-text="<?php echo _l('task_status'); ?>">
                <option value=""></option>
                <?php foreach($task_statuses as $status){ ?>
                <option value="<?php echo $status; ?>"><?php echo format_task_status($status,false,true); ?></option>
                <?php } ?>
              </select>
            </div>
            <?php if(has_permission('tasks','','edit')){ ?>
            <div class="form-group">
              <label for="task_bulk_priority" class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
              <select name="task_bulk_priority" class="selectpicker" id="task_bulk_priority" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <option value=""></option>
                <option value="1"><?php echo _l('task_priority_low'); ?></option>
                <option value="2"><?php echo _l('task_priority_medium'); ?></option>
                <option value="3"><?php echo _l('task_priority_high'); ?></option>
                <option value="4"><?php echo _l('task_priority_urgent'); ?></option>
              </select>
            </div>
            <?php } ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
          <a href="#" class="btn btn-info" onclick="tasks_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <?php } ?>
</div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
  taskid = '<?php echo $taskid; ?>';
  $(function(){
    tasks_kanban();
  });
</script>
</body>
</html>
