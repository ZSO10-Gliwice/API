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

/** Lucky class extends Module */
require_once 'module.php';

/**
 * Lucky numbers module
 * 
 * Lucky numbers are in Polish _Szczęśliwe Numerki_. Every school day at the
 * beginning of day two numbers are ruffled. People with those numbers
 * in class register, cannot be asked by teacher during lesson.
 * 
 * @author Marek Pikuła <marpirk@gmail.com>
 */
class Lucky extends Module {
    
    public static $settings = array(
        'range' => false,
        'sort'  => 0,
        'limit' => 0
    );
    
    /**
     * Main Lucky number API engine
     * 
     * @todo Optimize and split
     * @global mysqli $dblink
     */
    public function __construct() {
        /** Used for range based select */
        if (checkAttrib('range', false)) {
            Lucky::$settings['range'] = filter_input(INPUT_GET, 'range');
        }
        
        /** Sorting */
        if (checkAttrib('sort', false)) {
            $tmp = filter_input(INPUT_GET, 'sort');
            if ($tmp == 0 || $tmp == -1 || $tmp == 1) {
                Lucky::$settings['sort'] = $tmp;
            } else {
                GeneralError::endError(GeneralError::attrNotValid,
                        'sort value should be -1, 0, or 1',
                        array('attribute' => 'sort'));
            }
        }
        
        if (checkAttrib('limit', false)) {
            if (intval(filter_input(INPUT_GET, 'limit')) >= 0) {
                Lucky::$settings['limit'] = filter_input(INPUT_GET, 'limit');
            } else {
                GeneralError::endError(GeneralError::attrNotValid,
                        'Limit value should be greater or equal 0',
                        array('attribute' => 'limit'));
            }
        }
        
        /** Database query */
        $query = 'SELECT * FROM ' . \Config\DB\table_prefix . 'lucky';

        /** Date ranges */
        if (Lucky::$settings['range']) {
            if (checkAttrib('date', false)) { //present for range from 'date' to now
                $from_date = Lucky::validate_date(filter_input(INPUT_GET, 'date'));
                $to_date = date('Y-m-d');
            } else if (checkAttrib('date1', false) && checkAttrib('date2', false)) { //present for range from 'date1' to 'date2'
                $from_date = Lucky::validate_date(filter_input(INPUT_GET, 'date1'));
                $to_date = Lucky::validate_date(filter_input(INPUT_GET, 'date2'));
            } else {
                GeneralError::endError(GeneralError::noAttr,
                        'No date or date1/date2 attributes for date range');
            }
            $query .= ' WHERE date'
                    . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '"';
        } else if (checkAttrib('date', false)) { //filter exact date
            $query .= ' WHERE date="' . Lucky::validate_date(filter_input(INPUT_GET, 'date')) . '"';
        }

        if (Lucky::$settings['sort'] == '1') {
            $query .= ' ORDER BY date ASC';
        } else if (Lucky::$settings['sort'] == '-1') {
            $query .= ' ORDER BY date DESC';
        }

        if (Lucky::$settings['limit'] != '0') {
            $query .= ' LIMIT ' . (Lucky::$settings['limit'] + 1);
        }

        /** @var $result mysqli_result Result of MySQL query */
        global $dblink;
        $result = $dblink->query($query) or GeneralError::dbError();

        $i = 0; /** Coutner of dates */
        /** Final print of numbers data as XML */
        while ($row = $result->fetch_assoc()) {
            if (Lucky::$settings['limit'] != '0' && $i == Lucky::$settings['limit']) {
                /** @todo Chaaange in future */
                LuckyError::endError(LuckyError::limit, '',
                        array('limit' => Lucky::$settings['limit']));
            }
            echo '<lucky date="' . $row['date'] . '">' . $row['numbers'] . '</lucky>';
            $i++;
        }
        
        /**
         * If no numbers were printed throw nothing to show error.
         * 
         * @see GeneralError::nothing
         */
        if ($i == 0) {
            GeneralError::endError(GeneralError::nothing);
        }
    }
    
    /**
     * @param string $name Name of settings entry
     * @param string $value Value of settings entry
     */
    public static function db_settings($name, $value) {
        switch ($name) {
            case 'api_sort':
                /** If lucky module should sort MySQL table */
                Lucky::$settings['sort'] = $value; break;

            case 'api_limit':
                /** Limit of records to get from db */
                Lucky::$settings['limit'] = $value; break;

            default: break;
        }
    }
    
    /**
     * Validate if given date is in right format to prevent from SQL injection.
     * 
     * Date should be in format `Y-m-d`. If it's not `endError` is thrown.
     * 
     * @param string $date Date to validate
     * @return boolean If valid
     * 
     * @see GeneralError::endError()
     * @see GeneralError::parse
     */
    protected static function validate_date($date) {
        $date_arr = explode('-', $date);
        if (count($date_arr) != 3
                || $date_arr[0] == '' || $date_arr[1] == '' || $date_arr[2] == ''
                || !checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
            LuckyError::endError(LuckyError::dateFormat, '',
                    array('valid' => 'Y-m-d', 'wrong' => $date));
        }
        return $date;
    }

}

/**
 * Error for Lucky module
 * 
 * @see Lucky Lucky module
 * @see APIError Basic error
 */
class LuckyError extends APIError {
    
    const mid = ModuleList::lucky;
    
    const dateFormat = 1; /** Date format invalid */
    const limit = 2;      /** Exceeded limit of request length */
    
    static protected function getDefaultMessage($id, $attribs = array()) {
        parent::getDefaultMessage($id, $attribs);
        switch ($id) {
            case self::dateFormat: return 'Wrong date format for date '
                                        . '"' . $attribs['wrong'] . '"';
            case self::limit: return 'Limit of db records exceeded!';

            default: return 'Unknown error';
        }
    }
    
    static protected function validateAttributesArray($id, $arr) {
        parent::validateAttributesArray($id, $arr);
        
        if (($id == self::dateFormat) && (!array_key_exists('wrong', $arr))) {
            static::errorRuntimeError($id, true, 'wrong');
        } else if (($id == self::limit) && (!array_key_exists('limit', $arr))) {
            static::errorRuntimeError($id, true, 'limit');
        }
    }
    
}
