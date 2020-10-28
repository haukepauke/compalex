<?php

define('DIR_ROOT', dirname(__FILE__));
define('ENVIRONMENT_FILE', DIR_ROOT . '/.environment');
define('DRIVER_DIR', DIR_ROOT . '/driver/');
define('TEMPLATE_DIR', DIR_ROOT . '/template/');

if (!file_exists(ENVIRONMENT_FILE)) die('File "' . ENVIRONMENT_FILE . '" not exist. Please create file.');
$config = parse_ini_file(ENVIRONMENT_FILE, true, INI_SCANNER_RAW);

$dsnConfig = array();
foreach($config as $sectionName => $section){
    foreach ($section as $key => $value) {
        if($sectionName === "Main_Settings") {
            define($key, $value);    
        } else if (substr($sectionName, 0, 4) === "DSN_") {
            $dsnConfig[$sectionName] = $section;
        }
    }
}

if(empty($dsnConfig)) {
    die('No DSN configuration found in ' . ENVIRONMENT_FILE);
}

if(!defined('DATABASE_DRIVER') || !defined('DATABASE_ENCODING') || !defined('SAMPLE_DATA_LENGTH')) {
    die('Configuration values in Main_Settings missing in file ' . ENVIRONMENT_FILE);
}

//define('SECOND_DSN',  DATABASE_DRIVER.'://'.DATABASE_USER_SECONDARY.':'.DATABASE_PASSWORD_SECONDARY.'@'.DATABASE_HOST_SECONDARY.':'.DATABASE_PORT_SECONDARY.'/'.DATABASE_NAME_SECONDARY);

