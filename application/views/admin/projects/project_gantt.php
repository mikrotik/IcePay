<!-- Single button -->
<div class="btn-group pull-right mbot15">
  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Show Gantt View by <span class="caret"></span>
</button>
<ul class="dropdown-menu">
    <li class="<?php if(!$this->input->get('gantt_type') || $this->input->get('gantt_type') == 'milestones'){echo 'active';} ?>"><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_gantt&gantt_type=milestones'); ?>"><?php echo _l('project_milestones'); ?></a></li>
    <li class="<?php if($this->input->get('gantt_type') == 'members'){echo 'active';} ?>"><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_gantt&gantt_type=members'); ?>"><?php echo _l('project_members'); ?></a></li>
     <li class="<?php if($this->input->get('gantt_type') == 'status'){echo 'active';} ?>"><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_gantt&gantt_type=status'); ?>"><?php echo _l('task_status'); ?></a></li>
</ul>
</div>
<div class="clearfix"></div>
<div id="gantt"></div>
