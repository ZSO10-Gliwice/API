<?php
/**
 * Base module class.
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

require_once __DIR__ . '/../lib/attribs.php';

abstract class Module {
    
    public static $settings;
    
    public abstract static function db_settings($name, $value);
    public abstract function exec();
    
}

require_once __DIR__ . '/../lib/enum.php';

abstract class ModuleList extends BasicEnum {
    
    const general = 0;
    const lucky = 1;
    
}
