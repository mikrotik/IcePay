<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Utilities_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Add new event
     * @param array $data event $_POST data
     */
    public function event($data)
    {
        $data['userid'] = get_staff_user_id();
        $data['start']  = to_sql_date($data['start'], true);
        if ($data['end'] == '') {
            unset($data['end']);
        } else {
            $data['end'] = to_sql_date($data['end'], true);
        }
        if (isset($data['public'])) {
            $data['public'] = 1;
        } else {
            $data['public'] = 0;
        }
        $data['description'] = nl2br($data['description']);
        if (isset($data['eventid'])) {
            unset($data['userid']);
            $this->db->where('eventid', $data['eventid']);
            $event = $this->db->get('tblevents')->row();
            if (!$event) {
                return false;
            }
            if ($event->isstartnotified == 1) {
                if ($data['start'] > $event->start) {
                    $data['isstartnotified'] = 0;
                }
            }
            $this->db->where('eventid', $data['eventid']);
            $this->db->update('tblevents', $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        } else {
            $this->db->insert('tblevents', $data);
            $insert_id = $this->db->insert_id();
        }
        if ($insert_id) {
            return true;
        }
        return false;
    }
    /**
     * Get event by passed id
     * @param  mixed $id eventid
     * @return object
     */
    public function get_event_by_id($id)
    {
        $this->db->where('eventid', $id);
        return $this->db->get('tblevents')->row();
    }
    /**
     * Get all user events
     * @return array
     */
    public function get_all_events()
    {
        $is_staff_member = is_staff_member();
        $this->db->select('title,start,end,eventid,userid,color,public');
        // Check if is passed start and end date
        // $this->db->where('(start BETWEEN "' . $this->input->get('start') . '" AND "' . $this->input->get('end') . '")');
        $this->db->where('userid', get_staff_user_id());
        if ($is_staff_member) {
            $this->db->or_where('public', 1);
        }
        return $this->db->get('tblevents')->result_array();
    }
    public function get_event($id)
    {
        $this->db->where('eventid', $id);
        return $this->db->get('tblevents')->row();
    }
    public function get_calendar_data($client_id = '', $contact_id = '')
    {
        $is_admin                     = is_admin();
        $has_permission_invoices      = has_permission('invoices', '', 'view');
        $has_permission_invoices_own  = has_permission('invoices', '', 'view_own');
        $has_permission_estimates     = has_permission('estimates', '', 'view');
        $has_permission_estimates_own = has_permission('estimates', '', 'view_own');
        $has_permission_contracts     = has_permission('contracts', '', 'view');
        $has_permission_contracts_own = has_permission('contracts', '', 'view_own');
        $has_permission_proposals     = has_permission('proposals', '', 'view');
        $has_permission_proposals_own = has_permission('proposals', '', 'view_own');
        $data                         = array();

        $client_data = false;
        if (is_numeric($client_id) && is_numeric($contact_id)) {
            $client_data                      = true;
            $has_contact_permission_invoices  = has_contact_permission('invoices', $contact_id);
            $has_contact_permission_estimates = has_contact_permission('estimates', $contact_id);
            $has_contact_permission_proposals = has_contact_permission('proposals', $contact_id);
            $has_contact_permission_contracts = has_contact_permission('contracts', $contact_id);
            $has_contact_permission_projects  = has_contact_permission('projects', $contact_id);
        }

        $hook_data = array(
            'data' => $data,
            'client_data' => $client_data
        );

        if ($client_data == true) {
            $hook_data['client_id']  = $client_id;
            $hook_data['contact_id'] = $contact_id;
        }

        $hook_data = do_action('before_fetch_events', $hook_data);
        $data      = $hook_data['data'];

        if (get_option('show_invoices_on_calendar') == 1) {
            $this->db->select('duedate as date,number,id,clientid,hash');
            $this->db->from('tblinvoices');
            $this->db->where_not_in('status', array(
                2,
                5
            ));
            $this->db->where('duedate IS NOT NULL');
            // $this->db->where('(duedate BETWEEN "' . $this->input->get('start') . '" AND "' . $this->input->get('end') . '")');
            if ($client_data) {
                $this->db->where('clientid', $client_id);

                if (get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                    $this->db->where('status !=', 6);
                }

            } else {
                if (!$has_permission_invoices) {
                    $this->db->where('addedfrom', get_staff_user_id());
                }
            }
            $invoices = $this->db->get()->result_array();
            foreach ($invoices as $invoice) {
                if (!$has_permission_invoices && !$has_permission_invoices_own && !$client_data) {
                    continue;
                } else if ($client_data && !$has_contact_permission_invoices) {
                    continue;
                }

                $rel_showcase = '';
                if (!$client_data) {
                    $this->db->select('CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company');
                    $this->db->where('userid', $invoice['clientid']);
                    $rel_showcase = ' (' . $this->db->get('tblclients')->row()->company . ')';
                }

                $number              = format_invoice_number($invoice['id']);
                $invoice['_tooltip'] = _l('calendar_invoice') . ' - ' . $number . $rel_showcase;
                $invoice['title']    = $number;
                $invoice['color']    = get_option('calendar_invoice_color');
                if (!$client_data) {
                    $invoice['url'] = admin_url('invoices/list_invoices/' . $invoice['id']);
                } else {
                    $invoice['url'] = site_url('viewinvoice/' . $invoice['id'] . '/' . $invoice['hash']);
                }
                array_push($data, $invoice);
            }
        }
        if (get_option('show_estimates_on_calendar') == 1) {
            $this->db->select('expirydate as date,number,id,clientid,hash');
            $this->db->from('tblestimates');
            $this->db->where('status !=', 3);
            $this->db->where('status !=', 4);
            $this->db->where('expirydate IS NOT NULL');


            // $this->db->where('(expirydate BETWEEN "' . $this->input->get('start') . '" AND "' . $this->input->get('end') . '")');

            if ($client_data) {
                $this->db->where('clientid', $client_id);

                if (get_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
                    $this->db->where('status !=', 1);
                }
            } else {
                if (!$has_permission_estimates) {
                    $this->db->where('addedfrom', get_staff_user_id());
                }
            }

            $estimates = $this->db->get()->result_array();
            foreach ($estimates as $estimate) {

                if (!$has_permission_estimates && !$has_permission_estimates_own && !$client_data) {
                    continue;
                } else if ($client_data && !$has_contact_permission_estimates) {
                    continue;
                }

                $rel_showcase = '';
                if (!$client_data) {
                    $this->db->select('CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company');
                    $this->db->where('userid', $estimate['clientid']);
                    $rel_showcase = ' (' . $this->db->get('tblclients')->row()->company . ')';
                }

                $number               = format_estimate_number($estimate['id']);
                $estimate['_tooltip'] = _l('calendar_estimate') . ' - ' . $number . $rel_showcase;
                $estimate['title']    = $number;
                $estimate['color']    = get_option('calendar_estimate_color');
                if (!$client_data) {
                    $estimate['url'] = admin_url('estimates/list_estimates/' . $estimate['id']);
                } else {
                    $estimate['url'] = site_url('viewestimate/' . $estimate['id'] . '/' . $estimate['hash']);
                }
                array_push($data, $estimate);
            }
        }
        if (get_option('show_proposals_on_calendar') == 1) {
            $this->db->select('open_till as date,subject,id,hash');
            $this->db->from('tblproposals');
            $this->db->where('status !=', 2);
            $this->db->where('status !=', 3);
            $this->db->where('open_till IS NOT NULL');
            //  $this->db->where('(open_till BETWEEN "' . $this->input->get('start') . '" AND "' . $this->input->get('end') . '")');

            if ($client_data) {
                $this->db->where('rel_type', 'customer');
                $this->db->where('rel_id', $contact_id);
            } else {
                if (!$has_permission_proposals) {
                    $this->db->where('addedfrom', get_staff_user_id());
                }
            }

            $proposals = $this->db->get()->result_array();
            foreach ($proposals as $proposal) {

                if (!$has_permission_proposals && !$has_permission_proposals_own && !$client_data) {
                    continue;
                } else if ($client_data && !$has_contact_permission_proposals) {
                    continue;
                }

                $proposal['_tooltip'] = _l('proposal');
                $proposal['title']    = $proposal['subject'];
                $proposal['color']    = get_option('calendar_proposal_color');
                if (!$client_data) {
                    $proposal['url'] = admin_url('proposals/list_proposals/' . $proposal['id']);
                } else {
                    $proposal['url'] = site_url('viewproposal/' . $proposal['id'] . '/' . $proposal['hash']);
                }
                array_push($data, $proposal);
            }
        }

        if (get_option('show_tasks_on_calendar') == 1) {

            $client_projects = array();
            if ($client_data) {
                $this->db->where('clientid', $client_id);
                $projects = $this->db->get('tblprojects')->result_array();
                foreach ($projects as $p) {
                    array_push($client_projects, $p['id']);
                }
                // If no client projects found add some fake data for the where to prevent showing any tasks and error in the query
                if (count($client_projects) == 0) {
                    array_push($client_projects, '"' . uniqid() . '"');
                }
            }

            $this->db->select('duedate as date,name as title,id,rel_id,rel_type');
            $this->db->from('tblstafftasks');
            $this->db->where('status !=', 5);
            $this->db->where('duedate IS NOT NULL');
            if ($client_data) {
                $this->db->where('rel_type', 'project');
                $this->db->where('rel_id IN (' . implode(', ', $client_projects) . ')');
                $this->db->where('rel_id IN (SELECT project_id FROM tblprojectsettings WHERE name="view_tasks" AND value=1)');
                $this->db->where('visible_to_client', 1);
            }
            //  $this->db->where('(duedate BETWEEN "' . $this->input->get('start') . '" AND "' . $this->input->get('end') . '")');
            if (!$is_admin && !$client_data) {
                $this->db->where('(id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ') OR id IN (SELECT taskid FROM tblstafftasksfollowers WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id() . ' OR is_public = 1)');
            }
            $tasks = $this->db->get()->result_array();

            foreach ($tasks as $task) {
                $rel_showcase = '';
                if (!empty($task['rel_id']) && !$client_data) {
                    $task_rel_data  = get_relation_data($task['rel_type'], $task['rel_id']);
                    $task_rel_value = get_relation_values($task_rel_data, $task['rel_type']);
                    $rel_showcase   = ' (' . $task_rel_value['name'] . ')';
                }
                $name             = mb_substr($task['title'], 0, 60);
                $task['_tooltip'] = _l('calendar_task') . ' - ' . $name . $rel_showcase;
                $task['title']    = $name;
                $task['color']    = get_option('calendar_task_color');
                if (!$client_data) {
                    $task['onclick'] = 'init_task_modal(' . $task['id'] . '); return false';
                    $task['url']     = '#';
                } else {
                    $task['url'] = site_url('clients/project/' . $task['rel_id'] . '?group=project_tasks&taskid=' . $task['id']);
                }
                array_push($data, $task);
            }
        }

        $available_reminders = $this->perfex_base->get_available_reminders_keys();
        if (!$client_data) {
            foreach ($available_reminders as $key) {
                if (get_option('show_' . $key . '_reminders_on_calendar') == 1) {
                    $this->db->select('date,description,firstname,lastname,creator,staff,rel_id')->from('tblreminders')->where('isnotified', 0)->where('rel_type', $key)->join('tblstaff', 'tblstaff.staffid = tblreminders.staff');
                    $reminders = $this->db->get()->result_array();
                    foreach ($reminders as $reminder) {
                        if ((get_staff_user_id() == $reminder['creator'] || get_staff_user_id() == $reminder['staff']) || $is_admin) {
                            $_reminder['title'] = '';
                            if (get_staff_user_id() != $reminder['staff']) {
                                $_reminder['title'] .= '(' . $reminder['firstname'] . ' ' . $reminder['lastname'] . ') ';
                            }
                            $name                  = mb_substr($reminder['description'], 0, 60);
                            $_reminder['_tooltip'] = _l('calendar_' . $key . '_reminder') . ' - ' . $name;
                            $_reminder['title'] .= $name;
                            $_reminder['date']  = $reminder['date'];
                            $_reminder['color'] = get_option('calendar_reminder_color');
                            if ($key == 'customer') {
                                $url = admin_url('clients/client/' . $reminder['rel_id']);
                            } else if ($key == 'invoice') {
                                $url = admin_url('invoices/list_invoices/' . $reminder['rel_id']);
                            } else if ($key == 'estimate') {
                                $url = admin_url('estimates/list_estimates/' . $reminder['rel_id']);
                            } else if ($key == 'lead') {
                                $url = admin_url('leads/index/' . $reminder['rel_id']);
                            } else if ($key == 'proposal') {
                                $url = admin_url('proposals/list_proposals/' . $reminder['rel_id']);
                            } else if ($key == 'expense') {
                                $url = 'expenses/list_expenses/' . $reminder['rel_id'];
                            }
                            $_reminder['url'] = $url;
                            array_push($data, $_reminder);
                        }
                    }
                }
            }
        }

        if (get_option('show_contracts_on_calendar') == 1) {
            $this->db->select('subject as title,dateend,datestart,id,client,content');
            $this->db->from('tblcontracts');
            $this->db->where('trash', 0);
            if ($client_data) {
                $this->db->where('client', $client_id);
                $this->db->where('not_visible_to_client', 0);
            } else {
                if (!$has_permission_contracts) {
                    $this->db->where('addedfrom', get_staff_user_id());
                }
            }
            // $this->db->where('(dateend > "' . date('Y-m-d') . '" AND dateend IS NOT NULL AND dateend BETWEEN "' . $this->input->get('start') . '" AND "' . $this->input->get('end') . '")');
            $this->db->where('dateend > "' . date('Y-m-d') . '" AND dateend IS NOT NULL');
            $this->db->or_where('datestart >"' . date('Y-m-d') . '"');

            $contracts = $this->db->get()->result_array();

            foreach ($contracts as $contract) {

                if (!$has_permission_contracts && !$has_permission_contracts_own && !$client_data) {
                    continue;
                } else if ($client_data && !$has_contact_permission_contracts) {
                    continue;
                }

                $rel_showcase = '';
                if (!$client_data) {
                    $this->db->select('CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company');
                    $this->db->where('userid', $contract['client']);
                    $rel_showcase = ' (' . $this->db->get('tblclients')->row()->company . ')';
                }

                $name                  = $contract['title'];
                $_contract['title']    = $name;
                $_contract['color']    = get_option('calendar_contract_color');
                $_contract['_tooltip'] = _l('calendar_contract') . ' - ' . $name . $rel_showcase;
                if (!$client_data) {
                    $_contract['url'] = admin_url('contracts/contract/' . $contract['id']);
                } else {
                    if (empty($contract['content'])) {
                        // No url for contracts
                        $_contract['url'] = '#';
                    } else {
                        $_contract['url'] = site_url('clients/contract_pdf/' . $contract['id']);
                    }

                }
                if (!empty($contract['dateend'])) {
                    $_contract['date'] = $contract['dateend'];
                } else {
                    $_contract['date'] = $contract['datestart'];
                }
                array_push($data, $_contract);
            }
        }
        //calendar_project
        if (get_option('show_projects_on_calendar') == 1) {
            $this->load->model('projects_model');
            $this->db->select('name as title,deadline,start_date,id,clientid');
            $this->db->from('tblprojects');
            if ($client_data) {
                $this->db->where('clientid', $client_id);
            }
            $projects = $this->db->get()->result_array();
            foreach ($projects as $project) {
                $rel_showcase = '';

                if (!$client_data) {
                    if (!$this->projects_model->is_member($project['id']) && !$is_admin) {
                        continue;
                    }
                    $this->db->select('CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company');
                    $this->db->where('userid', $project['clientid']);
                    $rel_showcase = ' (' . $this->db->get('tblclients')->row()->company . ')';
                } else {
                    if (!$has_contact_permission_projects) {
                        continue;
                    }
                }

                $name                 = $project['title'];
                $_project['title']    = $name;
                $_project['color']    = get_option('calendar_project_color');
                $_project['_tooltip'] = _l('calendar_project') . ' - ' . $name . $rel_showcase;
                if (!$client_data) {
                    $_project['url'] = admin_url('projects/view/' . $project['id']);
                } else {
                    $_project['url'] = site_url('clients/project/' . $project['id']);
                }

                if ($project['deadline']) {
                    $_project['date'] = $project['deadline'];
                } else {
                    $_project['date'] = $project['start_date'];
                }

                array_push($data, $_project);
            }
        }
        if (!$client_data) {
            $events = $this->get_all_events();
            foreach ($events as $event) {
                if ($event['userid'] != get_staff_user_id() && !$is_admin) {
                    $event['is_not_creator'] = true;
                    $event['onclick']        = true;
                }
                $event['_tooltip'] = _l('calendar_event') . ' - ' . $event['title'];
                $event['color']    = $event['color'];
                array_push($data, $event);
            }
        }
        return $data;
    }
    /**
     * Delete user event
     * @param  mixed $id event id
     * @return boolean
     */
    public function delete_event($id)
    {
        $this->db->where('eventid', $id);
        $this->db->delete('tblevents');
        if ($this->db->affected_rows() > 0) {
            logActivity('Event Deleted [' . $id . ']');
            return true;
        }
        return false;
    }
}
