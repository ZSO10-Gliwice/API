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

/*
 * Versions
 */

//TODO get from db
define('VERSION_APP_ANDROID', 'beta');  //Current version of Android app
define('VERSION_API', '0.0.1');         //API current version

/*
 * Errors
 */

require_once 'enum.php';

//enum static class for defining error codes
//mainly used for GET attributes
abstract class APIError extends BasicEnum {
    const runtime       = 1; //runtime error
    const db            = 2; //database error
    const parse         = 3; //parse error
    const noAttr        = 4; //attribute not found
    const attrNotValid  = 5; //attribute not valid
    const nothing       = 6; //nothing to show
    
    //it's not end-user exposed so there's no need to translate these messages
    static private function getDefaultMessage($id, $attribs = array()) {
        switch ($id) {
            case APIError::runtime: return 'Runtime error! This should never'
                                            . 'happen! Get in touch with'
                                            . 'developers.';
            case APIError::db:      return 'Database error';
            case APIError::parse:   return 'Parse error';
            case APIError::noAttr:  return 'Attribute "' . $attribs['attribute'] . '" not found';
            case APIError::attrNotValid:
                $msg = 'Attribute "' . $attribs['attribute'] . '" not valid';
                if (array_key_exists('valid', $attribs)) { //yeah too many nested, but making awful oneliner would be worse
                    $msg .= ' (valid value: "' . $attribs['valid'] . '")';
                }
                return $msg;
            case APIError::nothing: return 'Nothing to show';
            
            default: return 'Unknown error';
        }
    }
    
    static private function validateAttributesArray($id, $arr) {
        if (array_key_exists('id', $arr)) {
            APIError::errorRuntimeError($id, false, 'id');
        }
        
        if (($id == APIError::db) && (!array_key_exists('db_errno', $arr))) {
            APIError::errorRuntimeError($id, true, 'db_errno');
        } else if ((($id == APIError::noAttr) || ($id == APIError::attrNotValid))
                && (!array_key_exists('attribute', $arr))) {
            APIError::errorRuntimeError($id, true, 'attribute');
        } else if (!APIError::isValidValue($id)) {
            APIError::runtimeError('Unknown error id: ' . $id,
                                    array('error_id' => $id));
        }
    }
    
    //write XML error
    static function error($id, $msg = '', $attribs = array()) {
        APIError::validateAttributesArray($id, $attribs);
        echo '<error id="' . $id . '"';
        foreach ($attribs as $name => $value) {
            echo ' ' . $name . '="' . $value . '"';
        }
        echo '>';

        if ($msg == '') {
            echo APIError::getDefaultMessage($id, $attribs);
        } else {
            echo $msg;
        }
        
        echo '</error>';
    }
    
    static private function errorRuntimeError($error_id, $should_contain, $problem_attrib) {
        $msg = 'Argument list for error(' . $error_id . ') function should ';
        if (!$should_contain) {
            $msg .= 'not ';
        }
        $msg .= 'contain "' . $problem_attrib . '" argument!';
        APIError::runtimeError($msg, array('error_id' => $error_id,
                                            'problem_attrib' => $problem_attrib));
    }
    
    //write XML end_error for emergency runtime error
    static function runtimeError($msg, $args = array()) {
        APIError::endError(APIError::runtime,
                $msg . ' ' . APIError::getDefaultMessage(APIError::runtime),
                $args);
    }

    //write XML end_error for db error
    static function dbError($errno, $error) {
        APIError::endError(APIError::db, $error, array('db_errno' => $errno));
    }

    //write XML error and end document
    static function endError($id, $msg = '', $arg = array()) {
        APIError::error($id, $msg, $arg);
        close();
    }
    
}

/*
 * Attribute checking
 */

//Check if attribute exists and if not - write error
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

function errorAttribNotValid($attrib, $valid = '', $msg = '') {
    $attributes['attribute'] = $attrib;
    if ($valid != '') {
        $attributes['valid'] = $valid;
    }
    APIError::error(APIError::attrNotValid, $msg, $attributes);
}

/*
 * XML handling
 */

define('XML_HEADER', '<?xml version="1.0" encoding="UTF-8"?>');
define('XML_API_OPEN', '<api version="' . VERSION_API . '">');
define('XML_API_CLOSE', '</api>');

//end XML document and exit
function close() {
    echo XML_API_CLOSE;
    exit();
}
