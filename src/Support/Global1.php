<?php

/**
 * Handles all the globals for the page.
 */
class Global1{
    private static $vars = array();

    // Sets the global one time.
    public static function set($_name, $_value, $definedOnce = true)
    {
        if($definedOnce && self::has($_name))
        {
            throw new Exception('Global1::set("' . $_name . '") - Argument already exists and cannot be redefined!');
        }
        else
        {
            self::$vars[$_name] = $_value;
            return $_value;
        }
    }

    // Get the global to use.
    public static function get($_name)
    {
        if(self::has($_name))
        {
            return self::$vars[$_name];
        }
        else
        {
            throw new Exception('Global1::get("' . $_name . '") - Argument does not exist in globals!');
        }
    }

    // Get the global to use.
    public static function has($_name)
    {
        return array_key_exists($_name, self::$vars);
    }
}
