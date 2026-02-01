<?php

/**
 * Preference is a key value class, it save and get value just like  session / cookie
 * This is a plan to save string value, Object, Model1, in database
 * Class DbPref1
 */
abstract class DbPref1
{
    /**
     * @var Model1;
     */
    static $model = null;

    /**
     * @return Model1
     */
    private static function initClass()
    {
        // init model
        if (static::$model) return static::$model;
        $className = '__' . static::class;
        $code = 'class ' . $className . ' extends Model1 {
            //pref var
            public $id = -1;
            public $user_id = "";
            public $key = "";
            public $value = null;
            public $name = "";
        }';
        eval($code);
        static::$model = new $className();
        return static::$model;
    }

    /**
     * @return Model1
     */
    static function toModel()
    {
        static::initClass();
        return static::$model;
    }

    static function tableCreate()
    {
        static::initClass();
        return static::$model::tableCreate();
    }

    static function tableTruncate()
    {
        static::initClass();
        return static::$model::tableTruncate();
    }

    static function tableReset()
    {
        static::initClass();
        return static::$model::tableReset();
    }
    //    static function tableCreate(){  static::initClass(); return Db1::exec(static::$model->toTableCreateQuery());  }
    //    static function tableTruncate(){  static::initClass(); return Db1::exec(static::$model::toDropTableQuery()) == static::tableCreate(); }


    /**
     * Save data with respect to Model Instance
     * @param $model
     * @param string $key
     * @param string $object_or_keyValueArray
     * @param bool $replace
     */
    static function save_modelData($model, $key = '', $object_or_keyValueArray = '', $replace = true)
    {
        return static::save($key, $object_or_keyValueArray, ($model->getModelClassName()) . '=' . $model->id, $replace);
    }

    /**
     * Delete Model Data
     * @param $model
     * @param string $key
     */
    static function delete_modelData($model, $key = '')
    {
        static::delete($key, ($model->getModelClassName()) . '=' . $model->id);
    }

    /**
     * Get data with respect to Model Instance
     * @param $model
     * @param string $key
     * @param null $defaultData
     * @return array|Model1|string
     */
    static function get_modelData($model, $key = '', $defaultData = null)
    {
        $value = static::get($key, ($model->getModelClassName()) . '=' . $model->id);
        if (count($value) == 2) {
            unset($value['__id']);
            return Array1::toStringNormalizeIfSingleArray($value);
        }
        return $value ? $value : $defaultData;
    }


    /**
     * Insert New / Append / Override Existing data
     * @param $name
     * @param null $object_or_keyValueArray
     * @param string $user_id
     * @param bool $replace
     * @return bool|mysqli_result|null
     *
     */
    public static function save($name, $object_or_keyValueArray = null, $user_id = '', $replace = true)
    {
        static::initClass();
        $data = get_parent_class($object_or_keyValueArray) == Model1::class ? $object_or_keyValueArray->toArray() : Array1::toArray($object_or_keyValueArray);
        // delete
        $query = $replace ? static::$model::toDeleteWhereQuery(['name' => $name, 'user_id' => $user_id], ' AND ', ' = ') : '';
        //insert all data(key=value) into table
        foreach ($data as $key => $value) $query .= static::$model::toInsertQuery(['name' => $name, 'user_id' => $user_id, 'key' => $key, 'value' => $value]);
        return Db1::exec($query, true);
    }


    /**
     * Retrieve Data from Preference Table
     * @param null $name
     * @param string $user_id
     * @param string $key
     * @return array|Model1|string
     */
    public static function get($name = null, $user_id = '', $key = null)
    {
        static::initClass();
        $where = ['name' => $name, 'user_id' => $user_id];
        if ($key) $where['key'] = $key;
        $data = static::$model::findAll($where);
        if (empty($data)) return null;
        $result = [];
        foreach ($data as $row) $result[$row->key] = Value1::parseToDataType($row->value);
        if ($key && isset($result[$key])) return $result[$key];
        return $result;
    }

    /**
     * Delete Data from Preference Table
     * @param $name
     * @param string $user_id
     */
    public static function delete($name, $user_id = '')
    {
        static::initClass();
        return static::$model::deleteWhere(['name' => $name, 'user_id' => $user_id], ' AND ');
    }

    /**
     * Get If Name Start with Data
     * @param string $namePrefix
     * @param string $user_id
     * @return array|Model1
     */
    public static function getManyIfStartWith($namePrefix = '_', $user_id = '')
    {
        return static::getMany($namePrefix, $user_id, '{data}%');
    }

    /*    /**
         * Get If Name Contain
         * @param string $name
         * @param string $user_id
         * @return array|Model1
         */
    public static function getManyIfContain($name = '_', $user_id = '')
    {
        return static::getMany($name, $user_id, '%{data}%');
    }

    /**
     * Get If Name End With Data
     * @param string $nameSuffix
     * @param string $user_id
     * @return array|Model1
     */
    public static function getManyIfEndWith($nameSuffix = '_', $user_id = '')
    {
        return static::getMany($nameSuffix, $user_id, '%{data}');
    }


    /**
     * Get All Row
     * @param array $where
     * @param string $login
     * @param string $operator
     * @return array|ArrayObject|bool|mysqli_result|null
     */
    public static function getRawRows($where = ['name' => '', 'user_id' => ''], $login = ' AND ', $operator = ' = ')
    {
        static::initClass();
        $query = ' SELECT * FROM ' . static::$model::getTableName() . ' ' . static::$model::toWhereBuilder($where, $login, $operator, "`%s`", "'%s'");
        $data = static::$model::exec($query, true, false);
        return empty($data) ? null : $data;
    }

    /**
     * Get Associated User Info
     * @param string $user_id
     * @return array
     */
    public static function getByUser($user_id = '')
    {
        return static::normalizeRawRows(static::getRawRows(['user_id' => $user_id]));
    }

    /**
     * Is Model Exists
     * @param $name
     * @param string $user_id
     * @return bool
     */
    public static function exists($name, $user_id = '')
    {
        return !empty(static::getRawRows(['name' => $name, 'user_id' => $user_id]));
    }
}
