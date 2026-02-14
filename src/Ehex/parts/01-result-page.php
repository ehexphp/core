<?php
/**
 * Class ResultStatus1
 *  ResultStatus1  could be use as Result for method [just to return text, number and boolean], could be boolean by defauit and any of its method could be accessible as well
 *      $result = ResultStatus1::make(true, 'data loading...', ['some data']);
 * if($result) echo 'Working...';
 * else echo $result->message();
 * Because it does not work well with Object returning by default, Therefore, do Not Use with Api, Use ResultObject1 Instead
 * @see ResultMethod1
 */
class ResultStatus1 extends \SimpleXMLElement
{
    /**
     * @return mixed|null
     */
    // The SimpleXMLElement Hack Secrete to return many value
    private function getParams()
    {
        preg_match("#<!\-\-(.+?)\-\->#", $this->asXML(), $matches);
        if (!$matches) return null;
        return unserialize(html_entity_decode(String1::toString($matches[1])));
    }

    /**
     * @param $status
     * @param $data
     * @return ResultStatus1
     */
    private static function setParams($status, $data)
    {
        $xml = '<!--' . htmlentities(serialize($data)) . "-->" . (($status) ? '<true>1</true>' : '<false/>');
        return new self($xml);
    }


    /**
     * @param bool $status
     * @param string $message
     * @param null $data
     * @param array $tag
     * @return ResultStatus1
     */
    static function make($status = true, $message = "", $data = null, $code = 0, $tag = [])
    {
        $newS = self::setParams($status, ['message' => $message, 'data' => $data, 'code' => $code, 'tag' => $tag,]);
        return $newS;
    }

    // Get Result
    function getStatus()
    {
        return ($this != false);
    }

    function getMessage()
    {
        return ($this->getParams()['message']);
    }

    function getData()
    {
        return ($this->getParams()['data']);
    }

    function getTag()
    {
        return ($this->getParams()['tag']);
    }

    function getCode()
    {
        return ($this->getParams()['code']);
    }

    /**
     * @return Popup1
     */
    function toPopup()
    {
        return (new Popup1(($this->getStatus() ? 'Action Successful' : 'Action Failed'), ($this->getStatus() ? '' : $this->getMessage()), ($this->getStatus() ? 'success' : 'error')));
    }

    // Quick Make
    static function falseMessage($message = '', $code = 400)
    {
        return self::make(false, $message, $message, $code);
    }

    static function trueData($data = null)
    {
        return self::make(true, is_string($result) ? String1::getSomeText($result, 150) : 'Done', $data, 200);
    }

    static function catchError(callable $runCallBackMethod)
    {
        try {
            $result = $runCallBackMethod();
            return self::make(!!$result, is_string($result) ? $result : 'Done', $result, 200);
        } catch (Exception $ex) {
            return self::make(false, $ex->getMessage(), $ex->getMessage(), 400);
        }
    }
}

/**
 * Class ResultObject1 for Api return result
 *  ResultObject1  could be use as Result to return Object
 *      $result = ResultStatus1::make(true, 'data loading...', ['some data']);
 * if($result->getStatus()) echo 'Working...';
 * else echo $result->getMessage();
 * Use mostly With Api, because it allows status and result together
 * @see ResultStatus1
 */
class ResultObject1
{
    public $status = false;
    public $message = "";
    public $data = "";
    public $code = "";

    public function __construct($m_status = true, $m_message = "", $m_data = "", $code = 200)
    {
        $this->status = (bool)$m_status;
        $this->message = String1::getSomeText(String1::toString($m_message), 150);
        $this->data = $m_data;
        $this->code = $code;
    }

    public function toArray()
    {
        return ['status' => $this->status, 'message' => $this->message, 'data' => $this->data, 'code' => $this->code];
    }

    public function toHtml()
    {
        return " <h4>Status</h4><p>$this->status " . ($this->code > 0 ? ($this->code) : '') . "</p> <br/><h4>Status Message</h4><p>$this->message</p> <br/> <h4>Result Data</h4><p>" . String1::toArrayTree($this->data) . "</p>";
    }

    public function __toString()
    {
        return "{Status:" . String1::toBoolean($this->status, 'true', 'false') . " ($this->code), Message:" . '"' . $this->message . '"' . ", Data:" . String1::toArrayTree($this->data) . "}";
    }


    function getStatus()
    {
        return ($this->status);
    }

    function getCode()
    {
        return ($this->code);
    }

    function getMessage()
    {
        return ($this->message);
    }

    function getData()
    {
        return ($this->data);
    }

    static function data($data = null)
    {
        return static::make(!!$data, method_exists($data, 'message') ? $data->message() : '', $data, $data ? 200 : 400);
    }

    static function falseMessage($message = '', $code = 400)
    {
        return new self(false, $message, $message, $code);
    }

    static function trueData($data = null)
    {
        return new self(true, is_string($data) ? $data : "Done", $data);
    }

    static function make($m_status = true, $m_message = "", $m_data = null, $code = 200)
    {
        return new self($m_status, $m_message, $m_data, $code);
    }

    static function catchError(callable $runCallBackMethod)
    {
        try {
            $result = $runCallBackMethod();
            return self::make(!!$result, $result, $result);
        } catch (Exception $ex) {
            return self::make(false, $ex->getMessage(), $ex->getMessage(), $ex->getCode());
        }
    }
}

/**
 * Class Page1
 * This is created for jquery $(document).ready
 * and can be used as
 * JQuery version > 2
 * $(function(){
 * alert('page loaded');
 * });
 *
 *
 * JQuery version < 2+
 * (function($){
 * alert('alert');
 * })($);
 *
 */
class Page1
{


    public static $FLAG_SHOW_LOAD_TIME = false;
    public static $FLAG_KEEP_OLD_REQUEST = false;
    private static $is_page_wrapper_set = false;


    /**
     * Add Global Variable to Page
     * @param $variable
     * @param string $value
     */
    public static $_VARIABLE = [];

    public static function setVariable($variable, $value = '')
    {
        return static::$_VARIABLE[$variable] = $value;
    }

    public static function getVariable($variable, $defaultValue = null)
    {
        return isset(static::$_VARIABLE[$variable]) ? static::$_VARIABLE[$variable] : $defaultValue;
    }

    public static function deleteVariable($variable)
    {
        unset(static::$_VARIABLE[$variable]);
    }

    static function saveSharedVariable($data = [])
    {
        if (empty($data)) return;
        $_SESSION['__SHARED_VARIABLE'] = $data;
    }

    /**
     * @param string $data
     * @param null $uniqueSaveKey
     */
    static function printOnce($data = '', $uniqueSaveKey = null)
    {
        if (!self::$is_page_wrapper_set) die('Page1::start() and Page1::stop() not included at the beginning of your script. Or Enable Config1::AUTO_PAGE_WRAPPER');
        $hashCode = $uniqueSaveKey ? $uniqueSaveKey : md5($data);
        if (!isset($_SESSION[Session1::$NAME][Url1::getPageFullUrl_noGetParameter()]['print_once'][$hashCode])) echo $data;
        $_SESSION[Session1::$NAME][Url1::getPageFullUrl_noGetParameter()]['print_once'][$hashCode] = true;
    }

    /**
     * Open Page Wrapper for JQuery
     * @param array $styleOrScriptList
     * @param array $sharedVariable
     */
    static function start(array $styleOrScriptList = [], $sharedVariable = [])
    {
        Global1::set('pageStartTime', microtime(true), false);
        $jqueryBuffer = '<!DOCTYPE html>';
        $jqueryBuffer .= '<script> 
                            window.q = []; 
                            window.$ = function(f){ 
                                q.push(f) 
                            };
                            window.pageStartTime = performance.now();
                            console.time("[JS]"); 
                         </script>';
        $jqueryBuffer .= implode(' ', $styleOrScriptList);
        self::$is_page_wrapper_set = true;

        // set shared data
        $shareData = isset($_SESSION['__SHARED_VARIABLE']) ? $_SESSION['__SHARED_VARIABLE'] : [];
        $shareData = @array_merge($shareData, !empty($sharedVariable) ? $sharedVariable : []);
        foreach (Array1::makeArray($shareData) as $key => $value) {
            global ${$key};
            $GLOBALS["$key"] = $value;
        }

        // easy js
        $jqueryBuffer .= PHP_EOL . '<script src="' . Url1::pathToUrl(PATH_LIB_ASSETS . "js/ehex.min.js?v=2.0") . '"></script>' . PHP_EOL . '<!-- Ehex -->' . PHP_EOL . PHP_EOL;
        echo $jqueryBuffer;
        echo "<script>Cookie1.set('url_hash', window.location.hash.replace('#', ''), 1)</script>";
    }


    /**
     * End Page Wrapper for jQUERY
     * @param array $scriptOrStyleList
     * @param bool $enableToast
     */
    static function end(array $scriptOrStyleList = [], $enableToast = true)
    {
        $executionTime = (microtime(true) - Global1::get('pageStartTime')).' ms';

        echo "<script>!window.jQuery && document.write('<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"><\/script>');</script>";
        echo '<script type="text/javascript">
                    $(function(){
                        console.log("[PHP] '.$executionTime.'" );
                        console.timeEnd("[JS]");
                    });

                    if(window.$.each){
                        $.each(q, function(index, f) {
                            $(f)
                        });
                    } else{
                        console.error("[Ehex] Jquery Failed. ehex.min.js file requires jquery")
                    }
               </script>';
        echo implode(' ', $scriptOrStyleList);

        if (static::$FLAG_SHOW_LOAD_TIME) {
            $loadTime = "\n\n[PHP] $executionTime \n[JS] <script> document.writeln( (performance.now() - window.pageStartTime) + ' ms' ) </script>";
            Console1::println('<h3 align="center"> Pageload Time'.$loadTime.' </h3><hr/><h6 align="center"><strong>Current Url : </strong>' . Url1::getPageFullUrl() . '</h6>');
        }
        unset($_SESSION[Session1::$NAME][Url1::getPageFullUrl_noGetParameter()]['print_once']);
        unset($_SESSION['__SHARED_VARIABLE']);
        // popup status
        if ($enableToast) Session1::popupStatus()->toToast();
    }

    /**
     * Get Ehex EasyCore Assets Path
     * @return string
     *
     */
    static function getEhexCoreAssetsPath()
    {
        return PATH_LIB_ASSETS;
    }


    /**
     * Add Pagination to page
     * Used in MySql1::makeLimitQueryAndPagination, Model::paginateRender()
     *
     * @param string $total_pages
     * @param string $templateClass
     * @param string $requestPageKeyName
     * @return string
     */
    static function renderPagination($total_pages = 'ceil($total / $limit)', $templateClass = BootstrapPaginationTemplate::class, $requestPageKeyName = 'page')
    {
        /**
         * Use Template for Current, Next and Previous
         * i.e Convert This to Template
         * $pagLink = "<div class='pagination'>";
         * for ($i=1; $i<=$total_pages; $i++) {
         * $pagLink .= "<a href='index.php?page=".$i."'>".$i."</a>";
         * };
         * echo $pagLink . "</div>";
         */
        if ($total_pages <= 1) return "";
        $current_page = String1::isset_or($_REQUEST[$requestPageKeyName], 1);
        $pageLink = $templateClass::getContainerOpen();
        if (($current_page - 1) > 0) $pageLink .= $templateClass::getPreviousItem($templateClass::$previousClass, Url1::getPageFullUrl([$requestPageKeyName => ($current_page - 1)]));
        $pageMore = Math1::getSurroundingValues($total_pages, $current_page);
        foreach ($pageMore as $i) {
            if (+$i === +$current_page) {
                $class = $templateClass::$activeClass . ' ' . $templateClass::$disableClass;
                $link = 'javascript:void(0)';
            } else {
                $class = '';
                $link = Url1::getPageFullUrl([$requestPageKeyName => ($i)]);
            }
            $pageLink .= $templateClass::getActiveItem($class, $link, $i);
        }
        if (($current_page + 1) <= $total_pages) $pageLink .= $templateClass::getNextItem($templateClass::$nextClass, Url1::getPageFullUrl([$requestPageKeyName => ($current_page + 1)]));
        $pageLink .= $templateClass::getContainerClose();
        return $pageLink;
    }



//    /**
//     * @param array $dataList
//     * @deprecated @use Page1::start() instead
//     */
//    static function pasteAfterHeader(array $dataList = []){ self::start($dataList); }
//
//    /**
//     * @param array $dataList
//     * @deprecated @use Page1::end() instead
//     */
//    static function pasteAfterFooter(array $dataList = []){ self::end($dataList); }

    static function isMobile()
    {
        $device = FileManager1::getDatasetFile("device_regex.json", true);
        return (preg_match($device['d1'], $_SERVER['HTTP_USER_AGENT']) || preg_match($device['d2'], substr($_SERVER['HTTP_USER_AGENT'], 0, 4)));
    }
}


/**
 * Task Manager
 */
class TaskManager1
{

    private static $tasks = array();

    public static function add($taskId, $func)
    {
        static::$tasks[$taskId] = $func;
    }

    public static function run()
    {
        foreach (static::$tasks as $taskId => $func) call_user_func($func);
        return true;
    }
}


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


/**
 * Alter Html Content
 * Remove Tag, Filter Form, Encode and decode
 * Class Html1
 */
