<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Check if client id is used in the system
 * @param  mixed  $id client id
 * @return boolean
 */
function is_client_id_used($id)
{

    $total = 0;
    $total += total_rows('tblcontracts', array(
        'client' => $id
    ));
    $total += total_rows('tblestimates', array(
        'clientid' => $id
    ));
    $total += total_rows('tblexpenses', array(
        'clientid' => $id
    ));
    $total += total_rows('tblinvoices', array(
        'clientid' => $id
    ));
    $total += total_rows('tblproposals', array(
        'rel_id' => $id,
        'rel_type' => 'customer'
    ));
    $total += total_rows('tbltickets', array(
        'userid' => $id
    ));
    $total += total_rows('tblprojects', array(
        'clientid' => $id
    ));
    $total += total_rows('tblstafftasks', array(
        'rel_id' => $id,
        'rel_type' => 'customer'
    ));

    if ($total > 0) {
        return true;
    }

    return false;
}
/**
 * On each update there is message/code inserted in the database
 */
function show_just_updated_message()
{
    if (get_option('update_info_message') != '') {
        if (is_admin()) {
            $message = get_option('update_info_message');
            update_option('update_info_message', '');
            echo $message;
        }
    }
}
/**
 * Show message on dashboard when environment is set to development or testing
 * @return void
 */
function show_development_mode_message()
{
    if (ENVIRONMENT == 'development' || ENVIRONMENT == 'testing') {
        if(is_admin()){
            echo '<div class="col-md-12">';
            echo '<div class="alert alert-warning">';
            echo 'Environment set to <b>' . ENVIRONMENT . '</b>. Don\'t forget to set back to <b>production</b> in the main index.php file after finishing your tests.';
            echo '</div>';
            echo '</div>';
        }
    }
}
/**
 * CHeck missing key from the main english language
 * @param  string $language language to check
 * @return void
 */
function check_missing_language_strings($language)
{
    $langs = array();
    $CI =& get_instance();
    $CI->lang->load('english_lang', 'english');
    $english       = $CI->lang->language;
    $langs[]       = array(
        'english' => $english
    );
    $original      = $english;
    $keys_original = array();
    foreach ($original as $k => $val) {
        $keys_original[$k] = true;
    }
    $CI->lang->is_loaded = array();
    $CI->lang->language  = array();
    $CI->lang->load($language . '_lang', $language);
    $$language           = $CI->lang->language;
    $langs[]             = array(
        $language => $$language
    );
    $CI->lang->is_loaded = array();
    $CI->lang->language  = array();
    $missing_keys        = array();
    for ($i = 0; $i < count($langs); $i++) {
        foreach ($langs[$i] as $lang => $data) {
            if ($lang != 'english') {
                $keys_current = array();
                foreach ($data as $k => $v) {
                    $keys_current[$k] = true;
                }
                foreach ($keys_original as $k_original => $val_original) {
                    if (!array_key_exists($k_original, $keys_current)) {
                        $keys_missing = true;
                        array_push($missing_keys, $k_original);
                        echo '<b>Missing language key</b> from language:' . $lang . ' - <b>key</b>:' . $k_original . '<br />';
                    }
                }
            }
        }
    }
    if (isset($keys_missing)) {
        echo '<br />--<br />Language keys missing please create <a href="https://www.perfexcrm.com/knowledgebase/overwrite-translation-text/" target="_blank">custom_lang.php</a> and add the keys listed above.';
        echo '<br /> Here is how you should add the keys (You can just copy paste this text above and add your translations)<br /><br />';
        foreach ($missing_keys as $key) {
            echo '$lang[\'' . $key . '\'] = \'Add your translation\';<br />';
        }
    } else {
        echo '<h1>No Missing Language Keys Found</h1>';
    }
    die;
}
/**
 * Parse email template with the merge fields
 * @param  mixed $template     template
 * @param  array  $merge_fields
 * @return object
 */
function parse_email_template($template, $merge_fields = array())
{
    $CI =& get_instance();
    if (!is_object($template) || $CI->input->post('template_name')) {
        $original_template = $template;
        if ($CI->input->post('template_name')) {
            $template = $CI->input->post('template_name');
        }
        $CI->db->where('slug', $template);
        $template = $CI->db->get('tblemailtemplates')->row();

        if ($CI->input->post('email_template_custom')) {
            $template->message = $CI->input->post('email_template_custom', FALSE);
            // Replace the subject too
            $template->subject = $original_template->subject;
        }
    }
    $merge_fields = array_merge($merge_fields, get_other_merge_fields());
    foreach ($merge_fields as $key => $val) {
        if (stripos($template->message, $key) !== false) {
            $template->message = str_ireplace($key, $val, $template->message);
        } else {
            $template->message = str_ireplace($key, '', $template->message);
        }
        if (stripos($template->fromname, $key) !== false) {
            $template->fromname = str_ireplace($key, $val, $template->fromname);
        } else {
            $template->fromname = str_ireplace($key, '', $template->fromname);
        }
        if (stripos($template->subject, $key) !== false) {
            $template->subject = str_ireplace($key, $val, $template->subject);
        } else {
            $template->subject = str_ireplace($key, '', $template->subject);
        }
    }
    return $template;
}

/**
 * Get system favourite colors
 * @return array
 */
function get_system_favourite_colors()
{
    // dont delete any of these colors are used all over the system
    $colors = array(
        '#28B8DA',
        '#03a9f4',
        '#c53da9',
        '#757575',
        '#8e24aa',
        '#d81b60',
        '#0288d1',
        '#7cb342',
        '#fb8c00',
        '#84C529',
        '#fb3b3b'
    );
    $colors = do_action('get_system_favourite_colors', $colors);
    return $colors;
}
/**
 * Get goal types for the goals feature
 * @return array
 */
function get_goal_types()
{
    $types = array(
        array(
            'key' => 1,
            'lang_key' => 'goal_type_total_income',
            'subtext' => 'goal_type_income_subtext'
        ),
        array(
            'key' => 2,
            'lang_key' => 'goal_type_convert_leads'
        ),
        array(
            'key' => 3,
            'lang_key' => 'goal_type_increase_customers_without_leads_conversions',
            'subtext' => 'goal_type_increase_customers_without_leads_conversions_subtext'
        ),
        array(
            'key' => 4,
            'lang_key' => 'goal_type_increase_customers_with_leads_conversions',
            'subtext' => 'goal_type_increase_customers_with_leads_conversions_subtext'
        ),
        array(
            'key' => 5,
            'lang_key' => 'goal_type_make_contracts_by_type_calc_database',
            'subtext' => 'goal_type_make_contracts_by_type_calc_database_subtext'
        ),
        array(
            'key' => 7,
            'lang_key' => 'goal_type_make_contracts_by_type_calc_date',
            'subtext' => 'goal_type_make_contracts_by_type_calc_date_subtext'
        ),
        array(
            'key' => 6,
            'lang_key' => 'goal_type_total_estimates_converted',
            'subtext' => 'goal_type_total_estimates_converted_subtext'
        )
    );
    return do_action('get_goal_types', $types);
}
/**
 * Translate goal type based on passed key
 * @param  mixed $key
 * @return string
 */
function format_goal_type($key)
{
    foreach (get_goal_types() as $type) {
        if ($type['key'] == $key) {
            return _l($type['lang_key']);
        }
    }
    return $type;
}
/**
 * Set session alert / flashdata
 * @param string $type    Alert type
 * @param string $message Alert message
 */
function set_alert($type, $message)
{
    $CI =& get_instance();
    $CI->session->set_flashdata('message-' . $type, $message);
}
/**
 * Redirect to blank page
 * @param  string $message Alert message
 * @param  string $alert   Alert type
 */
function blank_page($message = '', $alert = 'danger')
{
    set_alert($alert, $message);
    redirect(admin_url('not_found'));
}
/**
 * Redirect to access danied page and log activity
 * @param  string $permission If permission based to check where user tried to acces
 */
function access_denied($permission = '')
{
    set_alert('danger', _l('access_denied'));
    logActivity('Tried to access page where dont have permission [' . $permission . ']');
    if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        redirect($_SERVER['HTTP_REFERER']);
    } else {
        redirect(admin_url('access_denied'));
    }
}
/**
 * Set debug message - message wont be hidden in X seconds from javascript
 * @since  Version 1.0.1
 * @param string $message debug message
 */
function set_debug_alert($message)
{
    $CI =& get_instance();
    $CI->session->set_flashdata('debug', $message);
}
/**
 * Available date formats
 * @return array
 */
function get_available_date_formats()
{
    $date_formats = array(
        'd-m-Y|%d-%m-%Y' => 'd-m-Y',
        'd/m/Y|%d/%m/%Y' => 'd/m/Y',
        'm-d-Y|%m-%d-%Y' => 'm-d-Y',
        'm.d.Y|%m.%d.%Y' => 'm.d.Y',
        'm/d/Y|%m/%d/%Y' => 'm/d/Y',
        'Y-m-d|%Y-%m-%d' => 'Y-m-d',
        'd.m.Y|%d.%m.%Y' => 'd.m.Y'
    );
    return do_action('get_available_date_formats', $date_formats);
}
/**
 * Get weekdays as array
 * @return array
 */
function get_weekdays()
{
    return array(
        _l('wd_monday'),
        _l('wd_tuesday'),
        _l('wd_wednesday'),
        _l('wd_thursday'),
        _l('wd_friday'),
        _l('wd_saturday'),
        _l('wd_sunday')
    );
}
/**
 * Get non translated week days for query help
 * Do not edit this
 * @return array
 */
function get_weekdays_original()
{
    return array(
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
    );
}
/**
 * Short Time ago function
 * @param  datetime $time_ago
 * @return mixed
 */
function time_ago($time_ago)
{
    $time_ago     = strtotime($time_ago);
    $cur_time     = time();
    $time_elapsed = $cur_time - $time_ago;
    $seconds      = $time_elapsed;
    $minutes      = round($time_elapsed / 60);
    $hours        = round($time_elapsed / 3600);
    $days         = round($time_elapsed / 86400);
    $weeks        = round($time_elapsed / 604800);
    $months       = round($time_elapsed / 2600640);
    $years        = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
        return _l('time_ago_just_now');
    }
    //Minutes
    else if ($minutes <= 60) {
        if ($minutes == 1) {
            return _l('time_ago_minute');
        } else {
            return _l('time_ago_minutes', $minutes);
        }
    }
    //Hours
    else if ($hours <= 24) {
        if ($hours == 1) {
            return _l('time_ago_hour');
        } else {
            return _l('time_ago_hours', $hours);
        }
    }
    //Days
    else if ($days <= 7) {
        if ($days == 1) {
            return _l('time_ago_yesterday');
        } else {
            return _l('time_ago_days', $days);
        }
    }
    //Weeks
    else if ($weeks <= 4.3) {
        if ($weeks == 1) {
            return _l('time_ago_week');
        } else {
            return _l('time_ago_weeks', $weeks);
        }
    }
    //Months
    else if ($months <= 12) {
        if ($months == 1) {
            return _l('time_ago_month');
        } else {
            return _l('time_ago_months', $months);
        }
    }
    //Years
    else {
        if ($years == 1) {
            return _l('time_ago_year');
        } else {
            return _l('time_ago_years', $years);
        }
    }
}

/**
 * Slug function
 * @param  string $str
 * @param  array  $options Additional Options
 * @return mixed
 */
function slug_it($str, $options = array())
{
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str      = mb_convert_encoding((string) $str, 'UTF-8', mb_list_encodings());
    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(
            '
            /\b(ѓ)\b/i' => 'gj',
            '/\b(ч)\b/i' => 'ch',
            '/\b(ш)\b/i' => 'sh',
            '/\b(љ)\b/i' => 'lj'
        ),
        'transliterate' => true
    );
    // Merge options
    $options  = array_merge($defaults, $options);
    $char_map = array(
        // Latin
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ð' => 'D',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ő' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ű' => 'U',
        'Ý' => 'Y',
        'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'd',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'ý' => 'y',
        'þ' => 'th',
        'ÿ' => 'y',
        // Latin symbols
        '©' => '(c)',
        // Greek
        'Α' => 'A',
        'Β' => 'B',
        'Γ' => 'G',
        'Δ' => 'D',
        'Ε' => 'E',
        'Ζ' => 'Z',
        'Η' => 'H',
        'Θ' => '8',
        'Ι' => 'I',
        'Κ' => 'K',
        'Λ' => 'L',
        'Μ' => 'M',
        'Ν' => 'N',
        'Ξ' => '3',
        'Ο' => 'O',
        'Π' => 'P',
        'Ρ' => 'R',
        'Σ' => 'S',
        'Τ' => 'T',
        'Υ' => 'Y',
        'Φ' => 'F',
        'Χ' => 'X',
        'Ψ' => 'PS',
        'Ω' => 'W',
        'Ά' => 'A',
        'Έ' => 'E',
        'Ί' => 'I',
        'Ό' => 'O',
        'Ύ' => 'Y',
        'Ή' => 'H',
        'Ώ' => 'W',
        'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a',
        'β' => 'b',
        'γ' => 'g',
        'δ' => 'd',
        'ε' => 'e',
        'ζ' => 'z',
        'η' => 'h',
        'θ' => '8',
        'ι' => 'i',
        'κ' => 'k',
        'λ' => 'l',
        'μ' => 'm',
        'ν' => 'n',
        'ξ' => '3',
        'ο' => 'o',
        'π' => 'p',
        'ρ' => 'r',
        'σ' => 's',
        'τ' => 't',
        'υ' => 'y',
        'φ' => 'f',
        'χ' => 'x',
        'ψ' => 'ps',
        'ω' => 'w',
        'ά' => 'a',
        'έ' => 'e',
        'ί' => 'i',
        'ό' => 'o',
        'ύ' => 'y',
        'ή' => 'h',
        'ώ' => 'w',
        'ς' => 's',
        'ϊ' => 'i',
        'ΰ' => 'y',
        'ϋ' => 'y',
        'ΐ' => 'i',
        // Turkish
        'Ş' => 'S',
        'İ' => 'I',
        'Ç' => 'C',
        'Ü' => 'U',
        'Ö' => 'O',
        'Ğ' => 'G',
        'ş' => 's',
        'ı' => 'i',
        'ç' => 'c',
        'ü' => 'u',
        'ö' => 'o',
        'ğ' => 'g',
        // Russian
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'Yo',
        'Ж' => 'Zh',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'J',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'C',
        'Ч' => 'Ch',
        'Ш' => 'Sh',
        'Щ' => 'Sh',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'j',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sh',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
        // Ukrainian
        'Є' => 'Ye',
        'І' => 'I',
        'Ї' => 'Yi',
        'Ґ' => 'G',
        'є' => 'ye',
        'і' => 'i',
        'ї' => 'yi',
        'ґ' => 'g',
        // Czech
        'Č' => 'C',
        'Ď' => 'D',
        'Ě' => 'E',
        'Ň' => 'N',
        'Ř' => 'R',
        'Š' => 'S',
        'Ť' => 'T',
        'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c',
        'ď' => 'd',
        'ě' => 'e',
        'ň' => 'n',
        'ř' => 'r',
        'š' => 's',
        'ť' => 't',
        'ů' => 'u',
        'ž' => 'z',
        // Polish
        'Ą' => 'A',
        'Ć' => 'C',
        'Ę' => 'e',
        'Ł' => 'L',
        'Ń' => 'N',
        'Ó' => 'o',
        'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a',
        'ć' => 'c',
        'ę' => 'e',
        'ł' => 'l',
        'ń' => 'n',
        'ó' => 'o',
        'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',
        // Latvian
        'Ā' => 'A',
        'Č' => 'C',
        'Ē' => 'E',
        'Ģ' => 'G',
        'Ī' => 'i',
        'Ķ' => 'k',
        'Ļ' => 'L',
        'Ņ' => 'N',
        'Š' => 'S',
        'Ū' => 'u',
        'Ž' => 'Z',
        'ā' => 'a',
        'č' => 'c',
        'ē' => 'e',
        'ģ' => 'g',
        'ī' => 'i',
        'ķ' => 'k',
        'ļ' => 'l',
        'ņ' => 'n',
        'š' => 's',
        'ū' => 'u',
        'ž' => 'z'
    );
    // Make custom replacements
    $str      = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
    // Transliterate characters to ASCII
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }
    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);
    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}
/**
 * Get projcet billing type
 * @param  mixed $project_id
 * @return mixed
 */
function get_project_billing_type($project_id)
{
    $CI =& get_instance();
    $CI->db->where('id', $project_id);
    $project = $CI->db->get('tblprojects')->row();
    if ($project) {
        return $project->billing_type;
    }
    return false;
}
/**
 * Get client id by lead id
 * @since  Version 1.0.1
 * @param  mixed $id lead id
 * @return mixed     client id
 */
function get_client_id_by_lead_id($id)
{
    $CI =& get_instance();
    $CI->db->select('userid')->from('tblclients')->where('leadid', $id);
    return $CI->db->get()->row()->userid;
}
/**
 * Check if the user is lead creator
 * @since  Version 1.0.4
 * @param  mixed  $leadid leadid
 * @param  mixed  $id staff id (Optional)
 * @return boolean
 */
function is_lead_creator($leadid, $id = '')
{
    if (!is_numeric($id)) {
        $id = get_staff_user_id();
    }
    $is = total_rows('tblleads', array(
        'addedfrom' => $id,
        'id' => $leadid
    ));
    if ($is > 0) {
        return true;
    }
    return false;
}
/**
 * When ticket will be opened automatically set to open
 * @param integer  $current Current status
 * @param integer  $id      ticketid
 * @param boolean $admin   Admin opened or client opened
 */
function set_ticket_open($current, $id, $admin = true)
{
    if ($current == 1) {
        return;
    }
    $CI =& get_instance();
    $CI->db->where('ticketid', $id);
    $field = 'adminread';
    if ($admin == false) {
        $field = 'clientread';
    }
    $CI->db->update('tbltickets', array(
        $field => 1
    ));
}
/**
 * Get timezones list
 * @return array timezones
 */
function get_timezones_list()
{
    return do_action('get_timezones_list', array(
        'Pacific/Midway' => "(GMT-11:00) Midway Island",
        'US/Samoa' => "(GMT-11:00) Samoa",
        'US/Hawaii' => "(GMT-10:00) Hawaii",
        'US/Alaska' => "(GMT-09:00) Alaska",
        'US/Pacific' => "(GMT-08:00) Pacific Time (US &amp; Canada)",
        'America/Tijuana' => "(GMT-08:00) Tijuana",
        'US/Arizona' => "(GMT-07:00) Arizona",
        'US/Mountain' => "(GMT-07:00) Mountain Time (US &amp; Canada)",
        'America/Chihuahua' => "(GMT-07:00) Chihuahua",
        'America/Mazatlan' => "(GMT-07:00) Mazatlan",
        'America/Mexico_City' => "(GMT-06:00) Mexico City",
        'America/Monterrey' => "(GMT-06:00) Monterrey",
        'Canada/Saskatchewan' => "(GMT-06:00) Saskatchewan",
        'US/Central' => "(GMT-06:00) Central Time (US &amp; Canada)",
        'US/Eastern' => "(GMT-05:00) Eastern Time (US &amp; Canada)",
        'US/East-Indiana' => "(GMT-05:00) Indiana (East)",
        'America/Bogota' => "(GMT-05:00) Bogota",
        'America/Lima' => "(GMT-05:00) Lima",
        'America/Caracas' => "(GMT-04:30) Caracas",
        'Canada/Atlantic' => "(GMT-04:00) Atlantic Time (Canada)",
        'America/La_Paz' => "(GMT-04:00) La Paz",
        'America/Santiago' => "(GMT-04:00) Santiago",
        'Canada/Newfoundland' => "(GMT-03:30) Newfoundland",
        'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
        'Greenland' => "(GMT-03:00) Greenland",
        'America/Sao_Paulo' => "(GMT-03:00) Brazil/Sao Paulo",
        'Atlantic/Stanley' => "(GMT-02:00) Stanley",
        'Atlantic/Azores' => "(GMT-01:00) Azores",
        'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.",
        'Africa/Casablanca' => "(GMT) Casablanca",
        'Europe/Dublin' => "(GMT) Dublin",
        'Europe/Lisbon' => "(GMT) Lisbon",
        'Europe/London' => "(GMT) London",
        'Africa/Monrovia' => "(GMT) Monrovia",
        'Europe/Amsterdam' => "(GMT+01:00) Amsterdam",
        'Europe/Belgrade' => "(GMT+01:00) Belgrade",
        'Europe/Berlin' => "(GMT+01:00) Berlin",
        'Europe/Bratislava' => "(GMT+01:00) Bratislava",
        'Europe/Brussels' => "(GMT+01:00) Brussels",
        'Europe/Budapest' => "(GMT+01:00) Budapest",
        'Europe/Copenhagen' => "(GMT+01:00) Copenhagen",
        'Europe/Ljubljana' => "(GMT+01:00) Ljubljana",
        'Europe/Madrid' => "(GMT+01:00) Madrid",
        'Europe/Paris' => "(GMT+01:00) Paris",
        'Europe/Prague' => "(GMT+01:00) Prague",
        'Europe/Rome' => "(GMT+01:00) Rome",
        'Europe/Sarajevo' => "(GMT+01:00) Sarajevo",
        'Europe/Skopje' => "(GMT+01:00) Skopje",
        'Europe/Stockholm' => "(GMT+01:00) Stockholm",
        'Europe/Vienna' => "(GMT+01:00) Vienna",
        'Europe/Warsaw' => "(GMT+01:00) Warsaw",
        'Europe/Zagreb' => "(GMT+01:00) Zagreb",
        'Europe/Athens' => "(GMT+02:00) Athens",
        'Europe/Bucharest' => "(GMT+02:00) Bucharest",
        'Africa/Cairo' => "(GMT+02:00) Cairo",
        'Africa/Harare' => "(GMT+02:00) Harare",
        'Europe/Helsinki' => "(GMT+02:00) Helsinki",
        'Europe/Istanbul' => "(GMT+02:00) Istanbul",
        'Asia/Jerusalem' => "(GMT+02:00) Jerusalem",
        'Europe/Kiev' => "(GMT+02:00) Kyiv",
        'Europe/Minsk' => "(GMT+02:00) Minsk",
        'Europe/Riga' => "(GMT+02:00) Riga",
        'Europe/Sofia' => "(GMT+02:00) Sofia",
        'Europe/Tallinn' => "(GMT+02:00) Tallinn",
        'Europe/Vilnius' => "(GMT+02:00) Vilnius",
        'Asia/Baghdad' => "(GMT+03:00) Baghdad",
        'Asia/Kuwait' => "(GMT+03:00) Kuwait",
        'Africa/Nairobi' => "(GMT+03:00) Nairobi",
        'Asia/Riyadh' => "(GMT+03:00) Riyadh",
        'Europe/Moscow' => "(GMT+03:00) Moscow",
        'Asia/Tehran' => "(GMT+03:30) Tehran",
        'Asia/Baku' => "(GMT+04:00) Baku",
        'Europe/Volgograd' => "(GMT+04:00) Volgograd",
        'Asia/Muscat' => "(GMT+04:00) Muscat",
        'Asia/Tbilisi' => "(GMT+04:00) Tbilisi",
        'Asia/Yerevan' => "(GMT+04:00) Yerevan",
        'Asia/Kabul' => "(GMT+04:30) Kabul",
        'Asia/Karachi' => "(GMT+05:00) Karachi",
        'Asia/Tashkent' => "(GMT+05:00) Tashkent",
        'Asia/Kolkata' => "(GMT+05:30) Kolkata",
        'Asia/Kathmandu' => "(GMT+05:45) Kathmandu",
        'Asia/Yekaterinburg' => "(GMT+06:00) Ekaterinburg",
        'Asia/Almaty' => "(GMT+06:00) Almaty",
        'Asia/Dhaka' => "(GMT+06:00) Dhaka",
        'Asia/Novosibirsk' => "(GMT+07:00) Novosibirsk",
        'Asia/Bangkok' => "(GMT+07:00) Bangkok",
        'Asia/Jakarta' => "(GMT+07:00) Jakarta",
        'Asia/Krasnoyarsk' => "(GMT+08:00) Krasnoyarsk",
        'Asia/Chongqing' => "(GMT+08:00) Chongqing",
        'Asia/Hong_Kong' => "(GMT+08:00) Hong Kong",
        'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur",
        'Australia/Perth' => "(GMT+08:00) Perth",
        'Asia/Singapore' => "(GMT+08:00) Singapore",
        'Asia/Taipei' => "(GMT+08:00) Taipei",
        'Asia/Ulaanbaatar' => "(GMT+08:00) Ulaan Bataar",
        'Asia/Urumqi' => "(GMT+08:00) Urumqi",
        'Asia/Irkutsk' => "(GMT+09:00) Irkutsk",
        'Asia/Seoul' => "(GMT+09:00) Seoul",
        'Asia/Tokyo' => "(GMT+09:00) Tokyo",
        'Australia/Adelaide' => "(GMT+09:30) Adelaide",
        'Australia/Darwin' => "(GMT+09:30) Darwin",
        'Asia/Yakutsk' => "(GMT+10:00) Yakutsk",
        'Australia/Brisbane' => "(GMT+10:00) Brisbane",
        'Australia/Canberra' => "(GMT+10:00) Canberra",
        'Pacific/Guam' => "(GMT+10:00) Guam",
        'Australia/Hobart' => "(GMT+10:00) Hobart",
        'Australia/Melbourne' => "(GMT+10:00) Melbourne",
        'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
        'Australia/Sydney' => "(GMT+10:00) Sydney",
        'Asia/Vladivostok' => "(GMT+11:00) Vladivostok",
        'Asia/Magadan' => "(GMT+12:00) Magadan",
        'Pacific/Auckland' => "(GMT+12:00) Auckland",
        'Pacific/Fiji' => "(GMT+12:00) Fiji",
        'Africa/Algiers' => "(GMT+01:00) Algiers"
    ));
}
/**
 * Get available locaes predefined for the system
 * If you add a language and the locale do not exist in this array you can use action hook to add new locale
 * @return array
 */
function get_locales()
{
    $locales = array(
        "Arabic" => 'ar',
        "Bulgarian" => 'bg',
        "Catalan" => 'ca',
        "Czech" => 'cs',
        "Danish" => 'da',
        "Albanian" => 'sq',
        "German" => 'de',
        "Deutsch" => 'de',
        'Dutch' => 'nl',
        "Greek" => 'el',
        "English" => 'en',
        "Finland" => 'fi',
        "Spanish" => 'es',
        "Persian" => 'fa',
        "Finnish" => 'fi',
        "French" => 'fr',
        "Hebrew" => 'he',
        "Hindi" => 'hi',
        'Indonesian' => 'id',
        "Hindi" => 'hi',
        "Croatian" => 'hr',
        "Hungarian" => 'hu',
        "Icelandic" => 'is',
        "Italian" => 'it',
        "Japanese" => 'ja',
        "Korean" => 'ko',
        "Lithuanian" => 'lt',
        "Latvian" => 'lv',
        "Norwegian" => 'nb',
        "Netherlands" => 'nl',
        "Polish" => 'pl',
        "Portuguese" => 'pt',
        "Romanian" => 'ro',
        "Russian" => 'ru',
        "Slovak" => 'sk',
        "Slovenian" => 'sl',
        "Serbian" => 'sr',
        "Swedish" => 'sv',
        "Thai" => 'th',
        "Turkish" => 'tr',
        "Ukrainian" => 'uk',
        "Vietnamese" => 'vi'
    );

    $locales = do_action('before_get_locales', $locales);
    return $locales;
}
/**
 * Tinymce language set can be complicated and this function will scan the available languages
 * Will return lang filename in the tinymce plugins folder if found or if $locale is en will return just en
 * @param  [type] $locale [description]
 * @return [type]         [description]
 */
function get_tinymce_language($locale)
{
    $av_lang = list_files(FCPATH . 'assets/plugins/tinymce/langs/');
    $_lang   = '';
    if ($locale == 'en') {
        return $_lang;
    }

    if($locale == 'hi'){
        return 'hi_IN';
    } else if($locale == 'he'){
        return 'he_IL';
    } else if($locale == 'sv'){
        return 'sv_SE';
    }

    foreach ($av_lang as $lang) {
        $_temp_lang = explode('.', $lang);
        if ($locale == $_temp_lang[0]) {
            return $locale;
        } else if ($locale . '_' . strtoupper($locale) == $_temp_lang[0]) {
            return $locale . '_' . strtoupper($locale);
        }
    }

    return $_lang;
}
function app_select_plugin_js($locale = 'en'){
   echo "<script src='".base_url('assets/plugins/app-build/bootstrap-select.min.js')."'></script>".PHP_EOL;

   if($locale != 'en'){
    if(file_exists(FCPATH.'assets/plugins/bootstrap-select/js/i18n/defaults-'.$locale.'.min.js')){
        echo "<script src='".base_url('assets/plugins/bootstrap-select/js/i18n/defaults-'.$locale.'.min.js')."'></script>".PHP_EOL;

    } else if(file_exists(FCPATH.'assets/plugins/bootstrap-select/js/i18n/defaults-'.$locale.'_'.strtoupper($locale).'.min.js')){
        echo "<script src='".base_url('assets/plugins/bootstrap-select/js/i18n/defaults-'.$locale.'_'.strtoupper($locale).'.min.js')."'></script>".PHP_EOL;
    }
}
}
function app_jquery_validation_plugin_js($locale = 'en'){
    echo "<script src='".base_url('assets/plugins/jquery-validation/jquery.validate.min.js')."'></script>".PHP_EOL;
    if($locale != 'en'){
        if(file_exists(FCPATH.'assets/plugins/jquery-validation/localization/messages_'.$locale.'.min.js')){
            echo "<script src='".base_url('assets/plugins/jquery-validation/localization/messages_'.$locale.'.min.js')."'></script>".PHP_EOL;

        } else if(file_exists(FCPATH.'assets/plugins/jquery-validation/localization/messages_'.$locale.'_'.strtoupper($locale).'.min.js')){
            echo "<script src='".base_url('assets/plugins/jquery-validation/localization/messages_'.$locale.'_'.strtoupper($locale).'.min.js')."'></script>".PHP_EOL;
        }
    }
}
/**
 * Check if visitor is on mobile
 * @return boolean
 */
function is_mobile()
{
    $CI =& get_instance();
    if ($CI->agent->is_mobile()) {
        return true;
    }
    return false;
}
/**
 * All permissions available in the app with conditions
 * @return array
 */
function get_permission_conditions()
{
    return array(
        'contracts' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'tasks' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'help' => _l('help_tasks_permissions')
        ),
        'reports' => array(
            'view' => true,
            'view_own' => false,
            'edit' => false,
            'create' => false,
            'delete' => false
        ),
        'settings' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => false,
            'delete' => false
        ),
        'projects' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'help' => _l('help_project_permissions')
        ),
        'surveys' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'staff' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'customers' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'email_templates' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => false,
            'delete' => false
        ),
        'roles' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'expenses' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'bulk_pdf_exporter' => array(
            'view' => true,
            'view_own' => false,
            'edit' => false,
            'create' => false,
            'delete' => false
        ),
        'goals' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'knowledge_base' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'proposals' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'estimates' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'payments' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'invoices' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true
        ),
        'items' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true
        )
    );
}
/**
 * Function that will search possible proposal templates in applicaion/views/admin/proposal/templates
 * Will return any found files and user will be able to add new template
 * @return array
 */
function get_proposal_templates()
{
    $proposal_templates = array();
    foreach (list_files(VIEWPATH . 'admin/proposals/templates') as $template) {
        $proposal_templates[] = $template;
    }

    return $proposal_templates;
}
/**
 * Translated datatables language based on app languages
 * This feature is used on both admin and customer area
 * @return array
 */
function get_datatables_language_array()
{
    $lang = array(
        'emptyTable' => preg_replace("/{(\d+)}/", _l("dt_entries"), _l("dt_empty_table")),
        'info' => preg_replace("/{(\d+)}/", _l("dt_entries"), _l("dt_info")),
        'infoEmpty' => preg_replace("/{(\d+)}/", _l("dt_entries"), _l("dt_info_empty")),
        'infoFiltered' => preg_replace("/{(\d+)}/", _l("dt_entries"), _l("dt_info_filtered")),
        'lengthMenu' => preg_replace("/{(\d+)}/", _l("dt_entries"), _l("dt_length_menu")),
        'loadingRecords' => _l('dt_loading_records'),
        'processing' => '<div class="dt-loader"></div>',
        'search' => '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>',
        'searchPlaceholder' => _l('dt_search'),
        'zeroRecords' => _l('dt_zero_records'),
        'paginate' => array(
            'first' => _l('dt_paginate_first'),
            'last' => _l('dt_paginate_last'),
            'next' => _l('dt_paginate_next'),
            'previous' => _l('dt_paginate_previous')
        ),
        'aria' => array(
            'sortAscending' => _l('dt_sort_ascending'),
            'sortDescending' => _l('dt_sort_descending')
        )
    );
    return $lang;
}
/**
 * Translated jquery-comment language based on app languages
 * This feature is used on both admin and customer area
 * @return array
 */
function get_project_discussions_language_array()
{
    $lang = array(
        'discussion_add_comment' => _l('discussion_add_comment'),
        'discussion_newest' => _l('discussion_newest'),
        'discussion_oldest' => _l('discussion_oldest'),
        'discussion_attachments' => _l('discussion_attachments'),
        'discussion_send' => _l('discussion_send'),
        'discussion_reply' => _l('discussion_reply'),
        'discussion_edit' => _l('discussion_edit'),
        'discussion_edited' => _l('discussion_edited'),
        'discussion_you' => _l('discussion_you'),
        'discussion_save' => _l('discussion_save'),
        'discussion_delete' => _l('discussion_delete'),
        'discussion_view_all_replies' => _l('discussion_view_all_replies'),
        'discussion_hide_replies' => _l('discussion_hide_replies'),
        'discussion_no_comments' => _l('discussion_no_comments'),
        'discussion_no_attachments' => _l('discussion_no_attachments'),
        'discussion_attachments_drop' => _l('discussion_attachments_drop')
    );
    return $lang;
}
/**
 * Feature that will render all JS necessary data in admin head
 * @return void
 */
function render_admin_js_variables()
{
    $date_format = get_option('dateformat');
    $date_format = explode('|', $date_format);
    $date_format = $date_format[0];

    $js_vars     = array(
        'site_url' => site_url(),
        'admin_url' => admin_url(),
        '_is_mobile' => is_mobile(),
        'decimal_places'=>get_decimal_places(),
        'company_is_required' => get_option('company_is_required'),
        'default_view_calendar'=>get_option('default_view_calendar'),
        'show_table_columns_visibility' => do_action('show_table_columns_visibility', 0),
        'maximum_allowed_ticket_attachments' => get_option('maximum_allowed_ticket_attachments'),
        'show_setup_menu_item_only_on_hover' => get_option('show_setup_menu_item_only_on_hover'),
        'calendar_events_limit' => get_option('calendar_events_limit'),
        'lang_unit' => _l('unit'),
        'max_php_ini_upload_size' => bytesToSize('', file_upload_max_size()),
        'file_exceds_maxfile_size_in_form' => _l('file_exceds_maxfile_size_in_form'),
        'auto_check_for_new_notifications' => get_option('auto_check_for_new_notifications'),
        'tables_pagination_limit' => get_option('tables_pagination_limit'),
        'newsfeed_maximum_files_upload' => get_option('newsfeed_maximum_files_upload'),
        'newsfeed_maximum_file_size' => get_option('newsfeed_maximum_file_size'),
        'date_format' => $date_format,
        'decimal_separator' => get_option('decimal_separator'),
        'thousand_separator' => get_option('thousand_separator'),
        'currency_placement' => get_option('currency_placement'),
        'drop_files_here_to_upload' => _l('drop_files_here_to_upload'),
        'browser_not_support_drag_and_drop' => _l('browser_not_support_drag_and_drop'),
        'remove_file' => _l('remove_file'),
        'you_can_not_upload_any_more_files' => _l('you_can_not_upload_any_more_files'),
        'timezone' => get_option('default_timezone'),
        'dt_length_menu_all' => _l("dt_length_menu_all"),
        'dt_button_column_visibility' => _l('dt_button_column_visibility'),
        'dt_button_reload' => _l('dt_button_reload'),
        'dt_button_excel' => _l('dt_button_excel'),
        'dt_button_csv' => _l('dt_button_csv'),
        'dt_button_pdf' => _l('dt_button_pdf'),
        'dt_button_print' => _l('dt_button_print'),
        'dt_button_export' => _l('dt_button_export'),
        'item_field_not_formated' => _l('numbers_not_formated_while_editing'),
        'no_results_found' => _l('not_results_found'),
        'google_api' => '',
        'calendarIDs' => '',
        'has_tasks_permission' => has_permission('tasks', '', 'create'),
        'invoice_due_after' => get_option('invoice_due_after'),
        'media_files' => _l('media_files'),
        'proposal_save' => _l('proposal_save'),
        'contract_save' => _l('contract_save'),
        'calendar_expand' => _l('calendar_expand'),
        'allowed_files' => get_option('allowed_files'),
        'dropdown_non_selected_text' => _l('dropdown_non_selected_tex'),
        'confirm_action_prompt' => _l('confirm_action_prompt'),
        'mass_delete_btn' => _l('mass_delete'),
        'calendar_first_day' => get_option('calendar_first_day'),
        'estimate_number_exists' => _l('estimate_number_exists'),
        'invoice_number_exists' => _l('invoice_number_exists'),
        'no_results_text_search_dropdown' => _l('no_results_text_search_dropdown'),
        'email_exists' => _l('email_exists'),
        'options_string_translate' => _l('options'),
        'cf_translate_input_link_tip' => _l('cf_translate_input_link_tip'),
        'is_admin' => is_admin(),
        'is_staff_member' => is_staff_member(),
        'tag_string'=>_l('tag'),
        'invoice_task_billable_timers_found' => _l('invoice_task_billable_timers_found'),
    );
    $js_vars     = do_action('before_render_js_vars_admin', $js_vars);
    echo '<script>';
    foreach ($js_vars as $var => $val) {
        echo 'var ' . $var . '="' . $val . '";';
    }
    echo '</script>';
}
/**
 * For html5 form accepted attributes
 * This function is used for the tickets form attachments
 * @return string
 */
function get_ticket_form_accepted_mimes()
{
    $ticket_allowed_extensions  = get_option('ticket_attachments_file_extensions');
    $_ticket_allowed_extensions = explode(',', $ticket_allowed_extensions);
    $all_form_ext               = $ticket_allowed_extensions;
    if (is_array($_ticket_allowed_extensions)) {
        foreach ($_ticket_allowed_extensions as $ext) {
            $all_form_ext .= ',' . get_mime_by_extension($ext);
        }
    }
    return $all_form_ext;
}
/**
 * For html5 form accepted attributes
 * This function is used for the form attachments
 * @return string
 */
function get_form_accepted_mimes()
{
    $allowed_extensions  = get_option('allowed_files');
    $_allowed_extensions = explode(',', $allowed_extensions);
    $all_form_ext        = $allowed_extensions;
    if (is_array($_allowed_extensions)) {
        foreach ($_allowed_extensions as $ext) {
            $all_form_ext .= ',' . get_mime_by_extension($ext);
        }
    }
    return $all_form_ext;
}
/**
 * Function that will parse filters for datatables and will return based on a couple conditions.
 * The returned result will be pushed inside the $where variable in the table SQL
 * @param  array $filter
 * @return string
 */
function prepare_dt_filter($filter)
{
    $filter = implode(' ', $filter);
    if (_startsWith($filter, 'AND')) {
        $filter = substr($filter, 3);
    } else if (_startsWith($filter, 'OR')) {
        $filter = substr($filter, 2);
    }
    return $filter;
}
/**
 * Check if there is usage of some features that requires cron job to be setup
 * If the script found results will output a message inside the admin area only for admins
 * @return void
 */
function is_cron_setup_required()
{
    if (get_option('cron_has_run_from_cli') == 0) {
        if (is_admin()) {
            $used_features       = array();
            $using_cron_features = 0;
            $feature             = total_rows('tblreminders');
            $using_cron_features += $feature;
            if ($feature > 0) {
                array_push($used_features, 'Reminders');
            }
            $feature = get_option('auto_backup_enabled');
            $using_cron_features += $feature;
            if ($feature > 0) {
                array_push($used_features, 'Auto database backup');
            }


            $feature = total_rows('tblsurveysemailsendcron');
            $using_cron_features += $feature;
            if ($feature > 0) {
                array_push($used_features, 'Surveys');
            }
            $feature = total_rows('tblleadsintegration', array(
                'active' => 1
            ));
            $using_cron_features += $feature;

            if ($feature > 0) {
                array_push($used_features, 'Auto importing leads from email.');
            }
            $feature = total_rows('tblinvoices', array(
                'recurring >' => 0
            ));
            $using_cron_features += $feature;
            if ($feature > 0) {
                array_push($used_features, 'Recurring Invoices');
            }
            $feature = total_rows('tblexpenses', array(
                'recurring' => 1
            ));
            $using_cron_features += $feature;
            if ($feature > 0) {
                array_push($used_features, 'Recurring Expenses');
            }

            $feature = total_rows('tblstafftasks', array(
                'recurring' => 1
            ));
            $using_cron_features += $feature;
            if ($feature > 0) {
                array_push($used_features, 'Recurring Tasks');
            }

            $feature = total_rows('tblevents');
            $using_cron_features += $feature;

            if ($feature > 0) {
                array_push($used_features, 'Custom Calendar Events');
            }

            $feature = total_rows('tbldepartments', array(
                'host !=' => '',
                'password !=' => '',
                'email !=' => ''
            ));
            $using_cron_features += $feature;
            if ($feature > 0) {
                array_push($used_features, 'Auto Import Tickets via method IMAP (Setup->Support->Departments)');
            }

            if ($using_cron_features > 0 && get_option('hide_cron_is_required_message') == 0) {
                echo '<div class="col-md-12">';
                echo '<div class="alert alert-danger">';
                echo 'You are using some features that requires cron job setup to work properly.';
                echo '<br />Please follow the cron <a href="https://www.perfexcrm.com/documentation/installation/setup-cpanel-cron-job/" target="_blank">setup guide</a> in order all features to work well.';
                echo '<br /><br /><br />';
                echo '<p class="bold">You are using the following features that CRON Job setup is required:</p>';
                $i = 1;
                foreach ($used_features as $feature) {
                    echo '&nbsp;' . $i . '. ' . $feature . '<br />';
                    $i++;
                }
                echo '<br /><br /><a href="' . admin_url('misc/dismiss_cron_setup_message') . '" class="alert-link">Dont show this message again</a>';
                echo '</div>';
                echo '</div>';
            }
        }
    }
}
/**
 * All email client templates slugs used for sending the emails
 * If you create new email template you can and must add the slug here with action hook.
 * Those are used to identify in what language should the email template to be sent
 * @return array
 */
function get_client_email_templates_slugs()
{
    $client_email_templates_slugs = array(
        'new-client-created',
        'invoice-send-to-client',
        'new-ticket-opened-admin',
        'ticket-reply',
        'ticket-autoresponse',
        'assigned-to-project',
        'invoice-payment-recorded',
        'invoice-overdue-notice',
        'invoice-already-send',
        'estimate-send-to-client',
        'contact-forgot-password',
        'contact-password-reseted',
        'contact-set-password',
        'estimate-already-send',
        'contract-expiration',
        'proposal-send-to-customer',
        'proposal-client-thank-you',
        'proposal-comment-to-client',
        'estimate-thank-you-to-customer',
        'send-contract',
        'auto-close-ticket',
        'new-project-discussion-created-to-customer',
        'new-project-file-uploaded-to-customer',
        'new-project-discussion-comment-to-customer',
        'estimate-expiry-reminder',
        'estimate-expiry-reminder',
        'task-marked-as-finished-to-contacts',
        'task-added-attachment-to-contacts',
        'task-commented-to-contacts'
    );

    return do_action('client_email_templates', $client_email_templates_slugs);
}
/**
 * All email staff templates slugs used for sending the emails
 * If you create new email template you can and must add the slug here with action hook.
 * Those are used to identify in what language should the email template to be sent
 * @return array
 */
function get_staff_email_templates_slugs()
{

    $staff_email_templates_slugs = array(
        'new-ticket-created-staff',
        'ticket-reply-to-admin',
        'task-assigned',
        'task-added-as-follower',
        'task-commented',
        'staff-password-reseted',
        'staff-forgot-password',
        'task-marked-as-finished',
        'task-added-attachment',
        'task-unmarked-as-finished',
        'estimate-declined-to-staff',
        'estimate-accepted-to-staff',
        'proposal-client-accepted',
        'proposal-client-declined',
        'proposal-comment-to-admin',
        'task-deadline-notification',
        'invoice-payment-recorded-to-staff',
        'new-project-discussion-created-to-staff',
        'new-project-file-uploaded-to-staff',
        'new-project-discussion-comment-to-staff',
        'staff-added-as-project-member',
        'new-staff-created',
        'new-lead-assigned'
    );

    return do_action('staff_email_templates', $staff_email_templates_slugs);
}
/**
 * Function that will return in what language the email template should be sent
 * @param  string $template_slug the template slug
 * @param  string $email         email that this template will be sent
 * @return string
 */
function get_email_template_language($template_slug, $email)
{
    $CI =& get_instance();
    $language = get_option('active_language');

    if (total_rows('tblcontacts', array(
        'email' => $email
    )) > 0 && in_array($template_slug, get_client_email_templates_slugs())) {
        $CI->db->where('email', $email);

        $contact = $CI->db->get('tblcontacts')->row();
        $lang    = get_client_default_language($contact->userid);
        if ($lang != '') {
            $language = $lang;
        }

    } else if (total_rows('tblstaff', array(
            'email' => $email
        )) > 0 && in_array($template_slug, get_staff_email_templates_slugs())) {
        $CI->db->where('email', $email);
        $staff = $CI->db->get('tblstaff')->row();

        $lang = get_staff_default_language($staff->staffid);
        if ($lang != '') {
            $language = $lang;
        }
    }

    return $language;
}
/**
 * Based on the template slug and email the function will fetch a template from database
 * The template will be fetched on the language that should be sent
 * @param  string $template_slug
 * @param  string $email
 * @return object
 */
function get_email_template_for_sending($template_slug, $email)
{

    $CI =& get_instance();

    $language = get_email_template_language($template_slug, $email);

    if (!is_dir(APPPATH . 'language/' . $language)) {
        $language = 'english';
    }

    $CI->db->where('language', $language);
    $CI->db->where('slug', $template_slug);
    $template = $CI->db->get('tblemailtemplates')->row();

    // Template message blank use the active language default template
    if ($template->message == '') {
        $CI->db->where('language', get_option('active_language'));
        $CI->db->where('slug', $template_slug);
        $template = $CI->db->get('tblemailtemplates')->row();

        if ($template->message == '') {
            $CI->db->where('language', 'english');
            $CI->db->where('slug', $template_slug);
            $template = $CI->db->get('tblemailtemplates')->row();
        }
    }
    return $template;
}
/**
 * Function that will replace the dropbox link size for the images
 * This function is used to preview dropbox image attachments
 * @param  string $url
 * @param  string $bounding_box
 * @return string
 */
function optimize_dropbox_thumbnail($url, $bounding_box = '800')
{
    $url = str_replace('bounding_box=75', 'bounding_box=' . $bounding_box, $url);
    return $url;
}
/**
 * Prepare label when splitting weeks for charts
 * @param  array $weeks week
 * @param  mixed $week  week day - number
 * @return string
 */
function split_weeks_chart_label($weeks,$week){
      $week_start = $weeks[$week][0];
      end($weeks[$week]);
      $key = key($weeks[$week]);
      $week_end = $weeks[$week][$key];

      $week_start_year = date('Y',strtotime($week_start));
      $week_end_year = date('Y',strtotime($week_end));

      $week_start_month = date('m',strtotime($week_start));
      $week_end_month = date('m',strtotime($week_end));

      $label = '';

      $label .= date('d',strtotime($week_start));

        if($week_start_month != $week_end_month && $week_start_year == $week_end_year){
            $label .= ' ' . _l(date('F', mktime(0,0,0,$week_start_month,1)));
        }

    if($week_start_year != $week_end_year){
        $label .=  ' ' . _l(date('F', mktime(0,0,0,date('m',strtotime($week_start)),1))) . ' ' . date('Y',strtotime($week_start));
    }

    $label .= ' - ';
    $label .= date('d',strtotime($week_end));
    if($week_start_year != $week_end_year){
        $label .=  ' ' . _l(date('F', mktime(0,0,0,date('m',strtotime($week_end)),1))) .' ' . date('Y',strtotime($week_end));
    }

    if($week_start_year == $week_end_year){
        $label .=  ' ' . _l(date('F', mktime(0,0,0,date('m',strtotime($week_end)),1)));
        $label .= ' ' . date('Y',strtotime($week_start));
    }

    return $label;
}
/**
 * Get ranges weeks between 2 dates
 * @param  object $start_time date object
 * @param  objetc $end_time   date object
 * @return array
 */
function get_weekdays_between_dates($start_time,$end_time){

    $interval = new DateInterval('P1D');
    $end_time = $end_time->modify( '+1 day' );
    $dateRange = new DatePeriod($start_time, $interval, $end_time);
    $weekNumber = 1;
    $weeks = array();

    foreach ($dateRange as $date) {
        $weeks[$weekNumber][] = $date->format('Y-m-d');
        if ($date->format('w') == 0) {
            $weekNumber++;
        }
    }

    return $weeks;
}
