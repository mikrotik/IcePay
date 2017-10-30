<div class="row">
    <?php if(isset($currencies)){
        $col = 'col-md-2 col-xs-12 ';
        ?>
        <div class="<?php echo $col; ?> stats-total-currency">
            <div class="panel_s">
                <div class="panel-body">
                    <select class="selectpicker" name="expenses_total_currency" onchange="init_expenses_total();" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php foreach($currencies as $currency){
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
         $col = 'col-md-5ths col-xs-12 ';
     }
     ?>
     <div class="<?php echo $col ;?>total-column">
        <div class="panel_s">
            <div class="panel-body">
                <h3 class="text-muted _total">
                 <?php echo $totals['all']['total']; ?>
             </h3>
             <span class="text-warning"><?php echo _l('expenses_total'); ?></span>
         </div>
     </div>
 </div>
 <div class="<?php echo $col ;?>total-column">
    <div class="panel_s">
        <div class="panel-body">
            <h3 class="text-muted _total">
             <?php echo $totals['billable']['total']; ?>
         </h3>
         <span class="text-success"><?php echo _l('expenses_list_billable'); ?></span>
     </div>
 </div>
</div>
<div class="<?php echo $col ;?>total-column">
    <div class="panel_s">
        <div class="panel-body">
            <h3 class="text-muted _total">
             <?php echo $totals['non_billable']['total']; ?>
         </h3>
         <span class="text-warning"><?php echo _l('expenses_list_non_billable'); ?></span>
     </div>
 </div>
</div>
<div class="<?php echo $col ;?>total-column">
    <div class="panel_s">
        <div class="panel-body">
            <h3 class="text-muted _total">
             <?php echo $totals['unbilled']['total']; ?>
         </h3>
         <span class="text-danger"><?php echo _l('expenses_list_unbilled'); ?></span>
     </div>
 </div>
</div>
<div class="<?php echo $col ;?>total-column">
    <div class="panel_s">
        <div class="panel-body">
            <h3 class="text-muted _total">
             <?php echo $totals['billed']['total']; ?>
         </h3>
         <span class="text-success"><?php echo _l('expense_billed'); ?></span>
     </div>
 </div>
</div>
</div>
<div class="clearfix"></div>
<script>
    init_selectpicker();
</script>
