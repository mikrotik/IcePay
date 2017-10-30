<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <?php do_action( 'before_start_render_dashboard_content'); ?>
            <div class="col-md-12">
                <?php $this->load->view('admin/includes/widgets/top_stats'); ?>
            </div>
            <div class="col-md-8">
                <?php $this->load->view('admin/includes/widgets/finance_overview'); ?>
                <?php $this->load->view('admin/includes/widgets/user_data'); ?>
                <?php $this->load->view('admin/includes/widgets/calendar'); ?>
                 <div class="row">
                    <?php if(is_staff_member()){ ?>
                    <div class="col-md-6 col-sm-12">
                        <?php $this->load->view('admin/includes/widgets/leads_chart'); ?>
                    </div>
                    <?php } ?>
                    <div class="col-md-<?php if(!is_staff_member()){echo 12;}else{echo 6;};?> col-sm-12">
                        <?php $this->load->view('admin/includes/widgets/projects_chart'); ?>
                    </div>
                </div>
                <?php $this->load->view('admin/includes/widgets/weekly_payments_chart'); ?>
            </div>
            <div class="col-md-4">
                <?php $this->load->view('admin/includes/widgets/todos'); ?>
                <?php $this->load->view('admin/includes/widgets/upcoming_events'); ?>
                <?php $this->load->view('admin/includes/widgets/tickets_chart'); ?>
                <?php $this->load->view('admin/includes/widgets/projects_activity'); ?>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    google_api = '<?php echo $google_api_key; ?>';
    calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
</script>
<?php init_tail(); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view('admin/includes/dashboard_js'); ?>
</body>
</html>
