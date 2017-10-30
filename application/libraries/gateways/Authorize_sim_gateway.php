<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Omnipay\Omnipay;
require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');
class Authorize_sim_gateway
{
    private $id = '';

    private $name = '';

    private $settings = array();

    private $settings_values = array();


    function __construct()
    {
        $this->id   = 'authorize_sim';

        $this->name = 'Authorize.net SIM';

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
                'default_value' => 'Authorize.net',
                'label' => 'settings_paymentmethod_mode_label'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_api_login_id',
                'type' => 'input',
                'encrypted' => true,
                'label' => 'settings_paymentmethod_authorize_api_login_id'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_api_transaction_key',
                'type' => 'input',
                'label' => 'settings_paymentmethod_authorize_api_transaction_key',
                'encrypted' => true
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_api_secret_key',
                'type' => 'input',
                'label' => 'settings_paymentmethod_authorize_secret_key',
                'encrypted' => true
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_currencies',
                'type' => 'input',
                'label' => 'currency',
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
                'default_value' => 0,
                'label' => 'settings_paymentmethod_testing_mode'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_developer_mode_enabled',
                'type' => 'yes_no',
                'default_value' => 1,
                'label' => 'settings_paymentmethod_developer_mode'
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

        add_action('before_add_online_payment_modes', 'add_authorize_sim_online_mode');
        add_action('before_render_payment_gateway_settings', 'authorize_sim_notice');

        $this->ci =& get_instance();
    }
    public function process_payment($data)
    {
            $gateway = Omnipay::create('AuthorizeNet_SIM');
            $gateway->setApiLoginId($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_api_login_id')));
            $gateway->setTransactionKey($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_api_transaction_key')));
            $gateway->setHashSecret($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_api_secret_key')));
            $gateway->setTestMode(get_option('paymentmethod_' . $this->id . '_test_mode_enabled'));
            $gateway->setDeveloperMode(get_option('paymentmethod_' . $this->id . '_developer_mode_enabled'));

            $billing_data['billingCompany']  = $data['invoice']->client->company;
            $billing_data['billingAddress1'] = $data['invoice']->billing_street;
            $billing_data['billingName']     = '';
            $billing_data['billingCity']     = $data['invoice']->billing_city;
            $billing_data['billingState']    = $data['invoice']->billing_state;
            $billing_data['billingPostcode'] = $data['invoice']->billing_zip;

            $_country = '';
            $country = get_country($data['invoice']->billing_country);

            if($country){
                $_country = $country->short_name;
            }

            $billing_data['billingCountry']  = $_country;
            $trans_id = time();

            $requestData = array(
                'amount' => number_format($data['amount'], 2, '.', ''),
                'currency' => $data['invoice']->currency_name,
                'returnUrl'=>site_url('gateways/authorize_sim/complete_purchase'),
                'description' => 'Payment for invoice - ' . format_invoice_number($data['invoice']->id),
                'transactionId' => $trans_id,
                'card' => $billing_data
            );

           $oResponse = $gateway->purchase($requestData)->send();
           if($oResponse->isRedirect()) {
                $this->ci->db->where('id',$data['invoice']->id);
                $this->ci->db->update('tblinvoices',array('token'=>$trans_id));
                // redirect to offsite payment gateway
                $oResponse->redirect();
            } else {
                // payment failed: display message to customer
                echo $oResponse->getMessage();
            }
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

function add_authorize_sim_online_mode($modes)
{
    $CI =& get_instance();
    $modes[] = array(
        'id' => $CI->authorize_sim_gateway->get_id(),
        'name' => get_option('paymentmethod_' . $CI->authorize_sim_gateway->get_id() . '_label'),
        'description' => '',
        'selected_by_default'=>get_option('paymentmethod_' . $CI->authorize_sim_gateway->get_id() . '_default_selected'),
        'active' => get_option('paymentmethod_' . $CI->authorize_sim_gateway->get_id() . '_active')
    );

    return $modes;
}
function authorize_sim_notice($gateway)
{
    if ($gateway['id'] == 'authorize_sim') {
        echo '<p class="text-dark"><b>' . _l('currently_supported_currencies') . '</b>: USD, AUD, GBP, CAD, EUR, NZD</p>';
    }
}
