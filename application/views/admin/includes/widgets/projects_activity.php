<?php if(count($projects_activity) > 0){ ?>
<div class="panel_s projects-activity">
 <div class="panel-body padding-10">
  <p class="padding-5 mtop5"><?php echo _l('home_project_activity'); ?></p>
  <hr class="no-mtop" />
  <div class="activity-feed">
   <?php
   foreach($projects_activity as $activity){
    $name = $activity['fullname'];
    if($activity['staff_id'] != 0){
     $href = admin_url('profile/'.$activity['staff_id']);
   } else if($activity['contact_id'] != 0){
    $name = '<span class="label label-info inline-block mright5">'._l('is_customer_indicator') . '</span> - ' . $name;
    $href = admin_url('clients/client/'.get_user_id_by_contact_id($activity['contact_id']).'?contactid='.$activity['contact_id']);
  } else {
   $href = '';
   $name = '[CRON]';
 }
 ?>
 <div class="feed-item">
   <div class="date"><?php echo time_ago($activity['dateadded']); ?></div>
   <div class="text">
    <p class="bold no-mbot">
     <?php if($href != ''){ ?>
     <a href="<?php echo $href;?>"><?php echo $name; ?></a> -
     <?php } else { echo $name;} ;?>
     <?php echo $activity['description']; ?></p>
     <?php echo _l('project_name'); ?>: <a href="<?php echo admin_url('projects/view/'.$activity['project_id']); ?>"><?php echo $activity['project_name']; ?></a>
   </div>
   <?php if(!empty($activity['additional_data'])){ ?>
   <p class="text-muted mtop5"><?php echo $activity['additional_data']; ?></p>
   <?php } ?>
 </div>
 <?php } ?>
</div>
</div>
</div>
<?php } ?>
