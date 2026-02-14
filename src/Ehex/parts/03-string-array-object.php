<?php
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
        if(empty($stringArrayValue)){
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
        if(!$arrayList){
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


class RecursiveArrayObject1 extends \ArrayObject
{
    public function __construct($input = null, $flags = self::ARRAY_AS_PROPS, $iterator_class = "ArrayIterator")
    {
        foreach ($input as $k => $v) $this->__set($k, $v);
        return $this;
    }

    public function __set($name, $value)
    {
        if (is_array($value) || is_object($value))
            $this->offsetSet($name, (new self($value)));
        else
            $this->offsetSet($name, $value);
    }

    public function __get($name)
    {
        if ($this->offsetExists($name))
            return $this->offsetGet($name);
        elseif (array_key_exists($name, $this)) {
            return $this[$name];
        } else {
            throw new \InvalidArgumentException(sprintf('$this have not prop `%s`', $name));
        }
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this);
    }

    public function __unset($name)
    {
        unset($this[$name]);
    }
}

class ArrayObject1 extends \ArrayObject
{

    public function __get($index)
    {
        if ($this->offsetExists($index)) return $this->offsetGet($index); else  return null;
    }//throw new UnexpectedValueException('Undefined key ' . $index); }

    public function __set($index, $value)
    {
        $this->offsetSet($index, $value);
        return $this;
    }

    public function __isset($index)
    {
        return $this->offsetExists($index);
    }

    public function __unset($index)
    {
        return $this->offsetUnset($index);
    }

    public function __toString()
    {
        return serialize($this);
    }


    public function __construct(...$object_or_array)
    {
        foreach ($object_or_array as $arguments) {
            if (!empty($arguments)) {
                foreach ($arguments as $property => $argument) {
                    $this->{$property} = $argument;
                }
            }
        }
    }

    public function addObject(...$object_or_array)
    {
        foreach ($object_or_array as $arguments) {
            if (!empty($arguments)) {
                foreach ($arguments as $property => $argument) {
                    $this->{$property} = $argument;
                }
            }
        }
    }

    public function addMethod($methodName, callable $callback)
    {
        $this->{$methodName} = $callback;
    }


    public function __call($method, $arguments)
    {
        if (isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $arguments);
        } else {
            die("Fatal error: Call to undefined method stdObject::{$method}()");
        }
    }
}

class exArrayObject1 extends ArrayObject1
{
}

class Class1
{

    /**
     * @param array|object $object_or_array
     * @return mixed
     * <p>
     *     this can be use to merge php class object together,
     *     to make object behave like array and array like object,
     *     and also show all functions in each object.
     *     to add more function
     *         Example
     *             $flexibleObject = Class1::toArrayObject(true,  (new User), (new Picture) );
     *             $flexible->newMethod = function (){
     *                 return 'hello world';
     *             }
     *
     *
     *             echo $flexibleObject->fullName;
     *                         OR
     *             echo $flexibleObject['fullName'];
     *                         OR
     *             Console1::print( $flexibleObject );
     * </p>
     */
    public static function toArrayObject($addClassMethods, ...$object_or_array)
    {
        $object_or_array = !is_bool($addClassMethods) && !$object_or_array ? [$addClassMethods] : $object_or_array;
        // create flexible ArrayObject that allows onFly Method to be added
        $flexibleObject = null;
        for ($i = 0; $i < count($object_or_array); $i++) {
            if ($i === 0) $flexibleObject = new exArrayObject1($object_or_array[$i]); // new ArrayObject($object_OR_array, ArrayObject::ARRAY_AS_PROPS)
            else $flexibleObject->addObject($object_or_array[$i]); // merge multiple object
        }
        // Add List Of Existing Methods in '$object_or_array' to Current Object
        if ($addClassMethods) {
            foreach ($object_or_array as $object) {
                if (is_object($object)) {
                    foreach (get_class_methods($object) as &$method) { //// get_object_vars($clunker)
                        $flexibleObject->{$method} = function (...$param) use ($object, $method) {
                            return call_user_func_array([($object), $method], $param);
                        };
                    }
                }
            }
        }
        return $flexibleObject;
    }


    static function cast($object, $class = 'object', $addMethods = false)
    {
        if (!class_exists($class)) $class = __NAMESPACE__ . "\\$class";
        if (!class_exists($class)) throw new InvalidArgumentException(sprintf('Unknown class: %s.', $class));

        // case with serialization
        $newObject = @unserialize(
            preg_replace(
                '/^O:\d+:"[^"]++"/',
                'O:' . @strlen($class) . ':"' . $class . '"',
                serialize($object)
            )
        );

        // add methods
        if ($addMethods && is_object($object)) {
            foreach (get_class_methods(new $class) as &$method) {
                $newObject->{$method} = function (...$param) use ($object, $method) {
                    return call_user_func_array([($object), $method], $param);
                };
            }
        }

        return $newObject;
    }

    /**
     * Get current Method parament Information.
     * Pass in debug_backtrace like this. var_dump( Object1::getCurrentMethodParams( debug_backtrace(null, 2)[1]) );
     * @param null $debug_backtrace_instance
     * @return array
     * @throws ReflectionException
     */
    static function getCurrentMethodParams($debug_backtrace_instance = null, $defaultArgs = true)
    {
        try {
            $methodInfo = $debug_backtrace_instance ? $debug_backtrace_instance : debug_backtrace(null, 2)[1];
            if ($defaultArgs) return self::getMethodParams($methodInfo['function'], $methodInfo['class']);
            // pie args
            $params = [];
            foreach ((new \ReflectionClass($methodInfo['class']))->getMethod($methodInfo['function'])->getParameters() as $k => $parameter) $params[$parameter->name] = isset($methodInfo['args'][$k]) ? $methodInfo['args'][$k] : $parameter->getDefaultValue();
            return $params;
        } catch (Exception $ex) {
            return [];
        }
    }


    /**
     * @param $class
     * @param $method
     * @param array $overriderWith
     * @param bool $paramNameAsIndex
     * @throws ReflectionException
     */
    static function getMethodParams($method, $class = null, $overrideKeyValue = [], $paramNameAsIndex = true)
    {
        $r = $class ? new ReflectionMethod($class, $method) : new ReflectionFunction($method);
        $neededParam = [];
        foreach ($r->getParameters() as $param) {
            $paramName = $param->getName();
            $paramPrimaryValue = String1::isset_or($overrideKeyValue[$paramName], null);
            $paramValue = String1::isset_or($overrideKeyValue[$paramName], $param->isOptional() ? $param->getDefaultValue() : null);
            if (empty($overrideKeyValue)) {
                if ($paramNameAsIndex) $neededParam[$paramName] = $paramValue;
                else $neededParam[] = $paramValue;
            } else {
                if ($paramNameAsIndex) { // optional is not present in self::request(), hence
                    //if($param->isOptional() && empty($paramPrimaryValue)) $neededParam[$paramName] = $paramValue;
                    //else
                    $neededParam[$paramName] = $paramValue;
                } else {
                    //if($param->isOptional() && empty($paramPrimaryValue)) $neededParam[] = $paramValue;
                    //else
                    $neededParam[] = $paramValue;
                }
            }
        }
        return $neededParam;
    }


    /**
     * Convert Array to Object
     * @param $array
     * @param null $className
     * @param bool $addMethod
     * @return bool|mixed
     * @see convertArrayToObject()
     */
    static function toObject($array, $className = null, $addMethod = false)
    {
        return self::convertArrayToObject($array, $className, $addMethod);
    }

    /**
     * Convert Object to array
     * @param $object
     * @return mixed
     * @see convertObjectToArray()
     */
    static function toArray($object)
    {
        return is_array($object) ? $object : json_decode(json_encode($object), 1);
    }


    /**
     * Convert Object to Array
     * @param $object
     * @return array
     * @see toArray()
     */
    static function convertObjectToArray($object)
    {
        if (is_array($object)) return $object;
        $_arr = is_object($object) ? get_object_vars($object) : $object;
        $arr = array();
        foreach ($_arr as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? self::convertObjectToArray($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }


    /**
     * Convert array to object
     * @param $array
     * @param string $className
     * @param bool $addMethod
     * @return bool|mixed|$class
     * @see toObject()
     */
    static function convertArrayToObject($array, $className = null, $addMethod = false)
    {
        if (String1::startsWith($className, 'class@anonymous')) return $array;
        $value = json_decode(json_encode($array), FALSE);
        return ($className) ? self::cast($value, $className, $addMethod) : $value;
    }


    /**
     * get unique hashcode key for object
     * @param $obj
     * @return string
     */
    static function hashCode($obj)
    {
        return spl_object_hash($obj);
    }

    /**
     * Combine many Object into one
     * @param mixed ...$object_or_array
     * @return bool|mixed
     */
    static function mergeObject(...$object_or_array)
    {
        $className = '';
        $objArray = [];
        for ($i = 0; $i < count($object_or_array); $i++) {
            if ($i === 0) $className = get_class($object_or_array[$i]);
            $objArray = array_merge($objArray, (array)$object_or_array[$i]); // merge multiple object
        }
        return self::convertArrayToObject($objArray, $className);
    }


    /**
     * Get list of object variable available
     * @param $object
     * @return array
     */
    static function getClassObjectVariables($object)
    {
        return get_object_vars($object);
    }

    /**
     * both static and object variables
     * @param $object
     * @return array
     */
    static function getClassVariables($object)
    {
        return get_class_vars(get_class($object));
    }

    /**
     * It relies on an interesting property: the fact that get_object_vars only returns the non-static variables of an object.
     * @param $object
     * @return array
     */
    static function getClassStaticVariables($object)
    {
        //print_r( array_diff(self::getAllClassVariables($object), self::getClassVariables($object)) );
        if (is_string($object)) $object = new $object;
        return array_diff(get_class_vars(get_class($object)), get_object_vars($object));
    }


    /**
     * Get Session Executed Class by Name or by Parent Class with debug_backtrace()
     * @param array $classList
     * @param callable $onFoundCallBack
     * @param bool $searchParentClass
     * @return array
     */
    static function getExecutedClass($classList = [], $searchParentClass = false, callable $onFoundCallBack = null)
    {
        $classPie = [];
        foreach (debug_backtrace() as $calledClassInfo) {
            foreach (Array1::makeArray($classList) as $class) {
                if (isset($calledClassInfo['class']) && $calledClassInfo['class']) {
                    if ($calledClassInfo['class'] == $class || ($searchParentClass && self::isParentClassExistIn($calledClassInfo['class'], $class))) {
                        if ($onFoundCallBack) $onFoundCallBack($calledClassInfo['class']);
                        $classPie[] = $calledClassInfo['class'];
                    }
                }
            }
        }
        return $classPie;
    }


    /**
     * Find Parent Class
     * @param $class
     * @param $parentClass
     * @return bool
     */
    static function isParentClassExistIn($class, $parentClass)
    {
        return in_array($parentClass, class_parents($class));
    }

    /**
     * Find Parent Implementation
     * @param null $className
     * @param null $parentInterface
     * @return bool
     */
    static function isInterfaceImplementExistIn($className = null, $parentInterface = null)
    {
        return in_array($parentInterface, class_implements($className));
    }

    /**
     * check if class exists and match condition then return them
     *  $availableClass = Class1::getClassesIf(function($class){ return $class::isTableExists(); }, 'Inbox', 'User');
     * @param callable $filterCallback
     * @param array $classList
     * @return array
     */
    static function getClassesIf(callable $filterCallback = null, ...$classList)
    {
        $classes = [];
        foreach ($classList as $av) {
            if (class_exists($av) && ($filterCallback ? $filterCallback($av) : true)) $classes[] = $av;
        }
        return $classes;
    }

}

class Object1 extends Class1
{
}


class Function1
{
    static $_ENV = [];

    /**
     * Convert method to string
     * @param $function
     * @return string
     * @throws ReflectionException
     */
    static function convertToString($function)
    {
        return String1::phpFunctionToString($function);
    }

    /**
     * convert back to closure and execute it
     * @param null $serializedFunctionString
     */
    static function unSerializedClosureAndEval($serializedFunctionString = null)
    {
        eval(unserialize($serializedFunctionString));
    }

    /**
     * convert closure to string
     * @param $function
     * @return string\
     */
    static function serializedClosure($function)
    {
        return serialize(static::convertToString($function));
    }


    /**
     * Memoization of a function results in memory.
     * @param $func
     * @return Closure
     */
    static function runAndCache(string $methodName, array $args = [])
    {
        $serializedArgs = serialize($args);
        $name = $methodName . $serializedArgs;
        // cache
        if (!isset(self::$_ENV[$name])) self::$_ENV[$name] = $methodName(...$args);
        return self::$_ENV[$name];
    }

}


