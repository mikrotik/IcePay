<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Invoice_items_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Get invoice item by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */
    public function get($id = '')
    {
        $this->db->select('tblitems.id as itemid,rate,taxrate,tbltaxes.id as taxid,tbltaxes.name as taxname,description,long_description,group_id,tblitems_groups.name as group_name,unit');
        $this->db->from('tblitems');
        $this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax', 'left');
        $this->db->join('tblitems_groups', 'tblitems_groups.id = tblitems.group_id', 'left');
        $this->db->order_by('description', 'asc');
        if (is_numeric($id)) {
            $this->db->where('tblitems.id', $id);
            return $this->db->get()->row();
        }
        return $this->db->get()->result_array();
    }
    public function get_grouped()
    {

        $items = array();
        $this->db->order_by('name', 'asc');
        $groups = $this->db->get('tblitems_groups')->result_array();

        array_unshift($groups, array(
            'id' => 0,
            'name' => ''
        ));

        foreach ($groups as $group) {
            $this->db->select('*,tblitems_groups.name as group_name,tblitems.id as id');
            $this->db->where('group_id', $group['id']);
            $this->db->join('tblitems_groups', 'tblitems_groups.id = tblitems.group_id', 'left');
            $this->db->order_by('description', 'asc');
            $_items = $this->db->get('tblitems')->result_array();
            if (count($_items) > 0) {
                $items[$group['id']] = array();
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }
        return $items;
    }
    /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['itemid']);
        if ($data['tax'] == '') {
            unset($data['tax']);
        }
        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        $this->db->insert('tblitems', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Invoice Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');
            return $insert_id;
        }
        return false;
    }
    /**
     * Update invoiec item
     * @param  array $data Invoice data to update
     * @return boolean
     */
    public function edit($data)
    {
        $itemid = $data['itemid'];
        unset($data['itemid']);

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        $this->db->where('id', $itemid);
        $this->db->update('tblitems', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Invoice Item Updated [ID: ' . $itemid . ', ' . $data['description'] . ']');
            return true;
        }
        return false;
    }
    /**
     * Delete invoice item
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblitems');
        if ($this->db->affected_rows() > 0) {
            logActivity('Invoice Item Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
    public function get_groups()
    {
        $this->db->order_by('name', 'asc');
        return $this->db->get('tblitems_groups')->result_array();
    }
    public function add_group($data)
    {
        $this->db->insert('tblitems_groups', $data);
        logActivity('Items Group Created [Name: ' . $data['name'] . ']');
        return $this->db->insert_id();
    }
    public function edit_group($data, $id)
    {

        $this->db->where('id', $id);
        $this->db->update('tblitems_groups', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Items Group Updated [Name: ' . $data['name'] . ']');
            return true;
        }

        return false;
    }
    public function delete_group($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get('tblitems_groups')->row();

        if ($group) {
            $this->db->where('group_id', $id);
            $this->db->update('tblitems', array(
                'group_id' => 0
            ));

            $this->db->where('id', $id);
            $this->db->delete('tblitems_groups');

            logActivity('Item Group Deleted [Name: ' . $group->name . ']');
            return true;
        }

        return false;
    }
    /**
     * Get invoice items - ajax call for autocomplete when adding invoicei tems
     * @param  mixed $data query
     * @return array
     */
    public function get_all_items_ajax()
    {
        $this->db->select('tblitems.id as itemid,rate,taxrate,tbltaxes.id as taxid,tbltaxes.name as taxname,description as label,long_description,unit');
        $this->db->from('tblitems');
        $this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax', 'left');
        $this->db->order_by('description', 'asc');
        return $this->db->get()->result_array();
    }
}
