<?php
/**
 * Database based settings
 * 
 * @todo Check if all constants were created
 * @author Marek Pikuła <marpirk@gmail.com>
 * @copyright © 2015, Marek Pikuła
 */

/** Errors are required for DB errors handling */
require_once 'error.php';

/** @var $dblink mysqli MySQLi DB handler */
$dblink = new mysqli(\Config\DB\host, \Config\DB\user, \Config\DB\password,
                     \Config\DB\database, \Config\DB\port);
/** Handle connection error */
if ($dblink->connect_errno) {
    GeneralError::dbError();
}

/** @var $table_settings String Settgins table name */
$table_settings = \Config\DB\table_prefix . 'settings';
/** @var $table_modules String Modules table name */
$table_modules = \Config\DB\table_prefix . 'modules';

/** @var $query Database query */
$query = 'SELECT ' . $table_modules . '.module_name, '
       . $table_settings . '.name, '
       . $table_settings . '.value '
       . 'FROM ' . $table_settings . ' '
       . 'INNER JOIN ' . $table_modules . ' '
       . 'ON ' . $table_settings . '.module=' . $table_modules . '.id';

/** @var $result mysqli_result Result of query */
$result = $dblink->query($query) or GeneralError::dbError();

/**
 * Get constants from database
 * @todo Get modules from DB
 */
while ($row = $result->fetch_assoc()) {
    switch ($row['module_name']) {
        case 'general': General::db_settings($row['name'], $row['value']); break;
        case 'lucky': Lucky::db_settings($row['name'], $row['value']); break;

        default: break;
    }
    
    General::db_settings_check();
    Lucky::db_settings_check();
}
