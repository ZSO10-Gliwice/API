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

//Version
define('VERSION_API', '0.0.1'); //API current version

//Errors

require_once BASE_DIR . 'lib/enum.php';

//enum static class for defining error codes
//mainly used for GET attributes
abstract class APIError extends BasicEnum {
    const db = 0;       //database error
    const client = 1;
    const version = 2;
    const module = 3;
    const date = 4;
}

//write XML error
function error($name, $exists, $msg = '', $arg = '') {
    echo '<error id="' . APIError::getValue($name) . '"';
    if ($arg != '') {   //additional argument for error tag
        echo ' ' . $arg;
    }
    echo '>';

    if ($msg == '') {
        //default message is for attributes handling
        if ($exists) {
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
    end();
}

//write XML error from mysqli_error and exit
function db_error($dblink) {
    end_error('db', false, mysqli_error($dblink));
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

//XML tags consts
define('XML_API_OPEN', '<api version="' . VERSION_API . '">');
define('XML_API_CLOSE', '</api>');

//end XML document and exit
function end() {
    echo XML_API_CLOSE;
    exit();
}
