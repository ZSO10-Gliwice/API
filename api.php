<?php

/**
 * Main API interface
 * 
 * @author Marek Pikuła <marpirk@gmail.com>
 */

/* All attributes are handled as GET request. Each module is included from
 * different file under "api" directory.
 * 
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

/** It has to be first, because of $settings */
require_once 'modules/general.php';
/** Configuration is needed for database access */
require_once 'config.php';
/** Variables contain needed functions and constants */
require_once 'lib/attribs.php';
/** Required for safe XML tags handling */
require_once 'lib/xml_tags.php';
/** Errors are essential part of API handling */
require_once 'lib/error.php';

function __autoload($class_name) {
    $file = 'modules/' . strtolower($class_name) . '.php';
    if (file_exists($file)) {
        include $file;
    }
}

/** Settings gotten from database */
require_once 'lib/db_settings.php';

XML::openAPIIfNotOpened();

$general = new General();
$general->exec();

/** Check for module attribute */
if (checkAttrib('module')) {
    //check if attribute is valid and if so, include it
    switch (filter_input(INPUT_GET, 'module')) {
        case "lucky":
            $lucky = new Lucky();
            $lucky->exec();
            break;

        default: errorAttribNotValid('module', 'lucky'); break;  //error if module name was not found
    }
}

/** Close document */
close();
