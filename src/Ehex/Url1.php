<?php

class Url1
{

    /**
     * Use to fetch content with ajax
     *      // auto fetch and paste plain content
     *      Url1::loadContentByAjax( Form1::callApi("User::getField(1, 'full_name')?_token=".token()) );
     *
     *      // fetch input field value content
     *      <input id='textBox' style="width:500px" />
     *      Url1::loadContentByAjax( Form1::callApi("User::getField(1, 'avatar')?_token=".token()), null, 'textBox', 'val' );
     *
     *      // fetch and render content with pre value
     *      Url1::loadContentByAjax( Form1::callApi("User::getField(1, 'full_name')?_token=".token()), null, null, "My Fullname is %s" );
     *
     *      // fetch content with full tag, e.g image content. Just put $s in place of value
     *      Url1::loadContentByAjax( Form1::callApi("User::getField(1, 'avatar')?_token=".token()), null, null,  '<img style="width:500px;height:500px;"  src="%s"/>');
     *
     * @param $url
     * @param null $optionalFieldName
     * @param null $optionalDestinationContainerId
     * @param string $optionalDestinationAttributeOrFulltag
     */
    static function loadContentByAjax($url, $optionalFieldName = null, $optionalDestinationContainerId = null, $optionalDestinationAttributeOrFulltag = 'html')
    {
        /**$unique_id = $optionalDestinationContainerId? $optionalDestinationContainerId: 'ajax_field_'.Math1::getUniqueId();
         * echo $optionalDestinationContainerId? '':  "<span id='$unique_id'></span>"; ?>
         * <script>
         * $(function(){
         * Ajax1.requestGet("< ?= $url ?>", "<?= $optionalDestinationContainerId ?>", function(result){
         * result = ("< ?= $optionalFieldName ?>" && ("<?= $optionalFieldName ?>" !== ""))? result["<?= $optionalFieldName ?>"]: Object1.toJsonString(result);
         * result = result.replace(/"/g, "");
        * < ?php if(!String1::contains('%s', $optionalDestinationAttributeOrFulltag)){ ?>
         * $("#<?= $unique_id ?>").<?= $optionalDestinationAttributeOrFulltag ?>(result);
         * < ?php }else{ ?>
         * $("#<?= $unique_id ?>").html(`<?= $optionalDestinationAttributeOrFulltag ?>`.replace('%s', result));
         * < ?php } ?>
         * });
         * })
         * </script>
         * < ?php return '';**/
    }


    /**
     * This is a shorcut to Form1::callControllerAndBypassToken()
     * @param $token
     * @param string $lookupClassNameOrClassFunction
     * @param string $processMethod
     * @see Form1::callControllerAndBypassToken() for more
     */
    static function actionLink($lookupClassNameOrClassFunction = 'className@function(param1, param2)', $processMethod = 'processSave()')
    {
        return Form1::callControllerAndBypassToken(token(), $lookupClassNameOrClassFunction, $processMethod);
    }

    /**
     * @return string
     */
    static function getRequestRoute()
    {
        $url = explode("?", $_SERVER['REQUEST_URI']);
        $url = explode("#", $url[0]);
        return "/" . trim($url[0], "/");
    }


    /**
     * Ping Website, Remove Http://...
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @return bool
     */
    static function pingWithPort($host = 'www.google.com', $port = 80, $timeout = 6)
    {
        return !!(fsockopen($host, $port, $errno, $errstr, $timeout));
    }


    /**
     * Ping website
     * @param $host
     * @return bool
     */
    static function pingExec($host)
    {
        exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $value);
        return ($value === 0);
    }


    /**
     * Check if website available
     * @param string $host
     * @param bool $returnPageContent
     * @return bool|mixed
     */
    static function ping($host = 'www.google.com', $returnPageContent = false)
    {
        $url = $host;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode >= 200 && $httpcode < 300) return ($returnPageContent) ? $data : true;
        else return false;
    }


    /**
     * Create link to another directory
     * @param $fromDirectory
     * @param $toDirectory
     * @return string
     * @throws Exception
     */
    static function createSymLinks($fromDirectory, $toDirectory)
    {
        if (strcasecmp(substr(PHP_OS, 0, 3), 'WIN') === 0) die(Console1::println("Can't Create SymLinks on Windows OS. Please Copy the folder ehex or a shortcut to your project instead"));
        if (exec("ln -s '$fromDirectory'   '$toDirectory' ") && !is_link($toDirectory)) throw new Exception(Console1::println("Error Creating SymLinks from ['$fromDirectory'] to  ['$toDirectory'], Please create it manually") . '--- Creating createSymLinks Error --- ', 1);
        return '';
    }


    /**
     * @param null $to
     * @param string $subject
     * @param string $body
     * @param null $from
     * @return bool|ResultObject1
     */
    static function sendEmail($to, $subject, $message, $from = "admin@", $fromFullName = "", $contentType = "text/plain")
    {
        try {
            /* ini_set("SMTP", "aspmx.l.google.com");
            ini_set("sendmail_from", "****@gmail.com");*/
            @$from = String1::endsWith($from, "@") ? $from . $_SERVER['HTTP_HOST'] : $from;
            $headers = "From: $fromFullName <$from>" . PHP_EOL .
                "Reply-To: $fromFullName <$from>" . PHP_EOL .
                "Content-type: $contentType" . PHP_EOL .
                "X-Mailer: PHP/" . phpversion();
            return mail($to, $subject, $message, $headers);
        } catch (Exception $ex) {
        }
        return ResultStatus1::falseMessage($ex->getMessage());
    }


    /**
     * Send Mail with Mailer
     * @return ResultStatus1
     */
    static function sendMailer($to = null, $subject = '', $message = '', $from = null, $full_name = "", $attachment = null)
    {
        return (function_exists('framework_info') && framework_info()['name'] === 'ehex') ?
            exMail1::mailerSendMailToList([$to => Form1::extractUserName($to, false)], $subject, nl2br($message), $attachment, $from, $full_name) : null;
    }


    /**
     * @param null $ip
     * @param bool $deep_detect
     * @return string|null
     */
    static function getIPAddress($ip = null, $deep_detect = TRUE)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        } else $ip = $_SERVER["REMOTE_ADDR"];
        return $ip;
    }


    /**
     * Return Array  of ...   "ip", "country_code", "country_name", "region_code", "region_name", "city",  "zipcode", "latitude", "longitude",  "metro_code", "areacode",
     * @param null $ip
     * @param string $siteInfo
     * @return bool|string
     */
    static function getIPAddressInformation($ip = null, $siteInfo = 'http://freegeoip.net/json/')
    {
        return file_get_contents($siteInfo . (($ip) ? $ip : $_SERVER['REMOTE_ADDR']));
    }

    /**
     * normalize url path and remove //// or \/, or \// from url path
     * @param $url
     * @return string
     */
    static function stripSlashes($url)
    {
        return MySql1::url_strip_slashes($url);
    }

    static function getEhexCoreAssetsPath()
    {
        return Page1::getEhexCoreAssetsPath();
    }

    static function existInUrl($likelyUrl, $fullCurrentPageUrl = null)
    {
        $fullCurrentPageUrl = $fullCurrentPageUrl ? $fullCurrentPageUrl : self::getPageFullUrl();
        if ($likelyUrl == '/' || $likelyUrl == 'home' || $likelyUrl == 'index' || (trim($likelyUrl, '/') == trim(Url1::getPageFullUrl(), '/'))) if ((trim(Url1::getPageFullUrl(), '/') == trim(path_main_url(), '/')) || String1::endsWith(trim(Url1::getPageFullUrl(), '/'), 'index.php')) return true;
        if (empty($likelyUrl) || empty($fullCurrentPageUrl)) return false;
        return String1::contains(RegEx1::getSanitizeAlphaNumeric(urldecode(urldecode($likelyUrl))), RegEx1::getSanitizeAlphaNumeric(urldecode($fullCurrentPageUrl ? $fullCurrentPageUrl : self::getPageFullUrl())));
    }

    /**
     * Use for Dynamic Url Link
     * @param string $link
     * @param string $returnValue
     * @param string $elseReturnValue
     * @param bool $isUrlAbsolute e.g if url is home, like url('/'). that is absolute and shuld not be active with other url
     * @return string
     */
    static function ifExistInUrl($link = '/', $returnValue = 'active', $elseReturnValue = '', $isUrlAbsolute = false)
    {
        if ($isUrlAbsolute) {
            return (trim(self::getCurrentUrl(false), '/') === trim($link, '/')) ? $returnValue : $elseReturnValue;
        } else {
            return self::existInUrl($link) ? $returnValue : $elseReturnValue;
        }
    }

    static function ifUrlEquals($compareFullUrl = 'http://l...', $returnValue = 'active', $elseReturnValue = '')
    {
        return RegEx1::getSanitizeAlphaNumeric(urldecode(urldecode($compareFullUrl))) == RegEx1::getSanitizeAlphaNumeric(urldecode(urldecode(Url1::getPageFullUrl()))) ? $returnValue : $elseReturnValue;
    }

    static function buildParameter($param = [], $url = null)
    {
        $hash = '';
        if ($url) {
            $urlEx = explode("#", $url);
            $url = @$urlEx[0];
            $hashEx = @$urlEx[1];
            $hash = $hashEx ? "#$hashEx" : "";
        }
        if (!is_string($param) && empty($url)) return http_build_query($param);
        if (is_string($param)) {
            $param = [$param => $url];
            $url = null;
        }
        $url = $url ?? self::getPageFullUrl_noGetParameter();
        $urlArray = array_merge(self::convertUrlParamToArray($url), $param);
        return (explode("?", $url)[0] . '?' . http_build_query($urlArray)) . $hash;
    }

    static function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    static function isUrlExist($url)
    {
        $fp = @fopen($url, "r");
        if ($fp === false) return false;
        fclose($fp);
        return true;
    }

    /**
     * Check If path ends with .png
     * @param $filename
     * @param null $defaultImage
     * @return null|string
     */
    static function getFileImagePreview($filename, $defaultImage = null)
    {
        $ext = strtolower(FileManager1::getExtension($filename));
        switch ($ext) {
            case"ico":
            case"gif":
            case"jpg":
            case"jpeg":
            case"jpc":
            case"jp2":
            case"jpx":
            case"xbm":
            case"wbmp":
            case"png":
            case"bmp":
            case"tif":
            case"tiff":
            case"svg":
                return $filename;
            default:
                return $defaultImage ? $defaultImage : HtmlAsset1::getImageThumb();
        }
    }

    /**
     * Check is request is Ajax...
     *  Request header must contain 'X-Requested-With': 'XMLHttpRequest'.
     * @return bool
     */
    static function isAjaxRequest()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    static function relativePathToUrl($path, $fullDirectoryPath = __DIR__)
    {
        return self::pathToUrl($fullDirectoryPath) . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Clean Path from /../ or double slash ///
     * @param $path
     * @return string
     */
    static function normalizePath($path)
    {
        $parts = array();// Array to build a new path from the good parts
        $path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
        $path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
        $segments = explode('/', $path);// Collect path segments
        $test = '';// Initialize testing variable
        foreach ($segments as $segment) {
            if ($segment != '.') {
                $test = array_pop($parts);
                if (is_null($test)) $parts[] = $segment;
                else if ($segment == '..') {
                    if ($test == '..') $parts[] = $test;
                    if ($test == '..' || $test == '') $parts[] = $segment;
                } else {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }
        return implode('/', $parts);
    }


    static function pathToUrl($path, $redundantPath = null, $addHostName = false)
    {

        $path = static::normalizePath($path);
        $fileSystemRelativePath = String1::replace($path, $redundantPath ? $redundantPath : $_SERVER['DOCUMENT_ROOT'], '');
        //$fileSystemRelativePath = '/'.FileManager1::relativePath($_SERVER['DOCUMENT_ROOT'], $path);
        // return Url1::prependHttp($fileSystemRelativePath);
        return $addHostName ? self::getDomainName() . $fileSystemRelativePath : $fileSystemRelativePath;
    }

    static function urlToPath($url)
    {
        // convert
        $urlRelativePath1 = String1::replace($url, Url1::prependHttp($_SERVER['HTTP_HOST']), '');
        $urlRelativePath2 = String1::replace($url, Url1::getDomainName() . str_replace('index.php', '', $_SERVER['PHP_SELF']), '');
        $url = $_SERVER['DOCUMENT_ROOT'];

        // return
        if (self::isUrlExist($url . $urlRelativePath1)) return ($url . $urlRelativePath1);
        else    return ($url . $urlRelativePath2);
    }

    static function include_item($url)
    { //DOMXPath
        $fullPath = self::getRootDirectoryPath() . "/" . $url;      // this will assign relative path to file, so all link inside can be relative too
        include("$fullPath");
    }

    static function getRootDirectoryPath()
    {
        //echo getcwd();
        //echo realpath('./');
        //echo realpath(dirname($_SERVER['PHP_SELF']));
        return dirname($_SERVER['SCRIPT_FILENAME']); // remove 1-PHPClass
    }

    /** is not current url, Which suppose to be in $_GET
     * @param null $url
     * @return array
     */
    static function convertUrlParamToArray($url = null)
    {
        if (!$url) return $_GET;
        $split = explode('?', urldecode($url));
        $existParam = [];
        if (isset($split[1])) parse_str($split[1], $existParam);
        return $existParam;
    }

    static function prependHttp($url)
    {
        $http = self::isHttps() ? "https://" : "http://";
        return (String1::contains('http', strtolower($url)) ? $url : $http . $url);
    }


    static function isHttps()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');
    }

    static function getDomainName($server = null, $use_forwarded_host = false)
    {
        $server = $server ? $server : $_SERVER;
        $ssl = self::isHttps();
        $sp = strtolower($server['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $server['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = ($use_forwarded_host && isset($server['HTTP_X_FORWARDED_HOST'])) ? $server['HTTP_X_FORWARDED_HOST'] : (isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $server['SERVER_NAME'] . $port;
        return "$protocol://$host";
    }

    static function extractHostName($url)
    {
        return parse_url($url, PHP_URL_HOST);

//        //remove http://
//        $urlRaw = (String1::contains("//", $url)) ? explode("//", $url) : "";
//        $url = (is_array($urlRaw)) ? $urlRaw[1] : $url;
//
//        //remove / or ?
//        $data = (String1::contains("/", $url)) ? explode("/", $url) : explode("?", $url);
//        return (is_array($urlRaw) ? $urlRaw[0] . "//" : "") . $data[0];
    }

    static function getPageFullUrl($replaceParameter = [], $removeParameterKey = [], $server = null, $use_forwarded_host = false)
    {
        if (!empty($replaceParameter)) {
            return self::replaceParameterAndGetUrl($replaceParameter, $removeParameterKey); //Url1::buildParameter(array_merge(Url1::convertUrlParamToArray(), $replaceParameter), self::getPageFullUrl_noGetParameter());
        } else {
            $server = ($server) ? $server : $_SERVER;
            return self::getDomainName($server, $use_forwarded_host) . $server['REQUEST_URI'];
        }
    }


    static function getPageFullUrl_noGetParameter($server = null, $use_forwarded_host = false)
    {
        return explode('?', self::getPageFullUrl($server, $use_forwarded_host))[0];
    }

    static function getCurrentUrl($withParameter = false)
    {
        return $withParameter ? static::getPageFullUrl() : static::getPageFullUrl_noGetParameter();
    }

    static function replaceParameterAndGetUrl($replaceParameter = [], $removeParameterKey = [], $url = null)
    {
        $url = $url ?? self::getPageFullUrl();
        if (empty($replaceParameter) && $removeParameterKey) return $url;
        $allParam = array_merge(Url1::convertUrlParamToArray($url), $replaceParameter);
        foreach ($removeParameterKey as $key => $value) unset($allParam[$key]);
        return Url1::buildParameter($allParam, $url);
    }

    /**
     * Get url param as either array or string
     * @param bool $asArray
     * @return string|array
     */
    static function getParameter($asArray = false)
    {
        if ($asArray) return $_GET;
        $par = explode('?', urldecode(Url1::getPageFullUrl()));
        return isset($par[1]) ? $par[1] : '';
    }

    static function getPageName($url = null)
    {
        $url = ($url) ? $url : self::getPageFullUrl();
        $norLink = explode('/', explode('?', $url)[0]);
        return end($norLink);
    }

    static function getSiteName($url = null)
    {
        $url = ($url) ? $url : self::getDomainName();

        $url = self::extractHostName($url);
        $url = str_replace('//www.', '//', strtolower($url));

        //remove http://
        $urlRaw = (String1::contains("//", $url)) ? explode("//", $url) : "";
        $url = (is_array($urlRaw)) ? $urlRaw[1] : $url;

        $dataRaw = (String1::contains(".", $url)) ? explode(".", $url) : "";
        $data = (is_array($dataRaw)) ? $dataRaw[0] : $url;

        return $data;
    }


    /**
     * Goto Back Url or Main Page
     * @return string
     */
    static function backUrl()
    {
        $scheme = Url1::isHttps() ? "https://" : "http://";
        $url = String1::isset_or($_SERVER['HTTP_REFERER'], ("$scheme$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"));
        return (empty($url) || ($url === Url1::getCurrentUrl())) ? Url1::getDomainName() : $url;  // return htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
    }

    static function redirect($redirectUrl = '/', $status = [], $param = [])
    {
        if (Url1::getPageFullUrl() == $redirectUrl) return false;

        if ($redirectUrl === '' || $redirectUrl === null) $redirectUrl = self::backUrl();
        if (!empty($param)) Page1::saveSharedVariable($param);
        if (!empty($status)) Session1::setStatusFrom($status);

        //ob_start(); // add to top of the page
        // header('refresh:5;url=redirectpage.php ')
        if (!headers_sent()) {
            header('Location: ' . $redirectUrl);
            exit;
        }

        if (true) {
            echo '<script type="text/javascript">';
            echo '  console.log("Redirecting to : ' . $redirectUrl . '");';
            echo '  window.location.href="' . $redirectUrl . '";';
            echo '</script>';

            echo '<noscript>';
            echo '  <meta http-equiv="refresh" content="0;url=' . $redirectUrl . '" />';
            echo '</noscript>';
            exit;
        }

        //ob_end_flush();
        return '';
    }


    /**
     * Example
     * \Url1::redirectIf(\Session1::get('last_page'), 'Welcome Back', [\Session1::exists('last_page')]);
     * @param null $redirectUrl
     * @param array $message
     * @param array $trueConditionList
     * @param array $additionalData
     * @param callable|string $elseCallback
     * @return null
     */
    static function redirectIf($redirectUrl = null, $message = [], $trueConditionList = [true], $additionalData = [], callable $elseCallback = null)
    {
        if ($redirectUrl === '' || $redirectUrl === null) $redirectUrl = self::backUrl();
        else if ($redirectUrl === '/') $redirectUrl = $_SERVER['PHP_SELF'];
        foreach (Array1::toArray($trueConditionList) as $value) {
            if ($value) {
                if (!empty($message)) {
                    Session1::setStatusFrom($message);
                }    // set status
                Url1::redirect($redirectUrl);   // return redirected page and stop exec
                return null;
            }
        }
        if ($elseCallback && is_callable($elseCallback)) return $elseCallback();
        return null;
    }

    static function redirectWithMessage($actionResult = true, $redirectUrl = null, $trueMessage = 'Action Successful', $falseMessage = 'Action Failed', $additionalData = [])
    {
        return self::redirectIf($redirectUrl, ($actionResult) ? $trueMessage : $falseMessage, true, $additionalData);
    }


    static function openBlank($url)
    {
        echo '<script type="text/javascript">';
        echo "var win = window.open($url,'_blank'); win.focus()";
        echo '</script>';
    }


    //sanitizes php self;
    static function sanitize($url)
    {
        if (String1::is_empty($url)) return $url;
        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

        $strip = array('%0d', '%0a', '%0D', '%0A');
        $url = (string)$url;

        $count = 1;
        while ($count) {
            $url = str_replace($strip, '', $url, $count);
        }

        $url = str_replace(';//', '://', $url);

        $url = htmlentities($url);

        $url = str_replace('&amp;', '&#038;', $url);
        $url = str_replace("'", '&#039;', $url);

        if ($url[0] !== '/') { // We're only interested in relative links from $_SERVER['PHP_SELF']
            return '';
        } else {
            return $url;
        }
    }

    static function getPageContent($siteUrl = 'https://xamtax.com')
    {
        $html = file_get_contents(self::prependHttp($siteUrl));
        $html = preg_replace(array('@<head[^>]*?>.*?</head>@siu', "@<style[^>]*?>.*?</style>@siu", '@<script[^>]*?.*?</script>@siu', '@<object[^>]*?.*?</object>@siu', '@<embed[^>]*?.*?</embed>@siu', '@<applet[^>]*?.*?</applet>@siu', '@<noframes[^>]*?.*?</noframes>@siu', '@<noscript[^>]*?.*?</noscript>@siu', '@<noembed[^>]*?.*?</noembed>@siu', '@</?((address)|(blockquote)|(center)|(delete))@iu', '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu', '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu', '@</?((table)|(th)|(td)|(caption))@iu', '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu', '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu', '@</?((frameset)|(frame)|(iframe))@iu',), array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",), $html);
        return strip_tags($html);
    }

    static function getPageAssets($siteUrl = 'https://xamtax.com')
    {
        $separator = '---------_-------';
        $html = file_get_contents(self::prependHttp($siteUrl));
        $html = preg_replace(/** @lang text */ "(.css)+|(.js)+|(.html)+|(.png)+|(.gif)+|(.jpg)+|(.jpeg)", $separator, $html);
        return explode($separator, strip_tags($html));
    }


    /**
     * cURL constructor.
     * @param $url
     * @param array $postData
     * @param bool $allowCookie
     * @param string $cookie
     * @param string $compression
     * @param string $proxy
     *
     * $cc = new cURL();
     * $cc->get('http://www.example.com');
     * $cc->post('http://www.example.com','foo=bar');
     *
     * @return string
     */
    static function cURL($url = 'https://google.com?q=Ehex', $postData = [], $httpHeader = ['Content-type: application/x-www-form-urlencoded;charset=UTF-8'], $allowCookie = TRUE, $cookie = 'cookies.txt', $compression = 'gzip', $proxy = '')
    {
        $cookie = (function_exists("resources_path_cache") ? resources_path_cache() . DIRECTORY_SEPARATOR . $cookie : $cookie);
        //$init_headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $init_headers[] = 'Connection: Keep-Alive';
        $init_headers = array_merge($init_headers, $httpHeader);

        $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
        if ($allowCookie) {
            if (!file_exists($cookie)) {
                $fMake = fopen($cookie, 'w') or Console1::println('The cookie file could not be opened. Make sure this directory has the correct permissions', 'FILE PATH CREATING FAILED : ' . $cookie);
                fclose($fMake);
            }
        }

        $process = curl_init($url);

        //curl_setopt($process, CURLOPT_HEADER, empty($postData)?0:1);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($process, CURLOPT_HTTPHEADER, $init_headers);
        curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($process, CURLOPT_ENCODING, $compression);

        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);

        if ($allowCookie) {
            curl_setopt($process, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($process, CURLOPT_COOKIEJAR, $cookie);
        }

        if (!empty($proxy)) curl_setopt($process, CURLOPT_PROXY, $proxy);

        if (!empty($postData)) {
            curl_setopt($process, CURLOPT_POST, 1);
            curl_setopt($process, CURLOPT_POSTFIELDS, $postData);
        }

        $return = curl_exec($process);
        curl_close($process);
        return $return;
    }


    static function cURL_fromGuzzle($url = 'https://google.com?q=Ehex', $postParam = [], $header = [])
    {
        $header = isset($header["headers"]) ? $header['headers'] : $header;
        $postParam = isset($postParam["form_params"]) ? $postParam['form_params'] : $postParam;

        $newHeader = [];
        foreach ($header as $key => $value) {
            if (Math1::isNumber($key)) $newHeader[] = $value;
            else $newHeader[] = $key . ": " . $value;
        }
        return self::cURL($url, $postParam, $newHeader);
    }


    /**
     * @param string $url e.g 'https://tcapi.phphive.info/'.$APIToken.'/search/'.$no;
     * @return string
     */
    static function cURL_lite($url, array $header = ['Accept: application/json']): ResultObject1
    {
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_HTTPGET, true);
        $json_response = curl_exec($crl);
        $status = curl_getinfo($crl, CURLINFO_HTTP_CODE);
        if ($status != 200)
            $output = ResultObject1::falseMessage("Error: call to URL $url failed with status : $status, response : $json_response, curl_error : " . curl_error($crl) . ", curl_errno : " . curl_errno($crl));
        else {
            $output = ResultObject1::make(true, "success", $json_response);
        }
        curl_close($crl);
        return $output;
    }

    /**
     * @param $url
     * send a HTTP POST request without using cURL. This may be helpful for those of you that require an easy alternative to PHP’s cURL extension.
     * @param array $postVars
     * @return bool|string
     *
     */
    static function post($url, $postVars = array())
    {
        //Transform our POST array into a URL-encoded query string.
        $postStr = http_build_query($postVars);

        //Create an $options array that can be passed into stream_context_create.
        $options = array(
            'http' =>
                array(
                    'method' => 'POST', //We are using the POST HTTP method.
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postStr //Our URL-encoded query string.
                )
        );
        //Pass our $options array into stream_context_create.
        //This will return a stream context resource.
        $streamContext = stream_context_create($options);
        //Use PHP's file_get_contents function to carry out the request.
        //We pass the $streamContext variable in as a third parameter.
        $result = @file_get_contents($url, false, $streamContext);
        //If $result is FALSE, then the request has failed.

        if ($result === false) {
            //If the request failed, throw an Exception containing
            //the error.
            $error = error_get_last();
            die('POST request failed: ' . $error['message']);
        }

        //If everything went OK, return the response.
        return $result;
    }

    /**
     * Get The Hash value of current url
     * @return null|string
     */
    public static function getLastHashFragment($url = null)
    {
        $hf = !empty($url) ? @parse_url($url)["fragment"] : Cookie1::get('url_hash');
        return !empty($hf) ? "#$hf" : "";
    }


    /**
     * Generate a jwt token
     * @param array $data . Payload ['iat'=>time(), 'nbf'=time(), 'exp'=>time()+$expirersIn
     * @param string $expirersIn
     * @param string $secret
     * @param array $issuerIdentifier
     * @return string
     */
    public static function generateJWToken($data = ['user_id' => 1], $expirersIn = "2days", $secret = null, $issuerIdentifier = ['typ' => 'JWT', 'alg' => 'HS256'])
    {
        $base64UrlHeader = String1::base64_to_base64UrlSafe(base64_encode($header = json_encode($issuerIdentifier)));
        $base64UrlPayload = String1::base64_to_base64UrlSafe(base64_encode($payload = json_encode(array_merge(['iat' => time(), 'nbf' => time(), 'exp' => strtotime($expirersIn) < time() ? time() + strtotime($expirersIn) : strtotime($expirersIn)], $data))));
        $base64UrlSignature = String1::base64_to_base64UrlSafe(base64_encode(
            hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret ?? env('APP_KEY'), true)
        ));
        return $jwt = "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }

    /**
     * validate a jwt token
     * @return null|string
     */
    public static function validateJWToken($token, $validateTime = true, $secret = null)
    {
        if (!$token) return false;
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $token);
        $dataEncoded = "$headerEncoded.$payloadEncoded";
        $signature = base64_decode(String1::base64UrlSafe_to_base64($signatureEncoded));
        $payload = json_decode(base64_decode(String1::base64UrlSafe_to_base64($payloadEncoded)), true);
        if ($validateTime && ((isset($payload['exp']) && $payload['exp'] < time()) || (isset($payload['nbf']) && $payload['nbf'] > time()))) return false;
        $rawSignature = hash_hmac('sha256', $dataEncoded, $secret ?? env('APP_KEY'), true);
        return hash_equals($rawSignature, $signature) ? $payload : false;
    }

}