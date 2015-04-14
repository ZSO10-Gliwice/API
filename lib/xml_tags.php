<?php
/**
 * Basic XML tags class.
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

/**
 * Basic XML tag handler.
 */
abstract class XML {
    
    /** @var boolean If XML document has been opened */
    static private $opened = false;
    /** @var boolean If API tag has been opened */
    static private $APIOpened = false;
    
    /**
     * Open XML document.
     */
    static function open() {
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        self::$opened = true;
    }
    
    /**
     * Open XML document if it wasn't previously opened.
     */
    static function openIfNotOpened() {
        if (!self::$opened) {
            self::open();
        }
    }
    
    /**
     * Open API tag.
     * If VERSION_API is defined then add version info. It can be not defined if
     * error appeared while getting VERSION_API from db.
     */
    static function openAPI() {
        self::openIfNotOpened();
        
        echo '<api';
        if (defined('Config\Version\API')) {
            echo ' version="' . \Config\Version\API . '"';
        }
        echo '>';
        self::$APIOpened = true;
    }
    
    /**
     * Open API tag if it wasn't previously opened.
     */
    static function openAPIIfNotOpened() {
        if (!self::$APIOpened) {
            self::openAPI();
        }
    }
    
    /**
     * Close API tag.
     */
    static function closeAPI() {
        echo '</api>';
    }
    
}