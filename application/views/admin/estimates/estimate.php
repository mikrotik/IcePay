<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'estimate-form','class'=>'_transaction_form'));
			if(isset($estimate)){
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<?php $this->load->view('admin/estimates/estimate_template'); ?>
			</div>
			<?php echo form_close(); ?>
			<?php $this->load->view('admin/invoice_items/item'); ?>
		</div>
	</div>
</div>
</div>
<?php init_tail(); ?>
<script>
	$(function(){
	validate_estimate_form();
	// Init accountacy currency symbol
	init_currency_symbol();
});
</script>
</body>
</html>
