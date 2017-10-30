<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <?php echo form_open_multipart($this->uri->uri_string().'?group='.$group,array('id'=>'settings-form')); ?>
      <div class="row">
         <?php if($this->session->flashdata('debug')){ ?>
         <div class="col-lg-12">
            <div class="alert alert-warning">
               <?php echo $this->session->flashdata('debug'); ?>
            </div>
         </div>
         <?php } ?>
         <div class="col-md-3">
            <div class="panel_s">
               <div class="panel-body">
                  <ul class="nav navbar-pills nav-tabs nav-stacked">
                     <li class="active">
                        <a href="<?php echo admin_url('settings?group=general'); ?>" data-group="general">
                        <?php echo _l('settings_group_general'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=company'); ?>" data-group="company">
                        <?php echo _l('settings_sales_heading_company'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=localization'); ?>" data-group="localization">
                        <?php echo _l('settings_group_localization'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=tickets'); ?>" data-group="tickets">
                        <?php echo _l('settings_group_tickets'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=sales'); ?>" data-group="sales">
                        <?php echo _l('settings_group_sales'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=online_payment_modes'); ?>" data-group="online_payment_modes">
                        <?php echo _l('settings_group_online_payment_modes'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=email'); ?>" data-group="email">
                        <?php echo _l('settings_group_email'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=clients'); ?>" data-group="clients">
                        <?php echo _l('settings_group_clients'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=tasks'); ?>" data-group="tasks">
                        <?php echo _l('tasks'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=leads'); ?>" data-group="leads">
                        <?php echo _l('leads'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=calendar'); ?>" data-group="calendar">
                        <?php echo _l('settings_calendar'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=pdf'); ?>" data-group="pdf">
                        <?php echo _l('settings_pdf'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=cronjob'); ?>" data-group="cronjob">
                        <?php echo _l('settings_group_cronjob'); ?></a>
                     </li>
                     <li>
                        <a href="<?php echo admin_url('settings?group=misc'); ?>" data-group="misc">
                        <?php echo _l('settings_group_misc'); ?></a>
                     </li>
                     <?php if(is_admin()){ ?>
                     <li>
                        <a href="<?php echo admin_url('settings?group=update'); ?>" data-group="update">
                        <?php echo _l('settings_update'); ?></a>
                     </li>
                     <?php } ?>
                  </ul>
                  <div class="col-md-12 text-center">
                     <button type="submit" class="btn btn-info btn-block"><?php echo _l('settings_save'); ?></button>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-9">
            <div class="panel_s">
               <div class="panel-body">
                  <?php echo $group_view; ?>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
      <?php echo form_close(); ?>
   </div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>
<script>
   $(function(){
     $('.test_email').on('click', function() {
      var email = $('input[name="test_email"]').val();
      if (email != '') {
       $(this).attr('disabled', true);
       $.post(admin_url + 'emails/sent_smtp_test_email', {
        test_email: email
      }).done(function(data) {
        window.location.reload();
      });
    }
   });

     $('#update_app').on('click',function(e){
       e.preventDefault();
       $('input[name="settings[purchase_key]"]').parents('.form-group').removeClass('has-error');
       var purchase_key = $('input[name="settings[purchase_key]"]').val();
       var latest_version = $('input[name="latest_version"]').val();
       var update_errors;
       if(purchase_key != ''){
         var ubtn = $(this);
         ubtn.html('<?php echo _l('wait_text'); ?>');
         ubtn.addClass('disabled');
         $.post(admin_url+'auto_update',{purchase_key:purchase_key,latest_version:latest_version,auto_update:true}).done(function(){
           $.post(admin_url+'auto_update/database',{auto_update:true}).done(function(){
             window.location.reload();
           }).fail(function(){
             update_errors = JSON.parse(response.responseText);
             $('#update_messages .alert').append(update_errors[0]);
           });
         }).fail(function(response){
           update_errors = JSON.parse(response.responseText);
           $('#update_messages').html('<div class="alert alert-danger"></div>');
           for (var i in update_errors){
             $('#update_messages .alert').append('<p>'+update_errors[i]+'</p>');
           }
           ubtn.removeClass('disabled');
           ubtn.html($('.update_app_wrapper').data('original-text'));
         });
       } else {
        $('input[name="settings[purchase_key]"]').parents('.form-group').addClass('has-error');
       }
     });
   });
</script>
</body>
</html>
