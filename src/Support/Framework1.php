<?php

/**
 * Determine the framework using Easktax
 * Class Framework1
 */
class Framework1
{

    /**
     * Is Framework ehex
     * @return bool|mixed
     */
    static function Ehex()
    {
        if (function_exists('framework_info()') && (framework_info()['name'] === 'ehex')) return framework_info();
        return false;
    }


}
