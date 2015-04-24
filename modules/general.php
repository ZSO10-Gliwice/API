<?php
/**
 * General module
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

/** General class extends Module */
require_once 'module.php';

/**
 * General module
 * 
 * Handles essential request attributes and holds most of global constants.
 * 
 * @author Marek Pikuła <marpirk@gmail.com>
 */
class General extends Module {
    
    const mid = 1;
    
    protected static $db_mandatory = [
        'default_timezone',
        'version_android'
    ];
    
    /**
     * Attribute checks
     * 
     * Constructor checks for `client` and `version` attributes and sets
     * corresponding `$settings`.
     */
    public function __construct() {
        /** Check for client attribute and if it's valid */
        if ((checkAttrib('client')) && (strcasecmp(filter_input(INPUT_GET, 'client'), 'android') != 0)) {
            errorAttribNotValid('client', 'android');
        }

        /** Check for version attribute and for beta version – it's unstable,
         * so any API inconsistance is user fault. */
        if ((checkAttrib('version')) && (strcasecmp(filter_input(INPUT_GET, 'version'), 'beta') != 0)) {
            //check if client version is up to date
            if ((strcasecmp(filter_input(INPUT_GET, 'client'), 'android') == 0)         //check for Android version
                    && (filter_input(INPUT_GET, 'version') != self::$settings['Version\Client\Android'])) {
                errorAttribNotValid('version', self::$settings['Version\Client\Android']);
            }
        }
    }
    
    /**
     * @global mysqli $dblink
     * @param string $name Name of settings entry
     * @param string $value Value of settings entry
     */
    public static function db_settings($name, $value) {
        switch ($name) {
            case 'default_timezone':
                date_default_timezone_set($value); break;

            //Versions
            case 'version_android':
                /** Current version of Android app fetched from DB
                 * @package Constants */
                self::$settings['Version\Client\Android'] = $value; break;
            case 'version_api':
                if ($value != self::$settings['Version\API']) {
                    global $dblink;
                    $dblink->query('UPDATE ' . \Config\DB\table_prefix . 'settings '
                            . 'SET value="' . self::$settings['Version\API'] . '" '
                            . 'WHERE name="version_api"')
                            or static::dbError();
                }
                break;

            default: break;
        }
    }

}

/**
 * Error for General module
 * 
 * @see General General module
 * @see APIError Basic error
 */
class GeneralError extends APIError {
    
    const mid = ModuleList::general;
    
    const db           = 1;  /** Database error      */
    const noAttr       = 2;  /** Attribute not found */
    const attrNotValid = 3;  /** Attribute not valid */
    const nothing      = 4;  /** Nothing to show     */
    
    static protected function getDefaultMessage($id, $attribs = []) {
        parent::getDefaultMessage($id, $attribs);
        switch ($id) {
            case self::db:      return 'Database error';
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
    
    static protected function validateAttributesArray($id, $arr) {
        parent::validateAttributesArray($id, $arr);
        
        /** Checks for error ids */
        if (($id == self::db) && (!array_key_exists('db_errno', $arr))) {
            static::errorRuntimeError($id, true, 'db_errno');
        } else if ((($id == self::noAttr) || ($id == self::attrNotValid))
                    && (!array_key_exists('attribute', $arr))) {
            static::errorRuntimeError($id, true, 'attribute');
        } else if (!self::isValidValue($id)) {
            static::runtimeError('Unknown error id: ' . $id,
                                 ['error_id' => $id]);
        }
    }
    
    /**
     * Write XML endError for given mysqli errno and error message.
     * 
     * @see GeneralError::endError()
     * @global mysqli $dblink
     */
    static public function dbError() {
        global $dblink;
        static::endError(self::db, $dblink->error, ['db_errno' => $dblink->errno]);
    }
    
}
