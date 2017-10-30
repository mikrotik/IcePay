<div class="panel_s">
  <div class="panel-body">
    <h4 class="no-margin"><?php echo _l('clients_tickets_heading'); ?></h4>

  </div></div>
  <div class="panel_s">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
          <h3 class="text-success pull-left no-mtop"><?php echo _l('tickets_summary'); ?></h3>
          <a href="<?php echo site_url('clients/open_ticket'); ?>" class="btn btn-info pull-right"><?php echo _l('clients_ticket_open_subject'); ?></a>
          <div class="clearfix"></div>
          <hr />
        </div>
        <?php foreach($ticket_statuses as $status){ ?>
        <div class="col-md-2 border-right">
         <h3 class="bold">
          <a href="<?php echo site_url('clients/tickets/'.$status['ticketstatusid']); ?>"><?php echo total_rows('tbltickets',array('userid'=>get_client_user_id(),'status'=>$status['ticketstatusid'])); ?></a></h3>
          <span style="color:<?php echo $status['statuscolor']; ?>"><?php echo ticket_status_translate($status['ticketstatusid']); ?></span>
        </div>
        <?php } ?>
      </div>
      <div class="clearfix"></div>
      <hr />

      <div class="clearfix"></div>
      <?php get_template_part('tickets_table'); ?>
    </div>
  </div>
</div>
