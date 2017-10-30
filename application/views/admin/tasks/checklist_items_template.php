<?php
$total_checklist_items = total_rows('tbltaskchecklists',array('taskid'=>$task_id));
?>
<div class="clearfix"></div>
<?php if(count($checklists) > 0){ ?>
<h4 class="bold chk-heading th font-medium"><?php echo _l('task_checklist_items'); ?></h4>
<?php } ?>
<div class="progress mtop15 hide">
    <div class="progress-bar not-dynamic progress-bar-default task-progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
    </div>
</div>
<?php foreach($checklists as $list){ ?>
<div class="checklist" data-checklist-id="<?php echo $list['id']; ?>">
    <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="">
        <input type="checkbox" <?php if($list['finished'] == 1 && $list['finished_from'] != get_staff_user_id() && !is_admin()){echo 'disabled';} ?> name="checklist-box" <?php if($list['finished'] == 1){echo 'checked';}; ?>>
        <label for=""><span class="hide"><?php echo $list['description']; ?></span></label>
        <textarea name="checklist-description" rows="1"><?php echo clear_textarea_breaks($list['description']); ?></textarea>
        <?php if(has_permission('tasks','','delete') || $list['addedfrom'] == get_staff_user_id()){ ?>
        <a href="#" class="pull-right text-muted remove-checklist" onclick="delete_checklist_item(<?php echo $list['id']; ?>,this); return false;"><i class="fa fa-remove"></i></a>
        <?php } ?>
    </div>
    <?php if($list['finished'] == 1 && $list['finished_from'] != get_staff_user_id()){ ?>
    <p class="small mtop5"><?php echo _l('task_checklist_item_completed_by',get_staff_full_name($list['finished_from'])); ?></p>
    <?php } ?>
</div>
<?php } ?>
<script>
    $(function(){
     $("textarea[name='checklist-description']").keypress(function(event) {
      if(event.which == '13') {
        $(this).focusout();
        add_task_checklist_item('<?php echo $task_id; ?>');
        return false;
    }
});
     $("#checklist-items").sortable({
        helper: 'clone',
        items: 'div.checklist',
        update: function(event, ui) {
            update_checklist_order();
        }
    });
     setTimeout(function(){
        do_task_checklist_items_height();
    },200);
 })
</script>
