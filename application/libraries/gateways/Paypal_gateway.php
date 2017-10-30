<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Omnipay\Omnipay;
require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');
class Paypal_gateway
{
    private $id = '';

    private $name = '';

    private $settings = array();

    private $settings_values = array();

    function __construct()
    {
        $this->id = 'paypal';
        $this->name = 'Paypal';

        $this->settings = array(
            array(
                'name'=>'paymentmethod_'.$this->id.'_active',
                'type'=>'yes_no',
                'default_value'=>0,
                'label'=>'settings_paymentmethod_active',
                ),
            array(
                'name'=>'paymentmethod_'.$this->id.'_label',
                'type'=>'input',
                'default_value'=>$this->name,
                'label'=>'settings_paymentmethod_mode_label',
                ),
            array(
                'name'=>'paymentmethod_'.$this->id.'_username',
                'type'=>'input',
                'encrypted'=>true,
                'label'=>'settings_paymentmethod_paypal_username',
                ),
            array(
                'name'=>'paymentmethod_'.$this->id.'_password',
                'type'=>'input',
                'encrypted'=>true,
                'label'=>'settings_paymentmethod_paypal_password',
                ),
            array(
                'name'=>'paymentmethod_'.$this->id.'_signature',
                'type'=>'input',
                'encrypted'=>true,
                'label'=>'settings_paymentmethod_paypal_signature',
                ),
            array(
                'name'=>'paymentmethod_'.$this->id.'_currencies',
                'type'=>'input',
                'label'=>'settings_paymentmethod_currencies',
                'default_value'=>'EUR,USD',
                ),
             array(
                'name' => 'paymentmethod_' . $this->id . '_default_selected',
                'type' => 'yes_no',
                'default_value' => 1,
                'label' => 'settings_paymentmethod_default_selected_on_invoice'
            ),
            array(
                'name'=>'paymentmethod_'.$this->id.'_test_mode_enabled',
                'type'=>'yes_no',
                'default_value'=>1,
                'label'=>'settings_paymentmethod_testing_mode',
                ),
            );

        foreach($this->settings as $option){
            $val = '';
            if(isset($option['default_value'])){
                $val = $option['default_value'];
            }
            add_option($option['name'],$val);
            $this->settings_values[$option['name']] = get_option($option['name']);
        }

        add_action('before_add_online_payment_modes','add_paypal_online_mode');

        $this->ci =& get_instance();
    }

    public function process_payment($data)
    {
        // Process online for PayPal payment start
        $gateway = Omnipay::create('PayPal_Express');

        $gateway->setUsername(trim($this->ci->encryption->decrypt(get_option('paymentmethod_paypal_username'))));
        $gateway->setPassword(trim($this->ci->encryption->decrypt(get_option('paymentmethod_paypal_password'))));
        $gateway->setSignature(trim($this->ci->encryption->decrypt(get_option('paymentmethod_paypal_signature'))));

        $gateway->setTestMode(get_option('paymentmethod_paypal_test_mode_enabled'));
        $gateway->setlogoImageUrl(site_url('uploads/company/logo.png'));
        $gateway->setbrandName(get_option('companyname'));

        $request_data = array(
            'amount' => number_format($data['amount'], 2, '.', ''),
            'returnUrl' => site_url('gateways/paypal/complete_purchase?hash=' . $data['invoice']->hash . '&invoiceid=' . $data['invoiceid']),
            'cancelUrl' => site_url('viewinvoice/' . $data['invoiceid'] . '/' . $data['invoice']->hash),
            'currency' => $data['invoice']->currency_name,
            'description' =>'Payment for invoice ' . format_invoice_number($data['invoiceid']),
            );
        try {
            $response = $gateway->purchase($request_data)->send();
            if ($response->isRedirect()) {
                $this->ci->session->set_userdata(array(
                    'online_payment_amount' => number_format($data['amount'], 2, '.', ''),
                    'currency' => $data['invoice']->currency_name,
                    ));
                // Add the token to database
                $this->ci->db->where('id', $data['invoiceid']);
                $this->ci->db->update('tblinvoices', array(
                    'token' => $response->getTransactionReference()
                    ));
                $response->redirect();
            } else {
                exit($response->getMessage());
            }
        }
        catch (\Exception $e) {
            echo $e->getMessage() . '<br />';
            exit('Sorry, there was an error processing your payment. Please try again later.');
        }
    }
    public function complete_purchase($data)
    {
        $gateway = Omnipay::create('PayPal_Express');
        $gateway->setUsername(trim($this->ci->encryption->decrypt(get_option('paymentmethod_paypal_username'))));
        $gateway->setPassword(trim($this->ci->encryption->decrypt(get_option('paymentmethod_paypal_password'))));
        $gateway->setSignature(trim($this->ci->encryption->decrypt(get_option('paymentmethod_paypal_signature'))));
        $gateway->setTestMode(get_option('paymentmethod_paypal_test_mode_enabled'));
        $response       = $gateway->completePurchase(array(
            'transactionReference' => $data['token'],
            'payerId' => $this->ci->input->get('PayerID'),
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            ))->send();
        $paypalResponse = $response->getData();
        return $paypalResponse;
    }
    public function get_settings(){
        return $this->settings;
    }
    public function get_setting_value($name){

        return $this->settings_values[$name];
    }
    public function get_name(){
        return $this->name;
    }
    public function get_id(){
        return $this->id;
    }
}

function add_paypal_online_mode($modes){
    $CI = &get_instance();
    $modes[] = array(
        'id' => $CI->paypal_gateway->get_id(),
        'name' => $CI->paypal_gateway->get_setting_value('paymentmethod_'.$CI->paypal_gateway->get_id().'_label'),
        'description' => '',
        'selected_by_default'=>get_option('paymentmethod_' . $CI->paypal_gateway->get_id() . '_default_selected'),
        'active' => $CI->paypal_gateway->get_setting_value('paymentmethod_'.$CI->paypal_gateway->get_id().'_active'),
        );
    return $modes;
}
