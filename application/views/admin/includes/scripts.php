<?php include_once(APPPATH.'views/admin/includes/helpers_bottom.php'); ?>
<?php do_action('before_js_scripts_render'); ?>
<script src="<?php echo base_url('assets/plugins/app-build/jquery-with-ui.min.js'); ?>"></script>
<?php if(ENVIRONMENT !== 'production' || isset($jquery_migrate_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/jquery/jquery-migrate.js'); ?>"></script>
<?php } ?>
<script>
    $(window).on('load',function(){
        init_btn_with_tooltips();
    });
</script>
<script src="<?php echo base_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables/datatables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/app-build/metis-tagit-areyousure-bootstrapcolorpicker-dropzone-datetimepicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/app-build/moment.min.js'); ?>"></script>
<?php app_select_plugin_js($locale); ?>
<script src="<?php echo base_url('assets/plugins/tinymce/tinymce.min.js'); ?>"></script>
<?php app_jquery_validation_plugin_js($locale); ?>
<?php if(isset($chart_js_assets)){ ?>
<script id="chart-js-script" src="<?php echo base_url('assets/plugins/Chart.js/Chart.min.js'); ?>" type="text/javascript"></script>
<?php } ?>
<?php if(get_option('dropbox_app_key') != ''){ ?>
<script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="<?php echo get_option('dropbox_app_key'); ?>"></script>
<?php } ?>
<?php if(isset($lightbox_assets)){ ?>
<script id="lightbox-js" src="<?php echo base_url('assets/plugins/lightbox/js/lightbox.min.js'); ?>"></script>
<?php } ?>
<?php if(isset($form_builder_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/form-builder/form-builder.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/form-builder/form-render.min.js'); ?>"></script>
<?php } ?>
<?php if(isset($media_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/elFinder/js/elfinder.min.js'); ?>"></script>
<?php if(file_exists(FCPATH.'assets/plugins/elFinder/js/i18n/elfinder.'.$locale.'.js')){ ?>
<script src="<?php echo base_url('assets/plugins/elFinder/js/i18n/elfinder.'.$locale.'.js'); ?>"></script>
<?php } ?>
<?php } ?>
<?php if(isset($projects_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/jquery-comments/js/jquery-comments.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/gantt/js/jquery.fn.gantt.min.js'); ?>"></script>
<?php } ?>
<?php if(isset($circle_progress_asset)){ ?>
<script src="<?php echo base_url('assets/plugins/jquery-circle-progress/circle-progress.min.js'); ?>"></script>
<?php } ?>
<?php if(isset($accounting_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/accounting.js/accounting.min.js'); ?>"></script>
<?php } ?>
<?php if(isset($calendar_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/fullcalendar.min.js'); ?>"></script>
<?php if(get_option('google_api_key') != ''){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/gcal.min.js'); ?>"></script>
<?php } ?>
<?php if(file_exists(FCPATH.'assets/plugins/fullcalendar/locale/'.$locale.'.js')){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/locale/'.$locale.'.js'); ?>"></script>
<?php } ?>
<?php echo app_script('assets/js','calendar.js'); ?>
<?php } ?>
<?php echo app_script('assets/js','main.js'); ?>
<?php echo get_custom_fields_hyperlink_js_function(); ?>
<?php do_action('after_js_scripts_render'); ?>
<?php
$alertclass = "";
if($this->session->flashdata('message-success')){
    $alertclass = "success";
} else if ($this->session->flashdata('message-warning')){
    $alertclass = "warning";
} else if ($this->session->flashdata('message-info')){
    $alertclass = "info";
} else if ($this->session->flashdata('message-danger')){
    $alertclass = "danger";
}
if($alertclass != ''){
    $alert_message = '';
    $alert = $this->session->flashdata('message-'.$alertclass);
    if(is_array($alert)){
        foreach($alert as $alert_data){
            $alert_message.= '<span>'.$alert_data . '</span><br />';
        }
    } else {
        $alert_message .= $alert;
    }
    ?>
    <script>
        $(function(){
            alert_float('<?php echo $alertclass; ?>','<?php echo $alert_message; ?>');
        });
    </script>
    <?php } ?>


