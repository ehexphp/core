<?php

/**
 * Class Api1
 *  All Api Class must Extend this
 */
#[\AllowDynamicProperties]
abstract class Api1 extends ServerRequest1
{
    public static $api_id = '';
    public static $api_key = '';

    /**
     * @param $request
     * @return bool
     */
    public static function onApiStart($request)
    {
        return true;
    }

    /**
     * @return bool
     */
    public static function isApiAuthValid()
    {
        return isset($_REQUEST['_token']) ? is_token_valid($_REQUEST['_token']) : false;
    }

    /**
     * return !!User::getAllowedRoleLogin([]);
     * @return bool
     */
    public static function isUserAllowed()
    {
        return true;
    }

}
