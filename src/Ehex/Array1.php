<?php
class Array1
{


    /**
     * make value array, e.g 'samson' will become ['samson'] if no param passed in for $ifNuArray_SplitWith_orNullToWrapAsArray , ignore existing array
     * @param $value
     * @param null $optionalDelimiter
     * @return array|mixed
     *
     * @see Array1::toArray()
     */
    static function makeArray($value, $optionalDelimiter = null)
    {
        return self::toArray($value, $optionalDelimiter);
    }


    /**
     * @param string $stringArrayValue (e.g "['hello', 'world']")
     * @return array
     */
    static function stringArrayToArray($stringArrayValue = "['hello', 'world']", callable $optionalCallBackForItem = null)
    {
        if (empty($stringArrayValue)) {
            return [];
        }
        $category_list = [];
        $cat = explode(',', $stringArrayValue);
        foreach ($cat as $index => $item) {
            $item = trim($item, '\"\'[] ');
            $category_list[$index] = $optionalCallBackForItem ? $optionalCallBackForItem($item) : $item;
        }
        return $category_list;
    }

    /**
     * If Array contains only Single item, return only the item
     * @param array $arrayList
     * @return array|mixed
     */
    static function toStringNormalizeIfSingleArray($arrayList)
    {
        if (!is_array($arrayList)) return $arrayList;
        return count($arrayList) === 1 ? $arrayList[0] : $arrayList;
    }

    /**
     * Split array list with single key
     * @param array $arrayList
     * @param string $delimiterKey
     * @return array
     */
    static function split($arrayList = [], $delimiterKey = '')
    {
        $index = 0;
        $end = [];
        $start = [];
        $startListing = false;
        foreach ($arrayList as $key => $value) {
            if ($key === $delimiterKey || $value === $delimiterKey) $startListing = true;
            if ($startListing) $end[$key] = $value;
            else $start[$key] = $value;
            $index++;
        }
        // add first element to firstList
        $firstElement = [];
        foreach ($end as $key => $value) {
            $firstElement[$key] = $value;
            break;
        }
        $start = array_merge($start, $firstElement);

        return [$start, $end];
    }

    /**
     * Split array list with single key and return last list
     * @param array $arrayList
     * @param string $delimiterKey
     * @return array|mixed
     */
    static function splitAndGetLastList($arrayList = [], $delimiterKey = '')
    {
        $split = self::split($arrayList, $delimiterKey);
        return isset($split[1]) ? $split[1] : [];
    }

    /**
     * Split array list with single key and return first list
     * @param array $arrayList
     * @param string $delimiterKey
     * @return array|mixed
     */
    static function splitAndGetFirstList($arrayList = [], $delimiterKey = '')
    {
        $split = self::split($arrayList, $delimiterKey);
        return $split[0];
    }


    /**
     * make value array, e.g 'samson' will become ['samson'] if no param passed in for $ifNuArray_SplitWith_orNullToWrapAsArray , ignore existing array
     * @param $value
     * @param null $ifNuArray_SplitWith_orNullToWrapAsArray
     * @return array|mixed
     * @see Array1::makeArray()
     */
    static function toArray($value, $ifNuArray_SplitWith_orNullToWrapAsArray = null)
    {
        if (!$value) return [];
        if (is_array($value)) return $value;
        if (is_object($value)) return Object1::toArray($value);
        else {
            try {
                if ($ifNuArray_SplitWith_orNullToWrapAsArray) return explode($ifNuArray_SplitWith_orNullToWrapAsArray, $value);
            } catch (Exception $exception) {
            }
            return [$value];
        }
    }


    static function merge(...$objectList)
    {
        $arrBuf = [];
        foreach ($objectList as $obj) $arrBuf = @array_merge($arrBuf, Object1::toArray($obj));
        return $arrBuf;
    }

    static function toObject($value, $className = null)
    {
        return Object1::convertArrayToObject($value, $className);
    }


    /**
     *
     * orderBy(
     * [
     * ['id' => 2, 'name' => 'Joy'],
     * ['id' => 3, 'name' => 'Khaja'],
     * ['id' => 1, 'name' => 'Raja']
     * ],
     * 'id',
     * 'desc'
     * ); // [['id' => 3, 'name' => 'Khaja'], ['id' => 2, 'name' => 'Joy'], ['id' => 1, 'name' => 'Raja']]
     * @param array $items
     * @param string $keyToSortWith
     * @param string $orderType
     * @return array
     */
    static function orderBy(array $items, $keyToSortWith = 'id', $orderType = 'asc')
    {
        $sortedItems = [];
        foreach ($items as $item) {
            $key = is_object($item) ? $item->{$keyToSortWith} : $item[$keyToSortWith];
            $sortedItems[$key] = $item;
        }
        if ($orderType === 'desc') {
            krsort($sortedItems);
        } else {
            ksort($sortedItems);
        }
        return array_values($sortedItems);
    }

    /**
     * Groups the elements of an array based on the given function.
     * groupBy(['one', 'two', 'three'], 'strlen'); // [3 => ['one', 'two'], 5 => ['three']]
     * @param $items
     * @param $func
     * @return array
     */
    static function groupBy($items, $func)
    {
        $group = [];
        foreach ($items as $item) {
            if ((!is_string($func) && is_callable($func)) || function_exists($func)) {
                $key = call_user_func($func, $item);
                $group[$key][] = $item;
            } elseif (is_object($item)) {
                $group[$item->{$func}][] = $item;
            } elseif (isset($item[$func])) {
                $group[$item[$func]][] = $item;
            }
        }
        return $group;
    }


    /**
     * Flattens an array up to the one level depth.
     * flatten([1, [2], 3, 4]); // [1, 2, 3, 4]
     * @param array $items
     * @return array
     */
    static function flatten(array $items)
    {
        $result = [];
        foreach ($items as $item) {
            if (!is_array($item)) $result[] = $item;
            else $result = array_merge($result, array_values($item));
        }
        return $result;
    }

    /**
     * Deep flattens an array.
     * deepFlatten([1, [2], [[3], 4], 5]); // [1, 2, 3, 4, 5]
     * @param $items
     * @return array
     */
    static function deepFlatten($items)
    {
        $result = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $result = array_merge($result, self::deepFlatten($item));
            }
        }

        return $result;
    }

    public static function paginate($dataArray, $limit = 10, $templateClass = BootstrapPaginationTemplate::class, $requestPageKeyName = 'array_page')
    {
        $total = count($dataArray);
        $total_pages = ceil($total / $limit);

        $current_page = String1::isset_or($_REQUEST[$requestPageKeyName], 1);
        $current_page = ($total > 0) ? min($total_pages, $current_page) : 1;
        $start_from = $current_page * $limit - $limit;

        $smallArray = array_slice($dataArray, $start_from, $limit);

        return Object1::toArrayObject(['data' => $smallArray, 'paginate' => Page1::renderPagination($total_pages, $templateClass, $requestPageKeyName)]);
    }

    public static function isKeyValueArray($array)
    {
        return !(isset(array_keys($array)[0]) && array_keys($array)[0] === 0);
    }

    /**
     * make column name the index of the array
     * e.g Table data list with id as the list index
     * @param array $arrayList
     * @param $indexName
     * @return array
     */
    public static function columnAsIndex($arrayList = [], $indexName = "")
    {
        $planList = [];
        foreach ($arrayList as $row) $planList[$row[$indexName]] = $row;
        return $planList;
    }


    /**
     * Returns true if the provided function returns true for all elements of an array, false otherwise.
     * all([2, 3, 4, 5], function ($item) {
     * return $item > 1;
     * }); // true
     * @param $items
     * @param $functionToValidateWith
     * @return bool
     */
    function ifAll($items, $functionToValidateWith)
    {
        return count(array_filter($items, $functionToValidateWith)) === count($items);
    }

    /**
     * Returns true if the provided function returns true for at least one element of an array, false otherwise.
     *  any([1, 2, 3, 4], function ($item) {
     * return $item < 2;
     * }); // true
     * @param $items
     * @param $functionToValidateWith
     * @return string
     */
    static function ifAny($items, $functionToValidateWith)
    {
        return count(array_filter($items, $functionToValidateWith)) > 0;
    }


    /**
     * @param $array
     * @return string
     */
    static function hashCode($array)
    {
        return hash('md5', json_encode($array));
    }

    /**
     * Convert array to JSON
     */
    static function toJSON($array)
    {
        return json_encode($array);
    }

    /**
     * Load array from JSON
     * @param $jsonStringValue
     * @return array|mixed
     */
    static function fromJSON($jsonStringValue)
    {
        return static::toArray(json_decode($jsonStringValue, true));
    }

    /**
     * Save array to JSON Path
     * @param $array
     * @param null $toFilePath
     * @return bool
     * @see Array1::readFromJSON()
     */
    static function saveAsJSON($array, $toFilePath = null)
    {
        if (!$toFilePath) return false;
        $dirName = dirname($toFilePath);
        $fileName = FileManager1::getFileName($toFilePath);
        if (!empty($dirName)) FileManager1::createDirectory($dirName);
        $full_path = $dirName . '/' . $fileName;
        return FileManager1::write($full_path, static::toJSON($array));
    }

    /**
     * Load array from JSON Path
     * @param null $fromFilePath
     * @return bool|mixed
     * @see Array1::saveAsJSON()
     */
    static function readFromJSON($fromFilePath = null)
    {
        if (!file_exists($fromFilePath)) return false;
        return static::fromJSON(FileManager1::read($fromFilePath));
    }

    /**
     * Duplicate array value as key
     *  e.g [hi, hello, thnks] = [hi=hi, hello=hello, thnks=thnks]
     * @param $valueList
     * @return array
     */
    static function reUseValueAsKey($valueList)
    {
        if(!$valueList){
            return [];
        }

        $newArray = [];
        foreach ($valueList as $key => $value) {
            $newArray[$value] = $value;
        }
        return $newArray;
    }


    /**
     * This is a type of arrey that occured in form request array of form control
     * Example
     *      <input type="file" name="images[]">
     *      to get $_FILE['images'] as separate control, because the control name is array, you will need this
     *
     * "name"     =>  array(3)
     * [
     * 0 => string(8) "logo.png"
     * 1 => string(24) "FB_IMG_1477050973313.jpg"
     * 2 => string(24) "FB_IMG_1477050973313.jpg"
     * ]
     * "type"     =>  array(3)
     * [
     * 0 => string(9) "image/png"
     * 1 => string(10) "image/jpeg"
     * 2 => string(10) "image/jpeg"
     *
     *
     * @param $linearArray
     * @return array
     */
    static function normalizeLinearRequestList($linearArray)
    {
        $allKeys = array_keys($linearArray);
        $files = [];
        if (is_array($linearArray[$allKeys[0]])) {
            $totalCount = count($linearArray[$allKeys[0]]);
            for ($i = 0; $i < $totalCount; $i++) {
                foreach ($allKeys as $keyName) $files[$i][$keyName] = $linearArray[$keyName][$i];
            }
        } else return $linearArray;
        return $files;
    }


    /**
     * @param $list
     * @param string $logic
     * @return array
     */
    static function maxOrMinKeyValue($list, $logic = '>')
    {
        $keyCount = ($logic === '<') ? array_values(self::maxOrMinKeyValue($list, '>'))[0] : 0;
        foreach ($list as $value) {
            if ($logic === '>') {
                if ($value > $keyCount) $keyCount = $value;
            } else {
                if ($value < $keyCount) $keyCount = $value;
            }
        }
        $maxKey = array_search($keyCount, $list);
        return [$maxKey => $list[$maxKey]];
    }

    /**
     * @param $list
     * @param string $logic
     * @return int
     */
    static function maxOrMin($list, $logic = '>')
    {
        $keyCount = ($logic === '<') ? (self::maxOrMin($list, '>')) : 0;
        foreach ($list as $key => $value) {
            if ($logic === '>') {
                if ($value > $keyCount) $keyCount = $value;
            } else {
                if ($value < $keyCount) $keyCount = $value;
            }
        }
        return $keyCount;
    }


    /**
     * @param string $separator
     * @param $arrayList
     * @param bool $recursive
     * @return string
     */
    static function implode($separator = ',', $arrayList = [], $recursive = true)
    {
        $output = "";
        foreach ($arrayList as $av) {
            if (is_array($av) && $recursive) $output .= self::implode($separator, $av); // Recursive Use of the Array
            else $output .= $separator . $av;
        }
        return $output;
    }


    /**
     * @param string $separator
     * @param $arrayList
     * @param bool $recursive
     * @return string
     */
    static function trimKeyValue($arrayList, $removeEmptyData = true)
    {
        $buff = [];
        foreach ($arrayList as $key => $value) {
            $key = trim($key);
            $value = trim($value);
            if ($removeEmptyData) {
                if ("" != $key && "" != $value) $buff[$key] = $value;
            } else $buff[$key] = $value;
        }
        return $buff;
    }


    /**
     * Extract Array From Mark Up
     * @param $xmlObject
     * @return array
     */
    static function fromXMLObject($xmlObject)
    {
        $initArrayList = array();
        foreach ((array)$xmlObject as $index => $node)
            $initArrayList[$index] = (is_object($node)) ? self::fromXMLObject($node) : $node;
        return $initArrayList;
    }

    /**
     * Extract Array From Mark Up
     * @param $xml_data
     * @return SimpleXMLElement[]
     */
    static function fromXML($xml_data)
    {
        $xml = simplexml_load_string($xml_data); //return SimpleXMLElement, wic can be passsed to self::fromXMLObject()
        return $xml->xpath('/ROOT');
    }


    /**
     * @param $array array
     * @param string $append
     * @param string $prepend
     * @return array
     *      Surround Array Items with Appended/Prepended Data
     */
    static function wrap($array, $append = '', $prepend = '')
    {
        return array_map(function ($item) use ($append, $prepend) {
            return $append . $item . $prepend;
        }, $array);
    }

    /**
     * @param $array array
     * @return string Last Array
     */
    static function getLastItem($array)
    {
        return end($array);
    }

    /**
     * @param $array array
     * @return string Last Array
     */
    static function getFirstItem($array)
    {
        return isset($array[0]) ? $array[0] : null;
    }

    static function pickOne(array $options)
    {
        return $options[array_rand($options)];
    }

    /**
     * @param array $key_and_value
     * @param string $keyValueDelimiter
     * @param string $delimiter
     * @param string $keyWrap
     * @param string $valueWrap
     * @return string
     *      merger KeyValue together
     *      E.G self::mergeKeyValue($key_and_value = ['name'=>'samson', 'email'=>'sams@gmail.com'], $keyValueDelimiter = '=', $delimiter = ' , ', $keyWrap = "%s", $valueWrap = "(%s)")
     *          OUTPUT: name=(samson) , email=(sams@gmail.com)
     *
     */
    static function mergeKeyValue($key_and_value = [], $keyValueDelimiter = '=', $delimiter = ' ', $keyWrap = "%s", $valueWrap = "%s")
    {
        $str = '';
        $index = 0;
        foreach ($key_and_value as $key => $value) {
            if ($index != 0) $str .= $delimiter;
            $str .= sprintf($keyWrap, $key) . $keyValueDelimiter . sprintf($valueWrap, $value);
            $index++;
        }
        return $str;
    }


    /**
     * Pass in keyValue array like ['class'=>'col-3', 'value'=>'online', 'checked'] to output : class="col-3" value="online" checked
     * @param array $attributesList
     * @param array $defaultAttribute
     * @return string
     */
    static function toHtmlAttribute($attributesList = [], $defaultAttribute = [])
    {
        //d($attributesList, $defaultAttribute);
        $attributesList = array_merge(Array1::makeArray($defaultAttribute), Array1::makeArray($attributesList));

        // normalize for additional attribute
        $unsetKeyList = [];
        foreach ($attributesList as $key => $value) {
            if (String1::startsWith(trim($key), '+')) {
                $searchKey = String1::replaceStart($key, '+', '');
                $attributesList[$searchKey] = String1::isset_or($attributesList[$searchKey], '') . ' ' . $value;
                $unsetKeyList[] = $key;
            }
        }

        // unset key list
        foreach ($unsetKeyList as $key) unset($attributesList[$key]);


        // merge attribute
        if (empty($attributesList) || !is_array($attributesList)) return String1::toString($attributesList);
        $attributePairs = [];
        foreach ($attributesList as $key => $val) {
            if (is_int($key)) $attributePairs[] = $val;
            else {
                $val = htmlspecialchars($val, ENT_QUOTES);
                $attributePairs[] = "{$key}=\"{$val}\"";
            }
        }
        return join(' ', $attributePairs);
    }


    /**
     * Convert ArrayList to html table
     * Array1::toHtmlTable(User::all(), ['user_name', 'address', 'action'], [], [], function($key, $row){
     * if($key == "action"){
     * return "<a class='btn btn-danger' href='".url("/$row[id]/delete")."'>Delete</a> ";
     * }else{
     * return $row[$key];
     * }
     * })
     * @param $array
     * @param string $tableClass
     * @return string
     */
    static function toHtmlTable($array, array $allowedField = [], array $removedField = [], array $renameColumnName_oldName_equals_newName = [], callable $valueCallback = null, $maxLength = null, $tableClass = 'table table-striped table-bordered', $emptyText = '<i class="fa fa-folder-open" aria-hidden="true"></i> No Data Found!')
    {
        if (!$array || count($array) <= 0) return '<table class="' . $tableClass . '"><td>' . $emptyText . '</td></table>';

        // neat table header
        $headerListRaw = (array)$array[0]; //Array1::replaceKeyNames($array[0], $renameColumnName_oldName_equals_newName);
        $headerList = $headerListRaw;

        // Remove Header
        foreach ($removedField as $key) unset($headerList[$key]);

        // allowed column
        if (!empty($allowedField)) $headerList = array_flip($allowedField);

        // new column...
        $customHeader = [];
        foreach ($headerList as $fieldKey => $value) if (!isset($headerListRaw[$fieldKey])) $customHeader[$fieldKey] = "";

        // start table
        $html = "<table class='$tableClass'>";
        // create header row
        $html .= '<tr>';
        foreach ($headerList as $key => $value) $html .= '<th>' . ucwords(String1::convertToCamelCase(String1::isset_or($renameColumnName_oldName_equals_newName[$key], $key), ' ')) . '</th>';
        $html .= '</tr>';

        // add data rows
        foreach ($array as $key => $value) {
            // add non exists header key
            if (!empty($customHeader)) $value = Array1::merge($value, $customHeader);
            // create a row
            $html .= '<tr>';
            foreach (Array1::getCommonField(null, $value, $headerList) as $key2 => $value2) {
                $callbackOverride = $valueCallback ? $valueCallback($key2, Object1::toArrayObject($value)) : null;
                $value2 = $callbackOverride !== null ? $callbackOverride : (is_array($value2) ? json_encode($value2) : $value2);
                $html .= '<td title="' . @$value2 . '">' . ($maxLength ? String1::getSomeText(@$value2, $maxLength) : @$value2) . '</td>';
            }
            $html .= '</tr>';
        }
        // finish table and return it
        $html .= '</table>';
        return $html;
    }

    /**
     * @param string $startWith Start With String
     * @param null $andEndWith
     * @param array $arrayToSearch
     * @param array $except
     * @return array
     * @internal param string $endWith End With String
     */
    static function getArraysWith($startWith = null, $andEndWith = null, $arrayToSearch = [], $except = [])
    {
        $newVar = [];

        $isStartWithAvailable = ($startWith && $startWith !== '');
        $isEndWithAvailable = ($andEndWith && $andEndWith !== '');

        foreach ($arrayToSearch as $key => $value) {
            $addKey = [];
            if ($isStartWithAvailable && $isEndWithAvailable && !in_array($key, $except)) {
                if (String1::startsWith($key, $startWith) && String1::endsWith($key, $andEndWith)) $addKey[$key] = $value;

            } else if ($isStartWithAvailable && !in_array($key, $except)) {
                if (String1::startsWith($key, $startWith)) $addKey[$key] = $value;

            } else if ($isEndWithAvailable && !in_array($key, $except)) {
                if (String1::endsWith($key, $andEndWith)) $addKey[$key] = $value;

            } else if (!in_array($key, $except)) {
                $addKey[$key] = $value;

            }

            $newVar = array_merge($newVar, $addKey);
        }
        return $newVar;
    }


    /**
     * Filter and Remove Empty Space from Array
     * @param $delimiter
     * @param $string
     * @return array
     */
    static function splitAndFilterArrayItem($delimiter, $string)
    {
        $string = trim(String1::toString($string), $delimiter);
        return self::filterArrayItem(explode($delimiter, $string));
    }

    /**
     * Filter and Remove Empty Space from Array
     * @param $array
     * @param string $callbackFilterFunction
     * @return array
     */
    static function filterArrayItem($array, $callbackFilterFunction = 'strlen')
    {
        $strlen = function ($data) {
            return strlen(String1::toString($data));
        };
        return array_filter(Array1::toArray($array), $callbackFilterFunction === 'strlen' ? $strlen : $callbackFilterFunction);
    }


    /**
     * @param array $array_key_value
     * @param array $exceptKeyList
     * @param string $callbackSanitizeFunction
     * @return array
     *
     *  Filter array Item With A Function That accept $value Parameter
     */
    static function sanitizeArrayItemValue($array_key_value = [], $exceptKeyList = [], $callbackSanitizeFunction = 'static::getSanitizeValue')
    {
        $arrBuff = [];
        foreach ($array_key_value as $key => $value) {
            if ($exceptKeyList && (count($exceptKeyList) > 0) && in_array($key, $exceptKeyList)) $arrBuff[$key] = ($value);
            else $arrBuff[$key] = $callbackSanitizeFunction($value);
        }
        return $arrBuff;
    }


    /**
     * Filter and Remove Empty Space from Array
     * @param $arrayList
     * @param string $defaultValue
     * @param array $excludeKey
     * @return array
     */
    static function initEmptyValueTo($arrayList, $defaultValue = '', $excludeKey = [])
    {
        $arrayValueList = [];
        foreach ($arrayList as $key => $value) {
            $newData = [];

            if (!in_array($key, $excludeKey)) {
                if (String1::is_empty($value)) $newData[$key] = $defaultValue;
                else  $newData[$key] = $value;
                $arrayValueList += $newData;
            }
        }
        return $arrayValueList;
    }


    /**
     * Remove Un wanted Key From Array
     * @param $arrayList
     * @param array $excludeKey
     * @return array
     */
    static function except($arrayList, $excludeKey = [])
    {
        $arrayList = self::makeArray($arrayList);
        $excludeKey = self::makeArray($excludeKey);
        foreach ($excludeKey as $key) {
            if (isset($arrayList[$key])) unset($arrayList[$key]);
        }
        return $arrayList;
    }


    /**
     * Fill Data into array, usually useful in Table where you don't want to miss a column, and you trynna balance table rows together.
     *
     * @param $array
     * @param $spaceCountToFill
     * @param string $valueToFIll
     */
    static function fillRemainingSpace(&$array, $spaceCountToFill, $valueToFIll = ' ')
    {
        $array = array_merge($array, array_fill(0, ($spaceCountToFill), $valueToFIll));
    }


    static function trim($array = [], $trimCharSet = '( )"\'')
    {
        return array_map(function ($item) use ($trimCharSet) {
            return trim($item, $trimCharSet);
        }, $array);
    }

    public static function exists($arrayList, $keyToSearch)
    {
        if (!$arrayList) {
            return false;
        }

        if ($arrayList instanceof ArrayAccess) {
            return $arrayList->offsetExists($keyToSearch);
        }

        return array_key_exists($keyToSearch, $arrayList);
    }

    static function removeKeys($array, $keysToRemoveList = [])
    {
        foreach ($keysToRemoveList as $key) {
            if ($key) unset($array[$key]);
        };
        return $array;
    }

    static function replaceKeyName($array, $oldKeyName, $newKeyName)
    {
        $array[$newKeyName] = $array[$oldKeyName];
        unset($array[$oldKeyName]);
        return $array;
    }


    /**
     * Walk through array and replace victim value with callback
     * @param array $keyValueArrayList
     * @param array $searchKey
     * @param callable $callbackForFoundValue
     * @return array
     */
    static function replaceValueIfKeyExist($keyValueArrayList = ['name' => 'sam...'], $searchKey = ['name'], callable $callbackForFoundValue = null)
    {
        $buff = [];
        $searchKey = array_flip(array_values($searchKey));
        foreach ($keyValueArrayList as $key => $value) {
            if (isset($searchKey[$key])) $buff[$key] = $callbackForFoundValue($value);
            else $buff[$key] = $value;
        }
        return $buff;
    }

    /**
     * @param array $arrayList of Value to replace keyName
     * @param array $oldName_equals_to_newName (Replace $arrayList KeyName with keyValue)
     * @return array
     */
    static function replaceKeyNames($arrayList, $oldName_equals_to_newName = ['oldName' => 'newName'])
    {
        if (!$oldName_equals_to_newName || empty($oldName_equals_to_newName)) return $arrayList;

        $newNames = [];
        $allNewName = array_keys($oldName_equals_to_newName);
        foreach ($arrayList as $key => $value) {
            if (in_array($key, $allNewName)) $newNames[$oldName_equals_to_newName[$key]] = $value;
            else $newNames[$key] = $value;
        }
        return $newNames;
    }


    /**
     * Replace all Array Values with new input value
     * @param $arrayList
     * @param array $oldValue_equals_to_newValues
     * @return array
     */
    static function replaceValues($arrayList, $oldValue_equals_to_newValues = ['one' => '1'])
    {
        if (!$oldValue_equals_to_newValues || empty($oldValue_equals_to_newValues)) return $arrayList;
        $newValues = [];
        $allNewValues = array_keys($oldValue_equals_to_newValues);
        foreach ($arrayList as $key => $value) {
            if (in_array($value, $allNewValues)) $newValues[$key] = $allNewValues[$value];
            else $newValues[$key] = $value;
        }
        return $newValues;
    }

    /**
     * Replace all Array Key Values that contains the given string
     * @param array $arrayList
     * @param string $search
     * @param string $replace
     * @param string $searchPosition
     * @return array
     */
    static function replaceInKeys($arrayList = [], $search = '', $replace = '', $searchPosition = 'contain')
    {
        $newValues = [];
        foreach ($arrayList as $key => $value) {
            if ($searchPosition === 'start') $newKey = String1::replaceStart($key, $search, $replace);
            else if ($searchPosition === 'end') $newKey = String1::replaceEnd($key, $search, $replace);
            else $newKey = String1::replace($key, $search, $replace);
            $newValues[$newKey] = $value;
        }
        return $newValues;
    }

    /**
     * Replace all Array Key Start Values that contains the given string
     * @param array $arrayList
     * @param string $search
     * @param string $replace
     * @return array
     */
    static function replaceKeysStart($arrayList = [], $search = '', $replace = '')
    {
        return self::replaceInKeys($arrayList, $search, $replace, $searchPosition = 'start');
    }

    /**
     * Replace all Array Key End Values that contains the given string
     * @param array $arrayList
     * @param string $search
     * @param string $replace
     * @return array
     */
    static function replaceInKeysEnd($arrayList = [], $search = '', $replace = '')
    {
        return self::replaceInKeys($arrayList, $search, $replace, $searchPosition = 'end');
    }


    /**
     * Replace all Array Value Values that contains the given string
     * @param array $arrayList
     * @param string $search
     * @param string $replace
     * @param string $searchPosition
     * @return array
     */
    static function replaceInValues($arrayList = [], $search = '', $replace = '', $searchPosition = 'contain')
    {
        $newValues = [];
        foreach ($arrayList as $key => $value) {
            if ($searchPosition === 'start') $newValue = String1::replaceStart($value, $search, $replace);
            else if ($searchPosition === 'end') $newValue = String1::replaceEnd($value, $search, $replace);
            else $newValue = String1::replace($value, $search, $replace);
            $newValues[$key] = $newValue;
        }
        return $newValues;
    }

    /**
     * Replace all Array Value Start Values that contains the given string
     * @param array $arrayList
     * @param string $search
     * @param string $replace
     * @return array
     */
    static function replaceValuesStart($arrayList = [], $search = '', $replace = '')
    {
        return self::replaceInValues($arrayList, $search, $replace, $searchPosition = 'start');
    }

    /**
     * Replace all Array Value End Values that contains the given string
     * @param array $arrayList
     * @param string $search
     * @param string $replace
     * @return array
     */
    static function replaceValuesEnd($arrayList = [], $search = '', $replace = '')
    {
        return self::replaceInValues($arrayList, $search, $replace, $searchPosition = 'end');
    }


    public static function containValue($arrayList, $keyToSearch)
    {
        $valueList = array_values($arrayList);
        return isset($valueList[$keyToSearch]);
    }


    /**
     *
     * Search Array key and Value for a particular list of another array
     * @param array $arrayList
     * @param array $needleListToSearch
     * @param bool $recursive
     * @param string $searchPosition [ could be 'contain' or 'start' or 'end']
     * @return array
     * @see Array1::startsWith(),  @see Array1::endsWith()
     *
     */
    static function contain($arrayList = [], $needleListToSearch = [], $ignoreCase = true, $recursive = false, $searchPosition = 'contain')
    {
        $exists = [];
        foreach (self::makeArray($arrayList) as $key => $value) {
            foreach (self::makeArray($needleListToSearch) as $needleValue) {
                if ($ignoreCase) {
                    $key = !is_string($key) ? strtolower($key) : $key;
                    $value = !is_string($value) ? strtolower($value) : $value;
                    $needleValue = !is_string($needleValue) ? strtolower($needleValue) : $needleValue;
                }

                if ($recursive) if (is_array($value)) array_merge($exists, self::makeArray(self::search($value, $needleListToSearch, $recursive)));
                $isStartEnd = (($searchPosition === 'contain') && ((!is_array($key) && String1::contains($needleValue, $key)) || (!is_array($value) && String1::contains($needleValue, $value))));
                $isStart = (($searchPosition === 'start') && ((!is_array($key) && String1::startsWith($key, $needleValue)) || (!is_array($value) && String1::startsWith($value, $needleValue))));
                $isEnd = (($searchPosition === 'end') && ((!is_array($key) && String1::endsWith($key, $needleValue)) || (!is_array($value) && String1::endsWith($value, $needleValue))));

                if ($isStartEnd || $isStart || $isEnd) {
                    $exists[$key] = $value;
                    continue;
                }
            }
        }
        return $exists;
    }

    static function startsWith($arrayList = [], $needleListToSearch = [], $recursive = false)
    {
        return self::contain($arrayList, $needleListToSearch, $recursive, 'start');
    }

    static function endsWith($arrayList = [], $needleListToSearch = [], $recursive = false)
    {
        return self::contain($arrayList, $needleListToSearch, $recursive, 'end');
    }

    /**
     * Get Last Array
     * @param array $array
     * @return mixed
     */
    static function last($array = [])
    {
        $dd = $array;
        return end($dd);
    }

    /**
     * Array Pop off last Element
     * @param array $array
     * @return array
     */
    static function removeLast($array = [])
    {
        array_pop($array);
        return $array;
    }


    /**
     * Array Pop  off first Element
     * @param array $array
     * @return array
     */
    static function removeFirst($array = [])
    {
        array_shift($array);
        return $array;
    }


    /**
     * @param callable|null $valueCallback
     * @param array $primaryAndCompleteArray
     * @param array $otherArray
     * @return array
     *              return all common field present in $primaryAndCompleteArray and $otherArrayList1, $otherArrayList2...
     *              FOR LARAVEL REQUEST VALIDATE, USE Request2::getAvailableFields();
     */
    static public function getCommonField(callable $valueCallback = null, $primaryAndCompleteArray = [], $simpleArrayKeyList = [])
    {
        $requestKeyValue = [];
        foreach ((Array1::isKeyValueArray($simpleArrayKeyList) ? array_keys($simpleArrayKeyList) : $simpleArrayKeyList) as $key)
            if (isset($primaryAndCompleteArray[$key]))
                $requestKeyValue[$key] = ($valueCallback) ? $valueCallback($primaryAndCompleteArray[$key]) : $primaryAndCompleteArray[$key];
        return $requestKeyValue;
    }


    /**
     * Escapse Value with quote
     * @param $arrayList
     * @return string
     */
    static function addSlashes($arrayList)
    {
        if (is_array($arrayList)) {
            foreach ($arrayList as $n => $v) {
                $b[$n] = self::addSlashes($v);
            }
            return $b;
        } else {
            return addslashes($arrayList);
        }
    }


    /**
     * Get Sub Array
     * @param $array
     * @param int $endAt
     * @param int $startFrom
     * @return mixed
     */
    static function getSomeList($array, $endAt = -1, $startFrom = 0)
    {
        if ($endAt < 0) return $array;
        $total = Math1::getMinNumber([count($array), $endAt]);
        $buf = [];
        $index = 0;
        $startList = false;
        foreach ($array as $key => $value) {
            if ($index >= $startFrom) $startList = true;
            if ($startList && $index <= $total) $buf[$key] = $value;
            if ($index > $total) break;
            $index++;
        }
        return $buf;
    }


    /**
     * @param array $array
     * @param int $count
     * @param bool $allowDuplicates
     * @return array
     */
    public static function randomElements(array $array = array('a', 'b', 'c'), $count = 1, $allowDuplicates = false)
    {
        $allKeys = array_keys($array);
        $numKeys = count($allKeys);
        if (!$allowDuplicates && $numKeys < $count) throw new \LengthException(sprintf('Cannot get %d elements, only %d in array', $count, $numKeys));
        $highKey = $numKeys - 1;
        $keys = $elements = array();
        $numElements = 0;
        while ($numElements < $count) {
            $num = mt_rand(0, $highKey);
            if (!$allowDuplicates) {
                if (isset($keys[$num])) continue;
                $keys[$num] = true;
            }
            $elements[] = $array[$allKeys[$num]];
            $numElements++;
        }
        return $elements;
    }
}