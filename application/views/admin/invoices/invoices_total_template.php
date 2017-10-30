<div class="row">
<?php if(isset($invoices_total_currencies)){
   $col = 'col-lg-3 col-md-6 col-xs-12 ';
   ?>
<div class="<?php echo $col; ?> stats-total-currency total-column">
   <div class="panel_s">
      <div class="panel-body">
         <select class="selectpicker" name="total_currency" onchange="init_invoices_total();" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
            <?php foreach($invoices_total_currencies as $currency){
               $selected = '';
               if(!$this->input->post('currency')){
                 if($currency['isdefault'] == 1 || isset($_currency) && $_currency == $currency['id']){
                   $selected = 'selected';
                 }
               } else {
                 if($this->input->post('currency') == $currency['id']){
                  $selected = 'selected';
                }
               }
               ?>
            <option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?> data-subtext="<?php echo $currency['name']; ?>"><?php echo $currency['symbol']; ?></option>
            <?php } ?>
         </select>
      </div>
   </div>
</div>
<?php
   } else {
     $col = 'col-lg-4 col-xs-12 col-md-12 ';
   }
   ?>
 <div class="<?php echo $col; ?>total-column">
   <div class="panel_s">
      <div class="panel-body">
         <h3 class="text-muted _total">
            <?php echo format_money($total_result['due'],$total_result['symbol']); ?>
         </h3>
         <span class="text-warning"><?php echo _l('outstanding_invoices'); ?></span>
      </div>
   </div>
</div>
<div class="<?php echo $col; ?>total-column">
   <div class="panel_s">
      <div class="panel-body">
         <h3 class="text-muted _total">
            <?php echo format_money($total_result['overdue'],$total_result['symbol']); ?>
         </h3>
         <span class="text-danger"><?php echo _l('past_due_invoices'); ?></span>
      </div>
   </div>
</div>
 <div class="<?php echo $col; ?>total-column">
   <div class="panel_s">
      <div class="panel-body">
         <h3 class="text-muted _total">
            <?php echo format_money($total_result['paid'],$total_result['symbol']); ?>
         </h3>
         <span class="text-success"><?php echo _l('paid_invoices'); ?></span>
      </div>
   </div>
</div>
</div>
<div class="clearfix"></div>
<script>
   if(typeof(init_selectpicker) == 'function'){
      init_selectpicker();
   }
</script>
