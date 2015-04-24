<?php
/**
 * Attribute checking
 * 
 * @author Marek Pikuła <marpirk@gmail.com>
 * @copyright © 2015, Marek Pikuła
 */

/** Errors are required for exec_error */
require_once 'error.php';

/**
 * Check if GET attribute exists and execute error if necessary.
 * 
 * @param string $name Attribute name
 * @param boolean $exec_error If has to exec error if not found
 * @return boolean Attribute exists
 * 
 * @package Attributes
 */
function checkAttrib($name, $exec_error = true) {
    if (filter_input(INPUT_GET, $name)) {
        return true;
    } else {
        if ($exec_error) {
            GeneralError::error(GeneralError::noAttr, '', ['attribute' => $name]);
        }
        return false;
    }
}

/**
 * Throw "attribute not valid" error.
 * 
 * @param string $attrib Attribute name
 * @param string $valid The best valid value of attrib (not mandatory)
 * @param string $msg Additional error message (not mandatory)
 * 
 * @package Attributes
 */
function errorAttribNotValid($attrib, $valid = '', $msg = '') {
    $attributes['attribute'] = $attrib;
    if ($valid != '') {
        $attributes['valid'] = $valid;
    }
    GeneralError::error(GeneralError::attrNotValid, $msg, $attributes);
}
