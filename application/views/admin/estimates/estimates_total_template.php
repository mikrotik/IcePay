<div class="row">
<?php if(isset($currencies)){
    $col = 'col-md-2 col-xs-12 ';
    ?>
<div class="<?php echo $col; ?> stats-total-currency total-column">
    <div class="panel_s">
        <div class="panel-body">
            <select class="selectpicker" name="total_currency" onchange="init_estimates_total();" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
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

<?php
foreach($totals as $key => $data){
    $class = estimate_status_color_class($data['status']);
    $name = estimate_status_by_id($data['status']);
    ?>
<div class="<?php echo $col; ?>total-column">
    <div class="panel_s">
        <div class="panel-body">
            <h3 class="text-muted _total">
                <?php echo format_money($data['total'],$data['symbol']); ?>
            </h3>
            <span class="text-<?php echo $class; ?>"><?php echo $name; ?></span>
        </div>
    </div>
</div>
<?php } ?>
</div>
<div class="clearfix"></div>
<script>
    init_selectpicker();
</script>
