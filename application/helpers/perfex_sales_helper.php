<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Check if company using invoice with different currencies
 * @return boolean
 */
function is_using_multiple_currencies($table = 'tblinvoices')
{
    $CI =& get_instance();
    $CI->load->model('currencies_model');
    $currencies            = $CI->currencies_model->get();
    $total_currencies_used = 0;
    $other_then_base       = false;
    $base_found            = false;
    foreach ($currencies as $currency) {
        $CI->db->where('currency', $currency['id']);
        $total = $CI->db->count_all_results($table);
        if ($total > 0) {
            $total_currencies_used++;
            if ($currency['isdefault'] == 0) {
                $other_then_base = true;
            } else {
                $base_found = true;
            }
        }
    }

    if ($total_currencies_used > 1 && $base_found == true && $other_then_base == true) {
        return true;
    } else if ($total_currencies_used == 1 && $base_found == false && $other_then_base == true) {
        return true;
    } else if ($total_currencies_used == 0 || $total_currencies_used == 1) {
        return false;
    }
    return true;
}
/**
 * Check if client have invoices with multiple currencies
 * @return booelan
 */
function is_client_using_multiple_currencies($clientid = '', $table = 'tblinvoices')
{
    if ($clientid == '') {
        $clientid = get_client_user_id();
    }
    $CI =& get_instance();
    $CI->load->model('currencies_model');
    $currencies            = $CI->currencies_model->get();
    $total_currencies_used = 0;
    foreach ($currencies as $currency) {
        $CI->db->where('currency', $currency['id']);
        $CI->db->where('clientid', $clientid);
        $total = $CI->db->count_all_results($table);
        if ($total > 0) {
            $total_currencies_used++;
        }
    }
    if ($total_currencies_used > 1) {
        return true;
    } else if ($total_currencies_used == 0 || $total_currencies_used == 1) {
        return false;
    }
    return true;
}
/**
 * Get invoice total left for paying if not payments found the original total from the invoice will be returned
 * @since  Version 1.0.1
 * @param  mixed $id     invoice id
 * @param  mixed $invoice_total
 * @return mixed  total left
 */
function get_invoice_total_left_to_pay($id, $invoice_total)
{
    $CI =& get_instance();
    $CI->load->model('payments_model');
    $payments = $CI->payments_model->get_invoice_payments($id);
    foreach ($payments as $payment) {
        $invoice_total -= $payment['amount'];
    }
    return $invoice_total;
}
/**
 * Check invoice restrictions - hash, clientid
 * @since  Version 1.0.1
 * @param  mixed $id   invoice id
 * @param  string $hash invoice hash
 */
function check_invoice_restrictions($id, $hash)
{
    $CI =& get_instance();
    $CI->load->model('invoices_model');
    if (!$hash || !$id) {
        show_404();
    }
    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_invoice_only_logged_in') == 1) {
            redirect(site_url('clients/login'));
        }
    }
    $invoice = $CI->invoices_model->get($id);
    if (!$invoice || ($invoice->hash != $hash)) {
        show_404();
    }

    // Do one more check
    if (!is_staff_logged_in()) {
        if (get_option('view_invoice_only_logged_in') == 1) {
            if ($invoice->clientid != get_client_user_id()) {
                show_404();
            }
        }
    }
}
/**
 * Check estimate restrictions - hash, clientid
 * @since  Version 1.0.1
 * @param  mixed $id   estimate id
 * @param  string $hash estimate hash
 */
function check_estimate_restrictions($id, $hash)
{
    $CI =& get_instance();
    $CI->load->model('estimates_model');
    if (!$hash || !$id) {
        show_404();
    }
    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_estimate_only_logged_in') == 1) {
            redirect(site_url('clients/login'));
        }
    }
    $estimate = $CI->estimates_model->get($id);
    if (!$estimate || ($estimate->hash != $hash)) {
        show_404();
    }
    // Do one more check
    if (!is_staff_logged_in()) {
        if (get_option('view_estimate_only_logged_in') == 1) {
            if ($estimate->clientid != get_client_user_id()) {
                show_404();
            }
        }
    }
}
/**
 * Check if proposal hash is equal
 * @param  mixed $id   proposal id
 * @param  string $hash proposal hash
 * @return void
 */
function check_proposal_restrictions($id, $hash)
{
    $CI =& get_instance();
    $CI->load->model('proposals_model');
    if (!$hash || !$id) {
        show_404();
    }
    $proposal = $CI->proposals_model->get($id);
    if (!$proposal || ($proposal->hash != $hash)) {
        show_404();
    }
}
/**
 * Forat number with 2 decimals
 * @param  mixed $total
 * @return string
 */
function _format_number($total, $force_checking_zero_decimals = false)
{
    if (!is_numeric($total)) {
        return false;
    }
    $decimal_separator  = get_option('decimal_separator');
    $thousand_separator = get_option('thousand_separator');

    $d = get_decimal_places();
    if (get_option('remove_decimals_on_zero') == 1 || $force_checking_zero_decimals == true) {
        if (!is_decimal($total)) {
            $d = 0;
        }
    }

    return do_action('number_after_format',number_format($total, $d, $decimal_separator, $thousand_separator));
}
/**
 * Unformat formatted number. THIS FUNCTION IS NOT WELL TESTED
 * @param  mixed  $number
 * @param  boolean $force_number
 * @return mixed
 */
function number_unformat($number, $force_number = true)
{
    if ($force_number) {
        $number = preg_replace('/^[^\d]+/', '', $number);
    } else if (preg_match('/^[^\d]+/', $number)) {
        return false;
    }
    $dec_point     = get_option('decimal_separator');
    $thousands_sep = get_option('thousand_separator');
    $type          = (strpos($number, $dec_point) === false) ? 'int' : 'float';
    $number        = str_replace(array(
        $dec_point,
        $thousands_sep
    ), array(
        '.',
        ''
    ), $number);
    settype($number, $type);
    return $number;
}
/**
 * Format money with 2 decimal based on symbol
 * @param  mixed $total
 * @param  string $symbol Money symbol
 * @return string
 */
function format_money($total, $symbol = '')
{
    if (!is_numeric($total) && $total != 0) {
        return false;
    }

    $decimal_separator  = get_option('decimal_separator');
    $thousand_separator = get_option('thousand_separator');
    $currency_placement = get_option('currency_placement');
    $d                  = get_decimal_places();
    if (get_option('remove_decimals_on_zero') == 1) {
        if (!is_decimal($total)) {
            $d = 0;
        }
    }

    $total = number_format($total, $d, $decimal_separator, $thousand_separator);
    $total = do_action('money_after_format_without_currency',$total);

    if ($currency_placement === 'after') {
        $_formated = $total . '' . $symbol;
    } else {
        $_formated = $symbol . '' . $total;
    }

    $_formated = do_action('money_after_format_with_currency',$_formated);
    return $_formated;
}
/**
 * Check if passed number is decimal
 * @param  mixed  $val
 * @return boolean
 */
function is_decimal($val)
{
    return is_numeric($val) && floor($val) != $val;
}
/**
 * Function that will loop through taxes and will check if there is 1 tax or multiple
 * @param  array $taxes
 * @return boolean
 */
function mutiple_taxes_found_for_item($taxes)
{
    $names = array();
    foreach ($taxes as $t) {
        array_push($names, $t['taxname']);
    }
    $names = array_map("unserialize", array_unique(array_map("serialize", $names)));
    if (count($names) == 1) {
        return false;
    }
    return true;
}
/**
 * Format invoice status
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_invoice_status($status, $classes = '', $label = true)
{
    $id          = $status;
    $label_class = get_invoice_status_label($status);
    if ($status == 1) {
        $status = _l('invoice_status_unpaid');
    } else if ($status == 2) {
        $status = _l('invoice_status_paid');
    } else if ($status == 3) {
        $status = _l('invoice_status_not_paid_completely');
    } else if ($status == 4) {
        $status = _l('invoice_status_overdue');
    } else if ($status == 5) {
        $status = _l('invoice_status_cancelled');
    } else {
        // status 6
        $status = _l('invoice_status_draft');
    }
    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status invoice-status-' . $id . '">' . $status . '</span>';
    } else {
        return $status;
    }
}
/**
 * Return invoice status label class baed on twitter bootstrap classses
 * @param  mixed $status invoice status id
 * @return string
 */
function get_invoice_status_label($status)
{
    $label_class = '';
    if ($status == 1) {
        $label_class = 'danger';
    } else if ($status == 2) {
        $label_class = 'success';
    } else if ($status == 3) {
        $label_class = 'warning';
    } else if ($status == 4) {
        $label_class = 'warning';
    } else if ($status == 5 || $status == 6) {
        $label_class = 'default';
    } else {
        if (!is_numeric($status)) {
            if ($status == 'not_sent') {
                $label_class = 'default';
            }
        }
    }
    return $label_class;
}
/**
 * Format estimate status
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_estimate_status($status, $classes = '', $label = true)
{
    $id          = $status;
    $label_class = estimate_status_color_class($status);
    $status      = estimate_status_by_id($status);
    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status estimate-status-' . $id . ' estimate-status-' . $label_class . '">' . $status . '</span>';
    } else {
        return $status;
    }
}
/**
 * Return estimate status translated by passed status id
 * @param  mixed $id estimate status id
 * @return string
 */
function estimate_status_by_id($id)
{
    $status = '';
    if ($id == 1) {
        $status = _l('estimate_status_draft');
    } else if ($id == 2) {
        $status = _l('estimate_status_sent');
    } else if ($id == 3) {
        $status = _l('estimate_status_declined');
    } else if ($id == 4) {
        $status = _l('estimate_status_accepted');
    } else if ($id == 5) {
        // status 5
        $status = _l('estimate_status_expired');
    } else {
        if (!is_numeric($id)) {
            if ($id == 'not_sent') {
                $status = _l('not_sent_indicator');
            }
        }
    }

    $hook_data = do_action('estimate_status_label', array(
        'id' => $id,
        'label' => $status
    ));
    $status    = $hook_data['label'];

    return $status;
}
/**
 * Return estimate status color class based on twitter bootstrap
 * @param  mixed  $id
 * @param  boolean $replace_default_by_muted
 * @return string
 */
function estimate_status_color_class($id, $replace_default_by_muted = false)
{
    $class = '';
    if ($id == 1) {
        $class = 'default';
        if ($replace_default_by_muted == true) {
            $class = 'muted';
        }
    } else if ($id == 2) {
        $class = 'info';
    } else if ($id == 3) {
        $class = 'danger';
    } else if ($id == 4) {
        $class = 'success';
    } else if ($id == 5) {
        // status 5
        $class = 'warning';
    } else {
        if (!is_numeric($id)) {
            if ($id == 'not_sent') {
                $class = 'default';
                if ($replace_default_by_muted == true) {
                    $class = 'muted';
                }
            }
        }
    }

    $hook_data = do_action('estimate_status_color_class', array(
        'id' => $id,
        'class' => $class
    ));
    $class     = $hook_data['class'];

    return $class;
}
/**
 * Return proposal status color class based on twitter bootstrap
 * @param  mixed  $id
 * @param  boolean $replace_default_by_muted
 * @return string
 */
function proposal_status_color_class($id, $replace_default_by_muted = false)
{
    if ($id == 1) {
        $class = 'default';
    } else if ($id == 2) {
        $class = 'danger';
    } else if ($id == 3) {
        $class = 'success';
    } else if ($id == 4 || $id == 5) {
        // status sent and revised
        $class = 'info';
    } else if ($id == 6) {
        $class = 'default';
    }
    if ($class == 'default') {
        if ($replace_default_by_muted == true) {
            $class = 'muted';
        }
    }
    return $class;
}
/**
 * Format proposal status with label or not
 * @param  mixed  $status  proposal status id
 * @param  string  $classes additional label classes
 * @param  boolean $label   to include the label or return just translated text
 * @return string
 */
function format_proposal_status($status, $classes = '', $label = true)
{
    $id = $status;
    if ($status == 1) {
        $status      = _l('proposal_status_open');
        $label_class = 'default';
    } else if ($status == 2) {
        $status      = _l('proposal_status_declined');
        $label_class = 'danger';
    } else if ($status == 3) {
        $status      = _l('proposal_status_accepted');
        $label_class = 'success';
    } else if ($status == 4) {
        $status      = _l('proposal_status_sent');
        $label_class = 'info';
    } else if ($status == 5) {
        $status      = _l('proposal_status_revised');
        $label_class = 'info';
    } else if ($status == 6) {
        $status      = _l('proposal_status_draft');
        $label_class = 'default';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status proposal-status-' . $id . '">' . $status . '</span>';
    } else {
        return $status;
    }
}
/**
 * Update invoice status
 * @param  mixed $id invoice id
 * @return mixed invoice updates status / if no update return false
 * @return boolean $prevent_logging do not log changes if the status is updated for the invoice activity log
 */
function update_invoice_status($id, $force_update = false, $prevent_logging = false)
{
    $CI =& get_instance();

    $CI->load->model('invoices_model');
    $invoice = $CI->invoices_model->get($id);

    $CI->load->model('payments_model');
    $payments = $CI->payments_model->get_invoice_payments($id);

    $original_status = $invoice->status;

    if ($original_status == 6 && $force_update == false) {
        return false;
    }
    $total_payments = array();
    $status         = 1;

    // Check if the first payments is equal to invoice total
    if (isset($payments[0])) {
        if ($payments[0]['amount'] == $invoice->total) {
            // Paid status
            $status = 2;
        } else {
            foreach ($payments as $payment) {
                array_push($total_payments, $payment['amount']);
            }
            $total = array_sum($total_payments);
            if ($total == $invoice->total || $total > $invoice->total) {
                // Paid status
                $status = 2;
            } else if ($total == 0) {
                // Unpaid status
                $status = 1;
            } else {

                if ($invoice->duedate != null) {
                    if ($total > 0) {
                        // Not paid completely status
                        $status = 3;
                    } else if (date('Y-m-d', strtotime($invoice->duedate)) < date('Y-m-d')) {
                        $status = 4;
                    }
                } else {
                    // Not paid completely status
                    $status = 3;
                }
            }
        }
    } else {
        if ($invoice->total == 0) {
            $status = 2;
        } else {
            if ($invoice->duedate != null) {
                if (date('Y-m-d', strtotime($invoice->duedate)) < date('Y-m-d')) {
                    // Overdue status
                    $status = 4;
                }
            }
        }

    }
    $CI->db->where('id', $id);
    $CI->db->update('tblinvoices', array(
        'status' => $status
    ));

    if ($CI->db->affected_rows() > 0) {
        if ($prevent_logging == true) {
            return $status;
        }
        logActivity('Invoice Status Updated [Invoice Number: ' . format_invoice_number($invoice->id) . ', From: ' . format_invoice_status($original_status, '', false) . ' To: ' . format_invoice_status($status, '', false) . ']', NULL);

        $additional_activity = serialize(array(
            '<original_status>' . $original_status . '</original_status>',
            '<new_status>' . $status . '</new_status>'
        ));

        $CI->invoices_model->log_invoice_activity($invoice->id, 'invoice_activity_status_updated', false, $additional_activity);
        return $status;
    }
    return false;
}
/**
 * Check if the give invoice id is last invoice
 * @param  mixed  $id invoice id
 * @return boolean
 */
function is_last_invoice($id)
{
    $CI =& get_instance();
    $CI->db->select('id')->from('tblinvoices')->order_by('id', 'desc')->limit(1);
    $query           = $CI->db->get();
    $last_invoice_id = $query->row()->id;
    if ($last_invoice_id == $id) {
        return true;
    }
    return false;
}
/**
 * Check if the give estimate id is last invoice
 * @since Version 1.0.2
 * @param  mixed  $id estimateid
 * @return boolean
 */
function is_last_estimate($id)
{
    $CI =& get_instance();
    $CI->db->select('id')->from('tblestimates')->order_by('id', 'desc')->limit(1);
    $query            = $CI->db->get();
    $last_estimate_id = $query->row()->id;
    if ($last_estimate_id == $id) {
        return true;
    }
    return false;
}
/**
 * Format invoice number based on description
 * @param  mixed $id
 * @return string
 */
function format_invoice_number($id)
{
    $CI =& get_instance();
    $CI->db->select('date,number,prefix,number_format')->from('tblinvoices')->where('id', $id);
    $invoice = $CI->db->get()->row();
    if (!$invoice) {
        return '';
    }
    $format = $invoice->number_format;
    $prefix = $invoice->prefix;
    if ($format == 1) {
        // Number based
        return $prefix . str_pad($invoice->number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
    } else if ($format == 2) {
        return $prefix . date('Y', strtotime($invoice->date)) . '/' . str_pad($invoice->number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
    }
    return $number;
}
/**
 * Format estimate number based on description
 * @since  Version 1.0.2
 * @param  mixed $id
 * @return string
 */
function format_estimate_number($id)
{
    $CI =& get_instance();
    $CI->db->select('date,number,prefix,number_format')->from('tblestimates')->where('id', $id);
    $estimate = $CI->db->get()->row();
    if (!$estimate) {
        return '';
    }
    $format = $estimate->number_format;
    $prefix = $estimate->prefix;
    if ($format == 1) {
        // Number based
        return $prefix . str_pad($estimate->number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
    } else if ($format == 2) {
        return $prefix . date('Y', strtotime($estimate->date)) . '/' . str_pad($estimate->number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
    }
    return $number;
}
/**
 * Function that format proposal number based on the prefix option and the proposal id
 * @param  mixed $id proposa id
 * @return string
 */
function format_proposal_number($id)
{
    return get_option('proposal_number_prefix') . str_pad($id, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
}
/**
 * Helper function to get tax by passedid
 * @param  integer $id taxid
 * @return object
 */
function get_tax_by_id($id)
{
    $CI =& get_instance();
    $CI->db->where('id', $id);
    return $CI->db->get('tbltaxes')->row();
}
/**
 * Helper function to get tax by passed name
 * @param  string $name tax name
 * @return object
 */
function get_tax_by_name($name)
{
    $CI =& get_instance();
    $CI->db->where('name', $name);
    return $CI->db->get('tbltaxes')->row();
}
/**
 * Function that return invoice item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function get_invoice_item_taxes($itemid)
{
    $CI =& get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'invoice');
    $taxes = $CI->db->get('tblitemstax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }
    return $taxes;
}
/**
 * Function that return estimate item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function get_estimate_item_taxes($itemid)
{
    $CI =& get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'estimate');
    $taxes = $CI->db->get('tblitemstax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }
    return $taxes;
}
/**
 * Function that return proposal item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function get_proposal_item_taxes($itemid)
{
    $CI =& get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'proposal');
    $taxes = $CI->db->get('tblitemstax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }
    return $taxes;
}
/**
 * Check if payment mode is allowed for specific invoice
 * @param  mixed  $id payment mode id
 * @param  mixed  $invoiceid invoice id
 * @return boolean
 */
function is_payment_mode_allowed_for_invoice($id, $invoiceid)
{
    $CI =& get_instance();
    $CI->db->select('tblcurrencies.name as currency_name,allowed_payment_modes')->from('tblinvoices')->join('tblcurrencies', 'tblcurrencies.id = tblinvoices.currency', 'left')->where('tblinvoices.id', $invoiceid);
    $invoice       = $CI->db->get()->row();
    $allowed_modes = $invoice->allowed_payment_modes;
    if (!is_null($allowed_modes)) {
        $allowed_modes = unserialize($allowed_modes);
        if (count($allowed_modes) == 0) {
            return false;
        } else {
            foreach ($allowed_modes as $mode) {
                if ($mode == $id) {
                    // is offline payment mode
                    if (is_numeric($id)) {
                        return true;
                    }
                    // check currencies
                    $currencies = explode(',', get_option('paymentmethod_' . $id . '_currencies'));
                    foreach ($currencies as $currency) {
                        $currency = trim($currency);
                        if (strtoupper($currency) == strtoupper($invoice->currency_name)) {
                            return true;
                        }
                    }
                    return false;
                }
            }
        }
    } else {
        return false;
    }
    return false;
}
/**
 * Check if invoice mode exists in invoice
 * @since  Version 1.0.1
 * @param  array  $modes     all invoice modes
 * @param  mixed  $invoiceid invoice id
 * @param  boolean $offline   should check offline or online modes
 * @return boolean
 */
function found_invoice_mode($modes, $invoiceid, $offline = true, $show_on_pdf = false)
{
    $CI =& get_instance();
    $CI->db->select('tblcurrencies.name as currency_name,allowed_payment_modes')->from('tblinvoices')->join('tblcurrencies', 'tblcurrencies.id = tblinvoices.currency', 'left')->where('tblinvoices.id', $invoiceid);
    $invoice = $CI->db->get()->row();
    if (!is_null($invoice->allowed_payment_modes)) {
        $invoice->allowed_payment_modes = unserialize($invoice->allowed_payment_modes);
        if (count($invoice->allowed_payment_modes) == 0) {
            return false;
        } else {
            foreach ($modes as $mode) {
                if ($offline == true) {
                    if (is_numeric($mode['id']) && is_array($invoice->allowed_payment_modes)) {
                        foreach ($invoice->allowed_payment_modes as $allowed_mode) {
                            if ($allowed_mode == $mode['id']) {
                                if ($show_on_pdf == false) {
                                    return true;
                                } else {
                                    if ($mode['show_on_pdf'] == 1) {
                                        return true;
                                    } else {
                                        return false;
                                    }

                                }

                            }
                        }
                    }
                } else {
                    if (!is_numeric($mode['id']) && !empty($mode['id'])) {
                        foreach ($invoice->allowed_payment_modes as $allowed_mode) {
                            if ($allowed_mode == $mode['id']) {
                                // Check for currencies
                                $currencies = explode(',', get_option('paymentmethod_' . $mode['id'] . '_currencies'));
                                foreach ($currencies as $currency) {
                                    $currency = trim($currency);
                                    if (strtoupper($currency) == strtoupper($invoice->currency_name)) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return false;
}
/**
 * Pdf language loaded based on few options
 * @param  mixed $clientid client id
 * @return void
 */
function load_pdf_language($clientid)
{
    $CI =& get_instance();
    $lang     = get_option('active_language');
    // When cron or email sending pdf document the pdfs need to be on the client language
    $language = get_client_default_language($clientid);
    if (DEFINED('CRON') || DEFINED('EMAIL_TEMPLATE_SEND')) {
        if (!empty($language)) {
            $lang = $language;
        }
    } else {
        if (get_option('output_client_pdfs_from_admin_area_in_client_language') == 1) {
            if (!empty($language)) {
                $lang = $language;
            }
        }
    }
    if (file_exists(APPPATH . 'language/' . $lang)) {
        $CI->lang->load($lang . '_lang', $lang);
    }
}
/**
 * Fetches custom pdf logo url for pdf or use the default logo uploaded for the company
 * Additional statements applied because this function wont work on all servers. All depends how the server is configured.
 * @return [type] [description]
 */
function pdf_logo_url()
{
    $custom_pdf_logo_image_url = get_option('custom_pdf_logo_image_url');
    $width                     = get_option('pdf_logo_width');
    if ($width == '') {
        $width = 120;
    }
    if ($custom_pdf_logo_image_url != '') {
        if (strpos($custom_pdf_logo_image_url, 'localhost') !== false) {
            $cimg = $custom_pdf_logo_image_url;
        } else if (strpos($custom_pdf_logo_image_url, 'http') === false) {
            $cimg = FCPATH . $custom_pdf_logo_image_url;
        } else {
            /*  $cimg = do_curl_pdf_image($custom_pdf_logo_image_url);
            $formImage = imagecreatefromstring(base64_decode(strafter($cimg,'base64,')));
            $w = imagesx($formImage);
            $h = imagesy($formImage);
            */
            if (_startsWith($custom_pdf_logo_image_url, site_url()) !== FALSE) {
                $temp = str_replace(site_url(), '/', $custom_pdf_logo_image_url);
                $cimg = FCPATH . $temp;
                if (!file_exists($cimg)) {
                    $cimg = do_curl_pdf_image($custom_pdf_logo_image_url);
                }
            } else {
                $cimg = do_curl_pdf_image($custom_pdf_logo_image_url);
            }
        }
        $logo_url = '<img width="' . $width . 'px" src="' . $cimg . '">';

    } else {
        $logo_url = '<img width="' . $width . 'px" src="' . get_upload_path_by_type('company') . get_option('company_logo') . '">';
    }
    return $logo_url;
}
/**
 * Fetch curl image and returns as data base64
 * @param  string $url
 * @return string
 */
function do_curl_pdf_image($url)
{

    $path_parts = pathinfo($url);

    if (!isset($path_parts['extension']) || (isset($path_parts['extension']) && $path_parts['extension'] == null)) {
        $extension = get_file_extension($url);
    } else {
        $extension = $path_parts['extension'];
    }
    // On some hosting providers you cant access directly the url and throwing error unable to get image size
    // Will simulate like browser access to get the image.
    $ch = curl_init();
    // set url
    curl_setopt($ch, CURLOPT_URL, $url);
    // Return the transfer as a image
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    // $output contains the output image
    $output = curl_exec($ch);
    // close curl resource to free up system resources
    curl_close($ch);
    return 'data:image/' . $extension . ';base64,' . base64_encode($output);
}
/**
 * Get PDF format page
 * Based on the options will return the formated string that will be used in the PDF library
 * @param  string $option_name
 * @return array
 */
function get_pdf_format($option_name)
{
    $oFormat = strtoupper(get_option($option_name));
    $data    = array(
        'orientation' => '',
        'format' => ''
    );

    if ($oFormat == 'A4-PORTRAIT') {
        $data['orientation'] = 'P';
        $data['format']      = 'A4';
    } else if ($oFormat == 'A4-LANDSCAPE') {
        $data['orientation'] = 'L';
        $data['format']      = 'A4';
    } else if ($oFormat == 'LETTER-PORTRAIT') {
        $data['orientation'] = 'P';
        $data['format']      = 'LETTER';
    } else {
        // LETTER-LANDSCAPE
        $data['orientation'] = 'L';
        $data['format']      = 'LETTER';
    }
    return $data;
}
/**
 * Prepare general invoice pdf
 * @param  object $invoice Invoice as object with all necessary fields
 * @return mixed object
 */
function invoice_pdf($invoice, $tag = '')
{
    $CI =& get_instance();
    load_pdf_language($invoice->clientid);
    $CI->load->library('pdf');
    $invoice_number = format_invoice_number($invoice->id);
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');

    if ($font_size == '') {
        $font_size = 10;
    }

    $CI->load->model('payment_modes_model');
    $payment_modes = $CI->payment_modes_model->get();

    $i = 0;
    // In case user want to include {invoice_number} in PDF offline mode description
    foreach ($payment_modes as $mode) {
        if(isset($mode['description'])){
          $payment_modes[$i]['description'] = str_replace('{invoice_number}',format_invoice_number($invoice->id),$mode['description']);
        }
        $i++;
    }

    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false,false,'invoice');

    $pdf->SetTitle($invoice_number);
    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);

    $CI->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->setImageScale(1.53);
    $pdf->setJPEGQuality(100);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);

    if ($CI->input->get('print') == 'true') {
        // force print dialog
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    $status = $invoice->status;
    $swap   = get_option('swap_pdf_info');
    $CI->load->library('numberword', array(
        'clientid' => $invoice->clientid
    ));
    $invoice = do_action('invoice_html_pdf_data', $invoice);
    if (file_exists(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_invoicepdf.php')) {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_invoicepdf.php');
    } else {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/invoicepdf.php');
    }

    return $pdf;
}

/**
 * Prepare general estimate pdf
 * @since  Version 1.0.2
 * @param  object $estimate estimate as object with all necessary fields
 * @return mixed object
 */
function estimate_pdf($estimate, $tag = '')
{
    $CI =& get_instance();
    load_pdf_language($estimate->clientid);
    $CI->load->library('pdf');
    $estimate_number = format_estimate_number($estimate->id);
    $font_name       = get_option('pdf_font');
    $font_size       = get_option('pdf_font_size');

    if ($font_size == '') {
        $font_size = 10;
    }

    $formatArray = get_pdf_format('pdf_format_estimate');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false,false,'estimate');

    $pdf->SetTitle($estimate_number);
    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);
    $CI->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(1.53);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->setJPEGQuality(100);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        // force print dialog
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    $status = $estimate->status;
    $swap   = get_option('swap_pdf_info');
    $CI->load->library('numberword', array(
        'clientid' => $estimate->clientid
    ));
    $estimate = do_action('estimate_html_pdf_data', $estimate);
    if (file_exists(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_estimatepdf.php')) {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_estimatepdf.php');
    } else {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/estimatepdf.php');
    }
    return $pdf;
}
/**
 * Function that generates proposal pdf for admin and clients area
 * @param  object $proposal
 * @param  string $tag      tag to include in the proposal when using the bulk pdf exported
 * @return object
 */
function proposal_pdf($proposal, $tag = '')
{
    $CI =& get_instance();

    if ($proposal->rel_id != NULL && $proposal->rel_type == 'customer') {
        load_pdf_language($proposal->rel_id);
    }

    $CI->load->library('pdf');

    $number_word_lang_rel_id = 'unknown';
    if ($proposal->rel_type == 'customer') {
        $number_word_lang_rel_id = $proposal->rel_id;
    }
    $CI->load->library('numberword', array(
        'clientid' => $number_word_lang_rel_id
    ));

    $formatArray = get_pdf_format('pdf_format_proposal');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false,false,'proposal');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    $proposal_url = site_url('viewproposal/' . $proposal->id . '/' . $proposal->hash);
    $number       = format_proposal_number($proposal->id);
    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);

    $pdf->setImageScale(1.53);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->setJPEGQuality(100);
    $pdf->SetDisplayMode('default', 'OneColumn');
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        // force print dialog
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    $swap = get_option('swap_pdf_info');
    $CI->load->model('currencies_model');
    $total = '';
    if ($proposal->total != 0) {
        $total = format_money($proposal->total, $CI->currencies_model->get($proposal->currency)->symbol);
        $total = _l('proposal_total') . ': ' . $total;
    }
    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $proposal->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $proposal->content);
    // Add cellpadding to all tables inside the html
    $proposal->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="4">', $proposal->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $proposal->content = preg_replace('/[\t\n\r\0\x0B]/', '', $proposal->content);
    $proposal->content = preg_replace('/([\s])\1+/', ' ', $proposal->content);

    // Tcpdf does not support float css we need to adjust this here
    $proposal->content = str_replace('float: right', 'text-align: right', $proposal->content);
    $proposal->content = str_replace('float: left', 'text-align: left', $proposal->content);
    // Image center
    $proposal->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $proposal->content);
    /* $matches = array();
    preg_match_all('!http://[a-z0-9\-\.\/]+\.(?:jpe?g|png|gif)!Ui' , $proposal->content , $matches);
    if(isset($matches[0])){
    foreach($matches[0] as $m){
    if(strpos($m,site_url()) !== FALSE) {
    $test = str_replace(site_url(), '/', $m);
    $proposal->content = str_replace($m,$test,$proposal->content);
    } else {
    $proposal->content = str_replace($m,do_curl_pdf_image($m),$proposal->content);
    }
    }
    }*/
    $proposal          = do_action('proposal_html_pdf_data', $proposal);
    if (file_exists(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_proposalpdf.php')) {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_proposalpdf.php');
    } else {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/proposalpdf.php');
    }
    return $pdf;
}
/**
 * Generate contract pdf
 * @param  object $contract object db
 * @return mixed object
 */
function contract_pdf($contract)
{
    $CI =& get_instance();
    $CI->load->library('pdf');

    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false,false,'contract');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }
    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);

    $CI->pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->setImageScale(1.53);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        // force print dialog
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $contract->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $contract->content);
    // Add cellpadding to all tables inside the html
    $contract->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="4">', $contract->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $contract->content = preg_replace('/[\t\n\r\0\x0B]/', '', $contract->content);
    $contract->content = preg_replace('/([\s])\1+/', ' ', $contract->content);

    // Tcpdf does not support float css we need to adjust this here
    $contract->content = str_replace('float: right', 'text-align: right', $contract->content);
    $contract->content = str_replace('float: left', 'text-align: left', $contract->content);
    // Image center
    $contract->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $contract->content);

    if (file_exists(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_contractpdf.php')) {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_contractpdf.php');
    } else {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/contractpdf.php');
    }
    return $pdf;
}
/**
 * Generate payment pdf
 * @since  Version 1.0.1
 * @param  object $payment All payment data
 * @return mixed object
 */
function payment_pdf($payment, $tag = '')
{
    $CI =& get_instance();
    load_pdf_language($payment->invoice_data->clientid);
    $CI->load->library('pdf');

    $formatArray = get_pdf_format('pdf_format_payment');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false,false,'payment');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');

    if ($font_size == '') {
        $font_size = 10;
    }

    $swap = get_option('swap_pdf_info');
    $pdf->SetTitle(_l('payment') . ' #' . $payment->paymentid);
    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);
    $CI->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(1.53);
    $pdf->setJPEGQuality(100);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);

    if ($CI->input->get('print') == 'true') {
        // force print dialog
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    if (file_exists(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_paymentpdf.php')) {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_paymentpdf.php');
    } else {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/paymentpdf.php');
    }
    return $pdf;
}
/**
 * Calculate estimates percent by status
 * @param  mixed $status          estimate status
 * @param  mixed $total_estimates in case the total is calculated in other place
 * @return array
 */
function get_estimates_percent_by_status($status, $total_estimates = '')
{

    $has_permission_view = has_permission('estimates', '', 'view');

    if (!is_numeric($total_estimates)) {
        $where_total = array();
        if (!$has_permission_view) {
            $where_total['addedfrom'] = get_staff_user_id();
        }
        $total_estimates = total_rows('tblestimates', $where_total);
    }

    $data            = array();
    $total_by_status = 0;

    if (!is_numeric($status)) {
        if ($status == 'not_sent') {
            $total_by_status = total_rows('tblestimates', 'sent=0 AND status NOT IN(2,3,4)' . (!$has_permission_view ? ' AND addedfrom=' . get_staff_user_id() : ''));
        }
    } else {
        $where = array(
            'status' => $status
        );
        if (!$has_permission_view) {
            $where = array_merge($where, array(
                'addedfrom' => get_staff_user_id()
            ));
        }
        $total_by_status = total_rows('tblestimates', $where);
    }

    $percent                 = ($total_estimates > 0 ? number_format(($total_by_status * 100) / $total_estimates, 2) : 0);
    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_estimates;
    return $data;
}
/**
 * Calculate proposal percent by status
 * @param  mixed $status          proposal status
 * @param  mixed $total_estimates in case the total is calculated in other place
 * @return array
 */
function get_proposals_percent_by_status($status, $total_proposals = '')
{

    $has_permission_view = has_permission('proposals', '', 'view');

    if (!is_numeric($total_proposals)) {
        $where_total = array();
        if (!$has_permission_view) {
            $where_total['addedfrom'] = get_staff_user_id();
        }
        $total_proposals = total_rows('tblproposals', $where_total);
    }
    $data            = array();
    $total_by_status = 0;
    $where           = array(
        'status' => $status
    );
    if (!$has_permission_view) {
        $where = array_merge($where, array(
            'addedfrom' => get_staff_user_id()
        ));
    }
    $total_by_status = total_rows('tblproposals', $where);

    $percent = ($total_proposals > 0 ? number_format(($total_by_status * 100) / $total_proposals, 2) : 0);

    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_proposals;
    return $data;
}
/**
 * This function do not work with cancelled status
 * Calculate invoices percent by status
 * @param  mixed $status          estimate status
 * @param  mixed $total_invoices in case the total is calculated in other place
 * @return array
 */
function get_invoices_percent_by_status($status, $total_invoices = '')
{

    $has_permission_view = has_permission('invoices', '', 'view');

    if (!is_numeric($total_invoices)) {
        $where_total = 'status NOT IN(5)';
        if (!$has_permission_view) {
            $where_total .= ' AND addedfrom=' . get_staff_user_id();
        }
        $total_invoices = total_rows('tblinvoices', $where_total);
    }
    $data            = array();
    $total_by_status = 0;
    if (!is_numeric($status)) {
        if ($status == 'not_sent') {
            $total_by_status = total_rows('tblinvoices', 'sent=0 AND status NOT IN(2,5)' . (!$has_permission_view ? ' AND addedfrom=' . get_staff_user_id() : ''));
        }
    } else {
        $total_by_status = total_rows('tblinvoices', 'status = ' . $status . ' AND status NOT IN(5)' . (!$has_permission_view ? ' AND addedfrom=' . get_staff_user_id() : ''));
    }
    $percent                 = ($total_invoices > 0 ? number_format(($total_by_status * 100) / $total_invoices, 2) : 0);
    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_invoices;
    return $data;
}
/**
 * Load invoices total templates
 * This is the template where is showing the panels Outstanding Invoices, Paid Invoices and Past Due invoices
 * @return string
 */
function load_invoices_total_template(){

    $CI = &get_instance();
    $CI->load->model('invoices_model');
    $_data = $CI->input->post();
    if (!$CI->input->post('customer_id')) {
        $multiple_currencies = call_user_func('is_using_multiple_currencies');
    } else {
        $_data['customer_id'] = $CI->input->post('customer_id');
        $multiple_currencies  = call_user_func('is_client_using_multiple_currencies', $CI->input->post('customer_id'));
    }

    if ($CI->input->post('project_id')) {
        $_data['project_id'] = $CI->input->post('project_id');
    }

    if ($multiple_currencies) {
        $CI->load->model('currencies_model');
        $data['invoices_total_currencies'] = $CI->currencies_model->get();
    }

    $data['total_result'] = $CI->invoices_model->get_invoices_total($_data);
    $data['_currency']    = $data['total_result']['currencyid'];
    $CI->load->view('admin/invoices/invoices_total_template', $data);
}

/**
 * Return decimal places
 * The srcipt do not support more then 2 decimal places but developers can use action hook to change the decimal places
 * @return [type] [description]
 */
function get_decimal_places(){
    return do_action('app_decimal_places',2);
}
