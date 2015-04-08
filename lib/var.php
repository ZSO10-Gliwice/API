<?php

/*
 * Variables for API
 * 
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

date_default_timezone_set(\Config\timezone);

//Version
//TODO get from db
define('VERSION_APP_ANDROID', 'beta');  //Current version of Android app
define('VERSION_API', '0.0.1');         //API current version

//Errors

require_once 'enum.php';

//enum static class for defining error codes
//mainly used for GET attributes
abstract class APIError extends BasicEnum {
    const db        = 0; //database error
    const nothing   = 1; //nothing to show
    const client    = 2;
    const version   = 3;
    const module    = 4;
    const date      = 5;
}

//it's not end-user exposed so there's no need to translate these messages
//write XML error
function error($name, $exists, $msg = '', $arg = '') {
    $value = APIError::getValue($name);
    echo '<error';
    if ($value != '') {
        echo ' id="' . $value . '"';
    }
    if ($arg != '') {   //additional argument for error tag
        echo ' ' . $arg;
    }
    echo '>';

    if ($msg == '') {
        if ($name == 'nothing') {
            echo 'Nothing to show';
        } else if ($exists) { //default message is for attributes handling
            echo 'Invalid ' . $name;
        } else {
            echo 'No "' . $name . '" parameter';
        }
    } else {
        echo $msg;
    }

    echo '</error>';
}

//write XML error and end document
function end_error($name, $exists, $msg = '', $arg = '') {
    error($name, $exists, $msg, $arg);
    close();
}

//write XML error for db error
function db_error($errno, $error) {
    end_error('db', true, $error, 'db_errno="' . $errno . '"');
}

//Check if attribute exists and if not - write error
function check_attrib($name, $exec_error = true) {
    if (filter_input(INPUT_GET, $name)) {
        return true;
    } else {
        if ($exec_error) {
            error($name, false);
        }
        return false;
    }
}

//XML tags
define('XML_HEADER', '<?xml version="1.0" encoding="UTF-8"?>');
define('XML_API_OPEN', '<api version="' . VERSION_API . '">');
define('XML_API_CLOSE', '</api>');

//end XML document and exit
function close() {
    echo XML_API_CLOSE;
    exit();
}
