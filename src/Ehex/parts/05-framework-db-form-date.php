<?php
class Framework1
{

    /**
     * Is Framework ehex
     * @return bool|Config1|mixed
     */
    static function Ehex()
    {
        if (function_exists('framework_info()') && (framework_info()['name'] === 'ehex')) return framework_info();
        return false;
    }


}

class MySql1
{

    /**
     * Use instead mysqli_real_escape_string
     * @param $string
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
     * @return array of ['query'], ['paginate']
     */
    static function makeLimitQueryAndPagination($prefixQuery = '', $total = 0, $limit = 10, $templateClass = BootstrapPaginationTemplate::class, $requestPageKeyName = 'page')
    {
        $current_page = String1::isset_or($_REQUEST[$requestPageKeyName], 1);
        $query = $prefixQuery . ' ' . static::makeLimitQuery($current_page, $limit);
        $total_pages = ceil($total / $limit);
        return Object1::toArrayObject(['query' => $query, 'paginate' => Page1::renderPagination($total_pages, $templateClass, $requestPageKeyName)]);
    }


}

class Form1
{

    /**
     * All Form Data
     * @param array $array_key_value
     * @param array $exceptKeyList
     * @param string $sanitizeFunction
     * @return array
     */
    static function sanitizeAllValue($array_key_value = [], $exceptKeyList = [], $sanitizeFunction = 'static::getSanitizeValue')
    {
        return Array1::sanitizeArrayItemValue($array_key_value, $exceptKeyList, $sanitizeFunction);
    }

    /**
     * Sanitize Form Data
     * @param $data
     * @return bool|string
     */
    static function getSanitizeValue(&$data)
    {
        if (!isset($data)) return false;
        $newData = $data;

        $newData = trim($newData);
        $newData = stripcslashes($newData);
        $newData = htmlentities($newData); // for other language attack like german / arabi...
        $newData = htmlspecialchars($newData);

        return ($newData);
    }

    /**
     * @param string $lookupClassNameOrClassFunction
     * @param string $processMethod
     * @return string
     */
    static function toClassCallableLink($lookupClassNameOrClassFunction = 'className@function(param1, param2)', $processMethod = 'processSave()')
    {
        // Trim and Generate Url
        $lookupClassNameOrClassFunction = trim($lookupClassNameOrClassFunction);
        $processMethod = trim($processMethod);
        if (class_exists($lookupClassNameOrClassFunction)) $lookupClassNameOrClassFunction = ("$lookupClassNameOrClassFunction@$processMethod");// urlencode("$lookupClassNameOrClassFunction@$processMethod");
        return $lookupClassNameOrClassFunction;
    }

    /**
     * @param string $lookupClassNameOrClassFunction
     * @param string $processMethod
     * @return string
     */
    static function callController($lookupClassNameOrClassFunction = 'className@function(param1, param2)', $processMethod = 'processSave()')
    {
        return url('/ehex-form/' . self::toClassCallableLink($lookupClassNameOrClassFunction, $processMethod));
    }

    /**
     * Use instead of "callController" to remove token from request url
     * @param $token
     * @param string $lookupClassNameOrClassFunction
     * @param string $processMethod
     * @return string
     */
    static function callControllerAndBypassToken($token, $lookupClassNameOrClassFunction = 'className@function(param1, param2)', $processMethod = 'processSave()')
    {
        return self::callController(ServerRequest1::bypassToken(self::toClassCallableLink($lookupClassNameOrClassFunction, $processMethod), $token));
    }


    /**
     * Use instead of "callApi" to remove token from request url
     * @param string $lookupClassNameOrClassFunction
     * @param string $processMethod
     * @return string
     */
    static function callApi($lookupClassNameOrClassFunction = 'className::function(param1, param2)', $processMethod = 'processSave()')
    {
        return url('/ehex-api/' . self::toClassCallableLink($lookupClassNameOrClassFunction, $processMethod));
    }


    static function callApiAndBypassToken($token, $lookupClassNameOrClassFunction = 'className@function(param1, param2)', $processMethod = 'processSave()')
    {
        return self::callApi(ServerRequest1::bypassToken(self::toClassCallableLink($lookupClassNameOrClassFunction, $processMethod), $token));
    }

    // filter out html ( storable in db too)
    static function encodeHTML($data)
    {
        return htmlentities($data);
    }

    static function decodeHTML($data)
    {
        return html_entity_decode(String1::toString($data));
    }


    /**
     * re-use html when store in DataBase
     * @param $data
     * @return string
     */
    static function encodeDatabaseHTML($data)
    {
        return htmlspecialchars($data);
    }

    /**
     * @param $data
     * @return string
     */
    static function decodeDatabaseHTML($data)
    {
        return htmlspecialchars_decode($data);
    }


    static function getSanitizeNumber($id)
    {
        // XSS protection as we might print this value
        return preg_replace("/[^0-9]+/", "", $id);
    }

    static function getSanitizeAlphaNumeric($string)
    {
        // XSS protection as we might print this value
        return preg_replace("/[^a-zA-Z0-9]+/", "", $string);
    }

    static function getEncryptedToken($password, $addBrowserInformation = false)
    {
        //you can change this to user own salt
        $saltStart = "R%W11302&^H2Jk";
        $saltEnd = "^*&ˆH%RwSaMsOn!-oSi";

        $otherStuff = (($addBrowserInformation) ? self::getSanitizeValue($_SERVER['HTTP_USER_AGENT']) : "");
        return hash("sha512", $saltStart . $password . $saltEnd . $otherStuff);
    }


    static function urlParam_toArray($GET_LIKE_stringParam = 'name=osi&age=25')
    {
        $param = array();
        parse_str($_REQUEST[$GET_LIKE_stringParam], $param);   //parse_str($GET_stringParam, $param);
        return $param;
    }


    /******************************
     *  BASE 64
     ******************************/
    static function base64url_encode($plainText)
    {
        $base64 = base64_encode($plainText);
        $base64url = strtr($base64, '+/=', '-_,');
        return $base64url;
    }

    static function base64url_decode($plainText)
    {
        $base64url = strtr($plainText, '-_,', '+/=');
        $base64 = base64_decode($base64url);
        return $base64;
    }

    /**
     * Simple encode/decode
     *  (Not Safe) but enable $useEhexEncodeFunction to make save
     * @param $data
     * @param bool $useEhexEncodeFunction
     * @return string
     */
    static function encode_data($data, $useEhexEncodeFunction = false)
    {
        return $useEhexEncodeFunction ? encode_data($data, null) : self::base64url_encode($data);
    }

    static function decode_data($data, $useEhexEncodeFunction = false)
    {
        return $useEhexEncodeFunction ? decode_data($data, null) : self::base64url_decode($data);
    }


    /******************************
     *  Create Form field
     ******************************/
    static function extractUserName($from_string = '', $randomNumber = true)
    {
        $strIsEmail = String1::convertToSnakeCase($from_string);
        $strIsEmail = explode('@', $strIsEmail)[0];
        return self::getSanitizeAlphaNumeric($strIsEmail . ($randomNumber ? Number1::getRandomNumber(4, 1) : ''));
    }

    static function generatePassword($length = 16)
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }
}

class DateManager1
{
    static $date_asNumber = 'd-m-Y';
    static $date_asText = 'd D M Y';
    static $dateInverse_asNumber = 'Y-m-d';
    static $dateInverse_asText = 'Y-M-D';

    static $time_asAmPm = 'g:i a';
    static $time_as24Hours = 'h:i:s';

    static $dateTime_asNumber = 'd-m-Y h:i:s';
    static $dateTimeInverse_asNumber = 'Y-m-d h:i:s';
    static $database_timeStamp = 'Y-m-d h:i:s';
    static $dateTime_asText = 'l jS F Y,  g:i a';


    /**
     */
    static function carbon()
    {
        return carbonDate();
    }

    /**
     * @param $date
     */
    static function carbonParse($date)
    {
        return static::carbon()::parse($date);
    }

    /**
     * @param $date
     * @return string
     */
    static function diffForHumans($date)
    {
        return static::carbon()::parse($date)->diffForHumans();
    }


    /**
     * @param string $format
     * @param null $timeStamp
     * @param bool $timeStampStrictMode
     * @return false|int|string
     */
    static function date($format = 'd-m-Y h:i:s', $timeStamp = null, $timeStampStrictMode = true)
    {
        if ($timeStamp && $timeStampStrictMode && $timeStamp <= 0) return 0;
        return date($format, $timeStamp);
    }

    static function convert24HoursTime_toAmPm($time = '')
    {
        return date("g:i A", strtotime($time));
    }

    static function convertAmPmTime_to24Hours($time = '')
    {
        return date("G:i", strtotime($time));
    }

    static function now($pretty = false)
    {
        return $pretty ? self::prettyDateTime(self::now(false)) : date(self::$database_timeStamp);
    }

    static function nowDate($pretty = false)
    {
        return $pretty ? date(self::$dateInverse_asText) : date(self::$dateInverse_asNumber);
    }

    static function nowTime($pretty = false)
    {
        return $pretty ? date(self::$time_asAmPm) : date(self::$time_as24Hours);
    }


    static function prettyDateTime($date = null)
    {
        $date = $date ? $date : self::now();
        $x = explode('-', $date);
        $a = $x[0];
        $m = $x[1];
        $c = $x[2];
        if (strlen($c) > 2) {
            $y = $c;
            $d = $a;
        } else if (strlen($a) > 2) {
            $y = $a;
            $d = $c;
        } else return $date;
        $mon = "";
        switch ($m) {
            case '01':
                $mon = "Jan";
                break;
            case '02':
                $mon = "Feb";
                break;
            case '03':
                $mon = "Mar";
                break;
            case '04':
                $mon = "Apr";
                break;
            case '05':
                $mon = "May";
                break;
            case '06':
                $mon = "Jun";
                break;
            case '07':
                $mon = "Jul";
                break;
            case '08':
                $mon = "Aug";
                break;
            case '09':
                $mon = "Sep";
                break;
            case '10':
                $mon = "Oct";
                break;
            case '11':
                $mon = "Nov";
                break;
            case '12':
                $mon = "Dec";
                break;
        }
        return "$d $mon, $y";
    }

    static function getWeekDayName($date = null)
    {
        $date = $date ? $date : self::nowDate(false);
        $arr = explode('-', $date);
        $d = $arr[2];
        $m = $arr[1];
        $y = $arr[0];
        $tot = $feb = $sum = 0;
        if ($y % 4 == 0) {
            $tot = 366;
            $feb = 29;
        } else {
            $tot = 365;
            $feb = 28;
        }

        $mon = array(31, $feb, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        for ($i = 0; $i < $m - 1; $i++) {
            $sum += $mon[$i];
        }
        $dd = $d - 1;
        $sum += $dd;
        if ($y > 1) $dy = $y - 1;
        $ly = $dy / 4;
        $nly = $dy - $ly;
        $ly *= 366;
        $nly *= 365;
        $sum += $ly + $nly;
        $res = $sum % 7;

        switch ($res) {
            case 0:
                return "Sunday";
            case 1:
                return "Monday";
            case 2:
                return "Tuesday";
            case 3:
                return "Wednesday";
            case 4:
                return "Thursday";
            case 5:
                return "Friday";
            case 6:
                return "Saturday";
        }
        return '';
    }

    private $time = null;
    private $timeCompare = null;


    /**
     *
     *
     *
     *
     * $diff  = new DateManager1( '2018-05-31 22:01:14' ); // OR pass in strtotime('2018-05-31 22:01:14')
     * echo $diff->isTimeElapsed()? 'Time Up': $diff->getRemainingTime_asText();
     *
     *
     *
     *
     * DateManager1 constructor.
     * @param string $dataBaseTimeStamp
     * @param null $compareDate_defaultIsNow @default is Now()
     * @param string $dateFormat
     *
     */
    function __construct($dataBaseTimeStamp = '1988-08-10', $compareDate_defaultIsNow = null, $dateFormat = "U = Y-m-d H:i:s")
    {
        try {
            // is TimeStamp or Use as String
            $this->time = new DateTime();
            if ($dataBaseTimeStamp) $this->time = self::normalizeDateOrTimestamp_to_DateTime($dataBaseTimeStamp, $dateFormat);

            // compare
            $this->timeCompare = new DateTime();
            if ($compareDate_defaultIsNow) $this->timeCompare = self::normalizeDateOrTimestamp_to_DateTime($compareDate_defaultIsNow, $dateFormat);
        } catch (Exception $ex) {
            throw new Exception('Bad Date Format : ' . $ex->getMessage());
        }
    }

    /**
     * Know if Time is Up
     *  $dataBaseTimeStamp - $compareDate_defaultIsNow
     * @return bool
     */
    function isTimeElapsed()
    {
        if (!$this->time || !$this->timeCompare || (($this->timeCompare->getTimestamp() - $this->time->getTimestamp()) <= 0)) return false; else return true;
    }

    /**
     * @param string $time
     * @param string $dateFormat
     * @return DateTime|string
     */
    static function normalizeDateOrTimestamp_to_DateTime($time = '2007-02-14 20:25:25', $dateFormat = "U = Y-m-d H:i:s")
    {
        $data = new DateTime();
        if (is_numeric($time)) $data->setTimestamp($time);
        else {
            $data = new DateTime($time);
            $data->format($dateFormat);
        }
        return $data;
    }


    /**
     * echo getRemainingTime() //'Your age is %Y years and %d days' // Your age is 28 years and 19 days
     * @return DateInterval|false|int
     */
    function getRemainingTime_asDateInterval()
    {
        if ($this->isTimeElapsed()) return null;
        return date_diff($this->time, $this->timeCompare);
    }

    function getTotalDays()
    {
        if ($this->isTimeElapsed()) return 0;
        return date_diff($this->time, $this->timeCompare)->days;
    }

    function getTotalHours()
    {
        if ($this->isTimeElapsed()) return 0;
        return ($this->getTotalDays() > 0) ? ($this->getRemainingTime_asDateInterval()->h + ($this->getTotalDays() * 24)) : $this->getRemainingTime_asDateInterval()->h;
    }


    /**
     * echo getRemainingTime_asText() // Output: The difference is 28 years, 5 months, 19 days, 20 hours, 34 minutes, 36 seconds
     * @param string $defaultTimeElapseText
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    function getRemainingTime_asText($defaultTimeElapseText = 'Time Up ##:##:##', $prefix = ' ', $suffix = ', ')
    {
        if ($this->isTimeElapsed()) return $defaultTimeElapseText;
        $diff = $this->diff();
        $time = String1::ifNotEmpty($diff->y, " $prefix" . $diff->y . " " . String1::pluralize_if($diff->y, 'year', 'years') . $suffix);
        $time .= String1::ifNotEmpty($diff->m, " $prefix" . $diff->m . " " . String1::pluralize_if($diff->m, 'month', 'months') . $suffix);
        $time .= String1::ifNotEmpty($diff->d, " $prefix" . $diff->d . " " . String1::pluralize_if($diff->d, 'day', 'days') . $suffix);
        $time .= String1::ifNotEmpty($diff->h, " $prefix" . $diff->h . " " . String1::pluralize_if($diff->h, 'hour', 'hours') . $suffix);
        $time .= String1::ifNotEmpty($diff->i, " $prefix" . $diff->i . " " . String1::pluralize_if($diff->i, 'minute', 'minutes') . ' ');
        return trim($time, ', ');
    }

    function diff()
    {
        return date_diff($this->time, $this->timeCompare);
    }

    /**
     * echo getRemainingTime_asText() // Output: The difference is 28 years, 5 months, 19 days, 20 hours, 34 minutes, 36 seconds
     * @return string
     */
    function getRemainingTime_asTimeStamp()
    {
        if ($this->isTimeElapsed()) return 0;
        $remDiff = $this->time->diff($this->timeCompare);
        return $remDiff->format('%a');
        //return strtotime($this->getRemainingTime_asText('', '+', ' '));
    }












    /************************************************************************************************************************************************************************************/
    /*
     *
     *      Static
     *
            $from = (time() + (5 * 60 * 60));
            $fix = (time() + (3 * 60 * 60));
            echo DateManager1::getRemainingTime($from, $fix);
     *
     */
    /************************************************************************************************************************************************************************************/


    /************************************************************************************************************************************************************************************
     *
     *  Check if time elapse, i.e ($fromTime set in DataBase of fix somewhere) - (current time) > futureTimePassingIn as Hours, Days, Weeks
     *  Note That
     *        strtotime('+2 hour')
     *          is the same as time() + (2 * 60 * 60)
     *
     *        strtotime('+2 days') thesame as time() + (2 * 3600)
     *
     * @param int $dbFixTime
     * @param int $minuteAfter
     * @param int $hoursAfter
     * @param int $daysAfter
     * @param int $weeksAfter
     * @return bool
     *
     *      echo( strtotime("now") . "<br>");
     *      echo( strtotime("now") . "<br>");
     *      echo( strtotime("3 October 2005") . "<br>");
     *      echo( strtotime("+5 hours") . "<br>");
     *      echo( strtotime("+1 week") . "<br>");
     *      echo( strtotime("+1 week 3 days 7 hours 5 seconds") . "<br>");
     *      echo( strtotime("next Monday") . "<br>");
     *      echo( strtotime("last Sunday"));
     */
    static function isTimeElapse($dbFixTime = 0, $minuteAfter = 0, $hoursAfter = 0, $daysAfter = 0, $weeksAfter = 0)
    {
        return ($dbFixTime) < strtotime(self::dateTimeNormalizer($minuteAfter, $hoursAfter, $daysAfter, $weeksAfter));
    }

    /**
     *  If $dbFixTime less that $compareFutureTime already
     *
     * @param int $dbFixTime
     * @param $compareFutureTime
     * @return bool
     */
    static function isElapse($dbFixTime = 0, $compareFutureTime = null)
    {
        return ($dbFixTime < $compareFutureTime);
    }

    static function getDaysFrom($dbTimeStamp, $nowTimeStamp = null)
    {
        $str = (($nowTimeStamp) ? $nowTimeStamp : strtotime(date("M d Y "))) - ($dbTimeStamp);
        return floor($str / 3600 / 24);
    }

    /**
     *  Get Remaining Time after Subtracting $dbFixTime. Alternative to @param int $dbFixTime
     * @param int $minuteAfter
     * @param int $hoursAfter
     * @param int $daysAfter
     * @param int $weeksAfter
     * @return int
     * @see DateManager1::removeDateTime()
     *
     */
    static function getRemainingTime($dbFixTime = 0, $minuteAfter = 0, $hoursAfter = 0, $daysAfter = 0, $weeksAfter = 0)
    {
        $time = ($dbFixTime - strtotime(self::dateTimeNormalizer($minuteAfter, $hoursAfter, $daysAfter, $weeksAfter)));
        return ($time < 0 ? 0 : $time);
    }

    /**
     *
     *  strtotime() Normaliser.
     *  return some format like +2 months +1 week +3 days + 2 hours + 0 minute
     *
     * @param string $symbol , + or -
     * @param int $minute
     * @param int $hoursAfter
     * @param int $daysAfter
     * @param int $weeksAfter
     * @param int $month
     * @return string
     */
    static function dateTimeNormalizer($symbol = '+', $minute = 0, $hoursAfter = 0, $daysAfter = 0, $weeksAfter = 0, $month = 0)
    {
        $pie = $month > 0 ? "{$symbol}{$month} " . String1::pluralize_if($month, 'month', 'months') . " " : "";
        $pie .= $weeksAfter > 0 ? "{$symbol}{$weeksAfter} " . String1::pluralize_if($weeksAfter, 'week', 'weeks') . " " : "";
        $pie .= $daysAfter > 0 ? "{$symbol}{$daysAfter} " . String1::pluralize_if($daysAfter, 'day', 'days') . " " : "";
        $pie .= $hoursAfter > 0 ? "{$symbol}{$hoursAfter} " . String1::pluralize_if($hoursAfter, 'hour', 'hours') . " " : "";
        $pie .= $minute > 0 ? "{$symbol}{$minute} " . String1::pluralize_if($minute, 'minute', 'minutes') : "";
        return $pie;
    }


    /**
     * Add Some Minute, Hours... to  $initTime Date
     *
     * @param int $minute
     * @param int $hours
     * @param int $days
     * @param int $weeks
     * @return int
     */
    static function addDateTime_asDatabaseTimeStamp($minute = 0, $hours = 0, $days = 0, $weeks = 0)
    {
        return date(self::$dateTimeInverse_asNumber, \DateManager1::addDateTime(null, $minute, $hours, $days, $weeks));
    }

    /**
     * Add Some Minute, Hours... to  $initTime Date
     *
     * @param int $minute
     * @param int $hours
     * @param int $days
     * @param int $weeks
     * @return int
     */
    static function removeDateTime_asDatabaseTimeStamp($minute = 0, $hours = 0, $days = 0, $weeks = 0)
    {
        return date(self::$dateTimeInverse_asNumber, \DateManager1::removeDateTime(null, $minute, $hours, $days, $weeks));
    }

    /**
     * Add Some Minute, Hours... to  $initTime Date
     *
     * @param null|int $initTime @default time()
     * @param int $minute
     * @param int $hours
     * @param int $days
     * @param int $weeks
     * @return int
     */
    static function addDateTime($initTime = null, $minute = 0, $hours = 0, $days = 0, $weeks = 0)
    {
        $initTime = $initTime ? $initTime : time();
        $time = strtotime(self::dateTimeNormalizer('+', $minute, $hours, $days, $weeks), $initTime);
        return $time;
    }


    /**
     * Remove Some Minute, Hours... from  $initTime Date
     *
     * @param null|int $initTime @default time()
     * @param int $minute
     * @param int $hours
     * @param int $days
     * @param int $weeks
     * @return int
     */
    static function removeDateTime($initTime = null, $minute = 0, $hours = 0, $days = 0, $weeks = 0)
    {
        $initTime = $initTime ? $initTime : time();
        $time = strtotime(self::dateTimeNormalizer('-', $minute, $hours, $days, $weeks), $initTime);
        return $time;
    }
}

class Date1 extends DateManager1
{
}


/**
 * Managa HEadwe
 * Class Header1
 */
