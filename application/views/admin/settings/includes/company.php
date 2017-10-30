    <div role="tabpanel" class="tab-pane" id="company_info">
        <p class="text-muted">
            <?php echo _l('settings_sales_company_info_note'); ?>
        </p>
        <?php echo render_input('settings[invoice_company_name]','settings_sales_company_name',get_option('invoice_company_name')); ?>
        <?php echo render_input('settings[invoice_company_address]','settings_sales_address',get_option('invoice_company_address')); ?>
        <?php echo render_input('settings[invoice_company_city]','settings_sales_city',get_option('invoice_company_city')); ?>
        <?php echo render_input('settings[invoice_company_country_code]','settings_sales_country_code',get_option('invoice_company_country_code')); ?>
        <?php echo render_input('settings[invoice_company_postal_code]','settings_sales_postal_code',get_option('invoice_company_postal_code')); ?>
        <?php echo render_input('settings[invoice_company_phonenumber]','settings_sales_phonenumber',get_option('invoice_company_phonenumber')); ?>
        <?php echo render_input('settings[company_vat]','company_vat_number',get_option('company_vat')); ?>
        <hr />
        <?php echo render_custom_fields('company',0); ?>
    </div>
