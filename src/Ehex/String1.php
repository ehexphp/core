<?php

/**
 * Manipulate String
 * Class String1
 */
class String1
{

    /**
     * Encode any string or large file
     * @param string $data
     * @param string $password
     * @return string
     * @see String1::decodeData()  to decode data
     */
    static function encodeData($data, $password, $iv)
    {
        if (OPENSSL_VERSION_NUMBER <= 268443727) throw new RuntimeException('OpenSSL Version too old');
        $ciphertext = openssl_encrypt($data, 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv);
        $ciphertext_hex = bin2hex($ciphertext);
        return "$ciphertext_hex";
    }


    /**
     * Decode encoded string or large file
     * @param string $cipheredData
     * @param string $password
     * @return string
     * @see String1::encodeData()  to encode data
     */
    static function decodeData($cipheredData, $password, $iv)
    {
        $ciphertext = hex2bin($cipheredData);
        return openssl_decrypt($ciphertext, 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv);
    }


    /**
     * Convert php Function to pure string code
     * @param $fn
     * @return string
     * @throws ReflectionException
     */
    static function phpFunctionToString($fn)
    {
        $r = new ReflectionFunction($fn);
        $file = $r->getFileName();
        if (!is_readable($file)) return '';
        $lines = file($file);
        $start = $r->getStartLine() - 1;
        $length = $r->getEndLine() - $start;
        return implode('', array_slice($lines, $start, $length));
    }


    /**
     * Return 22-char compressed version of 32-char hex string (eg from PHP md5). adn URL Safe
     * @param $md5_hash_str
     * @return mixed
     */
    static function compressMD5($md5_hash_str)
    {
        // (we start with 32-char $md5_hash_str eg "a7d2cd9e0e09bebb6a520af48205ced1")
        $md5_bin_str = "";
        foreach (str_split($md5_hash_str, 2) as $byte_str) { // ("a7", "d2", ...)
            $md5_bin_str .= chr(hexdec($byte_str));
        }
        // ($md5_bin_str is now a 16-byte string equivalent to $md5_hash_str)
        $md5_b64_str = base64_encode($md5_bin_str);
        // (now it's a 24-char string version of $md5_hash_str eg "VUDNng4JvrtqUgr0QwXOIg==")
        $md5_b64_str = substr($md5_b64_str, 0, 22);
        // (but we know the last two chars will be ==, so drop them eg "VUDNng4JvrtqUgr0QwXOIg")
        $url_safe_str = str_replace(array("+", "/"), array("-", "_"), $md5_b64_str);
        // (Base64 includes two non-URL safe chars, so we replace them with safe ones)
        return $url_safe_str;
    }


    /**
     * If you now want a function to compress your hexadecimal MD5 values using URL safe characters, you can use this:
     * @param $hash
     * @return mixed
     */
    static function compressHash($hash)
    {
        return self::base64_to_base64UrlSafe(rtrim(self::base16_to_base64($hash), '='));
    }

    /**
     * And the inverse function:
     * @param $hash
     * @return mixed
     */
    static function uncompressHash($hash)
    {
        return self::base64_to_base16(self::base64UrlSafe_to_base64($hash));
    }

    /**
     * If you need Base-64 encoding with the URL and filename safe alphabet , you can use these functions:
     * @param $base64
     * @return string
     */
    static function base64_to_base64UrlSafe($base64)
    {
        return strtr($base64, '+/', '-_');
    }

    /**
     * @param $base64safe
     * @return string
     */
    static function base64UrlSafe_to_base64($base64safe)
    {
        return strtr($base64safe, '-_', '+/');
    }

    /**
     * Here are two conversion functions for Base-16 to Base-64 conversion and the inverse Base-64 to Base-16 for arbitrary input lengths:
     * @param $base16
     * @return string
     */
    static function base16_to_base64($base16)
    {
        return base64_encode(pack('H*', $base16));
    }

    /**
     * And the inverse function:
     * @param $base64
     * @return string
     */
    static function base64_to_base16($base64)
    {
        return implode('', unpack('H*', base64_decode($base64)));
    }


    /**
     * @var array for pluralize
     */
    private static $plural = array('/(quiz)$/i' => "$1zes", '/^(ox)$/i' => "$1en", '/([m|l])ouse$/i' => "$1ice", '/(matr|vert|ind)ix|ex$/i' => "$1ices", '/(x|ch|ss|sh)$/i' => "$1es", '/([^aeiouy]|qu)y$/i' => "$1ies", '/(hive)$/i' => "$1s", '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves", '/(shea|lea|loa|thie)f$/i' => "$1ves", '/sis$/i' => "ses", '/([ti])um$/i' => "$1a", '/(tomat|potat|ech|her|vet)o$/i' => "$1oes", '/(bu)s$/i' => "$1ses", '/(alias)$/i' => "$1es", '/(octop)us$/i' => "$1i", '/(ax|test)is$/i' => "$1es", '/(us)$/i' => "$1es", '/s$/i' => "s", '/$/' => "s");
    private static $singular = array('/(quiz)zes$/i' => "$1", '/(matr)ices$/i' => "$1ix", '/(vert|ind)ices$/i' => "$1ex", '/^(ox)en$/i' => "$1", '/(alias)es$/i' => "$1", '/(octop|vir)i$/i' => "$1us", '/(cris|ax|test)es$/i' => "$1is", '/(shoe)s$/i' => "$1", '/(o)es$/i' => "$1", '/(bus)es$/i' => "$1", '/([m|l])ice$/i' => "$1ouse", '/(x|ch|ss|sh)es$/i' => "$1", '/(m)ovies$/i' => "$1ovie", '/(s)eries$/i' => "$1eries", '/([^aeiouy]|qu)ies$/i' => "$1y", '/([lr])ves$/i' => "$1f", '/(tive)s$/i' => "$1", '/(hive)s$/i' => "$1", '/(li|wi|kni)ves$/i' => "$1fe", '/(shea|loa|lea|thie)ves$/i' => "$1f", '/(^analy)ses$/i' => "$1sis", '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis", '/([ti])a$/i' => "$1um", '/(n)ews$/i' => "$1ews", '/(h|bl)ouses$/i' => "$1ouse", '/(corpse)s$/i' => "$1", '/(us)es$/i' => "$1", '/s$/i' => "");
    private static $irregular = array('move' => 'moves', 'foot' => 'feet', 'goose' => 'geese', 'sex' => 'sexes', 'child' => 'children', 'man' => 'men', 'tooth' => 'teeth', 'person' => 'people', 'valve' => 'valves');
    private static $uncountable = array('sheep', 'fish', 'deer', 'series', 'species', 'money', 'rice', 'information', 'equipment');

    /**
     * pluralize value
     * @param $string
     * @return null|string|string[]
     */
    public static function pluralize($string)
    {
        if (in_array(strtolower($string), self::$uncountable))
            return $string;
        foreach (self::$irregular as $pattern => $result) {
            $pattern = '/' . $pattern . '$/i';
            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }
        foreach (self::$plural as $pattern => $result) {
            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }
        return $string;
    }

    /**
     * singularize value
     * @param $string
     * @return null|string|string[]
     */
    public static function singularize($string)
    {
        if (in_array(strtolower($string), self::$uncountable))
            return $string;
        foreach (self::$irregular as $result => $pattern) {
            $pattern = '/' . $pattern . '$/i';
            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }
        foreach (self::$singular as $pattern => $result) {
            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }
        return $string;
    }

    /**
     * Pluralize value only if count > 0
     * @param $count
     * @param $string
     * @return string
     */
    public static function pluralize_if($count, $string)
    {
        if ($count == 1)
            return "1 $string"; else
            return $count . " " . self::pluralize($string);
    }


    public static function isUpperCase($string)
    {
        return $string === strtoupper($string);
    }

    public static function isLowerCase($string)
    {
        return $string === strtolower($string);
    }

    /**
     * Returns the first string there is between the strings from the parameter start and end.
     * stringBetween('This is a [custom] string', '[', ']'); // custom
     * @param $haystack
     * @param $start
     * @param $end
     * @return string
     */
    public static function stringBetween($haystack, $start, $end)
    {
        return trim(strstr(strstr($haystack, $start), $end, true), $start . $end);
    }


    /**
     * @param $input
     * @param string $delimiter
     * @return string
     *     To convertCamelCase_toSnakeCase I.E FirstName = first_name
     */
    public static function convertToSnakeCase($input, $delimiter = '_')
    {
        return $word = preg_replace_callback("/(^|[a-z])([A-Z])/", function ($m) use ($delimiter) {
            return strtolower(strlen($m[1]) ? "$m[1]$delimiter$m[2]" : "$m[2]");
        }, $input);
    }

    /**
     * Convert both CamelCase and SnakeCase to Readable text
     * @param $input
     * @param string $delimiter
     * @return string
     */
    public static function convertToReadableCase($input, $delimiter = ' ')
    {
        return ucfirst(String1::convertToSnakeCase(String1::convertToCamelCase($input), $delimiter));
    }

    /**
     * @param $input
     * @param string $underScore_replace_with
     * @return string To convertSnakeCase_toCamelCase I.E first_name = FirstName
     * To convertSnakeCase_toCamelCase I.E first_name = FirstName
     */
    public static function convertToCamelCase($input, $underScore_replace_with = '')
    {
        return $word = preg_replace_callback(
            "/(^|_)([a-z])/",
            function ($m) use ($underScore_replace_with) {
                return $underScore_replace_with . strtoupper("$m[2]");
            },
            $input
        );
    }

    /**
     * @param $word
     * @return mixed
     *     echo create_slug('does this thing work or not');
     * //returns 'does-this-thing-work-or-not'
     */
    static function convertWordToSlug($word, $replacement = '-')
    {
        return (strtolower(preg_replace("/\W+/", $replacement, $word))); //By using \W+ you take care of all non-latin characters.
    }


    /**
     * get mysql variable from php variable
     * @param string $dataType
     * @return string
     */
    static function convertMySqlDataTypeToPhp($dataType = 'varchar', $defaultPhpDataType = null)
    {
        // remove ()
        $dataType = !String1::contains('(', $dataType) ? $dataType : substr($dataType, 0, strpos($dataType, '('));
        $dataType = trim(strtolower($dataType));
        switch ($dataType) {
            case 'boolean':
            case 'tinyint':
                return 'boolean';
            case 'varchar':
            case 'text':
            case 'enum':
            case 'blob':
            case 'timestamp':
            case 'char':
                return $dataType == 'text' ? 'STRING' : 'string';
            case 'int':
            case 'integer':
            case "bigint":
                return 'integer';
            default:
                return $defaultPhpDataType ? $defaultPhpDataType : $dataType;
        }
    }

    static function toString($value, $delimiter = ' ')
    {
        if ($value === NULL) return "";
        else if (is_string($value) || is_numeric($value) || is_bool($value)) $str = (string)$value;
        else if (is_object($value)) $str = self::toString(Object1::convertObjectToArray($value));
        else if (is_array($value)) $str = Array1::implode($delimiter, $value);
        else $str = print_r($value, true);
        return $str;
    }

    static function getDemoText($length = 500, $isPassword = false)
    {
        if ($isPassword) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
            return $password = substr(str_shuffle($chars), 0, 8);
        }
        return DemoGenerator::sentence($length);
    }

    static function mask($text, $start_from = 1, $maskKey = '*', $length = 20)
    {
        $allText = static::getSubString($text, $length, 0);
        $asArray = String1::toArray($allText);
        $asText = '';
        for ($i = 0; $i < count($asArray); $i++) {
            if ($i >= $start_from) $asText .= $maskKey;
            else $asText .= $asArray[$i];
        }
        return $asText;
    }

    /**
     * get string hash code
     * @param $value
     * @return float|int
     */
    static function hashCode($value)
    {
        $hashCode = 0;
        for ($i = 0; $i < strlen($value); $i++) $hashCode = $hashCode * 31 + ord(substr($value, $i, 1));
        return $hashCode;
    }

    /**
     * remove trailing quote
     * @param string $value
     * @param bool $toJavascript
     * @return string
     */
    static function escapeQuotes($value = 'ade is a "fine" Boy', $toJavascript = false)
    {
        if (!is_array($value)) $thearray = array($value);
        else $thearray = $value;
        foreach (array_keys($thearray) as $string) {
            $thearray[$string] = $toJavascript ? json_encode(addslashes($thearray[$string])) : addslashes($thearray[$string]);
            $thearray[$string] = preg_replace("/[\\/]+/", "/", $thearray[$string]);
        }
        if (!is_array($value)) return $thearray[0];
        else return $thearray;
    }

    /**
     * remove special character in string value
     * @param string $value
     * @return string
     */
    static function escapeStringAsEntity($value)
    {
        return strtr($value, array(
            "\0" => "",
            "'" => "&#39;",
            "\"" => "&#34;",
            "\\" => "&#92;",
            // more secure
            "<" => "&lt;",
            ">" => "&gt;",
        ));
    }


    static function startsWith($string, $needleToSearch)
    {
        $lastText = substr($string, 0, strlen($needleToSearch));
        return ($needleToSearch == $lastText);
    }

    static function endsWith($string, $needleToSearch)
    {
        $lastStrCount = strlen($string) - strlen($needleToSearch);
        $lastText = substr($string, $lastStrCount, strlen($needleToSearch));
        return ($needleToSearch == $lastText);
    }


    /**
     * Replace "$search" with "$replace"
     * @param $text
     * @param $search
     * @param $replace
     * @return mixed
     */
    static function replace($text, $search, $replace)
    {
        $text = $text === null? "": $text;
        return str_replace($search, $replace, $text);
    }

    /**
     * replace all character in text with single provided character
     * @param $text
     * @param array $searchItems
     * @param string $replaceThemWith
     * @return string
     */
    static function replaceMany($text, $searchItems = [], $replaceThemWith = '')
    {
        $buf = [];
        foreach (Array1::makeArray($searchItems) as $key) {
            $buf[$key] = $replaceThemWith;
        }
        return strtr($text, $buf);
    }

    /**
     * replace if first
     * @param $text
     * @param $search
     * @param $replace
     * @return mixed
     */
    static function replaceStart($text, $search, $replace)
    {
        if (trim($search) == '') return $text;
        $position = strpos($text, $search);
        if ($position !== false) return substr_replace($text, $replace, $position, strlen($search));
        return $text;
    }

    /**
     * replace if last
     * @param $text
     * @param $search
     * @param $replace
     * @return mixed
     */
    static function replaceEnd($text, $search, $replace)
    {
        $position = strrpos($text, $search);
        if ($position !== false) return substr_replace($text, $replace, $position, strlen($search));
        return $text;
    }


    /**
     * Removes trailing indentation in HEREDOC strings and other strings with multiple lines.
     * @param $x
     * @param int $leadingSpaces
     * @return string
     */
    static function hereDocMoonWalk($x, $leadingSpaces = 0)
    {
        //Make sure we don't start or endwith new lines
        $x = trim($x, "\r");
        $x = trim($x, "\n");
        // Find how many leading spaces are in the first line
        $spacesToRemove = strlen($x) - strlen(ltrim($x)) - $leadingSpaces;
        // Break up by new lines
        $lines = explode("\n", $x);
        //$lines = array_values(array_filter($lines,"not_empty"));
        // Remove that many leading spaces from the beginning of each string
        for ($x = 0; $x < sizeof($lines); $x++) {
            // Remove each space
            $lines[$x] = preg_replace('/\s/', "", $lines[$x], $spacesToRemove);
        }
        // Put back into string on seperate lines
        return implode("\n", $lines);
    }


    /**
     * convert to array
     * @param $value
     * @param string $delimiter
     * @return array
     */
    static function toArray($value, $delimiter = '')
    {
        return self::is_empty($delimiter) ? str_split($value) : explode($delimiter, $value);//preg_split('//i', $text)
    }


    /**
     * Translate Text
     * @param $text
     * @param string $fromLanguage
     * @param string $toLanguage
     * @param bool $cache
     * @param bool $returnDefaultOnFailed
     * @return mixed|null|string|string[]
     */
    static function translateLanguage($text, $fromLanguage = 'nl', $toLanguage = 'en', $cache = false, $returnDefaultOnFailed = true)
    {
        if ($fromLanguage === $toLanguage || $fromLanguage === '' || $toLanguage === '') return $text;

        //check cache
        $cachePath = "language-" . self::hashCode($text) . "-$fromLanguage-$toLanguage";
        if ($cache) if (Session1::exists($cachePath) && !empty(Session1::get($cachePath))) return Session1::get($cachePath);

        // filter
        if (String1::contains('.', $text)) $text = rtrim($text, '.') . '.';

        // init
        $filePath = function_exists('resources_path_cache') ? resources_path_cache() . "/transes.html" : $_SERVER['DOCUMENT_ROOT'] . "/transes.html";
        $googleTranslatorUrl = "http://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=" . $fromLanguage . "&tl=" . $toLanguage . "&hl=hl&q=";
        $res = "";
        $qqq = explode(".", $text);
        try {
            if (count($qqq) < 2) {
                @unlink($filePath);
                @copy($googleTranslatorUrl . urlencode(($text)), $filePath);
                if (file_exists($filePath)) {
                    $dara = file_get_contents($filePath);
                    $f = explode("\"", $dara);
                    $res .= $f[1];

                } else {
                    return null;
                }


            } else {
                for ($i = 0; $i < (count($qqq) - 1); $i++) {
                    if ($qqq[$i] == ' ' || $qqq[$i] == '') {
                    } else {
                        @copy($googleTranslatorUrl . urlencode($qqq[$i]), $filePath);
                        if (!file_exists($filePath)) return null;
                        $dara = file_get_contents($filePath);
                        @unlink($filePath);
                        $f = explode("\"", $dara);
                        $res .= $f[1] . ". ";
                    }
                }
            }


        } catch (Exception $ex) {
            return ($text);
        }

        // save cache
        if ($cache && !String1::is_empty($res)) Session1::set($cachePath, $res);
        return ((String1::is_empty($res) && $returnDefaultOnFailed)) ? $text : self::decodeUnicode($res);
    }


    /**
     * Translate Text and Cached It
     * @param array $textKeyValueList
     * @param string $fromLanguage
     * @param string $toLanguage
     * @param bool $cache
     * @param bool $returnDefaultOnFailed
     * @return array Example, and Array of $food = ['dinner'=>'pie', 'breakfast'=>'moimoi']
     * Example, and Array of $food = ['dinner'=>'pie', 'breakfast'=>'moimoi']
     * Would be converted to dinner=pie & breakfast=moimoi. as Sending Request
     * then output  ['dinner'=>'ahfdk', 'breakfast'=>'asfas']
     */
    static function translateLanguageKeyValue(array $textKeyValueList = [], $fromLanguage = 'en', $toLanguage = 'en', $cache = true, $returnDefaultOnFailed = true)
    {
        if ($fromLanguage === $toLanguage || $fromLanguage === '' || $toLanguage === '') return $textKeyValueList;
        $text = '';

        // convert to string
        $index = 0;
        if (is_array($textKeyValueList)) foreach ($textKeyValueList as $tKey => $tValue) {
            $text .= $index . "=$tValue&";
            $index++;
        };

        //check cache
        $cachePath = "language-" . self::hashCode($text) . "-$fromLanguage-$toLanguage";
        if ($cache) if (Session1::exists($cachePath) && !empty(Session1::get($cachePath))) return Session1::get($cachePath);
        //return $cachePath;

        // process
        $output = self::translateLanguage(trim($text, '&'), $fromLanguage, $toLanguage, false, $returnDefaultOnFailed);

        // convert back to array and assign default key name
        parse_str($output, $textArray);
        $index = 0;
        $newArray = [];
        $defaultKeyList = array_keys($textKeyValueList);
        foreach ($textArray as $tKey => $vValue) {
            $newArray[$defaultKeyList[$index]] = $vValue;
            $index++;
        }

        // save cache
        if ($cache && !empty($newArray)) Session1::set($cachePath, $newArray);
        return (!empty($textKeyValueList) && empty($newArray) && $returnDefaultOnFailed) ? $textKeyValueList : $newArray;
    }


    /**
     * Translate Text and Cached it
     * @param array $textKeyValueList
     * @param string $fromLanguage
     * @param string $toLanguage
     * @param bool $cache
     * @param string $defaultKey
     * @return bool|mixed
     */
    static function translateLanguageKeyAndManyValues(array $textKeyValueList = [], $fromLanguage = 'en', $toLanguage = 'en', $cache = true, $defaultKey = 'default')
    {
        //check cache
        $cachePath = '';
        if ($cache) {
            $cachePath = "language-" . Array1::hashCode($textKeyValueList) . "-$fromLanguage-$toLanguage";
            if (Session1::exists($cachePath) && !empty(Session1::get($cachePath))) return Session1::get($cachePath);// Object1::convertArrayToObject( Session1::get($cachePath) );
        }

        $languageUserDefinedList = $languageNotDefined = [];

        // separate user define translate from auto google translating.
        foreach ($textKeyValueList as $languageKey => $languagesValue) {
            if (is_array($languagesValue) && isset($languagesValue[$toLanguage])) $languageUserDefinedList[$languageKey] = $languagesValue[$toLanguage];
            else $languageNotDefined[$languageKey] = (is_array($languagesValue)) ? $languagesValue[$defaultKey] : $languagesValue;
        }

        // process
        $output = self::translateLanguageKeyValue($languageNotDefined, $fromLanguage, $toLanguage, false, true);
        $newArray = array_merge($languageUserDefinedList, $languageNotDefined);

        // save cache
        if ($cache && !empty($output)) Session1::set($cachePath, $newArray);

        //new Language
        //return Object1::convertArrayToObject((!empty($textKeyValueList) && empty($newArray))? $textKeyValueList: $newArray);
        return (!empty($textKeyValueList) && empty($newArray)) ? $textKeyValueList : $newArray;
    }


    /**
     * DeEncode from Unicode
     * @param $text
     * @return null|string|string[]
     */
    static function decodeUnicode($text)
    {
        if (String1::is_empty($text)) return '';
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $text);
    }

    /**
     * Encode to Number
     * @param $string
     * @return string
     */
    static function encodeStringToNumber($string)
    {
        return utf8_encode(join(array_map(function ($n) {
            return sprintf('%03d', $n);
        }, unpack('C*', $string))));
    }

    /** DeEncode From Number
     * @param $stringNumber
     * @return string
     */
    static function decodeStringBackFromNumber($stringNumber)
    {
        return $str = utf8_encode(join(array_map('chr', str_split($stringNumber, 3))));
    }

    /**
     * Encode to Short Alpha Numeric
     * @param $string
     * @return string
     */
    static function encodeToShortAlphaNum($string)
    {
        return Math1::encodeToShortAlphaNum(String1::encodeStringToNumber(strtolower(substr($string, 0, 1)) . substr($string, 1))); // fix for "Error" if Capital Letter Start $string
    }

    /**
     * @param $string
     * @return string
     */
    static function decodeFromShortAlphaNum($string)
    {
        $output = String1::decodeStringBackFromNumber(Math1::decodeFromShortAlphaNum($string));
        return ctype_upper(substr($output, 1, 1)) ? strtoupper(substr($output, 0, 1)) . substr($output, 1) : $output;  // fix for "Error" if Capital Letter Start $string. Restore it back, using second letter case
    }

    /**
     * @param $str1
     * @param $str2
     * @return bool
     */
    static function isHashEquals($str1, $str2)
    {
        if (strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for ($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
            return !$ret;
        }
    }

    /**
     * Generate Random String
     * @param int $length
     * @param null $uniqueId
     * @return bool|string
     */
    static function random($length = 10, $uniqueId = null)
    {
        return substr(base_convert(sha1(uniqid($uniqueId ? $uniqueId : mt_rand())), 16, 36), 0, $length);
    }

    /**
     * Re-Show inserted string in count (n) time
     * @param string $value
     * @param int $repeatCount
     * @return bool|string
     */
    static function repeat($value = '', $repeatCount = 2)
    {
        $buf = '';
        foreach (range(1, $repeatCount) as $count) $buf .= $value;
        return $buf;
    }

    /**
     * @param $text
     * @param $length
     * @param int $start
     * @return bool|string
     */
    static function getSubString($text, $length, $start = 0)
    {
        return substr($text, $start, $length);
    }

    /**
     * Get Small Text Out of Large Text
     * @param $text
     * @param string $length
     * @param string $ellipsis
     * @return string
     */
    static function getSomeText($text, $length = '20', $ellipsis = ' ...')
    {
        if(String1::is_empty($text)){
            return "";
        }
        return (strlen($text) < $length) ? $text : self::getSubString($text, $length) . $ellipsis;
    }

    /**
     * @param $needle
     * @param $haystack
     * @return bool
     */
    static function contains($needle, $haystack)
    {
        if (empty($needle) || empty($haystack)) return false;
        return strpos($haystack, $needle) !== false;
    }

    /**
     * @param array $needles
     * @param $haystack
     * @param string $operator
     * @param bool $asWord
     * @return bool|string
     */
    static function containsMany($needles = [], $haystack = "", $operator = 'or', $asWord = false)
    {
        if ($operator === 'or' || $operator === '||') {
            // Or Logical Operator
            if ($asWord) {
                //if (preg_match("/(foo|bar|baz)/i", $haystack) === 1){}
                $needle = implode('|', $needles);
                if (preg_match("/($needle)/i", $haystack) === 1) return true;

            } else {
                //if (preg_match("/(foo|bar|baz)/i", $haystack) === 1){}
                $needle = '';
                for ($i = 0; $i < count($needles); $i++) {
                    $needle .= ($i != 0) ? '|' : '';
                    $needle .= ".*$needles[$i]";
                }
                if (preg_match("/($needle)/i", $haystack) === 1) return true;
            }

        } else {
            // And Logical Operator
            if ($asWord) {
                // TO ARCHIVE THIS  if (preg_match('/^(?:foo()|bar()|baz()){3}\1\2\3$/s', $subject)) {}
                $needle = '';
                $needleNum = '';
                for ($i = 0; $i < count($needles); $i++) {
                    $needle .= ($i != 0) ? '|' : '';
                    $needle .= "$needles[$i]()";

                    $needleNum .= "\\" . ($i + 1);
                }
                $needle = '/^(?:' . $needle . ')' . '{' . count($needles) . '}' . $needleNum . '$/i';
                if (preg_match($needle, $haystack) === 1) return $needle;

            } else {
                //if (preg_match('/^(?=.*foo)(?=.*bar)(?=.*baz)/s', $subject)) {}
                $needle = '';
                foreach ($needles as $search) $needle .= "(?=.*$search)";
                if (preg_match("/^$needle/s", $haystack) === 1) return true;
            }
        }
        return false;
    }


    /**
     * @param $value
     * @param bool $trueValue
     * @param bool $falseValue
     * @return bool
     */
    static function toBoolean($value, $trueValue = true, $falseValue = false)
    {
        $isString = is_string($value) && (trim($value) != '' && strtolower(trim($value)) !== 'false' && strtolower(trim($value)) !== 'off' && strtolower(trim($value)) !== 'no' && strtolower(trim($value)) !== '0' && strtolower(trim($value)) !== 'null');
        $isBoolean = is_bool($value) && ($value === true);
        $isNumber = is_integer($value) && ($value >= 1);
        return ($value !== null && ($isBoolean || $isString || $isNumber)) ? $trueValue : $falseValue;
    }


    //incase we have
    //The "are" at the beginning of "area"
    //The "are" at the end of "hare"
    //The "are" in the middle of "fares"
    static function containsWord($text, $wholeWordToFind)
    {
        return !!preg_match('#\\b' . preg_quote($wholeWordToFind, '#') . '\\b#i', $text);
    }

    /**
     * @param $str
     * @param string $replaceWith
     * @return null|string|string[]
     */
    static function removeBracket($str, $replaceWith = '')
    {
        return preg_replace('/\([ˆ)]*\)|[()]/', $replaceWith, $str);
    }


    /**
     * @param $string
     * @param string $removeString
     * @return bool|string
     */
    static function leftTrim($string, $removeString = '')
    {
        if (!self::startsWith($string, $removeString)) return $string;
        return substr($string, strlen($removeString));
    }

    // normaliser
    static function toArrayTree($array, $delimiter = ',')
    {
        return implode('\n', explode($delimiter, json_encode(($array))));
    }

    /**
     * Pointer Data if Data not null or empty
     * @param $data
     * @param bool $stringScan
     * @return bool
     */
    static function is_empty(&$data, $stringScan = true)
    {
        if (!isset($data) || !$data) return true;
        if (is_array($data) && (count($data) < 1)) return true;
        if ((is_integer($data) || is_double($data)) && ($data < 0.1)) return true;
        if ((is_string($data) && (trim($data) === ''))) true;
        if (is_string($data) && $stringScan && strtolower($data) === 'null') return true;
        return false;
    }

    /** Non-Pointer Data if Data not null or empty
     * @param $data
     * @param bool $stringScan
     * @return bool
     */
    static function isEmpty($data, $stringScan = true)
    {
        return self::is_empty($data, $stringScan);
    }

    /**
     * Pointer, if Empty Then Return , Or ELse
     * @param $data
     * @param string $thenValue
     * @param string $elseValue
     * @return string
     */
    static function if_empty(&$data, $thenValue = '', $elseValue = '')
    {
        return self::is_empty($data) ? $thenValue : $elseValue;
    }

    /**
     * Non-Pointer, if Empty Then Return , Or ELse
     * @param $data
     * @param string $thenValue
     * @param string $elseValue
     * @return string
     */
    static function ifEmpty(&$data, $thenValue = '', $elseValue = '')
    {
        return self::if_empty($data, $thenValue, $elseValue);
    }


    /**
     * not empty
     * @param $data
     * @param string $thenValue
     * @param string $elseValue
     * @return string
     */
    static function ifNotEmpty($data, $thenValue = '', $elseValue = '')
    {
        return !self::is_empty($data) ? $thenValue : $elseValue;
    }

    /**
     * main If function
     * @param $data
     * @param string $thenValue
     * @param string $elseValue
     * @return string
     */
    static function IfThen($data, $thenValue = '', $elseValue = '')
    {
        return (($data == true) || ($data == 1) || (trim(strtolower($data)) == 'true')) ? $thenValue : $elseValue;
    }

    /**
     * Pointer , If Value isSet or Value Not Null or Empty then return Value Else Return DefaultValue
     * @param $data
     * @param string $defaultValue_IfNotSet
     * @return string
     */
    static function isset_or(&$data, $defaultValue_IfNotSet = "")
    {
        return self::if_empty($data, $defaultValue_IfNotSet, $data);
    }

    /**
     * Non-Pointer , If Value isSet or Value Not Null or Empty then return Value Else Return DefaultValue
     * @param $data
     * @param string $defaultValue_IfNotSet
     * @return string
     */
    static function isSetOr($data, $defaultValue_IfNotSet = "")
    {
        return self::isset_or($data, $defaultValue_IfNotSet);
    }

    /**
     * Many Confirmation
     * @param mixed ...$valueListInAscendingOrder
     * @return bool|mixed
     */
    static function isset_any(...$valueListInAscendingOrder)
    {
        foreach ($valueListInAscendingOrder as $i => $v) {
            if (!self::is_empty($valueListInAscendingOrder[$i])) return $valueListInAscendingOrder[$i];
        }
        return false;
    }

    /**
     * Return Any Not Empty or Null Value
     * @param array ...$valueListInAscendingOrder
     * @return mixed|null
     */
    static function useAvailableValue(...$valueListInAscendingOrder)
    {
        foreach ($valueListInAscendingOrder as $availableValue) {
            if (!self::is_empty($availableValue)) return $availableValue;
        }
        return false;
    }


    /**
     * Instantiate Null Value to Given Value
     * @param $data
     * @param string $defaultValue
     * @return string
     */
    static function nullTo($data, $defaultValue = '')
    {
        if ($data == null || @trim(strtolower($data)) == 'null') return $defaultValue;
        return $data;
    }


    /**
     * @param array $keyValueArray
     * @param string $else
     * @return mixed|string
     *     (return array key value if it's key = true... otherwise if none key is true, return $else variable)
     */
    static function ifKeyIsTrue_returnKeyValue($keyValueArray = [], $else = '')
    {
        foreach ($keyValueArray as $key => $value) if ($key == true) return $value;
        return $else;
    }

    static function ifKeyEqualValue($equalKeyValueList = [], $IfEqualThen = "active", $else = '')
    {
        foreach ($equalKeyValueList as $key => $value) if ($key === $value) return $IfEqualThen;
        return $else;
    }

    static function ifAllValueEquals($then = "active", $else = '', ...$valueList)
    {
        $isAllTrue = true;
        $lastValue = '------*-----';
        foreach ($valueList as $value) {
            if ($lastValue === '------*-----') $lastValue = $value;
            if ($lastValue !== $value) $isAllTrue = false;
        }
        return ($isAllTrue) ? $then : $else;
    }

    static function isAllTrue(...$conditionValueList)
    {
        foreach ($conditionValueList as $key) if (!$key || $key == false) return false;
        return true;
    }

    static function isAnyTrue(...$conditionValueList)
    {
        foreach ($conditionValueList as $key) if ($key == true) return true;
        return false;
    }

}