<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb');
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', FALSE);

class Install
{
    private $error = '';
    private $passed_steps = array();
    private $config_path = '../application/config/app-config-sample.php';
    function __construct()
    {
        $this->passed_steps = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false
        );
    }

    public function go()
    {
        $debug       = '';
        $step        = 1;

        if (isset($_POST) && !empty($_POST)) {
            if (isset($_POST['step']) && $_POST['step'] == 2) {
                $step                  = 2;
                $this->passed_steps[1] = true;
                $this->passed_steps[2] = true;
            } else if (isset($_POST['step']) && $_POST['step'] == 3) {

                if ($_POST['hostname'] == '') {
                    $this->error = 'Hostname is required';
                } else if ($_POST['database'] == '') {
                    $this->error = 'Enter database name';
                } else if ($_POST['password'] == '' && !$this->is_localhost()) {
                    $this->error = 'Enter database password';
                } else if ($_POST['username'] == '') {
                    $this->error = 'Enter database username';
                }
                $step                  = 3;
                $this->passed_steps[1] = true;
                $this->passed_steps[2] = true;
                if ($this->error === '') {
                    $this->passed_steps[3] = true;
                    $link                  = @mysqli_connect($_POST['hostname'], $_POST['username'], $_POST['password'], $_POST['database']);
                    if (!$link) {
                        $this->error .= "Error: Unable to connect to MySQL." . PHP_EOL;
                        $this->error .= "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
                        $this->error .= "Debugging error: " . mysqli_connect_error() . PHP_EOL;
                    } else {
                        $debug .= "Success: A proper connection to MySQL was made! The " . $_POST['database'] . " database is great." . PHP_EOL;
                        $debug .= "Host information: " . mysqli_get_host_info($link) . PHP_EOL;
                        $step = 4;
                        mysqli_close($link);
                    }
                }
            } else if (isset($_POST['requirements_success'])) {
                $step                  = 2;
                $this->passed_steps[1] = true;
                $this->passed_steps[2] = true;
            } else if (isset($_POST['permissions_success'])) {
                $step                  = 3;
                $this->passed_steps[1] = true;
                $this->passed_steps[2] = true;
                $this->passed_steps[3] = true;
            } else if (isset($_POST['step']) && $_POST['step'] == 4) {
                if ($_POST['admin_email'] == '') {
                    $this->error = 'Enter admin email address';
                } else if (filter_var($_POST['admin_email'], FILTER_VALIDATE_EMAIL) === false) {
                    $this->error = 'Enter valid email address';
                } else if ($_POST['admin_password'] == '') {
                    $this->error = 'Enter admin password';
                } else if ($_POST['admin_password'] != $_POST['admin_passwordr']) {
                    $this->error = 'Your password not match';
                } else if($_POST['base_url'] == ''){
                    $this->error = 'Please enter base url';
                }
                $this->passed_steps[1] = true;
                $this->passed_steps[2] = true;
                $this->passed_steps[3] = true;
                $this->passed_steps[4] = true;
                $step                  = 4;
            }
            if ($this->error === '' && isset($_POST['step']) && $_POST['step'] == 4) {
                $database = @file_get_contents('database.sql');
                $link     = mysqli_connect($_POST['hostname'], $_POST['username'], $_POST['password'], $_POST['database']);

                if (mysqli_multi_query($link, $database)) {

                    $this->write_app_config();
                    if(!$this->rename_app_config()){
                        $rename_failed = true;
                    }
                    $this->clean_up_db_query($link);
                    require_once('phpass.php');
                    $hasher      = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
                    $password    = $hasher->HashPassword($_POST['admin_passwordr']);
                    $email       = $_POST['admin_email'];
                    $datecreated = date('Y-m-d H:i:s');

                    $timezone = $_POST['timezone'];
                    $sql      = "UPDATE tbloptions SET value='$timezone' WHERE name='default_timezone'";
                    mysqli_query($link, $sql);

                    $di = time();
                    $sql      = "UPDATE tbloptions SET value='$di' WHERE name='di'";
                    mysqli_query($link, $sql);

                    $sql = "INSERT INTO tblstaff (password,email,datecreated,admin,active) VALUES('$password','$email','$datecreated',1,1)";
                    mysqli_query($link, $sql);

                    $this->passed_steps[1] = true;
                    $this->passed_steps[2] = true;
                    $this->passed_steps[3] = true;
                    $this->passed_steps[4] = true;
                    if(!file_exists('../.htaccess') && is_writable('../')){
                       fopen('../.htaccess', 'w');
                       $fp = fopen('../.htaccess','a+');
                       if($fp)
                       {
                          fwrite($fp,'RewriteEngine on'.PHP_EOL.'RewriteCond $1 !^(index\.php|resources|robots\.txt)'.PHP_EOL.'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL.'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL.'RewriteRule ^(.*)$ index.php?/$1 [L,QSA]'.PHP_EOL.'AddDefaultCharset utf-8');
                          fclose($fp);
                      }
                  }
                  $step                  = 5;

                }
            } else {
                $error = $this->error;
            }
        }
        $passed_steps = $this->passed_steps;
        require_once('html.php');
    }
    public function is_localhost()
    {
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            return true;
        }

        return false;
    }
    private function clean_up_db_query($link)
    {
        while (mysqli_more_results($link) && mysqli_next_result($link)) {

            $dummyResult = mysqli_use_result($link);

            if ($dummyResult instanceof mysqli_result) {
                mysqli_free_result($link);
            }
        }
    }
    private function write_app_config()
    {

        $hostname       = $_POST['hostname'];
        $database       = $_POST['database'];
        $username       = $_POST['username'];
        $password       = $_POST['password'];
        $base_url       = rtrim($_POST['base_url'], '/') . '/';

        $encryption_key = bin2hex($this->create_key(16));
        $config_path    = $this->config_path;

        @chmod($config_path, FILE_WRITE_MODE);

        $config_file = file_get_contents($config_path);
        $config_file = trim($config_file);

        $config_file = str_replace("define('APP_DB_HOSTNAME','localhost')", "define('APP_DB_HOSTNAME','" . $hostname . "')", $config_file);

        $config_file = str_replace("define('APP_DB_USERNAME','')", "define('APP_DB_USERNAME','" . $username . "')", $config_file);
        $config_file = str_replace("define('APP_DB_PASSWORD','')", "define('APP_DB_PASSWORD','" . $password . "')", $config_file);
        $config_file = str_replace("define('APP_DB_NAME','')", "define('APP_DB_NAME','" . $database . "')", $config_file);
        $config_file = str_replace("define('APP_ENC_KEY','')", "define('APP_ENC_KEY','" . $encryption_key . "')", $config_file);
        $config_file = str_replace("define('APP_BASE_URL','')", "define('APP_BASE_URL','" . $base_url . "')", $config_file);

        if (!$fp = fopen($config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
            return FALSE;
        }

        flock($fp, LOCK_EX);
        fwrite($fp, $config_file, strlen($config_file));
        flock($fp, LOCK_UN);
        fclose($fp);
        @chmod($config_path, FILE_READ_MODE);

        return true;
    }

    private function rename_app_config(){
        if(@rename('../application/config/app-config-sample.php','../application/config/app-config.php') == true){
            return true;
        }
        return false;
    }
    public function create_key($length)
    {
        if (function_exists('random_bytes')) {
            try {
                return random_bytes((int) $length);
            }
            catch (Exception $e) {
                echo $e->getMessage();
                return FALSE;
            }
        } elseif (defined('MCRYPT_DEV_URANDOM')) {
            return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        }

        $is_secure = NULL;
        $key       = openssl_random_pseudo_bytes($length, $is_secure);
        return ($is_secure === TRUE) ? $key : FALSE;
    }

    public function guess_base_url(){
        $base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
        $base_url .= '://'. $_SERVER['HTTP_HOST'];
        $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        $base_url = preg_replace('/install.*/', '', $base_url);
        return $base_url;
    }

    public function get_timezones_list()
    {
        return $timezones = array(
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
        );
    }
}
