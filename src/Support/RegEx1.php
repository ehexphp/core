<?php

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
