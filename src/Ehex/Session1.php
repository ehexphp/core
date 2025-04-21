<?php

class Session1
{
    public static $NAME = '__site';


    //|||||||||||||||||| Data |||||||||||||||||||\\
    public static function set($name, $data)
    {
        $_SESSION[static::$NAME][$name] = $data;
    }

    static function get($name = null)
    {
        if (!isset($_SESSION[static::$NAME])) return null;
        if (!$name) return Object1::toArrayObject($_SESSION[static::$NAME]);
        if (self::exists($name)) return $_SESSION[static::$NAME][$name];
        return null;
    }

    static function exists($name)
    {
        return (isset($_SESSION[static::$NAME][$name]));
    }

    public static function delete($name = null)
    {
        if (!$name) {
            unset($_SESSION[static::$NAME]);
            return;
        }
        unset($_SESSION[static::$NAME][$name]);
    }

    public static function getAndUnset($name)
    {
        $data = self::get($name);
        self::delete($name);
        return $data;
    }


    //|||||||||||||||||| Login |||||||||||||||||||\\
    private static function saveLogin($user_name, $password)
    {
        $_SESSION[self::$NAME]['u1'] = \Form1::encode_data($user_name, true);
        $_SESSION[self::$NAME]['p1'] = \Form1::encode_data($password, true);
    }

    private static function getLogin()
    {
        if (isset($_SESSION[self::$NAME]['u1'], $_SESSION[self::$NAME]['p1'])) return ['user_name' => \Form1::decode_data($_SESSION[self::$NAME]['u1'], true), 'password' => \Form1::decode_data($_SESSION[self::$NAME]['p1'], true)];
        return null;
    }

    public static function isLoginExists()
    {
        return !!(static::getLogin());
    }

    public static function deleteUserInfo($onlyAuthInfo = false)
    {
        if ($onlyAuthInfo) unset($_SESSION[self::$NAME]['u1'], $_SESSION[self::$NAME]['p1'], $_SESSION[self::$NAME]['usi1']);
        else {
            unset($_SESSION[self::$NAME]);
            Session1::set('cookie_login', 0);
            Cookie1::delete('usi1');
        }
        return true;
    }

    public static function saveUserInfo($user, $withCookie = true)
    {
        static::saveLogin(String1::isset_or($user['user_name'], $user['email']), $user['password']);
        $_SESSION[self::$NAME]['usi1'] = \Form1::encode_data(serialize(Object1::toArray($user)), true);

        if ($withCookie /*&& !Session1::exists('cookie_login')*/) {
            Cookie1::set('usi1', json_encode([$user['id'], isset_or($user['password'])]));
            Session1::set('cookie_login', 1);
        }
    }


    /**
     * @param bool $clearAuthSessionOnFailedAndRedirect
     * @param string $redirectTo
     * @param string $redirectMessage
     * @param string $userClassNameToCastTo
     * @return mixed|null
     *
     *      User extending AuthModel1  Required
     */
    public static function getUserInfo($clearAuthSessionOnFailedAndRedirect = false, $redirectTo = '', $redirectMessage = 'Session Expired, Please Login!', $userClassNameToCastTo = 'User')
    {


        // login not saved, therefore re-login again
        $userInfoArray = null;

        // fetch userInfo from USI1
        if ((!$userInfoArray) && isset($_SESSION[self::$NAME]['usi1'])) {
            $login = unserialize(\Form1::decode_data($_SESSION[self::$NAME]['usi1'], true));
            if ((!isset($login['user_name']) || empty($login['user_name'])) && isset($login['email']))
                $login['user_name'] = $login['email'];
            if ((isset($login['user_name']) && trim($login['user_name']) != '') && (isset($login['password']) && trim($login['password']) != ''))
                $userInfoArray = $login;
        }


        // generate userInfo from Cookie
        if ((!$userInfoArray) && ($us1 = Cookie1::exists('usi1')) /*&& (Session1::get('cookie_login') === 1)*/) {
            list($user_id, $password) = json_decode($us1);
            if (!empty($user_id) && !empty($password)) {
                $userInfoArray = $userClassNameToCastTo::login($user_id, $password, ['id'], ['password'], true);
            }
        }

        // generate userInfo
        if (!$userInfoArray) {
            try {
                $login = Session1::getLogin();
                if ($login) $userInfoArray = $userClassNameToCastTo::login(String1::isset_or($login['user_name'], null), String1::isset_or($login['password'], null));
            } catch (Exception $e) {
            }
        }


        // $userInfo
        if (!$userInfoArray) {
            if ($clearAuthSessionOnFailedAndRedirect) {
                self::deleteUserInfo();
                // redirect
                if (trim($redirectTo) !== '') {
                    // save last path
                    self::setLastAuthUrl(Url1::getPageFullUrl());
                    // now redirect
                    Url1::redirectIf($redirectTo, $redirectMessage, [true]);
                    return null;
                }
            }
            return null;
        }

        // cast array object to user
        return Object1::toArrayObject(Object1::convertArrayToObject($userInfoArray, (($userClassNameToCastTo) ? $userClassNameToCastTo : User::class)));
    }


    /**
     * @param $url
     * Save and Get Last Url before Requesting for login Auth. So you can resume user back to there init path
     */
    static function setLastAuthUrl($url = null)
    {
        self::set('last_auth_url', $url ? $url : Url1::getPageFullUrl());
    }

    static function getLastAuthUrl($unset = true, $defaultIfFailed = null)
    {
        $last_url = $unset ? self::getAndUnset('last_auth_url') : self::get('last_auth_url');
        return $last_url ? $last_url : $defaultIfFailed;
    }


    static function deleteAccountData($name = null)
    {
        if ($name === null) unset($_SESSION[self::$NAME]);
        else unset($_SESSION[self::$NAME][$name]);
    }

    //|||||||||||||||||| Status |||||||||||||||||||\\
    public static function setStatus($title = '', $message = '', $type = 'info', $appendStatus = true)
    {
        $_SESSION['sTitle'] = (isset($_SESSION['sTitle']) && $appendStatus) ? $_SESSION['sTitle'] : $title;
        $_SESSION['sStatus'] = (isset($_SESSION['sStatus']) && $appendStatus) ? array_merge(Array1::toArray($_SESSION['sStatus']), Array1::toArray($message)) : $message;
        $_SESSION['sType'] = $type;
        $_SESSION['sIsActive'] = true;
        return null;
    }

    public static function setStatusIf($condition = false, $title = '', $message = '', $type = 'info')
    {
        return $condition ? static::setStatus($title, $message, $type) : null;
    }


    /**
     * Use when you are confused about type of status
     *  array $status [e.g 'title', 'body', 'type']
     * @param array | ResultObject1 | ResultStatus1 $status (Set Status Message from  either Array , Method as Result class of Ehex)
     * @return array (Optional , return separated value)
     */
    public static function setStatusFrom($status = null)
    {
        $status = $status instanceof ResultObject1 || $status instanceof ResultStatus1 ?
            ['Status', $status->getMessage(), $status->getStatus() ? 'info' : 'error'] :
            Array1::makeArray($status);

        $title = 'Status';
        $body = '';
        $type = 'info';

        // extract
        if ((count($status) === 1) || (count($status) > 3)) $body = $status;
        else if (count($status) === 3) list($title, $body, $type) = $status;
        else if (count($status) === 2) list($title, $body) = $status;

        // assign
        $type = (strtolower($type) === 'danger') ? 'error' : $type;
        $body = Array1::toStringNormalizeIfSingleArray($body);
        static::setStatus($title, $body, $type);
        return ([
            'title' => $title,
            'body' => $body,
            'info' => $type,
        ]);
    }


    /**
     * @return array|null get and delete status
     */
    public static function getAndUnsetStatus()
    {
        $data = self::getStatus();
        self::deleteStatus();
        return $data;
    }

    public static function deleteStatus()
    {
        $_SESSION['sIsActive'] = false;
        unset($_SESSION['sIsActive'], $_SESSION['sTitle']);
        unset($_SESSION['sStatus']);
        unset($_SESSION['sType']);
    }

    static function getStatus()
    {
        if (!String1::isset_or($_SESSION['sIsActive'], false)) return null;
        if (isset($_SESSION['sTitle'], $_SESSION['sStatus'], $_SESSION['sType'])) {
            return [
                'title' => $_SESSION['sTitle'],     // brief description
                'body' => $_SESSION['sStatus'],     // more description
                'status' => $_SESSION['sStatus'],   // true or false
                'info' => $_SESSION['sType'],       // description type
            ];
        }
        return null;
    }

    static function isStatusSet()
    {
        return (isset($_SESSION['sIsActive']) && $_SESSION['sIsActive'] == true);
    }


    /**
     * @param null $errors
     * @param bool $unsetStatus
     * @return Popup1
     */
    static function popupStatus($errors = null, $unsetStatus = true)
    {
        $popup = new Popup1();
        if (isset($errors) && $errors->any()) {
            $popup = new Popup1('Error', '', Popup1::$TYPE_WARNING);
            foreach ($errors->all() as $error) $popup->addBody($error);

        } else if (static::isStatusSet()) {
            $popup = $popup->setDataFromArray(
                ($unsetStatus) ? Session1::getAndUnsetStatus() : Session1::getStatus()
            );
        }
        return $popup;
    }
}
