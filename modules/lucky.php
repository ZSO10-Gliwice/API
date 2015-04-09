<?php

/*
 * Lucky Numbers API
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

//TORETHINK after making server side generator – it basically ok, but some
//          things may change

/* @var $dblink mysqli */
$query = 'SELECT * FROM ' . \Config\DB\table_prefix . 'lucky';

//used for range based select
$range = false;
if (checkAttrib('range', false)) {
    $range = filter_input(INPUT_GET, 'range');
}

//validate if given date is in right format to prevent from SQL injection
//date should be in format 'Y-m-d'
function validate_date($date) {
    $date_arr = explode('-', $date);
    if (count($date_arr) != 3 || !checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
        APIError::endError(APIError::parse, 'Wrong date format for date "' . $date . '"',
                            array('valid' => 'Y-m-d', 'wrong' => $date));
    }
    return $date;
}

$from_date;
$to_date;
if ($range) {
    if (checkAttrib('date', false)) { //present for range from 'date' to now
        $from_date = validate_date(filter_input(INPUT_GET, 'date'));
        $to_date = date('Y-m-d');
    } else if (checkAttrib('date1', false) && checkAttrib('date2', false)) { //present for range from 'date1' to 'date2'
        $from_date = validate_date(filter_input(INPUT_GET, 'date1'));
        $to_date = validate_date(filter_input(INPUT_GET, 'date2'));
    } else {
        APIError::endError(APIError::noAttr, 'No date or date1/date2 for date range');
    }
} else if (checkAttrib('date', false) && !$range) {  //filter exact date
    $query .= ' WHERE date="' . validate_date(filter_input(INPUT_GET, 'date')) . '"';
}

/* @var $result mysqli_result */
$result = $dblink->query($query) or APIError::dbError($dblink->errno, $dblink->error);

$i = 0; //coutner of dates
while ($row = $result->fetch_assoc()) {
    //I assume, that server is inserting dates in order
    //Otherwise it may have unpredicted result! It can be handled by SQL
    //sorting, but I think that it's pointless in this situation
    if ($range) {
        if ($row['date'] < $from_date) {
            continue;
        } else if ($row['date'] > $to_date) {
            break;
        }
    }
    echo '<lucky date="' . $row['date'] . '">' . $row['numbers'] . '</lucky>';
    $i++;
}

if ($i == 0) {
    APIError::error(APIError::noAttr);
}
