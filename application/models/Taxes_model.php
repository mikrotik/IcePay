<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Taxes_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Get tax by id
     * @param  mixed $id tax id
     * @return mixed     if id passed return object else array
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tbltaxes')->row();
        }
        $this->db->order_by('taxrate', 'ASC');
        return $this->db->get('tbltaxes')->result_array();
    }
    /**
     * Add new tax
     * @param array $data tax data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['taxid']);
        $this->db->insert('tbltaxes', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Tax Added [ID: ' . $insert_id . ', ' . $data['name'] . ']');
            return true;
        }
        return false;
    }
    /**
     * Edit tax
     * @param  array $data tax data
     * @return boolean
     */
    public function edit($data)
    {
        if (total_rows('tblexpenses', array(
            'tax' => $data['taxid']
        )) > 0) {
            return array(
                'tax_is_using_expenses' => true
            );
        }
        $taxid        = $data['taxid'];
        $original_tax = get_tax_by_id($taxid);
        unset($data['taxid']);
        $this->db->where('id', $taxid);
        $this->db->update('tbltaxes', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Tax Updated [ID: ' . $taxid . ', ' . $data['name'] . ']');
            // Check if this task is used in settings
            $default_taxes = get_option('default_tax');
            $tax_name      = $original_tax->name . '|' . $original_tax->taxrate;
            if (strpos($default_taxes, $tax_name) !== false) {
                $default_taxes = str_ireplace($tax_name, $data['name'] . '|' . $data['taxrate'], $default_taxes);
                update_option('default_tax', $default_taxes);
            }
            return true;
        }
        return false;
    }
    /**
     * Delete tax from database
     * @param  mixed $id tax id
     * @return boolean
     */
    public function delete($id)
    {
        if (is_reference_in_table('tax', 'tblitems', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tbltaxes');
        if ($this->db->affected_rows() > 0) {
            logActivity('Tax Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
}
