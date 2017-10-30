<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                    <h4 class="bold no-margin font-medium">
                    <?php echo $title; ?>
                    </h4>
                    <hr />
                    <p class="mbot25 company_field_info text-info<?php if(isset($custom_field) && $custom_field->fieldto != 'company' || !isset($custom_field)){echo ' hide';} ?>"><?php echo _l('custom_field_company_info'); ?></p>
                        <?php if(isset($custom_field)){ ?>
                        <a href="<?php echo admin_url('custom_fields/field'); ?>" class="btn btn-success pull-left mbot20 display-block"><?php echo _l('new_custom_field'); ?></a>
                        <div class="clearfix"></div>
                        <?php } ?>
                        <?php echo form_open($this->uri->uri_string()); ?>
                        <?php
                        $disable = '';
                        if(isset($custom_field)){
                          if(total_rows('tblcustomfieldsvalues',array('fieldid'=>$custom_field->id,'fieldto'=>$custom_field->fieldto)) > 0){
                            $disable = 'disabled';
                        }
                    }
                    ?>
                    <label for="fieldto"><?php echo _l('custom_field_add_edit_belongs_top'); ?></label>
                    <select name="fieldto" id="fieldto" class="selectpicker <?php if(!isset($custom_field)){echo 'auto-toggle'; } ?>" data-width="100%" <?php echo $disable; ?> data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <option value="company" <?php if(isset($custom_field) && $custom_field->fieldto == 'company'){echo 'selected';} ?>><?php echo _l('custom_field_company'); ?></option>
                        <option value="leads" <?php if(isset($custom_field) && $custom_field->fieldto == 'leads'){echo 'selected';} ?>><?php echo _l('custom_field_leads'); ?></option>
                        <option value="customers" <?php if(isset($custom_field) && $custom_field->fieldto == 'customers'){echo 'selected';} ?>><?php echo _l('custom_field_customers'); ?></option>
                        <option value="contacts" <?php if(isset($custom_field) && $custom_field->fieldto == 'contacts'){echo 'selected';} ?>><?php echo _l('custom_field_contacts'); ?></option>
                        <option value="staff" <?php if(isset($custom_field) && $custom_field->fieldto == 'staff'){echo 'selected';} ?>><?php echo _l('custom_field_staff'); ?></option>
                        <option value="contracts" <?php if(isset($custom_field) && $custom_field->fieldto == 'contracts'){echo 'selected';} ?>><?php echo _l('custom_field_contracts'); ?></option>
                        <option value="tasks" <?php if(isset($custom_field) && $custom_field->fieldto == 'tasks'){echo 'selected';} ?>><?php echo _l('custom_field_tasks'); ?></option>
                        <option value="expenses" <?php if(isset($custom_field) && $custom_field->fieldto == 'expenses'){echo 'selected';} ?>><?php echo _l('custom_field_expenses'); ?></option>
                        <option value="invoice" <?php if(isset($custom_field) && $custom_field->fieldto == 'invoice'){echo 'selected';} ?>><?php echo _l('custom_field_invoice'); ?></option>
                        <option value="estimate" <?php if(isset($custom_field) && $custom_field->fieldto == 'estimate'){echo 'selected';} ?>><?php echo _l('custom_field_estimate'); ?></option>
                        <option value="proposal" <?php if(isset($custom_field) && $custom_field->fieldto == 'proposal'){echo 'selected';} ?>><?php echo _l('proposal'); ?></option>
                        <option value="projects" <?php if(isset($custom_field) && $custom_field->fieldto == 'projects'){echo 'selected';} ?>><?php echo _l('projects'); ?></option>
                        <option value="tickets" <?php if(isset($custom_field) && $custom_field->fieldto == 'tickets'){echo 'selected';} ?>><?php echo _l('tickets'); ?></option>
                    </select>
                    <div class="clearfix mbot15"></div>
                    <?php $value = (isset($custom_field) ? $custom_field->name : ''); ?>
                    <?php echo render_input('name','custom_field_name',$value); ?>
                    <label for="custom_type"><?php echo _l('custom_field_add_edit_type'); ?></label>
                    <select name="type" id="type" class="selectpicker"<?php if(isset($custom_field) && total_rows('tblcustomfieldsvalues',array('fieldid'=>$custom_field->id,'fieldto'=>$custom_field->fieldto)) > 0 && ($custom_field->type == 'checkbox' || $custom_field->type == 'select')){echo ' disabled';} ?> data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <option value="input" <?php if(isset($custom_field) && $custom_field->type == 'input'){echo 'selected';} ?>>Input</option>
                        <option value="colorpicker" <?php if(isset($custom_field) && $custom_field->type == 'colorpicker'){echo 'selected';} ?>>Color Picker</option>
                        <option value="textarea" <?php if(isset($custom_field) && $custom_field->type == 'textarea'){echo 'selected';} ?>>Textarea</option>
                        <option value="date_picker" <?php if(isset($custom_field) && $custom_field->type == 'date_picker'){echo 'selected';} ?>>Date Picker</option>
                        <option value="select" <?php if(isset($custom_field) && $custom_field->type == 'select'){echo 'selected';} ?>>Select</option>
                        <option value="checkbox" <?php if(isset($custom_field) && $custom_field->type == 'checkbox'){echo 'selected';} ?>>Checkbox</option>
                        <option value="link" <?php if(isset($custom_field) && $custom_field->type == 'link'){echo 'selected';} ?>>Hyperlink</option>
                    </select>
                    <div class="clearfix mbot15"></div>
                    <div id="options_wrapper" class="<?php if(!isset($custom_field) || isset($custom_field) && $custom_field->type != 'select' && $custom_field->type != 'checkbox'){echo 'hide';} ?>">
                        <?php $value = (isset($custom_field) ? $custom_field->options : ''); ?>
                        <?php echo render_textarea('options','custom_field_add_edit_options',$value,array('rows'=>3,'data-toggle'=>'tooltip','title'=>'custom_field_add_edit_options_tooltip')); ?>
                    </div>
                    <?php $value = (isset($custom_field) ? $custom_field->field_order : ''); ?>
                    <?php echo render_input('field_order','custom_field_add_edit_order',$value,'number'); ?>
                    <div class="form-group">
                        <label for="bs_column"><?php echo _l('custom_field_column'); ?></label>
                        <div class="input-group">
                            <span class="input-group-addon">col-md-</span>
                            <input type="number" class="form-control" name="bs_column" id="bs_column" value="<?php if(!isset($custom_field)){echo 12;} else{echo $custom_field->bs_column;} ?>">
                        </div>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="disabled" id="disabled" <?php if(isset($custom_field) && $custom_field->active == 0){echo 'checked';} ?>>
                        <label for="disabled"><?php echo _l('custom_field_add_edit_disabled'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="only_admin" id="only_admin" <?php if(isset($custom_field) && $custom_field->only_admin == 1){echo 'checked';} ?> <?php if(isset($custom_field) && $custom_field->fieldto == 'company'){echo 'disabled';} ?>>
                        <label for="only_admin"><?php echo _l('custom_field_only_admin'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary disalow_client_to_edit <?php if(!isset($custom_field) || (isset($custom_field) && !in_array($custom_field->fieldto,$client_portal_fields))){echo 'hide';} ?>">
                        <input type="checkbox" name="disalow_client_to_edit" id="disalow_client_to_edit" <?php if(isset($custom_field) && $custom_field->disalow_client_to_edit == 1){echo 'checked';} ?> <?php if(isset($custom_field) && $custom_field->fieldto == 'company'){echo 'disabled';} ?>>
                        <label for="disalow_client_to_edit"> <?php echo _l('custom_field_disallow_customer_to_edit'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary" id="required_wrap">
                        <input type="checkbox" name="required" id="required" <?php if(isset($custom_field) && $custom_field->required == 1){echo 'checked';} ?> <?php if(isset($custom_field) && $custom_field->fieldto == 'company'){echo 'disabled';} ?>>
                        <label for="required"><?php echo _l('custom_field_required'); ?></label>
                    </div>
                    <p class="bold text-info"><?php echo _l('custom_field_visibility'); ?></p>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="show_on_table" id="show_on_table" <?php if(isset($custom_field) && $custom_field->show_on_table == 1){echo 'checked';} ?> <?php if(isset($custom_field) && $custom_field->fieldto == 'company'){echo 'disabled';} ?>>
                        <label for="show_on_table"><?php echo _l('custom_field_show_on_table'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary show-on-pdf <?php if(!isset($custom_field) || (isset($custom_field) && !in_array($custom_field->fieldto,$pdf_fields))){echo 'hide';} ?>">
                        <input type="checkbox" name="show_on_pdf" id="show_on_pdf" <?php if(isset($custom_field) && $custom_field->show_on_pdf == 1){echo 'checked';} ?> <?php if(isset($custom_field) && $custom_field->fieldto == 'company'){echo 'disabled';} ?>>
                        <label for="show_on_pdf"><?php echo _l('custom_field_show_on_pdf'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary show-on-client-portal <?php if(!isset($custom_field) || (isset($custom_field) && !in_array($custom_field->fieldto,$client_portal_fields))){echo 'hide';} ?>">
                        <input type="checkbox" name="show_on_client_portal" id="show_on_client_portal" <?php if(isset($custom_field) && $custom_field->show_on_client_portal == 1){echo 'checked';} ?> <?php if(isset($custom_field) && $custom_field->fieldto == 'company'){echo 'disabled';} ?>>
                        <label for="show_on_client_portal"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('custom_field_show_on_client_portal_help'); ?>"></i> <?php echo _l('custom_field_show_on_client_portal'); ?></label>
                    </div>

                    <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
    var pdf_fields = <?php echo json_encode($pdf_fields); ?>;
    var client_portal_fields = <?php echo json_encode($client_portal_fields); ?>;
    $(function(){
      _validate_form($('form'), {
        fieldto: 'required',
        name: 'required',
        type: 'required',
        bs_column: 'required'
    });
      $('form').on('submit',function(){
        $('#fieldto,#type').removeAttr('disabled');
        return true;
    });
      $('select[name="fieldto"]').on('change', function() {
        var field = $(this).val();
        if ($.inArray(field, pdf_fields) !== -1) {
          $('.show-on-pdf').removeClass('hide');
      } else {
          $('.show-on-pdf').addClass('hide');
      }

      if ($.inArray(field, client_portal_fields) !== -1) {
          $('.show-on-client-portal').removeClass('hide');
          $('.disalow_client_to_edit').removeClass('hide');
      } else {
          $('.show-on-client-portal').addClass('hide');
          $('.disalow_client_to_edit').addClass('hide');
      }

      if(field == 'company'){
        $('input[name="only_admin"]').attr('disabled',true);
        $('input[name="disalow_client_to_edit"]').attr('disabled',true);
        $('input[name="required"]').attr('disabled',true);
        $('input[name="show_on_table"]').attr('disabled',true);
        $('input[name="show_on_table"]').attr('checked',true);
        $('input[name="show_on_pdf"]').attr('disabled',true);
        $('input[name="show_on_pdf"]').attr('checked',true);
        $('input[name="show_on_client_portal"]').attr('disabled',true);
        $('input[name="show_on_client_portal"]').attr('checked',true);
        $('.company_field_info').removeClass('hide');
    } else {
        $('.company_field_info').addClass('hide');
        $('input[name="only_admin"]').attr('disabled',false);
        $('input[name="disalow_client_to_edit"]').attr('disabled',false);
        $('input[name="required"]').attr('disabled',false);
        $('input[name="show_on_table"]').attr('disabled',false);
        $('input[name="show_on_pdf"]').attr('disabled',false);
        $('input[name="show_on_client_portal"]').attr('disabled',false);
    }

});
        // Editor cant be required
        $('select[name="type"]').on('change', function() {
          var type = $(this).val();
          if (type == 'select' || type == 'checkbox') {
            $('#options_wrapper').removeClass('hide');
        } else {
            $('#options_wrapper').addClass('hide');
        }
    });
    });
</script>
</body>
</html>
