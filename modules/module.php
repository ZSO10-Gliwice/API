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
    
    /**
     * Array of settings
     * 
     * In form `$name => $value`
     * @var array
     */
    public static $settings;
    
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
    public abstract static function db_settings($name, $value);
    
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
