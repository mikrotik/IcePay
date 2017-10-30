<h4 class="no-mtop bold"><?php echo _l('contracts_tickets_tab'); ?></h4>
<hr />
<div class="clearfix"></div>
<?php
if(isset($client)){
 echo AdminTicketsTableStructure('table-tickets-single');
} ?>
