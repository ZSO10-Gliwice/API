<?php
/**
 * Attribute checking
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
            GeneralError::error(GeneralError::noAttr, '', array('attribute' => $name));
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
