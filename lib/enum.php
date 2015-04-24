<?php
/**
 * Basic enum support
 * 
 * @author Marek Pikuła <marpirk@gmail.com>
 * @copyright © 2015, Marek Pikuła
 */

/**
 * Enumerator base class
 * 
 * @author Brian Cline and Marek Pikuła
 * (Basing on http://stackoverflow.com/a/254543)
 */
abstract class BasicEnum {
    
    /** @var array|null Cached array of constants */
    private static $constCacheArray = NULL;

    /**
     * Get list of constants
     * @return array Array of constants
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
     * @param string $name Constant name
     * @param boolean $strict If name should be checked case sensitive
     * @return boolean If enum name exists
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
     * Check if given value is represented by some constant.
     * 
     * @param integer $value Enum value
     * @return boolean If enum value exists
     */
    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }

    /**
     * Get name of enum from given value.
     * 
     * @param integer $value Enum value
     * @return string|null Enum name or NULL if value not present
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
     * Get value from given enum name.
     * 
     * @param string $name Enum name
     * @return integer|null Enum value or NULL if name not present
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
