<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mollie extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function verify_payment()
    {

        $invoiceid = $this->input->get('invoiceid');
        $hash      = $this->input->get('hash');
        check_invoice_restrictions($invoiceid, $hash);

        $this->db->where('id', $invoiceid);
        $invoice = $this->db->get('tblinvoices')->row();

        $oResponse = $this->mollie_gateway->fetch_payment(array(
            'transaction_id' => $invoice->token
        ));
        if ($oResponse->isSuccessful()) {
            $data = $oResponse->getData();
            if ($data['status'] == 'paid') {
                set_alert('success', _l('online_payment_recorded_success'));
            }
        } else {
            set_alert('danger', $oResponse->getMessage());
        }
        redirect(site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash));
    }

    public function webhook()
    {
        $ip = $this->input->ip_address();
        if (ip_in_range($ip, '77.245.85.226-77.245.85.245')) {
            $trans_id  = $this->input->post('id');
            $oResponse = $this->mollie_gateway->fetch_payment(array(
                'transaction_id' => $trans_id
            ));
            $data      = $oResponse->getData();
            if ($data['status'] == 'paid') {
                // Add payment to database
                $payment_data['amount']        = $data['amount'];
                $payment_data['invoiceid']     = $data['metadata']['order_id'];
                $payment_data['paymentmode']   = $this->mollie_gateway->get_id();
                $payment_data['paymentmethod']   = $data['method'];
                $payment_data['transactionid'] = $trans_id;
                $this->load->model('payments_model');
                $this->payments_model->add($payment_data);
            } else if ($data['status'] == 'refunded' || $data['status'] == 'cancelled' || $data['status'] == 'charged_back') {
                $this->db->where('invoiceid', $data['metadata']['order_id']);
                $this->db->where('transactionid', $trans_id);
                $this->db->delete('tblinvoicepaymentrecords');
                update_invoice_status($data['metadata']['order_id']);
            }

            header("HTTP/1.1 200 OK");
        }
    }
}
