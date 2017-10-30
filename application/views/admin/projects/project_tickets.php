<?php
    $this->load->view('admin/tickets/summary',array('project_id'=>$project->id));
    echo form_hidden('project_id',$project->id);
    echo '<div class="clearfix"></div>';
    echo AdminTicketsTableStructure('tickets-table');
?>
