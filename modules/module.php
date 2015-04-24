<?php
/**
 * Base module class
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

/** Attribute checking required for inherited methods of Module */
require_once __DIR__ . '/../lib/attribs.php';

/**
 * Abstract module parent class
 */
abstract class Module {
    
    /** Module id
     * @see ModuleList */
    const mid = 0;
    
    /**
     * Array of settings
     * 
     * In form `$name => $value`
     * @var array
     */
    public static $settings = [];
    
    /**
     * @var array Array of set by db setings
     */
    protected static $db_set = [];
    
    /**
     * Array of mandatory db settings.
     * 
     * Set by child.
     * @var type 
     */
    protected static $db_mandatory = [];
    
    /**
     * Database settings handler
     * 
     * Executed by `db_settings.php` on beginning. Saves values from settings
     * table to `$settings`.
     * 
     * @param string $name Name of settings entry
     * @param string $value Value of settings entry
     * @see static::$settings Settings container
     */
    public static function db_settings($name, $value) {
        static::$db_set[] = $name;
    }
    
    /**
     * Checks if all needed db settings was set.
     */
    public static function db_settings_check() {
        $bad = [];
        foreach (static::$db_mandatory as $name) {
            if (!in_array($name, static::$db_set)) {
                $bad[] = $name;
            }
        }
        if (sizeof($bad) != 0) {
            $msg = 'Database doesn\'t contain mandatory settings of module '
                    . ModuleList::getName(static::mid) . ': ';
            foreach ($bad as $name) {
                $msg .= $name . ', ';
            }
            substr($msg, 0, -2);
            GeneralError::runtimeError($msg);
        }
    }
    
}

/** Enum needed for ModuleList */
require_once __DIR__ . '/../lib/enum.php';

/**
 * Enum list of available modules
 * 
 * @todo Get from DB
 */
abstract class ModuleList extends BasicEnum {
    
    const general = 0;  /** General module       */
    const lucky = 1;    /** Lucky numbers module */
    
}
