<?php
/**
 * Lucky Numbers API
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

//TORETHINK after making server side generator – it basically ok, but some
//          things may change

/** Database query */
$query = 'SELECT * FROM ' . \Config\DB\table_prefix . 'lucky';

/** Used for range based select */
$range = false;
if (checkAttrib('range', false)) {
    $range = filter_input(INPUT_GET, 'range');
}

/**
 * Validate if given date is in right format to prevent from SQL injection.
 * 
 * Date should be in format 'Y-m-d'. If it's not parse endError is thrown.
 * 
 * @param string $date date to validate
 * @return boolean if valid
 * 
 * @see APIError::endError()
 * @see APIError::parse
 * 
 * @package Modules\Lucky
 */
function validate_date($date) {
    $date_arr = explode('-', $date);
    if (count($date_arr) != 3 || !checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
        APIError::endError(APIError::parse, 'Wrong date format for date "' . $date . '"',
                            array('valid' => 'Y-m-d', 'wrong' => $date));
    }
    return $date;
}

/**
 * Date ranges.
 */
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
    $query .= ' WHERE date BETWEEN "' . $from_date . '" AND "' . $to_date . '"';
} else if (checkAttrib('date', false) && !$range) {  //filter exact date
    $query .= ' WHERE date="' . validate_date(filter_input(INPUT_GET, 'date')) . '"';
}

/** @todo Possibility to limit requested numbers */

/** @var $result mysqli_result Result of MySQL query */
$result = $dblink->query($query) or APIError::dbError($dblink->errno, $dblink->error);

$i = 0; /** Coutner of dates */
/** Final print of numbers data as XML */
while ($row = $result->fetch_assoc()) {
    /**
     * I assume, that server is inserting dates in order
     * Otherwise it may have unpredicted result! It can be handled by SQL
     * sorting, but I think that it's pointless in this situation
     * 
     * @todo Use LUCKY_SORT constant
     * @todo Sort asc/desc attribute
     */
    echo '<lucky date="' . $row['date'] . '">' . $row['numbers'] . '</lucky>';
    $i++;
}

/**
 * If no numbers were printed throw nothing to show error.
 * 
 * @see APIError::nothing
 */
if ($i == 0) {
    APIError::endError(APIError::nothing);
}
