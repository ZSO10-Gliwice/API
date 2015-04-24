<?php
/**
 * API error handler
 * 
 * @author Marek Pikuła <marpirk@gmail.com>
 * @copyright © 2015, Marek Pikuła
 */

/** Enum is used as APIError parent */
require_once 'enum.php';
/** Required for safe XML document initialization */
require_once 'xml_tags.php';

/**
 * Enum static class for handling error reporting
 * 
 * @todo Per module errors
 * @author Marek Pikuła <marpirk@gmail.com>
 */
abstract class APIError extends BasicEnum {
    
    /** Module id
     * @see ModuleList */
    const mid = 0;
    
    /** Runtime error */
    const runtime = 0;
    
    /**
     * Generate default message for given error.
     * 
     * In attributes array it's possible to include additional informations
     * (it's mandatory for some errors).
     * 
     * It's not directly end-user exposed so there's no need to translate
     * these messages.
     * 
     * @param integer $id Error id
     * @param array $attribs Array of attributes
     * @return string Default message
     */
    static protected function getDefaultMessage($id, $attribs = []) {
        if ($id == static::runtime) {
            return 'Runtime error! This should never happen! '
                 . 'Please get in touch with developers.';
        }
    }
    
    /**
     * Validate attribute array for given error id.
     * 
     * Some errors have some mandatory attributes, which have to be included.
     * If some attribute is not included, then runtime error is thrown.
     * Not including attribute is only programmer's fault.
     * 
     * @param integer $id Error id
     * @param array $arr Array of attributes
     */
    static protected function validateAttributesArray($id, $arr) {
        /** These attributes are included in error function */
        self::validateAttributeArray('mid', $id, $arr);
        self::validateAttributeArray('mname', $id, $arr);
        self::validateAttributeArray('eid', $id, $arr);
        self::validateAttributeArray('ename', $id, $arr);
    }
    
    /**
     * Validate attribute if not exists.
     * 
     * If `$name` exists in `$arr`, throws `errorRuntimeError($id, false, $name)`.
     * 
     * @param string $name Name of attribute
     * @param integer $id Error id
     * @param array $arr Array of attributes
     */
    static private function validateAttributeArray($name, $id, $arr) {
        if (array_key_exists($name, $arr)) {
            static::errorRuntimeError($id, false, $name);
        }
    }
    
    /**
     * Write XML error.
     * 
     * @param integer $id Error id
     * @param string $msg Error message (if '' default error message is used as message)
     * @param array $attribs Array of attributes to include to XML tag
     */
    static public function error($id, $msg = '', $attribs = []) {
        static::validateAttributesArray($id, $attribs);
        
        XML::openAPIIfNotOpened();
        
        echo '<error mid="' . static::mid . '"'
                 . ' mname="' . ModuleList::getName(static::mid) . '"'
                 . ' eid="' . $id . '"'
                 . ' ename="' . static::getName($id) . '"';
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
     * Throw runtime error for wrong error attribute.
     * 
     * @param integer $id Errorneus error id
     * @param boolean $should_contain If should contain or not problem_attrib
     * @param string $problem_attrib Name of problematic argument
     * @see GeneralError::runtimeError()
     */
    static public function errorRuntimeError($id, $should_contain, $problem_attrib) {
        $msg = 'Argument list for error(' . $id . ') function should ';
        if (!$should_contain) {
            $msg .= 'not ';
        }
        $msg .= 'contain "' . $problem_attrib . '" argument!';
        static::runtimeError($msg, ['error_id' => $id,
                                    'problem_attrib' => $problem_attrib]);
    }
    
    /**
     * Throw runtime error with given message (including default error message).
     * 
     * @param string $msg Message of error
     * @param array $args Array of additional error arguments
     * @see GeneralError::endError()
     */
    static public function runtimeError($msg, $args = []) {
        static::endError(self::runtime,
                         $msg . ' ' . static::getDefaultMessage(self::runtime),
                         $args);
    }

    /**
     * Write XML error, end document and close().
     * 
     * @param integer $id Error id
     * @param string $msg Error message
     * @param array $attrib Error attributes array
     */
    static function endError($id, $msg = '', $attrib = []) {
        static::error($id, $msg, $attrib);
        close();
    }
    
}

/**
 * End XML document and exit.
 * 
 * @global mysqli $dblink
 * @package Variables
 */
function close() {
    XML::closeAPI();
    global $dblink;
    $dblink->close();
    exit();
}
