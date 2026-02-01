<?php

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
