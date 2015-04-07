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
define("VERSION_API", "0.0.1"); //API current version

//Errors
require_once BASE_DIR . '/lib/enum.php';
abstract class APIError extends BasicEnum {
    const client = 1;
    const version = 2;
    const module = 3;
}

function error($id, $exists, $arg = '', $msg = '') {
    echo '<error id="' . $id . ($arg == '' ? "" : " " . $arg) . '">';
    
    if ($msg == '') {
        if ($exists) {
            echo 'Invalid ' . APIError::getName($id);
        } else {
            echo 'No "' . APIError::getName($id) . '" parameter';
        }
    } else {
        echo $msg;
    }
    
    echo '</error>';
}

//XML
define("XML_API_OPEN", '<api version="' . VERSION_API . '">');
define("XML_API_CLOSE", '</api>');