<?php
/**
 * Basic editable configuration for API engine
 * 
 * @copyright © 2015, Marek Pikuła
 */

namespace {
    
    /** API current version fetched from DB */
    General::$settings['Version\API'] = '0.0.2';
    
}

/**
 * Basic database configuration constants
 * 
 * @author Marek Pikuła <marpirk@gmail.com>
 */
namespace Config\DB {
    
    /** Database hostname
     * @package Config\DB */
    define('Config\DB\host', 'localhost');
    /** Database connection port
     * @package Config\DB */
    define('Config\DB\port', '3306');
    /** Database user name
     * @package Config\DB */
    define('Config\DB\user', 'zso10app');
    /** Database password
     * @package Config\DB */
    define('Config\DB\password', 'fsadpass1029');
    /** Main ZSO10 App database name
     * @package Config\DB */
    define('Config\DB\database', 'zso10app');
    /**
     * Table name prefix.
     * For example: app_lucky
     * @package Config\DB
     */
    define('Config\DB\table_prefix', 'app_');
        
}