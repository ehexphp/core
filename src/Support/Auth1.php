<?php

/**
 * Class Auth1
 * the Aim is to Design Something similar to laravel Auth (but with 1)
 */
abstract class Auth1
{
    /**
     * @var User
     */
    public static $USER_CLASS = User::class;
    private static $USER_LOGIN = null;

    /**
     * @param bool $redirectOnFailed
     * @return AuthModel1|User
     */
    static function user($redirectOnFailed = false)
    {
        if (static::$USER_LOGIN) return static::$USER_LOGIN;
        return static::$USER_LOGIN = static::$USER_CLASS::getLogin($redirectOnFailed);
    }


    /**
     * Get a particular field/column of user class, return null if user not available or default value
     * @param null $fieldName | optional, return user->getLogin() information if null is passed in ]
     * @param null $defaultOnNull | return default value if null or if user is not set
     * @return AuthModel1|string|User
     */
    static function get($fieldName = null, $defaultOnNull = null)
    {
        return $fieldName ? String1::isset_or(static::user()[$fieldName], $defaultOnNull) : static::user();
    }

    /**
     * User Primary Id
     * @return string
     */
    static function id()
    {
        return String1::isset_or(static::user()['id'], null);
    }


    /**
     * Get User or Blank User Model if User Not Exists
     * @return AuthModel1|User
     */
    static function userOrInit()
    {
        return static::id() ? static::$USER_CLASS::getLogin(false) : static::$USER_CLASS::findOrInit();
    }

    /**
     * If User has not login
     * @return bool
     */
    static function isGuest()
    {
        return static::$USER_CLASS::isGuest() === true;
    }

    static function isAdmin($redirectToLoginPageIfGuest = false, $column_value = ['admin'], $column_name = 'role')
    {
        return static::$USER_CLASS::isAdmin($redirectToLoginPageIfGuest, $column_value, $column_name);
    }


    /**
     * Put At the top of the Page and Specify The Required Role
     *  If Role Failed, The Page will be redirected to login page
     * @param array $column_role_list
     * @param string $column_role_name
     * @param string $on_failed_redirect_to
     * @param callable|null $onSuccessCallBack
     * @param callable|null $onErrorCallBack
     * @param string $errorMessage
     * @param string $errorTitle
     * @return mixed
     */
    static function getAllowedRoleLogin($column_role_list = ['admin'], $column_role_name = 'role', $on_failed_redirect_to = '/login', callable $onSuccessCallBack = null, callable $onErrorCallBack = null, $errorMessage = 'You do not have permission to visit this page, Please login again', $errorTitle = 'Access Denied')
    {
        return static::$USER_CLASS::getAllowedRoleLogin($column_role_list, $column_role_name, $on_failed_redirect_to, $onSuccessCallBack, $onErrorCallBack, $errorMessage, $errorTitle);
    }
}
