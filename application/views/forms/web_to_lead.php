<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo $form->name; ?></title>
  <?php echo app_stylesheet('assets/css','reset.css'); ?>
  <link href='<?php echo base_url('assets/plugins/roboto/roboto.css'); ?>' rel='stylesheet'>
  <link href="<?php echo base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/plugins/datetimepicker/jquery.datetimepicker.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
  <?php echo app_stylesheet('assets/css','forms.css'); ?>
  <?php if(get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != '' && $form->recaptcha == 1){ ?>
  <script src='https://www.google.com/recaptcha/api.js'></script>
  <?php } ?>
  <?php if(file_exists(FCPATH.'assets/css/custom.css')){ ?>
  <link href="<?php echo base_url('assets/css/custom.css'); ?>" rel="stylesheet">
  <?php } ?>
  <?php render_custom_styles(array('general','buttons')); ?>
  <?php do_action('app_web_to_lead_form_head'); ?>
</head>
<body class="web-to-lead">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div id="response"></div>
        <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>$form->form_key)); ?>
        <?php echo form_hidden('key',$form->form_key); ?>
        <?php foreach($form_fields as $field){
          $type = $field->type;
          if($type == 'header' || $type == 'paragraph'){
            echo '<'.$field->subtype.' class="'.$field->className.'">'.nl2br($field->label).'</'.$field->subtype.'>';
          } else {
           echo '<div class="form-group">';
           echo '<label class="control-label" for="'.$field->name.'">'.(isset($field->required) ? ' <span class="text-danger">* </span> ': '').$field->label.''.(isset($field->description) ? ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="'.$field->description.'"></i>' : '').'</label>';
           if($type == 'file' || $type == 'text' || $type == 'email'){
            echo '<input'.(isset($field->required) ? ' required="true"': '').' placeholder="'.(isset($field->placeholder) ? $field->placeholder : '').'" type="'.$type.'" name="'.$field->name.'" id="'.$field->name.'" class="'.$field->className.'" value="'.(isset($field->value) ? $field->value : '').'"'.($field->type == 'file' ? ' accept="'.get_form_accepted_mimes().'"' : '').'>';
          } else if($type == 'textarea'){
            echo '<textarea'.(isset($field->required) ? ' required="true"': '').' id="'.$field->name.'" name="'.$field->name.'" rows="'.(isset($field->rows) ? $field->rows : '4').'" class="'.$field->className.'" placeholder="'.(isset($field->placeholder) ? $field->placeholder : '').'">'.(isset($field->value) ? $field->value : '').'</textarea>';
          } else if($type == 'date'){
            echo '<input'.(isset($field->required) ? ' required="true"': '').' type="text" class="'.$field->className.' datepicker" name="'.$field->name.'" id="'.$field->name.'" value="'.(isset($field->value) ? _d($field->value) : '').'">';
          } else if($type == 'color'){
           echo '<div class="input-group colorpicker-input">
           <input'.(isset($field->required) ? ' required="true"': '').' type="text" value="' . (isset($field->value) ? $field->value : '') . '" name="' . $field->name . '" id="' . $field->name . '" class="'.$field->className.'" />
           <span class="input-group-addon"><i></i></span>
         </div>';
       } else if($type == 'select'){
        echo '<select'.(isset($field->required) ? ' required="true"': '').' class="'.$field->className.'" name="'.$field->name.'" id="'.$field->name.'">';
        echo '<option value=""></option>';
        $values = array();
        if(isset($field->values) && count($field->values) > 0){
          foreach($field->values as $option){
            echo '<option value="'.$option->value.'" '.(isset($option->selected) ? ' selected' : '').'>'.$option->label.'</option>';
          }
        }
        echo '</select>';
      } else if($type == 'checkbox-group'){
        $values = array();
        if(isset($field->values) && count($field->values) > 0){
         $i = 0;
         echo '<div class="chk">';
         foreach($field->values as $checkbox){
          echo '<div class="checkbox">';
          echo '<input'.(isset($field->required) ? ' required="true"': '').' type="checkbox" id="chk_'.$field->name.'_'.$i.'" value="'.$checkbox->value.'" name="'.$field->name.'[]"'.(isset($checkbox->selected) ? ' checked' : '').'>';
          echo '<label for="chk_'.$field->name.'_'.$i.'">';
          echo $checkbox->label;
          echo '</label>';
          echo '</div>';
          $i++;
        }
        echo '</div>';
      }
    }
    echo '</div>';
  }
} ?>

<?php if(get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != '' && $form->recaptcha == 1){ ?>
<div class="form-group"><div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
<div id="recaptcha_response_field" class="text-danger"></div></div>
<?php } ?>
<div class="clearfix"></div>
<div class="text-left">
  <button class="btn btn-success" type="submit"><?php echo $form->submit_btn_name; ?></button>
</div>
<?php echo form_close(); ?>
</div>
</div>
</div>
</body>
<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jquery-validation/jquery.validate.min.js'); ?>"></script>
<?php
if($locale_key != 'en'){
if(file_exists(FCPATH.'assets/plugins/jquery-validation/localization/messages_'.$locale_key.'.min.js')){ ?>
<script src="<?php echo base_url('assets/plugins/jquery-validation/localization/messages_'.$locale_key.'.min.js'); ?>"></script>
<?php } else if(file_exists(FCPATH.'assets/plugins/jquery-validation/localization/messages_'.$locale_key.'_'.strtoupper($locale_key).'.min.js')){ ?>
<script src="<?php echo base_url('assets/plugins/jquery-validation/localization/messages_'.$locale_key.'_'.strtoupper($locale_key).'.min.js'); ?>"></script>
<?php } } ?>
<script src="<?php echo base_url('assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js'); ?>"></script>
<script>
 var form_id = '#<?php echo $form->form_key; ?>';
 $(function() {

     $('body').tooltip({
         selector: '[data-toggle="tooltip"]'
     });

     $('body').find('.colorpicker-input').colorpicker({
         format: "hex"
     });

     $('.datepicker').datetimepicker({
         format: '<?php echo $dateformat; ?>',
         timepicker: false,
         lazyInit: true,
         dayOfWeekStart: '<?php echo get_option('calendar_first_day '); ?>',
     });

     $.validator.setDefaults({
         highlight: function(element) {
             $(element).closest('.form-group').addClass('has-error');
         },
         unhighlight: function(element) {
             $(element).closest('.form-group').removeClass('has-error');
         },
         errorElement: 'p',
         errorClass: 'text-danger',
         errorPlacement: function(error, element) {
             if (element.parent('.input-group').length || element.parents('.chk').length) {
                 if (!element.parents('.chk').length) {
                     error.insertAfter(element.parent());
                 } else {
                     error.insertAfter(element.parents('.chk'));
                 }
             } else {
                 error.insertAfter(element);
             }
         },
     });

     var validateOptions = {};


     validateOptions.submitHandler = function(form) {
         var formURL = $(form).attr("action");
         var formData = $(form)[0];
         var formData = new FormData(formData);
         $.ajax({
             type: 'POST',
             data: formData,
             mimeType: "multipart/form-data",
             contentType: false,
             cache: false,
             processData: false,
             url: formURL,
             success: function(response) {
                 response = JSON.parse(response);
                 // In case action hook is used to redirect
                 if (response.redirect_url) {
                     window.top.location.href = response.redirect_url;
                 }
                 if (response.success == false) {
                     $('#recaptcha_response_field').html(response.message); // error message
                 } else if (response.success == true) {
                     $(form_id).remove();
                     $('#response').html(response.message);
                     $('html,body').animate({
                             scrollTop: $("#online_payment_form").offset().top
                         },
                         'slow');
                 } else {
                     $('#response').html('Something went wrong...');
                 }
                 if (typeof(grecaptcha) != 'undefined') {
                     grecaptcha.reset();
                 }
             },
             fail: function(data) {
                 if (typeof(grecaptcha) != 'undefined') {
                     grecaptcha.reset();
                 }
                 $('#response').html(data.responseText);
             }
         });
         return false;
     }
     $(form_id).validate(validateOptions);
 });
</script>
</html>
