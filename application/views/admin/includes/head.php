<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php if(get_option('favicon') != ''){ ?>
    <link href="<?php echo base_url('uploads/company/'.get_option('favicon')); ?>" rel="shortcut icon">
    <?php } ?>
    <title><?php if (isset($title)){ echo $title; } else { echo get_option('companyname'); } ?></title>
    <?php echo app_stylesheet('assets/css','reset.css'); ?>
    <link href='<?php echo base_url('assets/plugins/roboto/roboto.css'); ?>' rel='stylesheet'>
    <link href="<?php echo base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <?php if(is_rtl()){ ?>
    <link href="<?php echo base_url('assets/plugins/bootstrap-arabic/css/bootstrap-arabic.min.css'); ?>" rel="stylesheet">
    <?php } ?>
    <link href="<?php echo base_url('assets/plugins/jquery-ui/jquery-ui.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/datatables/datatables.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/datetimepicker/jquery.datetimepicker.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/animate.css/animate.min.css'); ?>" rel="stylesheet">
    <?php if(isset($calendar_assets)){ ?>
    <link href='<?php echo base_url('assets/plugins/fullcalendar/fullcalendar.min.css'); ?>' rel='stylesheet' />
    <?php } ?>
    <?php if(isset($lightbox_assets)){ ?>
    <link id="lightbox-css" href='<?php echo base_url('assets/plugins/lightbox/css/lightbox.min.css'); ?>' rel='stylesheet' />
    <?php } ?>
    <?php if(isset($form_builder_assets)){ ?>
    <link href='<?php echo base_url('assets/plugins/form-builder/form-builder.min.css'); ?>' rel='stylesheet' />
    <?php } ?>
    <?php if(isset($projects_assets)){ ?>
    <link href='<?php echo base_url('assets/plugins/jquery-comments/css/jquery-comments.css'); ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/plugins/gantt/css/style.css'); ?>' rel='stylesheet' />
    <?php } ?>
    <?php if(isset($media_assets)){ ?>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url('assets/plugins/elFinder/css/elfinder.min.css'); ?>">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url('assets/plugins/elFinder/themes/windows-10/css/theme.css'); ?>">
    <?php } ?>
    <?php echo app_stylesheet('assets/css','style.css'); ?>
    <?php if(file_exists(FCPATH.'assets/css/custom.css')){ ?>
    <link href="<?php echo base_url('assets/css/custom.css'); ?>" rel="stylesheet">
    <?php } ?>
    <?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>
    <?php render_custom_styles(array('general','tabs','buttons','admin','modals','tags')); ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <?php render_admin_js_variables(); ?>
        <script>
            var dt_lang = <?php echo json_encode(get_datatables_language_array()); ?>;
            var proposal_templates = <?php echo json_encode(get_proposal_templates()); ?>;
            var availableTags = <?php echo json_encode(get_tags_clean()); ?>;
            var availableTagsIds = <?php echo json_encode(get_tags_ids()); ?>;
            var bs_fields = ['billing_street','billing_city','billing_state','billing_zip','billing_country','shipping_street','shipping_city','shipping_state','shipping_zip','shipping_country'];
            var locale = '<?php echo $locale; ?>';
            var isRTL = '<?php echo $isRTL; ?>';
            var tinymce_lang = '<?php echo $tinymce_lang; ?>';
            var months_json = '<?php echo json_encode(array(_l('January'),_l('February'),_l('March'),_l('April'),_l('May'),_l('June'),_l('July'),_l('August'),_l('September'),_l('October'),_l('November'),_l('December'))); ?>';
            var _table_api,taskid,task_tracking_stats_data,taskAttachmentDropzone,leadAttachmentsDropzone,newsFeedDropzone,autocheck_notifications_timer_id = 0,task_track_chart,cfh_popover_templates = {};
        </script>
        <?php do_action('app_admin_head'); ?>
    </head>
    <body <?php if(is_rtl()){ echo 'dir="rtl"';} ?> class="<?php echo 'page'.($this->uri->segment(2) ? '-'.$this->uri->segment(2) : '') . '-'.$this->uri->segment(1); ?> admin <?php if(isset($bodyclass)){echo $bodyclass . ' '; } ?><?php if($this->session->has_userdata('is_mobile') && $this->session->userdata('is_mobile') == true){echo 'hide-sidebar ';} ?><?php if(is_rtl()){echo 'rtl';} ?>">
        <?php do_action('after_body_start'); ?>
