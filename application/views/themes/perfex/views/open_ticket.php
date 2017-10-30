<div class="row">
    <?php echo form_open_multipart('clients/open_ticket',array('class'=>'open-new-ticket-form')); ?>
    <div class="col-md-<?php echo (count($latest_tickets) > 0 ? 7 : 12); ?>">
        <div class="panel_s">
            <div class="panel-heading">
                <?php echo _l('clients_ticket_open_subject'); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="subject"><?php echo _l('customer_ticket_subject'); ?></label>
                            <input type="text" class="form-control" name="subject" id="subject" value="<?php echo set_value('subject'); ?>">
                            <?php echo form_error('subject'); ?>
                        </div>
                        <?php if(total_rows('tblprojects',array('clientid'=>get_client_user_id()))> 0 && has_contact_permission('projects')){ ?>
                        <div class="form-group">
                            <label for="project_id"><?php echo _l('project'); ?></label>
                            <select name="project_id" id="project_id" class="form-control selectpicker">
                                <option value=""></option>
                                <?php foreach($projects as $project){ ?>
                                <option value="<?php echo $project['id']; ?>" <?php echo set_select('project_id',$project['id']); ?>><?php echo $project['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="department"><?php echo _l('clients_ticket_open_departments'); ?></label>
                            <select name="department" id="department" class="form-control selectpicker">
                                <option value=""></option>
                                <?php foreach($departments as $department){ ?>
                                <option value="<?php echo $department['departmentid']; ?>" <?php echo set_select('department',$department['departmentid']); ?>><?php echo $department['name']; ?></option>
                                <?php } ?>
                            </select>
                            <?php echo form_error('department'); ?>
                        </div>
                        <?php
                        if(get_option('services') == 1){ ?>
                        <div class="form-group">
                            <label for="service"><?php echo _l('clients_ticket_open_service'); ?></label>
                            <select name="service" id="service" class="form-control selectpicker">
                                <option value=""></option>
                                <?php foreach($services as $service){ ?>
                                <option value="<?php echo $service['serviceid']; ?>" <?php echo set_select('service',$service['serviceid']); ?>><?php echo $service['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="priority"><?php echo _l('clients_ticket_open_priority'); ?></label>
                            <select name="priority" id="priority" class="form-control selectpicker">
                                <option value=""></option>
                                <?php foreach($priorities as $priority){ ?>
                                <option value="<?php echo $priority['priorityid']; ?>" <?php echo set_select('priority',$priority['priorityid']); ?>><?php echo ticket_priority_translate($priority['priorityid']); ?></option>
                                <?php } ?>
                            </select>
                            <?php echo form_error('priority'); ?>
                        </div>
                        <?php echo render_custom_fields('tickets','',array('show_on_client_portal'=>1)); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(count($latest_tickets) > 0){ ?>
    <div class="col-md-5">
        <div class="panel_s">
            <div class="panel-heading">
                <?php echo _l('clients_latest_tickets'); ?>
            </div>
            <div class="panel-body">
                <?php foreach($latest_tickets as $ticket) { ?>
                <a href="<?php echo site_url('clients/ticket/'.$ticket['ticketid']); ?>" target="_blank"><?php echo $ticket['subject']; ?></a><br />
                <span class="small"><?php echo _l('clients_ticket_posted',_dt($ticket['date'])); ?></span>
                <span class="label pull-right" style="background:<?php echo $ticket['statuscolor']; ?>"><?php echo ticket_status_translate($ticket['ticketstatusid']); ?></span>
                <hr />
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-heading">
                <?php echo _l('clients_ticket_open_body'); ?>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <textarea name="message" id="message" class="form-control" rows="15"><?php echo set_value('message'); ?></textarea>
                </div>
            </div>
            <div class="panel-footer attachments_area">
                <div class="row attachments">
                    <div class="attachment">
                        <div class="col-md-6 col-md-offset-3">
                           <label for="attachment" class="control-label"><?php echo _l('clients_ticket_attachments'); ?></label>
                           <div class="input-group">
                            <input type="file" class="form-control" name="attachments[]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                            <span class="input-group-btn">
                                <button class="btn btn-success add_more_attachments" type="button"><i class="fa fa-plus"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12 text-center mtop20">
    <button type="submit" class="btn btn-info" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
</div>
</div>
<?php echo form_close(); ?>
</div>
