<?php

/**
 * Convert/Get DataType
 * Class Value1
 */
class Value1
{
    const TYPE_BOOL = 'bool';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INT = 'int';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_DOUBLE = 'double';
    const TYPE_REAL = 'real';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';

    /**
     * @param mixed $value
     * @param mixed $default = null
     * @return mixed
     */
    public static function resolve($value, $default = null)
    {
        if (is_bool($value)) return $value;
        if ($value) {
            return $value;
        }
        return $default;
    }


    /**
     * @param string $type
     * @param mixed $value
     * @param mixed $default = null
     * @param bool $throwError
     * @return mixed
     */
    public static function typecast($type, $value, $default = null, $throwError = true)
    {
        switch ($type) {
            case static::TYPE_STRING:
                return (string)static::resolve((string)$value, $default);
            case static::TYPE_INT:
            case static::TYPE_INTEGER:
                return (int)static::resolve((int)$value, $default);
            case static::TYPE_FLOAT:
            case static::TYPE_DOUBLE:
            case static::TYPE_REAL:
                return (float)static::resolve((float)$value, $default);
            case static::TYPE_BOOL:
            case static::TYPE_BOOLEAN:
                return (bool)static::resolve((bool)$value, $default);
            case static::TYPE_ARRAY:
                return (array)static::resolve($value, $default);
            case static::TYPE_OBJECT:
                return (object)static::resolve($value, $default);
            default:
                if ($throwError) throw new \InvalidArgumentException(sprintf('Unexpected type "%s" for typecasting', $type));
        }
        return $default;
    }

    public static function getDataType($value)
    {
        return gettype($value);
    }

    /**
     * Convert A String value to apporpriate datatype value. e.g "24" to 24, "false" = false
     * @param $value
     * @return bool|int|string|null
     */
    public static function parseToDataType($value)
    {
        if (is_array($value) || is_null($value) || is_object($value)) return $value;
        $value = trim($value);
        if (is_numeric($value)) return +$value;
        if ($value === "true" || $value === "TRUE") return true;
        if ($value === "false" || $value === "FALSE") return false;
        if ($value === "null" || $value === "NULL") return null;
        return $value;
    }

    /**
     * if data is set and data not null
     * @param $data
     * @param string $defaultValue_IfNotSet
     * @return string
     */
    static function isset_or(&$data, $defaultValue_IfNotSet = "")
    {
        return (isset($data) && !empty($data)) ? $data : $defaultValue_IfNotSet;
    }
}
