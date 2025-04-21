<?php

class Cookie1
{
    private static function domain()
    {
        return (String1::startsWith($_SERVER['HTTP_HOST'], "localhost")) ? '' : "." . $_SERVER['HTTP_HOST'];
    }

    public static function set($name, $value = '', $days = 30, $secure = false)
    {


        $value = ((is_object($value) || is_array($value)) ? json_encode($value) : $value);
        @setcookie($name, $value, strtotime("+$days days"), '/', self::domain(), $secure);
        // $options = array (
        //     'expires' => strtotime( "+$days days" ),
        //     'path' => '/',
        //     'domain' => self::domain(), //'.example.com', // leading dot for compatibility or use subdomain
        //     'secure' => $secure,     // or false
        //     'httponly' => false,    // or false
        //     'samesite' => 'None' // None || Lax  || Strict
        // );
        // setcookie($name, $value, $options);
    }

    public static function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;

    }

    public static function getAll()
    {
        return $_COOKIE;
    }

    public static function getAndUnset($name)
    {
        $data = static::get($name);
        static::delete($name);
        return $data;
    }

    public static function exists($name)
    {
        return static::get($name);
    }

    public static function delete($name)
    {
        unset($_COOKIE[$name]);
        @setcookie($name, "");
        @setcookie($name, "", time() - 3600, '/');
        @setcookie($name, null, time() - 3600, '/', static::domain(), false);
        return true;
    }

    public static function deleteAll()
    {
        echo "<script>cookieStore.getAll().then(cookies => cookies.forEach(cookie => {
                    cookieStore.delete(cookie.name);
                }));</script>";

        /*if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time() - 1000);
                setcookie($name, '', time() - 1000, '/');
            }
        }*/
    }
}