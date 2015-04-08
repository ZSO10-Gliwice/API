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
//TODEBUG

$query = 'SELECT * FROM ' . \Config\DB\table_prefix . 'lucky';
/* @var $dblink mysqli */

//used for range based select
$range = false;
if (check_attrib('range', false)) {
    $range = filter_input(INPUT_GET, 'range');
}

//validate if given date is in right format to prevent from SQL injection
//date should be in format 'Y-m-d'
function validate_date($date) {
    $date_arr = explode('-', $date);
    if (count($date_arr) != 3 || !checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
        end_error('date', true, 'Wrong date format');
    }
    return $date;
}

$from_date;
$to_date;
if ($range) {
    if (check_attrib('date', false)) {                          //present for range from 'date' to now
        $from_date = validate_date(filter_input(INPUT_GET, 'date'));
        $to_date = date('Y-m-d');
    } else if (check_attrib('date1', false) && check_attrib('date2', false)) {//present for range from 'date1' to 'date2'
        $from_date = validate_date(filter_input(INPUT_GET, 'date1'));
        $to_date = validate_date(filter_input(INPUT_GET, 'date2'));
    } else {
        end_error('date', false, 'No date or date1/date2 for date range');
    }
} else if (check_attrib('date', false) && !$range) {  //filter exact date
    $query .= ' WHERE date="' . validate_date(filter_input(INPUT_GET, 'date')) . '"';
}

/* @var $result mysqli_result */
$result = $dblink->query($query) or db_error($dblink->errno, $dblink->error);

$i = 0; //coutner of dates
while ($row = $result->fetch_assoc()) {
    //I assume, that server is inserting dates in order
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
    error('nothing', false);
}
