<?php

/**
 * Managa HEadwe
 * Class Header1
 */
class Header1
{
    public static function downloadFile($filePath)
    {
        // 301 moved permanently (redirect):
        header('Content-Disposition: attachment; filename=' . urlencode($filePath));
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Description: File Transfer');
        header('Content-Length: ' . filesize($filePath));
        echo file_get_contents($filePath);
    }

    public static function pdf($url)
    {
        header('Content-Type: application/pdf');
        echo file_get_contents($url);
    }


    public static function redirectPermanent($url)
    {
        //302 (redirect):
        header("Location: $url");
        die('waiting for redirection... do it manually if it persist');
    }


    public static function error404()
    {
        header('HTTP/1.1 404 Not Found');
    }


    public static function serviceNotAvailable()
    {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 60');
    }

    public static function css()
    {
        header('Content-Type: text/css');
    }

    public static function javascript()
    {
        header('Content-Type: application/javascript');
    }

    public static function jpeg()
    {
        header('Content-Type: image/jpeg');
    }

    public static function png()
    {
        header('Content-Type: image/png');
    }

    public static function bitmap()
    {
        header('Content-Type: image/bmp');
    }

    public static function noCache()
    {
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Cache-Control: no-store, no-view_cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Pragma: no-view_cache');
    }

    public static function authenticate($userName, $password, callable $callback = null)
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="The Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authenticate Canceled...';
            die();
        } else {
            //always escape your data//
            if ($_SERVER['PHP_AUTH_USER'] == $userName && $_SERVER['PHP_AUTH_PW'] == $password) {
                if ($callback) $callback();
            }
        }

    }
}
