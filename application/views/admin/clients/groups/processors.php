<?php if(isset($client)){ ?>
    <h4 class="no-mtop bold"><?php echo _l('client_processors_tab'); ?></h4>
    <hr />
    <?php if(has_permission('processors','','create')){ ?>
        <a href="<?php echo admin_url('processors/processor?customer_id='.$client->userid); ?>" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('create_new_processor'); ?></a>
    <?php } ?>
<?php } ?>
