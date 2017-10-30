<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <a href="<?php echo admin_url('tasks'); ?>" class="btn btn-default pull-left"><?php echo _l('back_to_tasks_list'); ?></a>
            <div class="clearfix"></div>
            <hr />

            <?php echo form_open($this->uri->uri_string()); ?>
            <div class="row">
              <?php if(has_permission('tasks','','create')){ ?>
              <div class="col-md-2 border-right">
                <?php
                echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$staff_id,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
              </div>
              <?php } ?>
              <div class="col-md-2 border-right">
                <?php
                $months = array();

                for ($m = 1; $m <= 12; $m++) {
                  $data = array();
                  $data['month'] = $m;
                  $data['name'] = _l(date('F', mktime(0, 0, 0, $m, 1)));
                  $months[] = $data;
                }
                $selected = ($this->input->post('month') ? $this->input->post('month') : '');
                echo render_select('month',$months,array('month',array('name')),'',$selected,array('data-none-selected-text'=>_l('task_filter_detailed_all_months')),array(),'no-margin');
                ?>
              </div>
               <div class="col-md-2 text-center border-right">
                <div class="form-group no-margin">
                  <select name="status" id="status" class="selectpicker no-margin" data-width="100%" data-title="<?php echo _l('task_status'); ?>">
                    <option value="" selected><?php echo _l('task_list_all'); ?></option>
                    <?php foreach($task_statuses as $status){ ?>
                    <option value="<?php echo $status; ?>" <?php if($this->input->post('status') == $status){echo 'selected'; } ?>><?php echo format_task_status($status,false,true); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-2 border-right">
                   <select name="year" id="year" class="selectpicker no-margin" data-width="100%">
                    <?php foreach($years as $data){ ?>
                    <option value="<?php echo $data['year']; ?>" <?php if($this->input->post('year') == $data['year'] || date('Y') == $data['year']){echo 'selected'; } ?>><?php echo $data['year']; ?></option>
                    <?php } ?>
                  </select>
              </div>
              <div class="col-md-2 border-right" style="margin-top:-5px;">
                <div class="form-group">
                  <label><?php echo _l('task_filter_fetch_month_by'); ?></label>
                  <div class="clearfix"></div>
                  <div class="radio radio-primary">
                    <input type="radio" id="filter_duedate" name="month_from" value="duedate" <?php if($this->input->post('month_from') == 'duedate' || !$this->input->post('month_from')){echo 'checked';} ?>>
                    <label for="filter_duedate"><?php echo _l('task_duedate'); ?></label>
                  </div>
                  <div class="radio radio-primary">
                    <input type="radio" id="filter_startdate" name="month_from" value="startdate"  <?php if($this->input->post('month_from') == 'startdate'){echo 'checked';} ?>>
                    <label for="filter_startdate"><?php echo _l('tasks_dt_datestart'); ?></label>
                  </div>
                </div>
              </div>

              <div class="col-md-2">
                <button type="submit" class="btn btn-info btn-block"><?php echo _l('filter'); ?>
                </div>
              </div>
              <?php echo form_close(); ?>
            </div>
          </div>
          <div class="panel_s">
            <div class="panel-body">
              <?php foreach($overview as $month =>$data){ if(count($data) == 0){continue;} ?>
              <h4 class="no-margin bold text-success"><?php echo  _l(date('F', mktime(0, 0, 0, $month, 1))); ?></h4>
              <div class="table-responsive">
                <table class="table tasks-overview">
                  <thead>
                    <tr>
                      <th width="60%"><?php echo _l('tasks_dt_name'); ?></th>
                      <th><?php echo _l('task_status'); ?></th>
                      <th><?php echo _l('task_finished_on_time'); ?></th>
                      <th><?php echo _l('task_assigned'); ?></th>
                      <th><?php echo _l('tasks_dt_datestart'); ?></th>
                      <th><?php echo _l('task_duedate'); ?></th>
                    </tr>
                  </thead>
                  <tbody>
                   <?php
                   $total_logged_time = 0;
                   foreach($data as $task){
                    $where_total_time = '';
                    if(is_numeric($staff_id)){
                      $where_total_time = ' AND staff_id=' . $staff_id;
                    }
                    $task_total_logged_time_by_user = $this->tasks_model->calc_task_total_time($task['id'], $where_total_time);
                    $total_logged_time += $task_total_logged_time_by_user;
                    $row_class = ($task['status'] == 5 ? 'task-finished-table-green' : 'task-unfinished-table text-danger');
                    ?>
                    <tr class="<?php echo $row_class; ?>">
                      <td class="stripped-table-data" width="60%"><a href="#" onclick="init_task_modal(<?php echo $task['id']; ?>); return false;"><?php echo $task['name']; ?></a>
                        <div class="pull-right">
                          <!-- removes the last : on the language string task_total_logged_time -->
                          <span class="label label-default-light pull-left mright5" data-toggle="tooltip" data-title="<?php echo mb_substr(_l('task_total_logged_time'),0,-1); ?>">
                           <i class="fa fa-clock-o"></i> <?php echo seconds_to_time_format($task_total_logged_time_by_user); ?>
                         </span>
                         <span class="label <?php
                         if(total_rows('tbltaskchecklists',array('taskid'=>$task['id'])) == 0){
                          echo 'label-default-light';
                        } else if((total_rows('tbltaskchecklists',array('finished'=>1,'taskid'=>$task['id'])) != total_rows('tbltaskchecklists',array('taskid'=>$task['id'])))){
                          echo 'label-danger';
                        } else if(total_rows('tbltaskchecklists',array('taskid'=>$task['id'])) == total_rows('tbltaskchecklists',array('taskid'=>$task['id'],'finished'=>1))){echo 'label-success';} ?> pull-left mright5" data-toggle="tooltip" data-title="<?php echo _l('tasks_total_checklists_finished'); ?>">
                        <?php
                        $where_checklists_finished = array('finished'=>1,'taskid'=>$task['id']);
                        if(is_numeric($staff_id)){
                          $where_checklists_finished['finished_from'] = $staff_id;
                        }
                        ?>
                        <i class="fa fa-th-list"></i> <?php echo total_rows('tbltaskchecklists',$where_checklists_finished); ?>/<?php echo total_rows('tbltaskchecklists',array('taskid'=>$task['id'])); ?>
                      </span>
                      <span class="label label-default-light pull-left mright5" data-toggle="tooltip" data-title="<?php echo _l('tasks_total_added_attachments'); ?>">
                       <?php
                       $where_comments = array('taskid'=>$task['id']);
                       $where_files = array('rel_id'=>$task['id'],'rel_type'=>'task');
                       if(is_numeric($staff_id)){
                        $where_files['staffid'] = $staff_id;
                        $where_comments['staffid'] = $staff_id;
                      }
                      ?>
                      <i class="fa fa-paperclip"></i> <?php echo total_rows('tblfiles',$where_files); ?>
                    </span>
                    <span class="label label-default-light pull-left" data-toggle="tooltip" data-title="<?php echo _l('tasks_total_comments'); ?>">
                     <i class="fa fa-comments"></i> <?php echo total_rows('tblstafftaskcomments',$where_comments); ?>
                   </span>
                 </div>
               </td>
               <td><?php echo format_task_status($task['status']); ?></td>
               <td>
                <?php
                $finished_on_time_class = '';
                if(date('Y-m-d',strtotime($task['datefinished'])) > $task['duedate'] && $task['status'] == 5 && is_date($task['duedate'])){
                  $finished_on_time_class = 'text-danger';
                  $finished_showcase = _l('task_not_finished_on_time_indicator');
                } else if(date('Y-m-d',strtotime($task['datefinished'])) <= $task['duedate'] && $task['status'] == 5 && is_date($task['duedate'])){
                  $finished_showcase = _l('task_finished_on_time_indicator');
                } else {
                  $finished_on_time_class = '';
                  $finished_showcase = '';
                }
                ?>
                <span class="<?php echo $finished_on_time_class; ?>">
                 <?php echo $finished_showcase; ?>
               </span>
             </td>
             <td>
              <?php
              $assignees = $this->tasks_model->get_task_assignees($task['id']);
              $_assignees = '';
              foreach ($assignees as $assigned) {
                $_assignees .= '<a href="' . admin_url('profile/' . $assigned['assigneeid']) . '">' . staff_profile_image($assigned['assigneeid'], array(
                  'staff-profile-image-small mright5'
                  ), 'small', array(
                  'data-toggle' => 'tooltip',
                  'data-title' => get_staff_full_name($assigned['assigneeid'])
                  )) . '</a>';

              }
              echo $_assignees;
              ?>
            </td>
            <td><?php echo _d($task['startdate']); ?></td>
            <td><?php echo _d($task['duedate']); ?></td>
          </tr>
          <?php } ?>
        </tbody>
        <tfoot>
         <tr>
           <td colspan="5" class="bold td-border-left-transparent"><?php echo _l('task_total_logged_time'); ?> <?php echo seconds_to_time_format($total_logged_time); ?></td>
         </tr>
       </tfoot>
     </table>
   </div>
   <hr />
   <?php } ?>
 </div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>
