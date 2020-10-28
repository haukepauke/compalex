<?php
require_once 'config.php';

try {
    if (!file_exists(DRIVER_DIR . DATABASE_DRIVER . '.php')) throw new Exception('Driver ' . DATABASE_DRIVER . ' not found');

    // abstract class
    require_once DRIVER_DIR . 'abstract.php';
    require_once DRIVER_DIR . DATABASE_DRIVER . '.php';

    $dbNames = array_keys($dsnConfig);
    if (
        array_key_exists("first", $_REQUEST) && 
        array_key_exists("second", $_REQUEST) && 
        array_key_exists("DSN_" . $_REQUEST["first"], $dsnConfig) && 
        array_key_exists("DSN_" . $_REQUEST["second"], $dsnConfig)
    ) {
        $firstDb = $_REQUEST['first'];
        $secondDb = $_REQUEST['second'];
    } else {
        //get the first two db names from config
        $firstDb = substr($dbNames[0], 4);
        $secondDb = substr($dbNames[1], 4);
    }

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'tables';
    $driver = Driver::getInstance($dsnConfig, $firstDb, $secondDb);
    
    $additionalTableInfo = array();
    switch ($action) {
        case "tables":
            $tables = $driver->getCompareTables();
            $additionalTableInfo = $driver->getAdditionalTableInfo();
            break;
        case "views":
            $tables = $driver->getCompareViews();
            break;
        case "procedures":
            $tables = $driver->getCompareProcedures();
            break;
        case "functions":
            $tables = $driver->getCompareFunctions();
            break;
        case "indexes":
            $tables = $driver->getCompareKeys();
            break;
        case "triggers":
            $tables = $driver->getCompareTriggers();
            break;
        case "rows":
            $rows = $driver->getTableRows($_REQUEST['baseName'], $_REQUEST['tableName']);
            break;
    }

    $basesName = array(
        'fArray' => $dsnConfig["DSN_" . $firstDb]["DATABASE_NAME"],
        'sArray' => $dsnConfig["DSN_" . $secondDb]["DATABASE_NAME"]
    );

    if ($action == 'rows') {
        require_once TEMPLATE_DIR . 'rows.php';
    } else {
        require_once TEMPLATE_DIR . 'compare.php';
    }

} catch (Exception $e) {
    include_once TEMPLATE_DIR . 'error.php';
}

