<?php
/**
 * API error handler
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

/** Enum is used as APIError parent */
require_once 'enum.php';
/** Required for safe XML document initialization */
require_once 'xml_tags.php';

/**
 * Enum static class for handling error reporting.
 * 
 * @todo Per module errors
 * @author Marek Pikuła <marpirk@gmail.com>
 */
abstract class APIError extends BasicEnum {
    
    const mid = 0;
    
    /** Runtime error */
    const runtime = 1;
    
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
    static protected function getDefaultMessage($id, $attribs = array()) {
        if ($id == self::runtime) {
            return 'Runtime error! This should never happen! '
                 . 'Please get in touch with developers.';
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
    static protected function validateAttributesArray($id, $arr) {
        /** These attributes are included in error function */
        self::validateAttributeArray('mid', $id, $arr);
        self::validateAttributeArray('mname', $id, $arr);
        self::validateAttributeArray('eid', $id, $arr);
        self::validateAttributeArray('ename', $id, $arr);
    }
    
    static private function validateAttributeArray($name, $id, $arr) {
        if (array_key_exists($name, $arr)) {
            static::errorRuntimeError($id, false, $name);
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
    static public function error($id, $msg = '', $attribs = array()) {
        static::validateAttributesArray($id, $attribs);
        
        XML::openAPIIfNotOpened();
        
        echo '<error mid="' . static::mid . '" '
                  . 'mname="' . ModuleList::getName(static::mid) . '" '
                  . 'eid="' . $id . '" '
                  . 'ename="' . static::getName($id) . '"';
        foreach ($attribs as $name => $value) { //additional attributes
            echo ' ' . $name . '="' . $value . '"';
        }
        echo '>';

        if ($msg == '') {
            echo static::getDefaultMessage($id, $attribs);
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
     * @see GeneralError::runtimeError()
     */
    static public function errorRuntimeError($id, $should_contain, $problem_attrib) {
        $msg = 'Argument list for error(' . $id . ') function should ';
        if (!$should_contain) {
            $msg .= 'not ';
        }
        $msg .= 'contain "' . $problem_attrib . '" argument!';
        static::runtimeError($msg, array('error_id' => $id,
                                        'problem_attrib' => $problem_attrib));
    }
    
    /**
     * Throws runtime error with given message (including default error message)
     * 
     * @param string $msg message of error
     * @param array $args array of additional error arguments
     * @see GeneralError::endError()
     */
    static public function runtimeError($msg, $args = array()) {
        static::endError(self::runtime,
                         $msg . ' ' . static::getDefaultMessage(self::runtime),
                         $args);
    }

    /**
     * Write XML error, end document and close()
     * 
     * @param integer $id error id
     * @param string $msg error message
     * @param array $attrib error attributes array
     */
    static function endError($id, $msg = '', $attrib = array()) {
        static::error($id, $msg, $attrib);
        close();
    }
    
}

/**
 * End XML document and exit.
 * @package Variables
 */
function close() {
    XML::closeAPI();
    global $dblink;
    $dblink->close();
    exit();
}
