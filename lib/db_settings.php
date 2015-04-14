<?php
/**
 * Database based settings.
 * 
 * @todo Check if all constants were created
 * @author Marek Pikuła <marpirk@gmail.com>
 */

/* 
 * Copyleft (ↄ) 2015 Marek Pikuła
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/** Errors are required for DB errors handling */
require_once 'error.php';

/** @var $dblink mysqli MySQLi DB handler */
$dblink = new mysqli(\Config\DB\host, \Config\DB\user, \Config\DB\password,
                     \Config\DB\database, \Config\DB\port);
/** Handle connection error */
if ($dblink->connect_errno) {
    APIError::dbError();
}

/** @var $table_settings string Settgins table name */
$table_settings = \Config\DB\table_prefix . 'settings';
/** @var $table_modules string Modules table name */
$table_modules = \Config\DB\table_prefix . 'modules';

$query = 'SELECT ' . $table_modules . '.module_name, ' . $table_settings . '.name, ' . $table_settings . '.value '
        . 'FROM ' . $table_settings . ' '
        . 'INNER JOIN ' . $table_modules . ' '
        . 'ON ' . $table_settings . '.module=' . $table_modules . '.id';

/** @var $result mysqli_result Result of query */
$result = $dblink->query($query) or APIError::dbError();

/** Get constants from database */
while ($row = $result->fetch_assoc()) {
    switch ($row['module_name']) {
        case 'general':
            switch ($row['name']) {
                case 'default_timezone':
                    date_default_timezone_set($row['value']); break;

                //Versions
                case 'version_android':
                    /** Current version of Android app fetched from DB
                     * @package Constants */
                    define('Config\Version\Client\Android', $row['value']); break;
                case 'version_api':
                    if ($row['value'] != \Config\Version\API) {
                        $dblink->query('UPDATE ' . $table_settings . ' '
                                . 'SET value="' . \Config\Version\API . '" '
                                . 'WHERE name="version_api"')
                                or APIError::dbError();
                    }
                    break;
                
                default: break;
            }
            break;
        
        case 'lucky':
            switch ($row['name']) {
                case 'sort':
                    /** If lucky module should sort MySQL table
                     * @package Constants */
                    define('Config\Modules\Lucky\Sort', $row['value']); break;

                case 'limit':
                    /** Limit of records to get from db
                     * @package Constants */
                    define('Config\Modules\Lucky\Limit', $row['value']); break;
                    
                default: break;
            }
            break;

        default: break;
    }
}
