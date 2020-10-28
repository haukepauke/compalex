<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>COMPALEX Plus - database schema compare tool</title>
    <script src="./public/js/jquery.min.js"></script>
    <script src="./public/js/functional.js"></script>
    <style type="text/css" media="all">
        @import url("./public/css/style.css");
    </style>
</head>

<body>
<div class="modal-background" onclick="Data.hideTableData(); return false;">
    <div class="modal">
        <iframe src="" frameborder="0"></iframe>
    </div>
</div>

<div class="compare-database-block">
    <h1>Compalex Plus</h1>
    <h3>Database schema compare tool</h3>
    <table class="table">
        <tr class="panel">
            <form name="dbselect" action="./index.php">
                <label for="first">First Database:</label>
                <select id="first" name="first">
                    <?php  
                        foreach($dbNames as $db){
                            echo "<option name=" . substr($db, 4) . ">" . substr($db, 4) . "</option>";
                        }
                    
                    ?>
                </select>
                <label for="second">Second Database:</label>
                <select id="second" name="second">
                    <?php 
                         foreach($dbNames as $db){
                            echo "<option name=" . substr($db, 4) . ">" . substr($db, 4) . "</option>";
                        }
                    ?>
                </select>
                <input type="submit" value="Change Databases">
            </form>
        </tr>
    </table>
    <br/>
    <table class="table">
        <tr class="panel">
            <td>
                <?php
                switch (DATABASE_DRIVER) {
                    case 'oci8':
                    case 'oci':
                    case 'mysql':
                        $buttons = array('tables', 'views', 'procedures', 'functions', 'indexes', 'triggers');
                        break;
                    case 'sqlserv':
                    case 'mssql':
                    case 'dblib':
                        $buttons = array('tables', 'views', 'procedures', 'functions', 'indexes');
                        break;
                    case 'pgsql':
                        $buttons = array('tables', 'views', 'functions', 'indexes');
                        break;
                }

                if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'tables';
                foreach ($buttons as $li) {
                    echo '<a href="index.php?action=' . $li . '"  ' . ($li == $_REQUEST['action'] ? 'class="active"' : '') . '>' . $li . '</a>&nbsp;';
                }
                ?>

            </td>
            <td class="sp">
                <a href="#" onclick="Data.showAll(this); return false;" class="active">all</a>
                <a href="#" onclick="Data.showDiff(this); return false;">changed</a>
            </td>
        </tr>
    </table>
    <table class="table">
        <tr class="header">
            <td width="50%">
                <h2><?php echo $dsnConfig["DSN_" . $firstDb]["DATABASE_NAME"] ?></h2>
                <h4 style="color: darkred; margin-top: 2px; "><?php echo $dsnConfig["DSN_" . $firstDb]["DATABASE_DESCRIPTION"] ?></h4>
                <span><?php $spath = explode("@", $driver->getDSN($firstDb));
                    echo end($spath); ?></span>
            </td>
            <td  width="50%">
                <h2><?php echo $dsnConfig["DSN_" . $secondDb]["DATABASE_NAME"] ?></h2>
                <h4 style="color: darkred; margin-top: 2px; "><?php echo $dsnConfig["DSN_" . $secondDb]["DATABASE_DESCRIPTION"] ?></h4>
                <span><?php $spath = explode("@", $driver->getDSN($secondDb));
                    echo end($spath); ?></span>
            </td>
        </tr>
    <?php 
        $tablesToIgnore = explode(",", TABLES_TO_IGNORE);
        foreach ($tables as $tableName => $data) { 
            if (array_search($tableName, $tablesToIgnore) !== false) {
                continue;
            }
        ?>
        <tr class="data">
            <?php foreach (array('fArray', 'sArray') as $blockType) { ?>
            <td class="type-<?php echo $_REQUEST['action']; ?>">
                <h3><?php echo $tableName; ?> <sup style="color: red;"><?php 
                if ($data != null && isset($data[$blockType]) && $data[$blockType] != null) {
                    echo count($data[$blockType]); 
                }?></sup></h3>
                <div class="table-additional-info">
                    <?php if(isset($additionalTableInfo[$tableName][$blockType])) {
                            foreach ($additionalTableInfo[$tableName][$blockType] as $paramKey => $paramValue) {
                                if(strpos($paramKey, 'ARRAY_KEY') === false) echo "<b>{$paramKey}</b>: {$paramValue}<br />";
                            }
                        }
                    ?>
                </div>
                <?php if ($data[$blockType]) { ?>
                    <ul>
                        <?php foreach ($data[$blockType] as $fieldName => $tparam) { ?>
                            <li <?php if (isset($tparam['isNew']) && $tparam['isNew']) {
                                echo 'style="color: red;" class="new" ';
                            } ?>><b style="white-space: pre"><?php echo $fieldName; ?></b>
                                <span <?php if (isset($tparam['changeType']) && $tparam['changeType']): ?>style="color: red;" class="new" <?php endif;?>>
                                    <?php echo $tparam['dtype']; ?>
                                </span>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <?php if ($data != null && isset($data[$blockType]) && $data[$blockType] != null && count($data[$blockType]) && in_array($_REQUEST['action'], array('tables', 'views'))) { ?><a
                    target="_blank"
                    onclick="Data.getTableData('index.php?action=rows&baseName=<?php echo $basesName[$blockType]; ?>&tableName=<?php echo $tableName; ?>'); return false;"
                    href="#" class="sample-data">Sample data (<?php echo SAMPLE_DATA_LENGTH; ?> rows)</a><?php } ?>
            </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </table>
    <p><b>Ignored tables:</b> <?php echo str_replace(",", ", ", TABLES_TO_IGNORE); ?>.</p>
    <p>&nbsp;</p>
    <hr />
    <p>For more information go to <a href="https://github.com/haukepauke/compalex" target="_blank">github.com/haukepauke/compalex</a></p>
</div>
</body>
