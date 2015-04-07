<?php

/* 
 * Main API file
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

require_once 'var.php';
require_once BASE_DIR . '/api/var.php';

echo XML_HEADER . XML_API_OPEN;

if (!filter_input(INPUT_GET, 'client')) {
    error(APIError::client, false);
} else {
    if (strcasecmp(filter_input(INPUT_GET, 'client'), 'android') != 0) {
        error(APIError::client, true);
    }
}

if (!filter_input(INPUT_GET, 'version')) {
    error(APIError::version, false);
} else {
    if ((strcasecmp(filter_input(INPUT_GET, 'client'), 'android') == 0)
            && (filter_input(INPUT_GET, 'version') != VERSION_APP_ANDROID)) {
        error(APIError::version, true, 'current="' . VERSION_APP_ANDROID . '"');
    }
}

if (!filter_input(INPUT_GET, 'module')) {
    error(APIError::module, false);
} else {
    switch (filter_input(INPUT_GET, 'module')) {
        case "lucky": require 'api/lucky.php'; break;

        default: error(APIError::module, true); break;
    }
}

echo XML_API_CLOSE;
