<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stripe extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function complete_purchase()
    {
        if ($this->input->post()) {
            $data      = $this->input->post();
            $total     = $this->input->post('total');
            $this->load->model('invoices_model');
            $invoice             = $this->invoices_model->get($this->input->post('invoiceid'));
            check_invoice_restrictions($invoice->id, $invoice->hash);
            load_client_language($invoice->clientid);
            $data['amount']      = $total;
            $data['description'] = 'Payment for invoice ' . format_invoice_number($invoice->id);
            $data['currency']    = $invoice->currency_name;
            $data['clientid']    = $invoice->clientid;
            $oResponse      = $this->stripe_gateway->finish_payment($data);
            if ($oResponse->isSuccessful()) {
             $transactionid  = $oResponse->getTransactionReference();
             $oResponse = $oResponse->getData();
             if ($oResponse['status'] == 'succeeded') {
                    // Add payment to database
                $payment_data['amount']        = ($oResponse['amount'] / 100);
                $payment_data['invoiceid']     = $invoice->id;
                $payment_data['paymentmode']   = $this->stripe_gateway->get_id();
                $payment_data['transactionid'] = $transactionid;
                $this->load->model('payments_model');
                $success = $this->payments_model->add($payment_data);
                if ($success) {
                    set_alert('success', _l('online_payment_recorded_success'));
                } else {
                    set_alert('danger', _l('online_payment_recorded_success_fail_database'));
                }
                redirect(site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash));
            }
        } elseif ($oResponse->isRedirect()) {
            $oResponse->redirect();
        } else {
            set_alert('danger', $oResponse->getMessage());
            redirect(site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash));
        }
    }
}
public function make_payment()
{
    check_invoice_restrictions($this->input->get('invoiceid'), $this->input->get('hash'));
    $this->load->model('invoices_model');
    $invoice      = $this->invoices_model->get($this->input->get('invoiceid'));
    load_client_language($invoice->clientid);
    $data['invoice']      = $invoice;
    $data['total']        = $this->input->get('total');
    echo $this->get_view($data);
}

public function get_view($data = array()){
    ob_start(); ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>
            <?php echo _l('payment_for_invoice') . ' ' . format_invoice_number($data['invoice']->id); ?>
        </title>
              <?php if(get_option('favicon') != ''){ ?>
      <link href="<?php echo base_url('uploads/company/'.get_option('favicon')); ?>" rel="shortcut icon">
      <?php } ?>
        <?php echo app_stylesheet('assets/css','reset.css'); ?>
        <!-- Bootstrap -->
        <link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href='<?php echo base_url('assets/plugins/roboto/roboto.css'); ?>' rel='stylesheet'>
        <?php echo app_stylesheet(template_assets_path().'/css','style.css'); ?>
    </head>
    <body>
        <div class="container">
            <div class="col-md-8 col-md-offset-2 mtop30">
                <div class="row">
                    <div class="panel_s">
                        <div class="panel-body">
                         <h4 class="bold no-margin font-medium">
                          <?php echo _l('payment_for_invoice'); ?> <a href="<?php echo site_url('viewinvoice/'. $data['invoice']->id . '/' . $data['invoice']->hash); ?>"><?php echo format_invoice_number($data['invoice']->id); ?></a>
                      </h4>
                      <hr />
                      <p><span class="bold"><?php echo _l('payment_total',format_money($data['total'],$data['invoice']->symbol)); ?></span></p>
                      <?php
                      $form = '
                      <form action="' . site_url('gateways/stripe/complete_purchase') . '" method="POST">
                        <script
                        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                        data-key="' . trim(get_option('paymentmethod_stripe_api_publishable_key')) . '"
                        data-amount="' . ($data['total'] * 100). '"
                        data-name="' . get_option('companyname') . '"
                        data-description="Payment for invoice ' . format_invoice_number($data['invoice']->id) . '";
                        data-locale="auto"
                        data-currency="'.$data['invoice']->currency_name.'"
                        >
                    </script>
                    '.form_hidden('invoiceid',$data['invoice']->id).'
                    '.form_hidden('total',$data['total']).'
                </form>';
                echo $form;
                ?>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
<?php
$contents = ob_get_contents();
ob_end_clean();
return $contents;
}
}

