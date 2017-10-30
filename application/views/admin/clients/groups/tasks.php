  <h4 class="no-mtop bold"><?php echo _l('tasks'); ?></h4>
  <hr />
<?php if(isset($client)){
    init_relation_tasks_table(array( 'data-new-rel-id'=>$client->userid,'data-new-rel-type'=>'customer'));
} ?>
