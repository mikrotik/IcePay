<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Used for phpass_helper
define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', FALSE);
// Admin url
define('ADMIN_URL', 'admin');
// CRM server update url
define('UPDATE_URL','https://www.perfexcrm.com/perfex_updates/index.php');
// Get latest version info
define('UPDATE_INFO_URL','https://www.perfexcrm.com/perfex_updates/update_info.php');

// Defined folders
// CRM temporary path
define('TEMP_FOLDER',FCPATH .'temp' . '/');
// Database backups folder
define('BACKUPS_FOLDER',FCPATH.'backups'.'/');
// Customer attachments folder from profile
define('CLIENT_ATTACHMENTS_FOLDER',FCPATH.'uploads/clients'.'/');
// All tickets attachments
define('TICKET_ATTACHMENTS_FOLDER',FCPATH .'uploads/ticket_attachments' . '/');
// Company attachemnts, favicon,logo etc..
define('COMPANY_FILES_FOLDER',FCPATH .'uploads/company' . '/');
// Staff profile images
define('STAFF_PROFILE_IMAGES_FOLDER',FCPATH .'uploads/staff_profile_images' . '/');
// Contact profile images
define('CONTACT_PROFILE_IMAGES_FOLDER',FCPATH .'uploads/client_profile_images' . '/');
// Newsfeed attachments
define('NEWSFEED_FOLDER',FCPATH . 'uploads/newsfeed' . '/');
// Contracts attachments
define('CONTRACTS_UPLOADS_FOLDER',FCPATH . 'uploads/contracts' . '/');
// Tasks attachments
define('TASKS_ATTACHMENTS_FOLDER',FCPATH . 'uploads/tasks' . '/');
// Invoice attachments
define('INVOICE_ATTACHMENTS_FOLDER',FCPATH . 'uploads/invoices' . '/');
// Estimate attachments
define('ESTIMATE_ATTACHMENTS_FOLDER',FCPATH . 'uploads/estimates' . '/');
// Proposal attachments
define('PROPOSAL_ATTACHMENTS_FOLDER',FCPATH . 'uploads/proposals' . '/');
// Expenses receipts
define('EXPENSE_ATTACHMENTS_FOLDER',FCPATH . 'uploads/expenses' . '/');
// Lead attachments
define('LEAD_ATTACHMENTS_FOLDER',FCPATH . 'uploads/leads' . '/');
// Project files attachments
define('PROJECT_ATTACHMENTS_FOLDER',FCPATH . 'uploads/projects' . '/');
// Project discussions attachments
define('PROJECT_DISCUSSION_ATTACHMENT_FOLDER',FCPATH . 'uploads/discussions' . '/');
