<?php

class Function1
{
    static $_ENV = [];

    /**
     * Convert method to string
     * @param $function
     * @return string
     * @throws ReflectionException
     */
    static function convertToString($function)
    {
        return String1::phpFunctionToString($function);
    }

    /**
     * convert back to closure and execute it
     * @param null $serializedFunctionString
     */
    static function unSerializedClosureAndEval($serializedFunctionString = null)
    {
        eval(unserialize($serializedFunctionString));
    }

    /**
     * convert closure to string
     * @param $function
     * @return string\
     */
    static function serializedClosure($function)
    {
        return serialize(static::convertToString($function));
    }


    /**
     * Memoization of a function results in memory.
     * @param $func
     * @return Closure
     */
    static function runAndCache(string $methodName, array $args = [])
    {
        $serializedArgs = serialize($args);
        $name = $methodName . $serializedArgs;
        // cache
        if (!isset(self::$_ENV[$name])) self::$_ENV[$name] = $methodName(...$args);
        return self::$_ENV[$name];
    }

}