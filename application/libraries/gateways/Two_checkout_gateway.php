<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Omnipay\Omnipay;
require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');
class Two_checkout_gateway
{
    private $id = '';

    private $name = '';

    private $settings = array();

    private $settings_values = array();

    private $required_address_line_2_country_codes = 'CHN, JPN, RUS';

    private $required_state_country_codes = ' ARG, AUS, BGR, CAN, CHN, CYP, EGY, FRA, IND, IDN, ITA, JPN, MYS, MEX, NLD, PAN, PHL, POL, ROU, RUS, SRB, SGP, ZAF, ESP, SWE, THA, TUR, GBR, USA';

    private $required_zip_code_country_codes = 'ARG, AUS, BGR, CAN, CHN, CYP, EGY, FRA, IND, IDN, ITA, JPN, MYS, MEX, NLD, PAN, PHL, POL, ROU, RUS, SRB, SGP, ZAF, ESP, SWE, THA, TUR, GBR, USA';

    function __construct()
    {
        $this->id   = 'two_checkout';
        $this->name = '2Checkout';

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
                'name' => 'paymentmethod_' . $this->id . '_account_number',
                'type' => 'input',
                'label' => 'paymentmethod_two_checkout_account_number'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_private_key',
                'type' => 'input',
                'label' => 'paymentmethod_two_checkout_private_key',
                'encrypted' => true
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_publishable_key',
                'type' => 'input',
                'label' => 'paymentmethod_two_checkout_publishable_key'
            ),
            array(
                'name' => 'paymentmethod_' . $this->id . '_currencies',
                'type' => 'input',
                'label' => 'settings_paymentmethod_currencies',
                'default_value' => 'USD,EUR'
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
                'label' => 'settings_paymentmethod_testing_mode',
                'default_value' => 1
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

        add_action('before_add_online_payment_modes', 'add_two_checkout_online_mode');
        add_action('before_render_payment_gateway_settings', 'two_checkout_ssl_notice');

        $this->ci =& get_instance();

        $line_address_2_required                     = $this->required_address_line_2_country_codes;
        $this->required_address_line_2_country_codes = array();
        foreach (explode(', ', $line_address_2_required) as $cn_code) {
            array_push($this->required_address_line_2_country_codes, $cn_code);
        }
        $state_country_codes_required       = $this->required_state_country_codes;
        $this->required_state_country_codes = array();
        foreach (explode(', ', $state_country_codes_required) as $cn_code) {
            array_push($this->required_state_country_codes, $cn_code);
        }
        $zip_code_country_codes_required       = $this->required_zip_code_country_codes;
        $this->required_zip_code_country_codes = array();
        foreach (explode(', ', $zip_code_country_codes_required) as $cn_code) {
            array_push($this->required_zip_code_country_codes, $cn_code);
        }
    }
    public function process_payment($data)
    {
        $this->ci->session->set_userdata(array(
            'total_2checkout' => $data['amount']
        ));
        redirect(site_url('gateways/two_checkout/make_payment?invoiceid=' . $data['invoiceid'] . '&hash=' . $data['invoice']->hash));
    }
    public function finish_payment($data)
    {
        $gateway = Omnipay::create('TwoCheckoutPlus_Token');
        $gateway->setAccountNumber(get_option('paymentmethod_two_checkout_account_number'));
        $gateway->setPrivateKey($this->ci->encryption->decrypt(get_option('paymentmethod_two_checkout_private_key')));
        $gateway->setTestMode(get_option('paymentmethod_two_checkout_test_mode_enabled'));

        $billing_data                    = array();
        $billing_data['billingName']     = $this->ci->input->post('billingName');
        $billing_data['billingAddress1'] = $this->ci->input->post('billingAddress1');

        if ($this->ci->input->post('billingAddress2')) {
            $billing_data['billingAddress2'] = $this->ci->input->post('billingAddress2');
        }
        $billing_data['billingCity'] = $this->ci->input->post('billingCity');

        if ($this->ci->input->post('billingState')) {
            $billing_data['billingState'] = $this->ci->input->post('billingState');
        }
        if ($this->ci->input->post('billingPostcode')) {
            $billing_data['billingPostcode'] = $this->ci->input->post('billingPostcode');
        }
        $billing_data['billingCountry'] = $this->ci->input->post('billingCountry');
        $billing_data['email']          = $this->ci->input->post('email');


        $oResponse = $gateway->purchase(array(
            'amount' => number_format($data['amount'], 2, '.', ''),
            'currency' => $data['currency'],
            'token' => $this->ci->input->post('token'),
            'transactionId' => $data['invoice']->id,
            'card' => $billing_data
        ))->send();

        return $oResponse;

    }
    public function get_required_address_2_by_country_code()
    {
        return $this->required_address_line_2_country_codes;
    }
    public function get_required_state_by_country_code()
    {
        return $this->required_state_country_codes;
    }
    public function get_required_zip_by_country_code()
    {
        return $this->required_zip_code_country_codes;
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

function add_two_checkout_online_mode($modes)
{
    $CI =& get_instance();
    $modes[] = array(
        'id' => $CI->two_checkout_gateway->get_id(),
        'name' => get_option('paymentmethod_' . $CI->two_checkout_gateway->get_id() . '_label'),
        'description' => '',
        'selected_by_default'=>get_option('paymentmethod_' . $CI->two_checkout_gateway->get_id() . '_default_selected'),
        'active' => get_option('paymentmethod_' . $CI->two_checkout_gateway->get_id() . '_active')
    );
    return $modes;
}
function two_checkout_ssl_notice($gateway)
{
    if ($gateway['id'] == 'two_checkout') {
        echo '<p class="text-warning">' . _l('2checkout_usage_notice') . '</p>';
    }
}
