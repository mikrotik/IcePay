<?php echo form_hidden('project_id',$project->id); ?>
<div class="panel_s">
    <div class="panel-body">
       <h3 class="bold no-margin project-name"><?php echo $project->name; ?></h3>
   </div>
</div>
<div class="panel_s">
    <div class="panel-body">
        <?php get_template_part('projects/project_tabs'); ?>
        <div class="clearfix mtop15"></div>
        <?php get_template_part('projects/'.$group); ?>
    </div>
</div>
