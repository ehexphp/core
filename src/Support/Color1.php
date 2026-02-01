<?php

class Color1
{
    private static $ALL_COLOR = null;
    private static $COLOR_OFF_WHITE = ['#a36eb1', '#aa0d0d', '#af80a4', '#5be47a', '#d5cf64', '#67355a', '#f30089', '#ff4535', '#352aff', '#c6c89e', '#8900c2', '#2e4848', '#444444', '#406626', '#1a9a67', '#666626'];
    private static $COLOR = array(
        'inverse' => '#2c3e50',
        'info' => '#2d7cb5',

        'primary' => '#337ab7',
        'success' => '#6FAE6F',
        'danger' => '#EC604E',
        'error' => '#a94442',
        'warning' => '#E6AF5F',
    );

    static function initAllColor()
    {
        return static::$ALL_COLOR = (static::$ALL_COLOR) ? static::$ALL_COLOR : array_merge(static::$COLOR, String1::isset_or($_SESSION['website_color_list'], []));
    }

    static function set($name = '', $color = '')
    {
        static::$ALL_COLOR[$name] = $color;
        $_SESSION['website_color_list'][$name] = $color;
    }

    static function get($name = null)
    {
        return $name ? String1::isset_or(static::initAllColor()[$name], '') : Object1::toArrayObject(static::initAllColor());
    }

    static function getAll()
    {
        static::$ALL_COLOR;
    }

    // Fix Color
    static function getDanger()
    {
        return static::get('danger');
    }

    static function getSuccess()
    {
        return static::get('success');
    }

    static function getWarning()
    {
        return static::get('warning');
    }

    static function getInverse()
    {
        return static::get('inverse');
    }

    static function getInfo()
    {
        return static::get('info');
    }

    static function getPrimary()
    {
        return static::get('primary');
    }

    // Random Color
    static function getRandomRBG()
    {
        return "rgb(" . rand(1, 255) . "," . rand(1, 255) . "," . rand(1, 255) . ")";
    }

    static function getRandomName($nameList = ['danger', 'success', 'warning', 'inverse', 'info', 'primary'])
    {
        return Array1::pickOne($nameList);
    }

    static function getRandomList($list = null)
    {
        return Array1::pickOne(static::$COLOR_OFF_WHITE);
    }

    static function getRandomHex()
    {
        return "#" . dechex(rand(1, 255)) . dechex(rand(1, 255)) . dechex(rand(1, 255));
    }
}
