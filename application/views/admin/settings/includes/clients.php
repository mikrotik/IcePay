<?php echo form_hidden('settings[customer_settings]','true'); ?>
<div class="form-group">
	<label for="clients_default_theme" class="control-label"><?php echo _l('settings_clients_default_theme'); ?></label>
	<select name="settings[clients_default_theme]" id="clients_default_theme" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
		<?php foreach(get_all_client_themes() as $theme){ ?>
		<option value="<?php echo $theme; ?>" <?php if(active_clients_theme() == $theme){echo 'selected';} ?>><?php echo ucfirst($theme); ?></option>
		<?php } ?>
	</select>
</div>
<hr />
<?php echo render_select( 'settings[customer_default_country]',get_all_countries(),array( 'country_id',array( 'short_name')), 'customer_default_country',get_option('customer_default_country')); ?>
<hr />
<?php render_yes_no_option('company_is_required','company_is_required'); ?>
<hr />
<?php render_yes_no_option('company_requires_vat_number_field','company_requires_vat_number_field'); ?>
<hr />
<?php render_yes_no_option('allow_registration','settings_clients_allow_registration'); ?>
<hr />
<?php render_yes_no_option('allow_primary_contact_to_view_edit_billing_and_shipping','allow_primary_contact_to_view_edit_billing_and_shipping'); ?>
<hr />
<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('only_own_files_contacts_help'); ?>"></i>
<?php render_yes_no_option('only_own_files_contacts','only_own_files_contacts'); ?>
<hr />
<?php render_yes_no_option('allow_contact_to_delete_files','allow_contact_to_delete_files'); ?>
<hr />
<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('settings_general_use_knowledgebase_tooltip'); ?>"></i>
<?php render_yes_no_option('use_knowledge_base','settings_general_use_knowledgebase'); ?>
<hr />
<?php render_yes_no_option('knowledge_base_without_registration','settings_clients_allow_kb_view_without_registration'); ?>
<hr />
<?php $default_contact_permissions = unserialize(get_option('default_contact_permissions')); ?>
<div class="form-group">
	<label for="" class="control-label"><?php echo _l('default_contact_permissions'); ?></label>
	<?php foreach($contacts_permissions as $p){ ?>
	<div class="checkbox checkbox-primary">
		<input type="checkbox" name="settings[default_contact_permissions][]" <?php if(is_array($default_contact_permissions) && in_array($p['id'],$default_contact_permissions)){echo 'checked';} ?> id="dcp_<?php echo $p['id']; ?>" value="<?php echo $p['id']; ?>">
		<label for="dcp_<?php echo $p['id']; ?>"><?php echo $p['name']; ?></label>
	</div>
	<?php } ?>
</div>
