<?php

class MySql1
{

    /**
     * Use instead mysqli_real_escape_string
     * @param $value
     * @param null $DB_CONNECTION
     * @return string
     */
    static function mysqli_real_escape($value, $DB_CONNECTION = null)
    {
        $type = gettype($value);
        if($type === 'array') {
            return null;
        }
        if($type !== 'string') {
            return $value;
        }

        if (!$DB_CONNECTION) {
            Db1::open();
            $DB_CONNECTION = Db1::$DB_HANDLER;
        }

        try{
            $value = trim($value);
            $value = !is_numeric($value)? Db1::$DB_HANDLER->real_escape_string($value): $value;
            return $value;
        }catch (Exception $e){
            return $value;
        }
    }


    /**
     * Use to reverse mysqli_real_escape_string
     * @param $string
     * @return mixed
     */
    static function mysql_unreal_escape($string)
    {
        $characters = array('x00', 'n', 'r', '\\', '\'', '"', 'x1a');
        $o_chars = array("\x00", "\n", "\r", "\\", "'", "\"", "\x1a");
        for ($i = 0; $i < strlen($string); $i++) {
            if (substr($string, $i, 1) == '\\') {
                foreach ($characters as $index => $char) {
                    if ($i <= strlen($string) - strlen($char) && substr($string, $i + 1, strlen($char)) == $char) {
                        $string = substr_replace($string, $o_chars[$index], $i, strlen($char) + 1);
                        break;
                    }
                }
            }
        }
        return $string;
    }

    /**
     * Use to reverse mysqli_real_escape_string
     * @param $string
     * @return string
     */
    static function mysql_unreal_escape_lite($string)
    {
        return stripslashes(str_replace('\r\n', '<br/>', nl2br($string)));
    }

    /**
     * remove \/ or \//, or \\ from url
     * @param $url
     * @return string
     * @see Url1::stripSlashes()
     */
    static function url_strip_slashes($url)
    {
        while (strpos($url, '\/') > 1) $url = static::mysql_unreal_escape_lite($url);
        return $url;
    }

    /**
     * Use to reverse mysqli_real_escape_string
     * @param array $dbRowKeyValueArray
     * @param array $filterValueForKeyList
     * @return array
     */
    static function unFilterValueIfKeyExist($dbRowKeyValueArray = ['name' => 'blablabla'], $filterValueForKeyList = ['name'])
    {
        return Array1::replaceValueIfKeyExist($dbRowKeyValueArray, $filterValueForKeyList, function ($value) {
            return static::mysql_unreal_escape($value);
        });
    }


    /**
     * @param array $columnsToSearchFrom
     * @param array $textToSearch
     * @param string $logic
     * @param string $operator
     * @return string
     *      Run Many Where Query Against Columns(s)
     *          E.G
     *              function search($text){
     *                 echo static::whereValuesInColumns($columns = ['`title`', '`body`'], $values = ["%$text%", "$text"], $logic = 'OR', $operator = ' LIKE ')
     *              }
     *          OUTPUT : where title LIKE "%text%" OR title LIKE "text" OR body LIKE "%text%" OR body LIKE "text"
     *
     *  ------------------------------------
     *  Use to SelectMany
     *      $builder = Book::selectMany(false, ' WHERE '.MySql1::toWhereValuesInColumnsQuery(['title', 'body'], $searchBreak, 'OR', ' LIKE ').' ORDER BY updated_at desc', Book::$COMMON_FIELD_LITE);
     *
     */
    static function toWhereValuesInColumnsQuery($columnsToSearchFrom = [], $textToSearch = [], $logic = 'OR', $operator = '=')
    {
        $columnsToSearchFrom = Array1::filterArrayItem($columnsToSearchFrom);
        $textToSearch = Array1::filterArrayItem($textToSearch);
        $whereQuery = '';
        for ($m = 0; $m < count($columnsToSearchFrom); $m++) {
            if ($m != 0) $whereQuery .= ' ' . $logic . ' ';
            for ($i = 0; $i < count($textToSearch); $i++) {
                if ($i != 0) $whereQuery .= ' ' . $logic . ' ';
                $whereQuery .= ' ' . $columnsToSearchFrom[$m] . ' ' . $operator . " '$textToSearch[$i]' ";
            }
        }
        return $whereQuery;
    }


    /**
     * @param int $page
     * @param int $limit
     * @return string
     */
    static function makeLimitQuery($page = 1, $limit = 10)
    {
        $start_from = ($page - 1) * $limit;
        return " LIMIT $start_from, $limit ";
    }


    /**
     * @param string $prefixQuery
     * @param int $total
     * @param int $limit
     * @param string $templateClass
     * @param string $requestPageKeyName
     * @return array
     */
    static function makeLimitQueryAndPagination($prefixQuery = '', $total = 0, $limit = 10, $templateClass = 'BootstrapPaginationTemplate', $requestPageKeyName = 'page')
    {
        $current_page = String1::isset_or($_REQUEST[$requestPageKeyName], 1);
        $query = $prefixQuery . ' ' . static::makeLimitQuery($current_page, $limit);
        $total_pages = ceil($total / $limit);
        return Object1::toArrayObject(['query' => $query, 'paginate' => Page1::renderPagination($total_pages, $templateClass, $requestPageKeyName)]);
    }


}
