<?php
/**
 * Basic XML tags class
 * 
 * @author Marek Pikuła <marpirk@gmail.com>
 * @copyright © 2015, Marek Pikuła
 */

/**
 * Basic XML tag handler
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
        if (isset(General::$settings['Version\API'])) {
            echo ' version="' . General::$settings['Version\API'] . '"';
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
     * Write debug tag.
     * 
     * For example: _&lt;dbg attr="fsad"&gt;Message&lt;/dbg&gt;_
     * 
     * @param string $msg Debug message
     * @param array $attrib Tag attribute array
     */
    static function debug($msg, $attrib = []) {
        echo '<dbg';
        foreach ($attrib as $name => $value) {
            echo ' ' . $name . '="' . $value . '"';
        }
        echo '>' . $msg . '</dbg>';
    }
    
    /**
     * Close API tag.
     */
    static function closeAPI() {
        echo '</api>';
    }
    
}