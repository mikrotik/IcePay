<?php
$i = 0;
$where = array();
if($this->input->get('project_id')){
  $where['rel_id'] = $this->input->get('project_id');
  $where['rel_type'] = 'project';
}
foreach ($statuses as $status) {
  $total_pages = $this->tasks_model->do_kanban_query($status,$this->input->get('search'),1,true,$where);
  $total_pages = ceil($total_pages/get_option('tasks_kanban_limit'));
  ?>
  <ul class="kan-ban-col tasks-kanban" data-col-status-id="<?php echo $status; ?>" data-total-pages="<?php echo $total_pages; ?>">
    <li class="kan-ban-col-wrapper">
      <div class="border-right panel_s">
        <div class="panel-heading-bg <?php echo get_status_label($status).'-bg'; ?>" data-status-id="<?php echo $status; ?>">
          <div class="kan-ban-step-indicator<?php if($i == count($statuses) -1){ echo ' kan-ban-step-indicator-full'; } ?>"></div>
          <span class="heading"><?php echo format_task_status($status,false,true); ?>
          </span>
          <a href="#" onclick="return false;" class="pull-right color-white">
          </a>
        </div>
        <div class="kan-ban-content-wrapper">
          <div class="kan-ban-content">
            <ul class="status tasks-status sortable relative" data-task-status-id="<?php echo $status; ?>">
              <?php
              $tasks = $this->tasks_model->do_kanban_query($status,$this->input->get('search'),1,false,$where);
              $total_tasks = count($tasks);
              foreach ($tasks as $task) {
                if ($task['status'] == $status) {
                  $this->load->view('admin/tasks/_kan_ban_card',array('task'=>$task,'status'=>$status));
                } } ?>
                <?php if($total_tasks > 0 ){ ?>
                <li class="text-center not-sortable kanban-load-more" data-load-status="<?php echo $status; ?>">
                 <a href="#" class="btn btn-default btn-block<?php if($total_pages <= 1){echo ' disabled';} ?>" data-page="1" onclick="kanban_load_more(<?php echo $status; ?>,this,'tasks/tasks_kanban_load_more',270,360); return false;"; autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('load_more'); ?></a>
               </li>
               <?php } ?>
               <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_tasks > 0){echo ' hide';} ?>">
                <h4 class="text-muted">
                  <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                  <?php echo _l('no_tasks_found'); ?></h4>
                </li>
              </ul>
            </div>
          </div>
        </li>
      </ul>
      <?php $i++; } ?>
