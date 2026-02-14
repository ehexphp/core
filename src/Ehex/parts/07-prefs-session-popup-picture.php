<?php
class FilePref1
{
    public $path;
    public $database;
    protected $d_settings;
    protected $dev_mode;

    public function __construct($database_name = 'default', $path = 'pref_db/', $dev_mode = true)
    {
        $this->path = $path;
        $this->database = $path . md5($database_name) . '.json';
        $this->d_settings = $path . 'd_settings.json';
        $this->dev_mode = true;
        if (!file_exists($path)) {
            mkdir($path);
        }
        file_put_contents($this->d_settings, '{"dev_mode": ' . $this->dev_mode . ', "recent_update": ' . time() . '}');
    }

    private function handleError($text, $override = false)
    {
        if ($this->dev_mode == true || $override == true) {
            echo '<br>Error: ' . $text;
        }
    }

    public function getDatabaseContent()
    {
        if (file_exists($this->database)) {
            $data_file = file_get_contents($this->database);
            return (array)json_decode($data_file);
        } else {
            return array();
        }
    }

    public function getPointer()
    {
        return count($this->getDatabaseContent());
    }

    private function newDatabase()
    {
        file_put_contents($this->database, '{"d_info_created" : ' . time() . '}');
        return true;
    }

    public function set($key, $value, $expire = 0)
    {
        if (!is_string($key)) {
            $this->handleError('Key must be a string, not "' . gettype($key) . '"');
        } else {
            $var_type = gettype($value);
            $data = array();
            $data[$key]['a'] = time();
            $data[$key]['t'] = substr($var_type, 0, 1);
            $data[$key]['d'] = $value;
            if (isset($expire) && is_numeric($expire) && $expire >= 1) {
                $data['e'] = $expire;
            }
            if (file_exists($this->database)) {
                $data_array = $this->getDatabaseContent();
                $new_array = array_merge($data_array, $data);
                file_put_contents($this->database, json_encode($new_array));
                return true;
            } else {
                if ($this->newDatabase() == true) {
                    $data_array = $this->getDatabaseContent();
                    $new_array = array_merge($data_array, $data);
                    file_put_contents($this->database, json_encode($new_array));
                    return true;
                } else {
                    $this->handleError('Failed to create new database, read and write permissions are required', true);
                    return false;
                }
            }
        }
    }

    public function fileOps($key, $request)
    {
        $data_file = $this->getDatabaseContent();
        if (isset($data_file[$key])) {
            switch ($request) {
                case 'delete':
                    unset($data_file[$key]);
                    return is_numeric(file_put_contents($this->database, json_encode($data_file)));
                    break;
                case 'search':
                    return true;
                    break;
                case "return":
                    return $data_file[$key]->d;
            }
        } else {
            return false;
        }
    }

    public function del($key)
    {
        return $this->fileOps($key, 'delete');
    }

    public function get($key)
    {
        return $this->fileOps($key, 'return');
    }

    public function search($key)
    {
        return $this->fileOps($key, 'search');
    }

    public function getAll($include_meta_data = false)
    {
        if ($include_meta_data == true) {
            return array_shift(json_decode(file_get_contents($this->datatbase)));
        } else {
            return json_decode(file_get_contents($this->database));
        }
    }
}

/**
 * Save to File
 * Class SessionPreferenceSave1
 */
class SessionPreferenceSave1
{
    static function sec_session_start()
    {
        $secure = true;
        // This stops JavaScript being able to access the session id.
        $httponly = true;

        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params($cookieParams["lifetime"],
            $cookieParams["path"],
            $cookieParams["domain"],
            $secure,
            $httponly);
        // Sets the session name to the one set above.
        session_start();            // Start the PHP session
        //session_regenerate_id(true);    // regenerated the session, delete the old one.
    }
}

/**
 * Handles all the globals for the page.
 */
class Global1{
    private static $vars = array();

    // Sets the global one time.
    public static function set($_name, $_value, $definedOnce = true)
    {
        if($definedOnce && self::has($_name))
        {
            throw new Exception('Global1::set("' . $_name . '") - Argument already exists and cannot be redefined!');
        }
        else
        {
            self::$vars[$_name] = $_value;
            return $_value;
        }
    }

    // Get the global to use.
    public static function get($_name)
    {
        if(self::has($_name))
        {
            return self::$vars[$_name];
        }
        else
        {
            throw new Exception('Global1::get("' . $_name . '") - Argument does not exist in globals!');
        }
    }

    // Get the global to use.
    public static function has($_name)
    {
        return array_key_exists($_name, self::$vars);
    }
}

class Cookie1
{
    private static function domain()
    {
        return (String1::startsWith($_SERVER['HTTP_HOST'], "localhost")) ? '' : "." . $_SERVER['HTTP_HOST'];
    }

    public static function set($name, $value = '', $days = 30, $secure = false)
    {


        $value = ((is_object($value) || is_array($value)) ? json_encode($value) : $value);
        @setcookie($name, $value, strtotime("+$days days"), '/', self::domain(), $secure);
        // $options = array (
        //     'expires' => strtotime( "+$days days" ),
        //     'path' => '/',
        //     'domain' => self::domain(), //'.example.com', // leading dot for compatibility or use subdomain
        //     'secure' => $secure,     // or false
        //     'httponly' => false,    // or false
        //     'samesite' => 'None' // None || Lax  || Strict
        // );
        // setcookie($name, $value, $options);
    }

    public static function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;

    }

    public static function getAll()
    {
        return $_COOKIE;
    }

    public static function getAndUnset($name)
    {
        $data = static::get($name);
        static::delete($name);
        return $data;
    }

    public static function exists($name)
    {
        return static::get($name);
    }

    public static function delete($name)
    {
        unset($_COOKIE[$name]);
        @setcookie($name, "");
        @setcookie($name, "", time() - 3600, '/');
        @setcookie($name, null, time() - 3600, '/', static::domain(), false);
        return true;
    }

    public static function deleteAll()
    {
        echo "<script>cookieStore.getAll().then(cookies => cookies.forEach(cookie => {
                    cookieStore.delete(cookie.name);
                }));</script>";

        /*if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time() - 1000);
                setcookie($name, '', time() - 1000, '/');
            }
        }*/
    }
}

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

class Popup1
{
    // plugins
    // pnotify
    // swal

    static $TYPE_ERROR = "error";
    static $TYPE_WARNING = "warning";
    static $TYPE_SUCCESS = "success";
    static $TYPE_INFORMATION = "info";


    // variable
    public $title = '';
    public $body = [];
    public $type = '';


    // init data
    function __construct($title = '', $body = '', $type = 'info')
    {
        $this->setType($type);
        $this->setTitle($title);
        $this->setBody($body);

        return $this;
    }

    // set data
    function setDataFromArray($data = [])
    {
        if (!empty($data) && isset($data['title'])) return new self(@$data['title'], @$data['body'], @$data['info']);
    }

    function setData($title = '', $body = '', $type = 'info')
    {
        return new self($title, $body, $type);
    }

    function setTitle($title = '')
    {
        $this->title = $title;
        return $this;
    }

    function setBody($body = '')
    {
        if (String1::is_empty($body)) return '';
        $this->body[] = $body;
        return $this;
    }

    function setType($type = 'info')
    {
        $this->type = $type;
        return $this;
    }

    function addBody($body = '')
    {
        if (String1::is_empty($body)) return '';
        $this->body[] = $body;
        return $this;
    }


    // get data
    function issetData()
    {
        return (String1::is_empty($this->title) && (count($this->body) < 1)) ? false : true;
    }

    function getBody($listItemOpeningTag = '<li>', $listItemClosingTag = '</li>')
    {
        $itemList = '';
        foreach ($this->body as $item) {
            //if(is_array($item)) $itemList .=  $listItemOpeningTag.implode(' : ', $item).$listItemClosingTag;
            if (is_array($item) && (count($item) > 1)) {
                $itemListBuffer = '';
                for ($ii = 0; $ii < count($item); $ii++) {
                    $startCount = ($listItemOpeningTag == '') ? '(' . ($ii + 1) . ') ' : '';
                    $itemListBuffer .= $startCount . $listItemOpeningTag . String1::escapeQuotes(@$item[$ii]) . ' ' . $listItemClosingTag;
                }
                $itemList = $itemListBuffer;
            } else $itemList .= String1::toString(Array1::toArray(String1::escapeQuotes($item)), ' ');
        }
        return $itemList;
    }

    function getTitle()
    {
        return $this->title;
    }

    function getType()
    {
        return $this->type;
    }


    // dialog
    function toWindowsAlert()
    {
        if ($this->issetData()) Console1::popup($this->getTitle() . '\n' . $this->getBody('', ''));
    }

    function toToast($listItemOpeningTag = '', $listItemClosingTag = '')
    {
        if ($this->issetData()) echo HtmlWidget1::toast($this->getTitle(), $this->getBody($listItemOpeningTag, $listItemClosingTag), $this->getType());
    }

    function toHtmlList($listItemOpeningTag = '<li>', $listItemClosingTag = '</li>')
    {
        if ($this->issetData()) return "<div class='alert alert-" . $this->getType() . "> <h4><strong><i class='fa fa-$this->type'></i> $this->title</strong></h4><ol>" . $this->getBody($listItemOpeningTag, $listItemClosingTag) . "</ol> </div>";
        return null;
    }

    function toText($titleBreak = '<hr/>', $listItemOpeningTag = '', $listItemClosingTag = '<br/>')
    {
        if ($this->issetData()) return "$this->title $titleBreak" . $this->getBody($listItemOpeningTag, $listItemClosingTag);
        return null;
    }

    function toPanel($listItemOpeningTag = '<p>', $listItemClosingTag = '</p>')
    {
        /**if (!$this->issetData()) return;
         * ?>
         * <div class="panel panel-default panel-< ?php echo $this->getType() ?>">
         * <div class="panel-heading">< ?php echo $this->getTitle() ?></div>
         * <div class="panel-body"> < ?php echo $this->getBody($listItemOpeningTag, $listItemClosingTag ) ?> </div>
         * </div>
         * < ?php*/
    }


    /**
     *  Display Swal Alert with instance data
     * @param bool $wrapJQueryReadyScript
     * @param string $itemListOpenTag
     * @param string $itemListCloseTag
     * @return string
     */
    function toSwalAlert($wrapJQueryReadyScript = true, $itemListOpenTag = '<div style=\"padding:6px;border-bottom:1px solid #eeeeee\">', $itemListCloseTag = '</div>')
    {
        if (!$this->issetData()) return '';
        $response = sprintf('
                <script>
                    (function(){
                          swal({title:"%s", html:"%s", type:"%s"})
                    })($);
                </script>',
            $this->getTitle(),
            $this->getBody($itemListOpenTag, $itemListCloseTag),
            $this->getType()
        );
        echo $response;
    }


    /**
     * @param $title
     * @param string $data
     * @param string $type
     * @return string
     */
    static function swalAlert($title, $data = '', $type = 'info')
    {
        return sprintf('<script> (function(){ swal("%s", "%s", "%s") })($);</script>', $title, $data, $type);
    }
}


class Picture1
{

    /* function:  generates thumbnail */
    static function generateThumb($imagePath, $saveToDestination = '_thumb', $newWidth = 100)
    {
        /* read the source image */
        $source_image = imagecreatefromjpeg($imagePath);
        $width = imagesx($source_image);
        $height = imagesy($source_image);
        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($newWidth / $width));
        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($newWidth, $desired_height);
        /* copy source image at a resized size */
        imagecopyresized($virtual_image, $source_image, 0, 0, 0, 0, $newWidth, $desired_height, $width, $height);
        /* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $saveToDestination);
    }

    static function isImage($source_url)
    {
        if (function_exists("exif_imagetype")) return !!exif_imagetype($source_url);
        $img = getimagesize($source_url);
        return !empty($img[2]);
    }


    /**
     * File Extension
     * @param bool $commonPictureImage
     * @return array
     */
    static function getExtensionList($commonPictureImage = false)
    {
        $commonImg = array('png', 'jpeg', 'gif', 'jpg');
        return $commonPictureImage ? $commonImg : array_merge(['bmp', 'tiff', 'image', 'icns', 'ico'], $commonImg);
    }


    /**
     * The higher the number, the better the quality, but unfortunately the larger the size. You also can resize images with functions like imagecopyresampled and imagecopyresized.
     * @param $source_url
     * @param $destination_url
     * @param $quality
     * @return mixed
     */
    function compressAndUploadPicture_asJpeg($source_url, $destination_url, $quality = 60)
    {
        $info = getimagesize($source_url);
        if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
        elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
        elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
        else return false;
        imagejpeg($image, $destination_url, $quality);
        return $destination_url;
    }

    /**
     * The quality works only for JPG�s images. But if you want to change the file to PNG�s, you have to change manually via code. GIF doesn't affect the quality
     * Default quality for PNG: 9 ( 0 - no compression, 9 - max compression ) Create a new instance of a class
     * This function will return only the name of new image compressed with your respective extension
     *
     * @param $file_path
     * @param null $destination
     * @param int $quality
     * @param int $pngQuality
     * @return bool
     */
    public static function compressAndUploadPicture($file_path, $destination = null, $quality = 60, $pngQuality = 9)
    {
        //Send image array
        $array_img_types = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
        $new_image = null;
        $image_extension = null;
        $maxsize = 5245330;
        try {
            //Get image width, height, mimetype, etc..
            $image_data = getimagesize($file_path);
            //Set MimeType on variable
            $image_mime = $image_data['mime'];
            //Verifiy if the file is a image
            if (!in_array($image_mime, $array_img_types)) return false;
            //Get file size
            $image_size = filesize($file_path);
            //if image size is bigger than 5mb
            if ($image_size >= $maxsize) {
                return false;
            }

            //Switch to find the file type
            switch ($image_mime) {
                //if is JPG and siblings
                case 'image/jpeg':
                case 'image/pjpeg':
                    //Create a new jpg image
                    $new_image = imagecreatefromjpeg($file_path);
                    imagejpeg($new_image, $destination, $quality);
                    break;
                //if is PNG and siblings
                case 'image/png':
                case 'image/x-png':
                    //Create a new png image
                    $new_image = imagecreatefrompng($file_path);
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                    imagepng($new_image, $destination, $pngQuality);
                    break;
                // if is GIF
                case 'image/gif':
                    //Create a new gif image
                    $new_image = imagecreatefromgif($file_path);
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                    imagegif($new_image, $destination);
            }

        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        //Return the new image resized
        return $new_image;
    }


    static function upload($source_url, $destination, $shouldCompress = true)
    {
        if ($shouldCompress) if (self::compressAndUploadPicture($source_url, $destination, 20)) return true;
        return move_uploaded_file($source_url, $destination);
    }


    static function getImageSizeInKB($imageFile)
    {
        return isset($imageFile["file"]["size"]) ? ($imageFile["file"]["size"] / 1024) : false;
    }


    public static function getPictureFromGravatar($email, $size = 25, $fetchContent = true)
    {
        if (\Url1::isHttps()) $url = 'https://secure.gravatar.com/';
        else $url = 'http://www.gravatar.com/';
        $url .= 'avatar/' . md5($email) . '?s=' . (int)abs($size);
        // sprintf('https://www.gravatar.com/avatar/%s?s=100', md5($email))
        return $fetchContent ? @file_get_contents($url) : $url;
    }

    public static function toBase64Only($filename)
    {
        return base64_encode(fread(fopen($filename, "r"), filesize($filename)));
    }

    public static function toBase64($filename)
    {
        $imageDetails = getimagesize($filename);
        if ($fp = fopen($filename, "rb", 0)) {
            $picture = fread($fp, filesize($filename));
            fclose($fp);
            // base64 encode the binary data, then break it
            // into chunks according to RFC 2045 semantics
            $base64 = chunk_split(base64_encode($picture));
            $imageData = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;
        } else {
            $imageData = $filename;
        }
        return $imageData;
    }


    static function resize($source_image, $destination, $imageWidth = 100, $imageHeight = 100, $quality = 80, $watermarkSource = false)
    {
        // The getimagesize functions provides an "imagetype" string contstant, which can be passed to the image_type_to_mime_type function for the corresponding mime type
        $info = getimagesize($source_image);
        $imgtype = image_type_to_mime_type($info[2]);
        // Then the mime type can be used to call the correct function to generate an image resource from the provided image
        switch ($imgtype) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($source_image);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($source_image);
                break;
            case 'image/png':
                $source = imagecreatefrompng($source_image);
                break;
            default:
                die('Invalid image type.');
        }
        // Now, we can determine the dimensions of the provided image, and calculate the width/height ratio
        $src_w = imagesx($source);
        $src_h = imagesy($source);
        $src_ratio = $src_w / $src_h;
        // Now we can use the power of math to determine whether the image needs to be cropped to fit the new dimensions, and if so then whether it should be cropped vertically or horizontally. We're just going to crop from the center to keep this simple.
        if ($imageWidth / $imageHeight > $src_ratio) {
            $new_h = $imageWidth / $src_ratio;
            $new_w = $imageWidth;
        } else {
            $new_w = $imageHeight * $src_ratio;
            $new_h = $imageHeight;
        }
        $x_mid = $new_w / 2;
        $y_mid = $new_h / 2;
        // Now actually apply the crop and resize!
        $newpic = imagecreatetruecolor(round($new_w), round($new_h));
        imagecopyresampled($newpic, $source, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
        $final = imagecreatetruecolor($imageWidth, $imageHeight);
        imagecopyresampled($final, $newpic, 0, 0, ($x_mid - ($imageWidth / 2)), ($y_mid - ($imageHeight / 2)), $imageWidth, $imageHeight, $imageWidth, $imageHeight);
        // If a watermark source file is specified, get the information about the watermark as well. This is the same thing we did above for the source image.
        if ($watermarkSource) {
            $info = getimagesize($watermarkSource);
            $imgtype = image_type_to_mime_type($info[2]);
            switch ($imgtype) {
                case 'image/jpeg':
                    $watermark = imagecreatefromjpeg($watermarkSource);
                    break;
                case 'image/gif':
                    $watermark = imagecreatefromgif($watermarkSource);
                    break;
                case 'image/png':
                    $watermark = imagecreatefrompng($watermarkSource);
                    break;
                default:
                    die('Invalid watermark type.');
            }
            // Determine the size of the watermark, because we're going to specify the placement from the top left corner of the watermark image, so the width and height of the watermark matter.
            $wm_w = imagesx($watermark);
            $wm_h = imagesy($watermark);
            // Now, figure out the values to place the watermark in the bottom right hand corner. You could set one or both of the variables to "0" to watermark the opposite corners, or do your own math to put it somewhere else.
            $wm_x = $imageWidth - $wm_w;
            $wm_y = $imageHeight - $wm_h;
            // Copy the watermark onto the original image
            // The last 4 arguments just mean to copy the entire watermark
            imagecopy($final, $watermark, $wm_x, $wm_y, 0, 0, $imageWidth, $imageHeight);
        }
        // Ok, save the output as a jpeg, to the specified destination path at the desired quality.
        // You could use imagepng or imagegif here if you wanted to output those file types instead.
        if (Imagejpeg($final, $destination, $quality)) {
            return true;
        }
        // If something went wrong
        return false;
    }


}
