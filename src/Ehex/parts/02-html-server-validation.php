<?php
class Html1
{
    private $dataTorender = [];

    /**
     * Enclose Html for render() method to render it.
     * Use mostly for ehex manage render();
     * Html1 constructor.
     * @param callable|null $dataTorender
     */
    public function __construct(callable $dataTorender = null)
    {
        return $this->append($dataTorender);
    }

    /**
     * Render constructor input
     * @return null|string
     */
    public function render(...$param)
    {
        $result_list = '';
        foreach ($this->dataTorender as $callableMethod)
            if (is_callable($callableMethod))
                $result_list .= String1::toString(call_user_func($callableMethod, ...$param));
        return $result_list;
    }


    /**
     * Enclose Html for render() method to render it.
     * Use mostly for ehex manage render();
     * @param callable|null $dataTorender
     */
    public function append(callable $dataTorender = null)
    {
        $this->dataTorender[] = $dataTorender;
        return $this;
    }


    /**
     * Util class for removing Html tag
     * @param $htmlContent
     * @param array $ignoreTag
     * @return null|string|string[]
     */
    static function removeTag($htmlContent, $ignoreTag = array())
    {
        $ignoreTag = array_map('strtolower', $ignoreTag);
        $rhtml = preg_replace_callback('/<\/?([^>\s]+)[^>]*>/i', function ($matches) use (&$ignoreTag) {
            return in_array(strtolower($matches[1]), $ignoreTag) ? $matches[0] : '';
        }, $htmlContent);
        return $rhtml;
    }


    /**
     * set/change attribute for tag
     * @param string $htmlContent
     * @param string $tagName
     * @param string $attribute
     * @return null|string|string[]
     */
    static function setTagAttribute($htmlContent = '<a href="https://www.xamtax.com" class="cw-link" title="xamtax">Visit xamtax</a>', $tagName = 'a', $attribute = 'style="color:red"')
    {
        return preg_replace("/(<$tagName\b[^><]*)>/i", "$1 $attribute>", $htmlContent);
    }

    /**
     * Render content in an iframe
     * @param $htmlContent
     * @param $style
     * @return string
     */
    public static function renderAsIframe($htmlContent, $style)
    {
        $htmlContent = htmlspecialchars($htmlContent);
        return <<<HTML
            <iframe srcdoc="$htmlContent" allowtransparency="true" onload="this.style.height=(this.contentDocument.body.scrollHeight+45) +'px';" style="$style; width:100%;border:none;overflow-y:hidden;overflow-x:hidden;"></iframe>
        HTML;
    }

}


/**
 * Class ServerRequest Use to call method directly with string like url
 */
class ServerRequest1
{
    public static $api_id = '';
    public static $api_key = '';
    protected static $_request = [];

    /**
     * access full parameter, either from $_REQUEST or function paramenters
     * Best for Api Request
     * @param array $defaultKeyValue
     * @return array
     */
    public static function request($defaultKeyValue = [], $forcePhpInput = false)
    {
        $targetMethod_debug_backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1];
        $signature = Array1::hashCode($targetMethod_debug_backtrace);
        global $__ENV;
        if (!isset($__ENV['method_request_param'][$signature])) $__ENV['method_request_param'][$signature] = array_merge(Object1::getCurrentMethodParams($targetMethod_debug_backtrace, false), static::$_request);
        $request = array_merge($defaultKeyValue, $__ENV['method_request_param'][$signature], ($forcePhpInput || empty($_POST) ? Array1::makeArray(@json_decode(file_get_contents('php://input'))) : []));
        return Object1::toArrayObject(false, $request);
    }


    /**
     * @param string $lookupFunction @default Format is 'className::function(param1, param2)'
     * @param string $paramDelimiter
     * @param bool $apiValidation (if true, means cannot call function, only class method can be called and the class method must have a corresponding token, api_id and api_key). (turn off if you want to use token only, or on for either token or api)
     * @return ResultObject1|mixed|string
     */
    static function callFunction($lookupFunction = 'className::function(param1, param2)', $paramDelimiter = ',', $apiValidation = true)
    {
        // It's just an ordinary route
        if(strpos($lookupFunction, '(') <= 0) {
            return ($lookupFunction);
        }

        // replace :: to @ because :: failed sometimes form/user@process...
        //String1::replace($lookupFunction, '::', '@');

        // validate API keys
        $breakSymbol = self::breakSymbol($lookupFunction);
        $lookupFunction = static::validateAndNormalizeFunction($lookupFunction, $apiValidation, $breakSymbol);
        if ($lookupFunction instanceof ResultObject1) die($lookupFunction);

        // use regex to check if string contain ( , ' ) comma and single quote
        $lookupFunction = trim($lookupFunction, '/');
        $isDoubleQuote = (!preg_match('#,[ ]*\'#', $lookupFunction) ? '"' : "'");
        //return str_getcsv($lookupFunction, $paramDelimiter, $isDoubleQuote );

        // is valid request
        if (strpos($lookupFunction, '(') <= 0) {
            return 'Not a valid CLF Request';
        }

        // split up string
        $openB = strpos($lookupFunction, '(');
        $functionAndClass = substr($lookupFunction, 0, $openB);
        $rawParameters = substr($lookupFunction, $openB);

        // split of Url if it contains '?'
        $urlParameter = [];
        if (!String1::endsWith(')', $rawParameters) && String1::contains('?', $rawParameters)) {
            $urlParameter = explode('?', $rawParameters);
            $rawParameters = $urlParameter[0];
            parse_str($urlParameter[1], $urlParameter);
        }

        // split and filter parameter to array (filter out ", ', (, ), ;, )  // first remove space and later rater remove quote because of string like "      hello", so the space in quote preserved for some reason would not be removed as well
        $parameterList = [];
        if (trim($rawParameters, "()") != "") {
            $parameterList = array_map(function ($item) {
                return trim($item, ' ');
            }, str_getcsv(trim($rawParameters, '();'), $paramDelimiter, $isDoubleQuote));
            $parameterList = array_map(function ($item) {
                return Value1::parseToDataType(trim($item, '"\''));
            }, $parameterList);
        }


        // run the method and function
        if (strpos($lookupFunction, $breakSymbol) > 0) {
            try {
                $class_and_method = explode($breakSymbol, $functionAndClass);
                $callAs = (($breakSymbol == '@' || $breakSymbol == '.') ? (new $class_and_method[0]) : $class_and_method[0]);
                // add parameter to request for verbose access via static::$request in ServerRequest extended Class
                static::$_request = static::getMergeRequestWithFunctionParameter($parameterList, $class_and_method[1], $callAs);

                // override method param with self::request() value in proper order
                $request = self::request();
                $request['request'] = String1::isset_or($request['request'], $request);
                $request['args'] = String1::isset_or($request['args'], $parameterList);
                $parameterList = Class1::getMethodParams($class_and_method[1], $callAs, $request, false);

                // return processed data
                return call_user_func_array([$callAs, $class_and_method[1]], $parameterList);
            } catch (Exception $exception) {
                die(self::serverErrorAsResultObject1($functionAndClass, $parameterList, 'method_call_error-' . $exception->getMessage()));
            }
        } else {
            // insert function and param // Only Method
            $request = self::request();
            $request['request'] = String1::isset_or($request['request'], $request);
            $request['args'] = String1::isset_or($request['args'], $parameterList);
            $parameterList = Class1::getMethodParams($functionAndClass, null, $request, false);

            try {
                return call_user_func_array($functionAndClass, $parameterList);
            } catch (Exception $exception) {
                die(self::serverErrorAsResultObject1($functionAndClass, $parameterList, 'function_call_error-' . $exception->getMessage()));
            }
        }
    }


    /**
     * Add all parameter by name to request... for verbose access via static::$request in ServerRequest extended Class
     * @param $functionParamerList
     * @param $functionName
     * @param null $className
     * @return array
     * @throws ReflectionException]
     */
    public static function getMergeRequestWithFunctionParameter($functionParamerList, $functionName, $className = null)
    {
        $classInfo = $className ? (new ReflectionMethod($className, $functionName)) : (new ReflectionMethod($functionName));
        $classParam = [];
        $index = 0;
        foreach ($classInfo->getParameters() as $param) {
            $classParam[$param->name] = Value1::isset_or($functionParamerList[$index], null);
            $index++;
        }
        return array_merge($classParam, $_REQUEST, $_FILES);
    }


    private static function serverErrorAsResultObject1($functionAndClass = 'functionName', $parameterList = [], $exception = '')
    {
        return json_encode((new ResultObject1(false, $exception, String1::escapeQuotes($functionAndClass), 401))->toArray()); // RegEx1::getSanitizeAlphaNumeric($functionAndClass, '_') . '( '       .implode(',,,', $parameterList).        ' ) call'
    }


    /**
     * @param $functionName
     * @return null|string
     * Extract The Symbol Used as a break
     *  if Symbol is @ or ., call class object method and static method. else if symbol is ::, call static method only
     */
    private static function breakSymbol($functionName)
    {
        if (strpos($functionName, '(') > -1) $functionName = String1::getSubString($functionName, strpos($functionName, '('));
        else if (strpos($functionName, '?') > -1) $functionName = String1::getSubString($functionName, strpos($functionName, '?'));

        if (String1::contains('@', $functionName)) return '@';
        else if (String1::contains('::', $functionName)) return '::';
        else if (String1::contains('.', $functionName)) return '.';
        else return null;
    }

    /**
     * @param $functionName
     * @param bool $enableApiAuth . turn off if you want to use token only, or on for either token or api
     * @return ResultObject1|string
     */
    private static function validateAndNormalizeFunction($functionName, $enableApiAuth = true, $breakSymbol = null)
    {
        // normalize url
        $functionName = (String1::contains($breakSymbol, $functionName) || static::class === self::class) ? $functionName : static::class . $breakSymbol . $functionName;
        if (!String1::contains('(', $functionName)) {
            if (String1::contains('?', $functionName)) {
                $sp = explode('?', $functionName);
                $functionName = $sp[0] . '()' . $sp[0];
            } else
                $functionName .= '()';
        }


        // check if class::method exist in token bypass session or class::$CLF_BYPASS_TOKEN_LIST = [] consist of bypassable method
        if (!self::validateCLFAndBypassedToken($functionName, $breakSymbol)) {
            // is auth required
            $className = explode($breakSymbol, $functionName)[0];

            // check if serverRequest class is called and not method
            if ($enableApiAuth) {
                if (!String1::contains($breakSymbol, $functionName) || !class_exists($className) || !array_key_exists(ServerRequest1::class, class_parents($className)))
                    die(self::serverErrorAsResultObject1($functionName, [], 'invalid_function_called- ServerRequest1 or API1 extended Class required!'));
            }

            // validate if API_KEY is valid with REQUEST[API_KEY] or if token is valid with Saved token data
            if (class_exists($className)) {
                if (method_exists($className, 'onApiStart')) {
                    if (!$className::onApiStart($_REQUEST)) return die(self::serverErrorAsResultObject1($functionName, [], 'permission_denied- onApiStart request denied'));
                }
                if ((!method_exists($className, 'isUserAllowed') || !$className::isUserAllowed()) && $className !== Db1::class) die(self::serverErrorAsResultObject1($functionName, [], 'permission_denied- permission denied, user cannot access non-api/non-user-permitted class'));

                // validate token
                if (!$enableApiAuth && method_exists($className, 'isApiAuthValid') && !$className::isApiAuthValid()) die(self::serverErrorAsResultObject1($functionName, [], 'permission_denied- request token not valid, please go back'));
                if ($enableApiAuth) {
                    // validate api_key/api_id where needed
                    $isModelOrControllerClassExists = array_key_exists(Model1::class, class_parents($className)) || array_key_exists(Controller1::class, class_parents($className));
                    if ($isModelOrControllerClassExists) {
                        if (empty($className::$api_id) && empty($className::$api_key)) die(self::serverErrorAsResultObject1($functionName, [], 'permission_not_set- request to Model1/Controller1 class as api requires api_id/api_key to be set in class. Otherwise, extends Api1 class instead'));
                    }
                    if (!(array_key_exists(Api1::class, class_parents($className)) && String1::isset_or($_REQUEST['api_id'], '') == $className::$api_id && String1::isset_or($_REQUEST['api_key'], '') == $className::$api_key)) die(self::serverErrorAsResultObject1($functionName, [], 'permission_denied- request api_id or api_key not valid'));
                }
            }
        }


        // return parse name
        return $functionName;
    }


    /**
     * Use bypassToken if you want to avoid the use of token, maybe token is too long in your url or other cases.
     *  Note, only use when it is absolutly neccessary... and not on form. Because of CSRF attack
     * @param string $functionName
     * @param $token
     */
    static function bypassToken($functionName = "class@function(...)", $token = null)
    {
        if (!$token || ($token !== token())) return die(Console1::println("bypassToken Required a valid token"));
        $_SESSION[Session1::$NAME]['__bypassed_request'][$functionName] = $token;
        return $functionName;
    }

    /**
     * This method check if developer has override method Using SessionCookie and Class static $CLF_BYPASS_TOKEN_LIST variable
     * @param null $fullClassFunctionName
     * @param null $delimiterSymbol
     * @return bool
     */
    static function validateCLFAndBypassedToken($fullClassFunctionName = null, $delimiterSymbol = null)
    {
        //check if class::method exist in token bypass session
        if (isset($_SESSION[Session1::$NAME]['__bypassed_request'][$fullClassFunctionName])) return $_SESSION[Session1::$NAME]['__bypassed_request'][$fullClassFunctionName];
        else {
            // verify if class::$CLF_BYPASS_TOKEN_LIST = [] consist of the bypassable method
            if ($delimiterSymbol) {
                list($className, $method) = explode($delimiterSymbol, $fullClassFunctionName);
                $method = explode("(", $method)[0];

                // is method allow in CLF_CALLABLE_LIST
                if (!config('DEBUG_MODE', true) && isset($className::$CLF_CALLABLE_LIST) && (trim(@$className::$CLF_CALLABLE_LIST[0]) !== '*')) {
                    if (!in_array($method, $className::$CLF_CALLABLE_LIST)) return die(self::serverErrorAsResultObject1($method, [], "permission_denied- method '$method' not exists in model CLF_CALLABLE_LIST list, please include it"));
                }

                // should we bypass method
                if (isset($className::$CLF_BYPASS_TOKEN_LIST)) {
                    if (in_array($method, $className::$CLF_BYPASS_TOKEN_LIST)) return true;
                }
            }
        }
        return false;
    }


    /**
     * @param string $exec uses eval to execute any php code given [ note: this is dangerous and not the best way, use @call_url instead]
     * @return mixed
     */
    //static function makeAndCall($exec = 'className::function("param1", "param2")'){
    //    return eval($exec.";");
    //}


    /**
     * Get all available method
     */
    static public function help()
    {
        echo '<div style="margin:100px"><h3>Ehex ' . ucfirst(static::class) . ' Class Method List</h3><hr/>';
        $param = String1::contains('?', Url1::getPageFullUrl()) ? explode('?', Url1::getPageFullUrl())[1] : null;
        foreach (get_class_methods(static::class) as $method) {
            $full_link = Form1::callApi(static::class . '@' . $method . '(...)' . ($param ? '?' . $param : ''));
            echo Console1::d("<h3>function $method(...) <br/><a href='$full_link' target='_blank'>$full_link</a></h3><hr/>");
        }
        echo '</div>';
        return 'Ehex. ' . ucfirst(static::class) . ' Api Class ';
    }
}


/**
 * Regular expression operation
 * Class RegEx1
 */
class RegEx1
{

    static function removeTags($htmlString)
    {
        return preg_replace('/<[^>]*>/', ' ', $htmlString);
    }

    static function removeMultipleSpace($string)
    {
        trim(preg_replace('/ {2,}/', ' ', $string));
    }


    static function splitStringByBracket($bracketDelimiterString = 'hello (world) i am (samson)')
    {
        return preg_split("/[()]+/", $bracketDelimiterString, -1, PREG_SPLIT_NO_EMPTY);
    }


    static function splitStringByTagAndAttribute($bracketDelimiterString = '<a href="test">text [and] test is good</a>')
    {
        $str = '<option value="123">abc</option><option value="123">aabbcc</option>';
        preg_match_all("#<.*? ([^>]*)>([^<]*)< ?/ ?\1>#", $str, $foo);
        Console1::println($foo);
    }

    static function extractBrackets($bracketDelimiterString = 'hello (world), my name (is andrew) and my number is (845) 235-0184')
    {
        preg_match_all('/\(([A-Za-z0-9 ]+?)\)/', $bracketDelimiterString, $out);
        return $out;
    }

    static function getSanitizeAlphaNumeric($string, $additionalCharacter = '')
    {
        // XSS protection as we might print this value
        return preg_replace("/[^a-zA-Z0-9$additionalCharacter]+/", "", $string);
    }

}

/**
 * Validate User Info Form
 * Class Validation1
 */
class Validation1
{
    static public function validateEmail($email = '')
    {
        return ResultStatus1::make((filter_var($email, FILTER_VALIDATE_EMAIL)), 'Not a valid email address');
    }

    static public function validateUserName($value = '')
    {
        return ResultStatus1::make(preg_match('/^[a-zA-Z0-9]{5,20}$/', $value), 'Username should contain only alphabets and digits');
    }

    static public function validateFullName($value = '')
    {
        return ResultStatus1::make(preg_match('/^[a-zA-Z ]*$/', $value), 'Full Name should only contain alphabets');
    }

    static public function validatePhoneNumber($value = '')
    {
        return ResultStatus1::make(preg_match('/^[0-9]{10}$/', $value), 'Not a valid phone no.');
    }

    static public function validatePassword($password = '')
    {
        $status = false;
        $regex = '/^[a-zA-Z0-9!@#$%^&*_]{6,50}$/';
        if (preg_match($regex, $password)) {
            $x = str_split($password);
            $ar = array('!', '@', '#', '$', '%', '^', '&', '*', '_');
            $flag = 0;
            foreach ($x as $v) {
                if (in_array($v, $ar)) {
                    $flag = 1;
                    break;
                }
            }
            if ($flag == 1) $status = true;
            else $status = false;
        }
        return ResultStatus1::make($status, 'Password should contain minimum 6 characters and either of !@#$%^&*_');
    }

    public static function validateFileName($value)
    {
        return ResultStatus1::make(preg_match('/^[0-9A-Za-z\_\-]{1,63}$/', $value), 'Not a valid filename.');
    }

    /**
     * Form Validation like Laravel
     * Visit https://github.com/rakit/validation for more
     * @return \Illuminate\Support\Facades\Validator
     */
    public static function validator()
    {
        return new Rakit\Validation\Validator();
    }


    /**
     * Validate Form
     * [Read more on Form Validation](https://ehexphp.github.io/ehex-docs/#/BasicUsage#Quick%20Form%20validation)
     * @param null $request e.g $_POST + $_FILES
     *
     * @param array $rules [
     *       'name'                  => 'required|alpha_num',
     *       'email'                 => 'required|email',
     *       'age'                   => 'required|numeric|min:18'
     *       'password'              => 'required|min:6',
     *       'confirm_password'      => 'required|same:password',
     *       'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
     *       'skills'                => 'array',
     *       'skills.*.id'           => 'required|numeric',
     *       'skills.*.percentage'   => 'required|numeric',
     *        OR
     *       'photo' => [
     *          'required',
     *          $validator('uploaded_file')->fileTypes('jpeg|png')->message('Photo must be jpeg/png image')
     *       ]
     * ]
     * @param array $renameField [
     *      'province_id' => 'Province',
     *      'district_id' => 'District'
     * ]
     * @param array $messages [
     *      'required' => ':attribute is required',
     *      'email' => ':email must be a validate email',
     *      'age:min' => '18+ only',
     *
     * ]
     * @param boolean $redirect
     * @return ResultObject1
     */
    public static function validate($request = null, $rules = [], $renameField = [], $messages = [], $redirect = false): ResultObject1
    {
        $validator = self::validator();
        if (empty($request)) $request = $_POST + $_FILES;
        else if (!Array1::isKeyValueArray($request)) {
            $requestNew = Array1::getCommonField(null, $_POST + $_FILES, array_flip($request));
            if (count($requestNew) < count($request)) return ResultObject1::falseMessage(["Some Field missing, Expected fields are (" . implode(',', $request) . ") but only found (" . String1::isSetOr(implode(',', array_keys($requestNew)), "nothing") . ")"]);
            $request = $requestNew;
        }
        if (empty($rules)) foreach ($request as $key => $value) $rules[$key] = 'required' . (String1::contains('email', strtolower($key)) ? '|email' : '');
        if (!empty($renameField)) $validation->setAliases($renameField);
        $formData = $validator->validate($request, $rules, $messages);
        if ($redirect) Url1::redirectIf(Url1::backUrl(), ['Validation Failed', $formData->errors->all(), 'error'], $formData->fails(), $_REQUEST);
        $pass = $formData->passes();
        return ResultObject1::make($pass, $pass ? "Validation Passed" : "Validation Failed: " . String1::toString($formData->errors->all()), $formData->errors->all(), $pass ? 200 : 400);
    }
}


/**
 * Manipulate String
 * Class String1
 */
