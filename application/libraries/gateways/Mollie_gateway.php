<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Omnipay\Omnipay;
require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');
class Mollie_gateway
{
    private $id = '';

    private $name = '';

    private $settings = array();

    private $settings_values = array();


    function __construct()
    {
        $this->id   = 'mollie';
        $this->name = 'Mollie';


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
                'name' => 'paymentmethod_' . $this->id . '_api_key',
                'type' => 'input',
                'encrypted' => true,
                'label' => 'settings_paymentmethod_mollie_api_key'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_currencies',
                'type' => 'input',
                'label' => 'currency',
                'default_value' => 'EUR'
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

        add_action('before_add_online_payment_modes', 'add_mollie_online_mode');

        $this->ci =& get_instance();
    }
    public function process_payment($data)
    {
        $gateway = Omnipay::create('Mollie');
        $gateway->setApiKey($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->id . '_api_key')));

        $oResponse = $gateway->purchase(array(
            'amount' => number_format($data['amount'], 2, '.', ''),
            'description' => 'Payment for invoice ' . format_invoice_number($data['invoice']->id),
            'returnUrl' => site_url('gateways/mollie/verify_payment?invoiceid=' . $data['invoice']->id . '&hash=' . $data['invoice']->hash),
            'notifyUrl' => site_url('gateways/mollie/webhook'),
            'metadata' => array(
                'order_id' => $data['invoice']->id
            )
        ))->send();

        // Add the token to database
        $this->ci->db->where('id', $data['invoiceid']);
        $this->ci->db->update('tblinvoices', array(
            'token' => $oResponse->getTransactionReference()
        ));

        if ($oResponse->isRedirect()) {
            $oResponse->redirect();
        } elseif ($oResponse->isPending()) {
            echo "Pending, Reference: " . $oResponse->getTransactionReference();
        } else {
            echo "Error " . $oResponse->getCode() . ': ' . $oResponse->getMessage();
        }
    }

    public function fetch_payment($data)
    {
        $gateway = Omnipay::create('Mollie');
        $gateway->setApiKey($this->ci->encryption->decrypt(get_option('paymentmethod_' . $this->get_id() . '_api_key')));
        return $gateway->fetchTransaction(array(
            'transactionReference' => $data['transaction_id']
        ))->send();
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

function add_mollie_online_mode($modes)
{
    $CI =& get_instance();
    $modes[] = array(
        'id' => $CI->mollie_gateway->get_id(),
        'name' => get_option('paymentmethod_' . $CI->mollie_gateway->get_id() . '_label'),
        'description' => '',
        'selected_by_default'=>get_option('paymentmethod_' . $CI->mollie_gateway->get_id() . '_default_selected'),
        'active' => get_option('paymentmethod_' . $CI->mollie_gateway->get_id() . '_active')
    );

    return $modes;
}
