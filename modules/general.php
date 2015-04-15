<?php

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

require_once 'module.php';

class General extends Module {
    
    public function exec() {
        /** Check for client attribute and if it's valid */
        if ((checkAttrib('client')) && (strcasecmp(filter_input(INPUT_GET, 'client'), 'android') != 0)) {
            errorAttribNotValid('client', 'android');
        }

        /**
         * Check for version attribute and for beta version – it's unstable,
         * so any API inconsistance is user fault.
         */
        if ((checkAttrib('version')) && (strcasecmp(filter_input(INPUT_GET, 'version'), 'beta') != 0)) {
            //check if client version is up to date
            if ((strcasecmp(filter_input(INPUT_GET, 'client'), 'android') == 0)         //check for Android version
                    && (filter_input(INPUT_GET, 'version') != self::$settings['Version\Client\Android'])) {
                errorAttribNotValid('version', self::$settings['Version\Client\Android']);
            }
        }
    }

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
                            or APIError::dbError();
                }
                break;

            default: break;
        }
    }

}