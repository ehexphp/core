<?php

abstract class AuthModel1 extends Model1
{

    /**
     * Default Field to be included in all Auth Model
     * @var array
     */
    public static $FIX_COLUMN = ['id', 'created_at', 'updated_at', 'last_login_at'];

    /**
     * Should Contain Available Role List, in Descending order
     * @return array
     */
    static function getRoles()
    {
        return ['developer', 'admin', 'staff', 'user'];
    }

    /**
     * get allowed role access list
     * @param string $role
     * @return array|mixed
     */
    static function getRolesFrom($role = 'user')
    {
        return Array1::splitAndGetFirstList(static::getRoles(), $role);
    }

    /**
     * Confirm if logged in user role exists within First Row and Specified $toWhichRole End Role... e.g from admin to staff
     *  e.g User::isRoleWithin($userInfo->role, 'staff'); // if user is within the role
     *  Or Simply
     *      User::isRoleWithin(['admin', 'manager', 'staff']) // or
     *      User::isRoleWithin( User::getRolesFrom('staff') )
     * @param string $userRoleOrRoleList
     * @param string $toWhichRole
     * @return array
     */
    static function isRoleWithin($userRoleOrRoleList, $toWhichRole = 'staff')
    {
        if (is_array($userRoleOrRoleList)) return static::isAdmin(false, $userRoleOrRoleList);
        return Array1::contain(Array1::splitAndGetFirstList(static::getRoles(), $toWhichRole), $userRoleOrRoleList);
    }


    /**
     * @param null $requestKeyValue (Default is  $_REQUEST)
     * @param array $uniqueColumn (Columns That Must not Exists Twice)
     * @param bool $encryptPassword
     * @return static|bool|Model1|ResultStatus1
     *
     *        Register User and Return Account Info
     */
    static function register($requestKeyValue = null, $uniqueColumn = ['email', 'user_name'], $encryptPassword = false)
    {
        $requestKeyValue = $requestKeyValue ? $requestKeyValue : $_REQUEST;
        $requestKeyValue['password'] = $encryptPassword ? encrypt_data($requestKeyValue['password']) : $requestKeyValue['password'];
        return static::insert($requestKeyValue, $uniqueColumn);
    }


    /**
     * @param $user_name_or_email
     * @param $password
     * @param array $search_in_likely_column_name
     * @param array $search_in_likely_column_password
     * @param bool $tryPasswordEncryptVerification
     * @param bool $withCookie
     * @return array|ArrayObject|bool|mysqli_result|null|ResultStatus1|string|static
     *
     *    Login, Save Login Information to Session, and Return Login
     *        Use getLoginInfo() on any AuthRequiredPage
     */
    static function login($user_name_or_email, $password, $search_in_likely_column_name = ['email', 'user_name'], $search_in_likely_column_password = ['password'], $tryPasswordEncryptVerification = false, $withCookie = true)
    {
        if (String1::is_empty($user_name_or_email) || String1::is_empty($password)) return ResultStatus1::falseMessage('Complete login details required');

        // fetch user info
        $result = static::findAgainst($user_name_or_email, $tryPasswordEncryptVerification ? '' : $password, $search_in_likely_column_name, $tryPasswordEncryptVerification ? [] : $search_in_likely_column_password, ' AND ', ' OR ', ' = '); //["`".static::$PRIMARY_KEY_NAME."`"]
        if (static::$FLAG_SHOW_EXEC_QUERY) return $result;

        // assign user info
        $user = null;
        if ($result && !empty($result) && isset($result[0][static::$PRIMARY_KEY_NAME]) && $result[0][static::$PRIMARY_KEY_NAME] > 0) {
            if ($tryPasswordEncryptVerification) {
                $dbPassword = static::getField($result[0][static::$PRIMARY_KEY_NAME], 'password', '');
                if (($password !== $dbPassword) && !encrypt_validate($password, $dbPassword))
                    return ResultStatus1::falseMessage('Password not Valid');
            }
            $user = static::findOrInit($result[0]);
        }


        if ($user) {
            Config1::onLogin($user->getModel()); // Call Config onLogin // Console1::log($user);
            if (!Session1::exists('last_login_at') && isset(array_flip(static::$FIX_COLUMN)['last_login_at'])) {
                Session1::set('last_login_at', $user['last_login_at']);
                $user->update(['last_login_at' => date(static::$SQL_TIMESTAMP_FORMAT)]);
            }

            // save user login info
            Session1::saveUserInfo($user, $withCookie);
            return $user;
        } else return ResultStatus1::falseMessage('Credentials Not Found');
    }


    /**
     *  Refresh and Retrieve New Login Information in Cache, Call This After Update to Profile
     */
    static function re_login()
    {
        $userInfo = static::getLogin(false);
        Session1::deleteUserInfo(true);
        if ($userInfo && !empty($userInfo) && isset($userInfo['id'])) static::login(String1::isset_or($userInfo['user_name'], isset($userInfo['email']) ? $userInfo['email'] : $userInfo['id']), $userInfo['password']);
    }


    /**
     * @var User|Auth1|mixed|null
     */
    public static $userInfo = null;

    /**
     * @param bool $orRedirectToLoginPage
     * @param string $on_failed_redirect_to
     * @param string $redirectMessage
     *
     *    use login() to Login, Save Login Information to Session, and Return Login
     *    Use getLoginInfo() on any AuthRequiredPage, If Failed, It Will Redirect to loginPage
     * @return User|Auth1|mixed|null
     */
    static function getLogin($orRedirectToLoginPage = true, $on_failed_redirect_to = '/login', $redirectMessage = 'Session Expired, Please login again')
    {
        $user = Session1::getUserInfo($orRedirectToLoginPage, url($on_failed_redirect_to), $redirectMessage, static::getModelClassName());
        if (!$user) {
            return null;
        }

        if (static::$userInfo) {
            return static::$userInfo;
        }

        // get fresh user
        static::$userInfo = User::find($user->id);
        static::$userInfo['last_login_at'] = Session1::get('last_login_at');
        return static::$userInfo;
    }


    /**
     * Is Login Available in Cache
     * @return bool
     */
    static function isLoginExist()
    {
        return Session1::isLoginExists();
    }

    static function isGuest()
    {
        return !Session1::isLoginExists();
    }

    static function isAdmin($redirectToLoginPageIfGuest = false, $column_value = ['admin'], $column_name = 'role')
    {
        if (!static::isLoginExist()) return $redirectToLoginPageIfGuest ? redirect(routes()->login, ['Login Required', 'Please login', 'error']) : false;
        static::re_login();
        $column_value = Array1::makeArray($column_value);
        $current_role = static::getLogin()[$column_name];
        foreach ($column_value as $value) if ($current_role == $value) return true;
        return false;
    }

    /**
     * check if User Instance role exist in role list
     * @param array $roles
     * @param string $role_column_name
     * @return bool
     */
    function isUserRole($roles = ['admin'], $role_column_name = 'role')
    {
        return in_array($this->{$role_column_name}, Array1::makeArray($roles));
    }

    function isUserRoleGreaterThanOrEqual($role = 'staff', $role_column_name = 'role')
    {
        return User::isRoleWithin($this->{$role_column_name}, $role);
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
     * @return Auth1|mixed|null|User
     */
    static function getAllowedRoleLogin($column_role_list = ['admin'], $column_role_name = 'role', $on_failed_redirect_to = '/login', callable $onSuccessCallBack = null, callable $onErrorCallBack = null, $errorMessage = 'You do not have permission to visit this page, Please login with the right permission.', $errorTitle = 'Access Denied!')
    {
        $error = [$errorTitle, $errorMessage, 'error'];
        $login = static::getLogin(true, $on_failed_redirect_to, $error);
        if (static::isLoginExist() && in_array($login[$column_role_name], Array1::toArray($column_role_list))) {
            if ($onSuccessCallBack) return $onSuccessCallBack();
            return $login;
        } else {
            if ($onErrorCallBack) $onErrorCallBack();
            else {
                Session1::setLastAuthUrl(Url1::getPageFullUrl());
                redirect(url($on_failed_redirect_to), $error);
            }
        }
    }


    /**
     *  Clear Cache Data for User, This will also clear entire account reference cache
     * @param string $redirectTo
     * @return bool
     */
    static function logout($redirectTo = '/')
    {
        Config1::onLogout();
        Cookie1::deleteAll();
        session_destroy();
        unset($_SESSION);
        echo '<script>localStorage.clear(); for (const it in $.cookie()) $.removeCookie(it);</script>';
        if (Session1::deleteUserInfo()) {
            Url1::redirectIf($redirectTo, 'Logout Successfully!', true);
            return true;
        } else {
            Session1::setStatus('Failed', 'Logout Failed', 'error');
            return false;
        }
    }


    /**
     *  Upload User Avatar
     * @param null $source_url $file @Expecting $_FILE['avatar']['tmp_name'];
     * @param null|string $name @default "uploads/{$this.id}/avatar.jpg"
     * @return null|string
     * @internal param null
     */
    function uploadAvatar($source_url = null, $name = 'avatar.jpg')
    {
        $result = $this->uploadFile($source_url, $name, false, true, ['width' => 300, 'height' => 300]);
        if ($result) if ($this->update(['avatar' => $result])) return $result;
        return false;
    }

    /**
     * Get User Avatar
     * @param null|string $name
     * @param string $orDemoPictureUrl
     * @return null|string Verify If Image Exists, Then Return Image Path else, return null or Demo Image
     *  Verify If Image Exists, Then Return Image Path else, return null or Demo Image if Specified
     */
    function getAvatar($name = 'avatar.jpg', $orDemoPictureUrl = '...')
    {
        if ($orDemoPictureUrl === '...') $orDemoPictureUrl = HtmlAsset1::getImageAvatar();
        return $this->getFileUrl($name, $orDemoPictureUrl);
    }


    /**
     * Upload Any File to Id [Default is First User Account, which is usually Admin]
     * @param null $source_url
     * @param null $unique_file_name
     * @param int $user_id
     * @return bool|null|string
     */
    static function uploadMainFile($source_url = null, $unique_file_name = null, $user_id = 1)
    {
        return static::withId($user_id)->uploadFile($source_url, $unique_file_name);
    }

    /**
     * Get Any Main Uploaded File Url or Default Demo
     * @param null $file_name
     * @param int $user_id
     * @param string $orDemoPictureUrl
     * @return mixed
     */
    static function getMainFileUrl($file_name = null, $user_id = 1, $orDemoPictureUrl = '...')
    {
        return static::withId($user_id)->getFileUrl($file_name, $orDemoPictureUrl);
    }

    static function getMainFilePath($file_name = null, $user_id = 1, $uploadMainDirectory = 'uploads')
    {
        return static::withId($user_id)->getFilePath($file_name, $uploadMainDirectory);
    }

    /**
     * Delete Any Uploaded Main File
     * @param null $file_name
     * @param int $user_id
     * @return mixed
     */
    static function deleteMainFile($file_name = null, $user_id = 1)
    {
        return static::withId($user_id)->deleteFile($file_name);
    }

}
