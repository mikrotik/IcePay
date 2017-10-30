<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Omnipay\Omnipay;
require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

class Paypal_braintree_gateway
{
    private $id = '';

    private $name = '';

    private $settings = array();

    private $settings_values = array();


    function __construct()
    {
        $this->id   = 'paypal_braintree';
        $this->name = 'Braintree';

        $this->settings = array(
            array(
                'name' => 'paymentmethod_' . $this->id . '_active',
                'type' => 'yes_no',
                'default_value' => 0,
                'label' => 'settings_paymentmethod_active'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_label',
                'type' => 'input',
                'default_value' => $this->name,
                'label' => 'settings_paymentmethod_mode_label'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_merchant_id',
                'type' => 'input',
                'encrypted' => true,
                'label' => 'paymentmethod_braintree_merchant_id'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_api_public_key',
                'type' => 'input',
                'label' => 'paymentmethod_braintree_public_key'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_api_private_key',
                'type' => 'input',
                'encrypted' => true,
                'label' => 'paymentmethod_braintree_private_key'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_currencies',
                'type' => 'input',
                'label' => 'settings_paymentmethod_currencies',
                'default_value' => 'USD'
            ),
             array(
                'name' => 'paymentmethod_' . $this->id . '_default_selected',
                'type' => 'yes_no',
                'default_value' => 1,
                'label' => 'settings_paymentmethod_default_selected_on_invoice'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_test_mode_enabled',
                'type' => 'yes_no',
                'default_value' => 1,
                'label' => 'settings_paymentmethod_testing_mode'
            )
        );

        foreach ($this->settings as $option) {
            $val = '';
            if (isset($option['default_value'])) {
                $val = $option['default_value'];
            }
            add_option($option['name'], $val);
            $this->settings_values[$option['name']] = get_option($option['name']);
        }
        add_action('before_add_online_payment_modes', 'add_braintree_online_mode');
        $this->ci =& get_instance();
    }
    public function process_payment($data)
    {
        redirect(site_url('gateways/braintree/make_payment?invoiceid=' . $data['invoiceid'] . '&total=' . $data['amount'] . '&hash=' . $data['invoice']->hash));
    }
    public function fetch_payment($transaction_id) {
         $gateway = Omnipay::create('Braintree');
         $gateway->setMerchantId(trim($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_merchant_id'))));
         $gateway->setPrivateKey(trim($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_api_private_key'))));
         $gateway->setPublicKey(trim(get_option('paymentmethod_' . $this->id . '_api_public_key')));
         $gateway->setTestMode(get_option('paymentmethod_'.$this->id.'_test_mode_enabled'));
         return $gateway->find(array('transactionReference'=>$transaction_id))->send();
    }
    public function generate_token(){

         $gateway = Omnipay::create('Braintree');
         $gateway->setMerchantId(trim($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_merchant_id'))));
         $gateway->setPrivateKey(trim($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_api_private_key'))));
         $gateway->setPublicKey(trim(get_option('paymentmethod_' . $this->id . '_api_public_key')));
         $gateway->setTestMode(get_option('paymentmethod_'.$this->id.'_test_mode_enabled'));
         return $gateway->clientToken()->send()->getToken();
    }
    public function finish_payment($data)
    {
        // Process online for PayPal payment start
        $gateway = Omnipay::create('Braintree');
        $gateway->setMerchantId(trim($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_merchant_id'))));
        $gateway->setPrivateKey(trim($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_api_private_key'))));
        $gateway->setPublicKey(trim(get_option('paymentmethod_' . $this->id . '_api_public_key')));
        $gateway->setTestMode(get_option('paymentmethod_'.$this->id.'_test_mode_enabled'));

        $response = $gateway->purchase(array(
            'amount' => number_format($data['amount'], 2, '.', ''),
            'currency' => $data['currency'],
            'token' => $data['nonce'],
            ))->send();

        return $response;
    }

    public function get_settings()
    {
        return $this->settings;
    }
    public function get_setting_value($name)
    {
        return $this->settings_values[$name];
    }
    public function get_name()
    {
        return $this->name;
    }
    public function get_id()
    {
        return $this->id;
    }
}

function add_braintree_online_mode($modes)
{
    $CI =& get_instance();
    $modes[] = array(
        'id' => $CI->paypal_braintree_gateway->get_id(),
        'name' => get_option('paymentmethod_' . $CI->paypal_braintree_gateway->get_id() . '_label'),
        'description' => '',
        'selected_by_default'=>get_option('paymentmethod_' . $CI->paypal_braintree_gateway->get_id() . '_default_selected'),
        'active' => get_option('paymentmethod_' . $CI->paypal_braintree_gateway->get_id() . '_active')
    );
    return $modes;
}
