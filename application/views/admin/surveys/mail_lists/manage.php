<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
			 <?php if(has_permission('surveys','','create')){ ?>
				<div class="panel_s _buttons">
					<div class="panel-body">
						<a href="<?php echo admin_url('surveys/mail_list'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_mail_list'); ?></a>
					</div>
				</div>
				<?php } ?>
				<div class="panel_s">
					<div class="panel-body">
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('id'),
							_l('mail_lists_dt_list_name'),
							_l('mail_lists_dt_datecreated'),
							_l('mail_lists_dt_creator'),
							_l('options'),
							),'mail-lists'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
	    initDataTable('.table-mail-lists', window.location.href, [4], [4]);
	</script>
</body>
</html>
