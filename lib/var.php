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

/** @var $dblink mysqli MySQLi DB handler */
$dblink = new mysqli(\Config\DB\host, \Config\DB\user, \Config\DB\password,
                     \Config\DB\database, \Config\DB\port);
//handle connection error
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
            define('VERSION_APP_ANDROID', $row['value']); break;    //Current version of Android app
        case 'version_api':
            define('VERSION_API', $row['value']); break;            //API current version

        default:
            break;
    }
}

/*
 * Errors
 */

require_once 'enum.php';

/**
 * Enum static class for handling error reporting.
 */
abstract class APIError extends BasicEnum {
    /** Runtime error       */
    const runtime       = 1;
    /** Database error      */
    const db            = 2;
    /** Parse error         */
    const parse         = 3;
    /** Attribute not found */
    const noAttr        = 4;
    /** Attribute not valid */
    const attrNotValid  = 5;
    /** Nothing to show     */
    const nothing       = 6;
    
    /**
     * Generates default message for given error.
     * 
     * In attributes array it's possible to include additional informations
     * (it's mandatory for some errors).
     * 
     * It's not directly end-user exposed so there's no need to translate
     * these messages.
     * 
     * @param integer $id error id
     * @param array $attribs array of attributes
     * @return string default message
     */
    static private function getDefaultMessage($id, $attribs = array()) {
        switch ($id) {
            case self::runtime: return 'Runtime error! This should never happen!'
                                        . 'Get in touch with developers.';
            case self::db:      return 'Database error';
            case self::parse:   return 'Parse error';
            case self::noAttr:  return 'Attribute "' . $attribs['attribute'] . '" not found';
            case self::attrNotValid:
                $msg = 'Attribute "' . $attribs['attribute'] . '" not valid';
                if (array_key_exists('valid', $attribs)) { //yeah too many nested, but making awful oneliner would be worse
                    $msg .= ' (valid value: "' . $attribs['valid'] . '")';
                }
                return $msg;
            case self::nothing: return 'Nothing to show';
            
            default: return 'Unknown error';
        }
    }
    
    /**
     * Validates attribute array for given error id.
     * 
     * Some errors have some mandatory attributes, which have to be included.
     * If some attribute is not included, then runtime error is thrown.
     * Not including attribute is only programmer's fault.
     * 
     * @param integer $id error id
     * @param array $arr array of attributes
     */
    static private function validateAttributesArray($id, $arr) {
        /** 'id' and 'name' attributes are included in error function */
        if (array_key_exists('id', $arr)) {
            self::errorRuntimeError($id, false, 'id');
        }
        if (array_key_exists('name', $arr)) {
            self::errorRuntimeError($id, false, 'name');
        }
        
        /** Checks for error ids */
        if (($id == self::db) && (!array_key_exists('db_errno', $arr))) {
            self::errorRuntimeError($id, true, 'db_errno');
        } else if ((($id == self::noAttr) || ($id == self::attrNotValid))
                    && (!array_key_exists('attribute', $arr))) {
            self::errorRuntimeError($id, true, 'attribute');
        } else if (!self::isValidValue($id)) {
            self::runtimeError('Unknown error id: ' . $id,
                               array('error_id' => $id));
        }
    }
    
    /**
     * Write XML error.
     * 
     * @param integer $id error id
     * @param string $msg error message (if '' default error message is used
     *                    as message
     * @param array $attribs array of attributes to include to XML tag
     */
    static function error($id, $msg = '', $attribs = array()) {
        self::validateAttributesArray($id, $attribs);
        
        echo '<error id="' . $id . '" name="' . self::getName($id) . '"';
        foreach ($attribs as $name => $value) { //additional attributes
            echo ' ' . $name . '="' . $value . '"';
        }
        echo '>';

        if ($msg == '') {
            echo self::getDefaultMessage($id, $attribs);
        } else {
            echo $msg;
        }
        
        echo '</error>';
    }
    
    /**
     * Throws runtime error for wrong error attribute.
     * 
     * @param integer $id errorneus error id
     * @param boolean $should_contain if should contain or not problem_attrib
     * @param stirng $problem_attrib name of problematic argument
     * @see APIError::runtimeError()
     */
    static private function errorRuntimeError($id, $should_contain, $problem_attrib) {
        $msg = 'Argument list for error(' . $id . ') function should ';
        if (!$should_contain) {
            $msg .= 'not ';
        }
        $msg .= 'contain "' . $problem_attrib . '" argument!';
        self::runtimeError($msg, array('error_id' => $id,
                                       'problem_attrib' => $problem_attrib));
    }
    
    //write XML end_error for emergency runtime error
    /**
     * Throws runtime error with given message (including default error message)
     * 
     * @param string $msg message of error
     * @param array $args array of additional error arguments
     * @see APIError::endError()
     */
    static function runtimeError($msg, $args = array()) {
        self::endError(APIError::runtime,
                       $msg . ' ' . self::getDefaultMessage(APIError::runtime),
                       $args);
    }

    //write XML end_error for db error
    /**
     * Write XML endError for given mysqli errno and error message.
     * 
     * @param integer $errno mysqli's error number
     * @param string $error mysqli's error message
     * @see APIError::endError()
     */
    static function dbError($errno, $error) {
        self::endError(self::db, $error, array('db_errno' => $errno));
    }

    /**
     * Write XML error, end document and close()
     * 
     * @param integer $id error id
     * @param string $msg error message
     * @param array $attrib error attributes array
     */
    static function endError($id, $msg = '', $attrib = array()) {
        self::error($id, $msg, $attrib);
        close();
    }
    
}

/*
 * Attribute checking
 */

//Check if attribute exists and if not - write error
/**
 * Check if GET attribute exists and execute error if necessary.
 * 
 * @param string $name attribute name
 * @param boolean $exec_error if has to exec error if not found
 * @return boolean attribute exists
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

/** XML header tag. */
define('XML_HEADER', '<?xml version="1.0" encoding="UTF-8"?>');
/** XML API opening tag */
define('XML_API_OPEN', '<api version="' . VERSION_API . '">');
/** XML API closing tag */
define('XML_API_CLOSE', '</api>');

/** End XML document and exit. */
function close() {
    echo XML_API_CLOSE;
    exit();
}
