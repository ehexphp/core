<?php

class Model1FileLocator
{
    /**
     * @var Model1
     */
    private static $model = null;

    private static function initClass()
    {
//        static function getTableName(){ return String1::convertToSnakeCase(Model1FileLocator::class); }
//        static function getModelClassName(){  return static::class; }
        // init model
        if (static::$model) return static::$model;
        $className = '__' . static::class;
        $code = 'class ' . $className . ' extends Model1{
            public $id = 0;
            public $file_name = null;
            public $file_url = null;
            public $other_url = null;
            public $model_name = \'\';
            public $model_id = 0;
            public $tag = null;
            public $created_at = null;
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
//    static function tableCreate() {  static::initClass(); return static::$model::tableCreate(); }
//    static function tableTruncate() {  static::initClass(); return static::$model::tableTruncate(); }
//    static function tableReset() {  static::initClass(); return static::$model::tableReset(); }
//    static function getTableName() {  static::initClass(); return static::$model::getTableName(); }

//    public function __call($method, $parameters = null) {
//        return call_user_func_array(array($this, $method), $parameters);
//    }

    public static function __callStatic($method, $parameters)
    {
        static::initClass();
        call_user_func_array(array(static::$model, $method), $parameters);
    }

















    /***********************************
     * [ SAVE ]
     **************************/

    /**
     * Upload Model File System Asset to server database
     * @param Model1 $model ( consist of Model class and ID )
     * @param bool $append
     * @return array
     */
    public static function insertAll_fromFile_toDb($model, $append = false)
    {
        static::initClass();
        if (!$append) static::$model::deleteMany(['model_name' => $model->getModelClassName(), 'model_id' => $model->id]);
        $allFile = array_map(function ($row) use ($model) {
            return ['model_name' => $model->getModelClassName(), 'model_id' => $model->id, 'file_name' => FileManager1::getFileName($row), 'file_url' => Url1::pathToUrl($row)];
        }, $model->getFilePathList());

        // save all
        $buff = [];
        foreach ($allFile as $newModelRow) if ($newModelRow) $buff[] = static::$model::insert($newModelRow, [], 'OR', $append);
        return $buff; //return static::$model::insertMany(...$allFile);
    }


    /**
     * Just Save File Information to DB
     * @param Model1 $model ( consist of Model class and ID )
     * @param null $file_url
     * @param null $file_name
     * @param null $tag
     * @param null $other_url
     * @return mixed
     *
     */
    public static function insertUrl($model, $file_url = null, $file_name = null, $tag = null, $other_url = null)
    {
        static::initClass();
        if (!$file_url) return false;
        $file_name = (!$file_name && String1::startsWith($file_url, 'http')) ? time() . '_' . rand(1, 80) . '_' . FileManager1::getFileName($file_url) : $file_name;
        return static::$model::insert(['model_name' => $model->getModelClassName(), 'model_id' => $model->id, 'file_name' => $file_name, 'file_url' => $file_url, 'tag' => $tag, 'other_url' => $other_url], [], 'OR', true) ? $file_url : false;
    }


    /**
     * Upload File and Save File Information to DB
     * @param Model1 $model
     * @param $fileRequest
     * @param bool $append
     * @return array|bool
     */
    public static function uploadFiles_andInsertUrl($model, $fileRequest, $append = true)
    {
        if (!$model || empty($fileRequest)) return false;
        static::initClass();
        foreach (Array1::normalizeLinearRequestList($fileRequest) as $file) $model->uploadFile($file);
        return static::insertAll_fromFile_toDb($model, $append);
    }







    /***********************************
     * [ GET ]
     **************************/

    /**
     * Get File from File and DataBase
     * @param null $file_name
     * @param Model1 $model
     * @param string $orDemoPictureUrl
     * @return string
     */
    public static function find($model = null, $file_name = null, $orDemoPictureUrl = null)
    {
        $file = static::find_inFile($model, $file_name, $orDemoPictureUrl);
        return $file ? $file : static::find_inDb($model, $file_name);
    }

    /**
     * Search DataBase
     * @param string $q
     * @param int $limit
     * @param bool $urlOnly
     * @param Model1 $model (optional)
     * @param bool $asObject
     * @return string
     */
    public static function find_likely($q = '', $limit = -1, $urlOnly = true, $model = null, $asObject = false)
    {
        static::initClass();
        $limit = $limit > -1 ? ' limit ' . $limit : '';
        $query = $model ? "model_name = '" . $model->getModelClassName() . "' AND model_id = '$model->id' AND " : "";
        $whereClause = "WHERE $query file_name like '%$q%'  $limit ";
        $result = $urlOnly ? static::$model::selectManyAsList($whereClause, 'file_url') : static::$model::selectMany($asObject, $whereClause);
        return $result;
    }

    /**
     * @param null $file_name
     * @param Model1 $model
     * @param string $orDemoPictureUrl
     * @return string
     */
    public static function find_inFile($model = null, $file_name = null, $orDemoPictureUrl = '...')
    {
        return ($model && $model->id > 0) ? $model->getFileUrl($file_name, $orDemoPictureUrl) : null;
    }

    /**
     * @param null $file_name
     * @param null $model
     * @return mixed
     */
    public static function find_inDb($model = null, $file_name = null, $urlOnly = true)
    {
        if (!$model || $model->id < 1) return null;
        static::initClass();
        $whereClause = "WHERE model_name = '" . $model->getModelClassName() . "' AND model_id = '$model->id' AND file_name='$file_name' limit 1 ";
        $result = $urlOnly ? static::$model::selectManyAsList($whereClause, 'file_url') : static::$model::selectMany(true, $whereClause);
        return isset($result[0]) ? $result[0] : null;
    }


    /**
     * Get All from File and DataBase
     * @param null $model
     * @return array
     */
    public static function selectAll($model = null)
    {
        return array_unique(Array1::merge(static::selectAll_fromFile($model), static::selectAll_fromDb($model)));
    }

    /**
     * from db
     * @param Model1 $model
     * @param bool $urlOnly
     * @param bool $asObject
     * @return array
     */
    public static function selectAll_fromDb($model, $urlOnly = true, $asObject = false)
    {
        static::initClass();
        $whereClause = "WHERE model_name = '" . $model->getModelClassName() . "' AND model_id = '$model->id'  ";
        return $urlOnly ? static::$model::selectManyAsList($whereClause, 'file_url') : static::$model::selectMany($asObject, $whereClause);
    }

    /**
     * Get All Model File From FileSystem
     * @param Model1 $model
     * @param $extension
     * @param $recursive
     * @return array
     */
    public static function selectAll_fromFile($model, $extension = [], $recursive = false)
    {
        return $model->getFileUrlList($extension, $recursive);
    }




    /***********************************
     * [ DELETE ]
     *************************/
    /**
     * @param $model Model1
     * @param $file_name
     * @return array|ArrayObject|bool|mysqli_result|null
     */
    static function deleteAll($model = null)
    {
        static::initClass();
        $model->deleteAssetDirectory();
        return static::$model::deleteMany(['model_name' => $model->getModelClassName(), 'model_id' => $model->id]);
    }

    /**
     * @param $model
     * @param $file_name
     * @return array|ArrayObject|bool|mysqli_result|null
     */
    static function delete($model = null, $file_name = null)
    {
        static::delete_fromFile($model, $file_name);
        return static::delete_fromDb($model, $file_name);
    }

    /**
     * @param Model1 $model
     * @param null $file_name
     * @return bool
     */
    static function delete_fromFile($model, $file_name = null)
    {
        return $model->deleteFile($file_name);
    }

    /**
     * @param Model1 $model
     * @param null $file_name
     * @return bool
     */
    static function delete_fromDb($model, $file_name = null)
    {
        static::initClass();
        return static::$model::deleteMany(['model_name' => $model->getModelClassName(), 'model_id' => $model->id, 'file_name' => $file_name]);
    }

    /**
     * Use mostly for deleting with ID in Database
     * @param int $uniqueField
     * @param string $columnName
     * @return array|ArrayObject|bool|mysqli_result|null|ResultStatus1
     */
    static function delete_fromDb_byFieldName($uniqueField = -1, $columnName = 'id')
    {
        static::initClass();
        return static::$model::deleteBy($uniqueField, $columnName);
    }
}
