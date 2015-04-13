<?php
/**
 * Variables and basic methods for API
 * 
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

/** @var $dblink mysqli MySQLi DB handler */
$dblink = new mysqli(\Config\DB\host, \Config\DB\user, \Config\DB\password,
                     \Config\DB\database, \Config\DB\port);
/** Handle connection error */
if ($dblink->connect_errno) {
    APIError::dbError($dblink->connect_errno, $dblink->connect_error);
}

/** @var $result mysqli_result Result of query */
$result = $dblink->query('SELECT * FROM ' . \Config\DB\table_prefix . 'settings');

/** Get constants from database */
while ($row = $result->fetch_assoc()) {
    switch ($row['name']) {
        case 'default_timezone':
            date_default_timezone_set($row['value']); break;
        
        //Versions
        case 'version_android':
            /** Current version of Android app fetched from DB
             * @package Variables */
            define('VERSION_APP_ANDROID', $row['value']); break;
        case 'version_api':
            /** API current version fetched from DB
             * @package Variables */
            define('VERSION_API', $row['value']); break;

        default:
            break;
    }
}

/*
 * Attribute checking
 */

/** Errors are required for attribute checking exec_error */
require_once 'error.php';

/**
 * Check if GET attribute exists and execute error if necessary.
 * 
 * @param string $name attribute name
 * @param boolean $exec_error if has to exec error if not found
 * @return boolean attribute exists
 * 
 * @package Variables
 */
function checkAttrib($name, $exec_error = true) {
    if (filter_input(INPUT_GET, $name)) {
        return true;
    } else {
        if ($exec_error) {
            APIError::error(APIError::noAttr, '', array('attribute' => $name));
        }
        return false;
    }
}

/**
 * Throw attribute not valid error.
 * 
 * @param string $attrib attribute name
 * @param string $valid the best valid value of attrib (not mandatory)
 * @param string $msg additional error message (not mandatory)
 * 
 * @package Variables
 */
function errorAttribNotValid($attrib, $valid = '', $msg = '') {
    $attributes['attribute'] = $attrib;
    if ($valid != '') {
        $attributes['valid'] = $valid;
    }
    APIError::error(APIError::attrNotValid, $msg, $attributes);
}

/*
 * XML default tags
 */

/** XML header tag
 * @package Variables */
define('XML_HEADER', '<?xml version="1.0" encoding="UTF-8"?>');
/** XML API opening tag 
 * @package Variables */
define('XML_API_OPEN', '<api version="' . VERSION_API . '">');
/** XML API closing tag
 * @package Variables */
define('XML_API_CLOSE', '</api>');

/**
 * End XML document and exit.
 * @package Variables
 */
function close() {
    echo XML_API_CLOSE;
    global $dblink;
    $dblink->close();
    exit();
}
