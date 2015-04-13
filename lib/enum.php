<?php
/**
 * Basic enum support
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
 * Enumerator base class
 * 
 * @author Brian Cline and Marek Pikuła
 * (Basing on http://stackoverflow.com/a/254543)
 */
abstract class BasicEnum {
    
    /** Cached array of constants */
    private static $constCacheArray = NULL;

    /**
     * Get list of constants
     * @return array array of constants
     */
    private static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    /**
     * Check if given const name is present in class.
     * 
     * @param string $name constant name
     * @param boolean $strict if name should be checked case sensitive
     * @return boolean if enum name exists
     */
    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    /**
     * Check if given value is represented by some constant
     * 
     * @param integer $value enum value
     * @return boolean if enum value exists
     */
    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }

    /**
     * Get name of enum from given value
     * 
     * @param integer $value enum value
     * @return string|null enum name or NULL if value not present
     */
    public static function getName($value) {
        $constants = self::getConstants();

        foreach ($constants as $name => $v) {
            if ($v == $value) {
                return $name;
            }
        }

        return NULL;
    }

    /**
     * Get value from given enum name
     * 
     * @param string $name enum name
     * @return integer|null enum value or NULL if name not present
     */
    public static function getValue($name) {
        $constants = self::getConstants();

        foreach ($constants as $n => $value) {
            if ($n == $name) {
                return $value;
            }
        }

        return NULL;
    }

}
