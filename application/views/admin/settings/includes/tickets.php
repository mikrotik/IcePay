<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active">
    <a href="#set_tickets_general" aria-controls="set_tickets_general" role="tab" data-toggle="tab"><?php echo _l('settings_group_general'); ?></a>
  </li>
  <li role="presentation">
    <a href="#set_tickets_piping" aria-controls="set_tickets_piping" role="tab" data-toggle="tab"><?php echo _l('tickets_piping'); ?></a>
  </li>
</ul>
<div class="tab-content mtop30">
  <div role="tabpanel" class="tab-pane active" id="set_tickets_general">
    <?php render_yes_no_option('services','settings_tickets_use_services'); ?>
    <hr />
    <?php render_yes_no_option('staff_access_only_assigned_departments','settings_tickets_allow_departments_access'); ?>
    <hr />
    <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('receive_notification_on_new_ticket_help'); ?>"></i>
    <?php render_yes_no_option('receive_notification_on_new_ticket','receive_notification_on_new_ticket'); ?>
    <hr />
    <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('staff_members_open_tickets_to_all_contacts_help'); ?>"></i>
    <?php render_yes_no_option('staff_members_open_tickets_to_all_contacts','staff_members_open_tickets_to_all_contacts'); ?>
    <hr />
    <?php render_yes_no_option('access_tickets_to_none_staff_members','access_tickets_to_none_staff_members'); ?>
    <hr />
    <?php render_yes_no_option('allow_customer_to_change_ticket_status','allow_customer_to_change_ticket_status'); ?>
    <hr />
    <?php render_yes_no_option('only_show_contact_tickets','only_show_contact_tickets'); ?>
    <hr />
    <?php echo render_input('settings[maximum_allowed_ticket_attachments]','settings_tickets_max_attachments',get_option('maximum_allowed_ticket_attachments'),'number'); ?>
    <hr />
    <?php echo render_input('settings[ticket_attachments_file_extensions]','settings_tickets_allowed_file_extensions',get_option('ticket_attachments_file_extensions')); ?>
  </div>
  <div role="tabpanel" class="tab-pane" id="set_tickets_piping">
    <code>cPanel forwarder path <?php echo FCPATH .'pipe.php'; ?></code>
    <hr />
    <?php render_yes_no_option('email_piping_only_registered','email_piping_only_registered'); ?>
    <hr />
    <?php render_yes_no_option('email_piping_only_replies','email_piping_only_replies'); ?>
    <hr />

    <?php echo render_select('settings[email_piping_default_priority]',$ticket_priorities,array('priorityid','name'),'email_piping_default_priority',get_option('email_piping_default_priority')); ?>
  </div>
