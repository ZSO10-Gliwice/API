<?php
/**
 * Main API interface
 * 
 * All attributes are handled as GET request. Each module is included from
 * different file under "modules" directory.
 * 
 * @author Marek Pikuła <marpirk@gmail.com>
 * @copyright © 2015, Marek Pikuła
 */

/** It has to be first, because of constants in $settings */
require_once 'modules/general.php';
/** Configuration is needed for database access */
require_once 'config.php';
/** Attributes checking */
require_once 'lib/attribs.php';
/** Required for safe XML tags handling */
require_once 'lib/xml_tags.php';
/** Errors are essential part of API handling */
require_once 'lib/error.php';

/**
 * Autoload module php files.
 * 
 * @param string $class_name Class name
 */
function __autoload($class_name) {
    $file = 'modules/' . strtolower($class_name) . '.php';
    if (file_exists($file)) {
        include_once $file;
    }
}

/** Get settings from database. */
require_once 'lib/db_settings.php';

XML::openAPIIfNotOpened();

$general = new General();

/** Check for module attribute */
if (checkAttrib('module')) {
    /** Check if attribute is valid and if so, include it */
    switch (filter_input(INPUT_GET, 'module')) {
        case "lucky": $lucky = new Lucky(); break;

        default: errorAttribNotValid('module', 'lucky'); break; //error if module name was not found
    }
}

/** Close document */
close();
