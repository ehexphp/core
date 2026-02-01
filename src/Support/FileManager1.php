<?php

class FileManager1
{
    /**
     * @param $fileName
     * @param bool $convertToArray
     * @param string $path
     * @return bool|mixed|string
     */
    static function getDatasetFile($fileName, $convertToArray = false, $path = __DIR__ . '/dataset/')
    {
        $path = $path . $fileName;
        return !$convertToArray ? $path : Array1::readFromJSON($path);
    }

    /**
     * @param $uri
     * @return null|string
     */
    static function removeDuplicateSlash($uri)
    {
        return preg_replace('/\/+/', '/', '/' . $uri);
    }

    /**
     * Get All Data in Directory and pass to callback
     * @param $path
     * @param bool $supplyFullPath
     * @param callable|null $callBack
     * @return array
     */
    static function getDirectoryFiles($path, $supplyFullPath = true, callable $callBack = null)
    {
        $all = [];
        foreach (scandir($path) as $file) {
            $pathName = $supplyFullPath ? $path . DIRECTORY_SEPARATOR . $file : $file;
            if ($callBack) {
                $allRaw = $callBack($pathName);
                if ($allRaw) $all[] = $allRaw;
            } else $all[] = $pathName;
        }
        return $all;
    }


    /**
     * Get Directory Folders
     * @param string $path_orPaths
     * @param string $prepend
     * @param string $append
     * @return array|string
     */
    static function getDirectoriesFolders($path_orPaths = '.', $prepend = '', $append = '')
    {
        $pathList = [];
        foreach (Array1::makeArray($path_orPaths) as $path) {
            foreach (Array1::makeArray(@scandir($path)) as $folder) {
                $fullPath = $prepend . $path . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $append;
                if (is_dir($fullPath)) $pathList[] = $fullPath;
            }
        }
        return $pathList;
    }


    /**
     * Get all File in derectory
     * @param string $pathList
     * @param array $filterExtension
     * @param array $ignoreExtension
     * @param int $fileCount
     * @param bool $recursive
     * @return array An array, item is a file
     */
    static function getDirectoriesFiles($pathList = '.', array $filterExtension = array(), array $ignoreExtension = array(), $fileCount = -1, $recursive = false)
    {
        $filterExtension = !empty($filterExtension) ? array_map('strtolower', $filterExtension) : $filterExtension;
        $allFiles = array();
        foreach (Array1::toArray($pathList) as $path) {
            $handle = @opendir($path);
            while ($handle && $file = readdir($handle)) {
                $fullPath = rtrim($path, '/\\') . '/' . $file;
                $ext = strtolower(self::getExtension($file));
                if (!in_array($file, ['.', '..'])) {
                    if (is_file($fullPath) && !in_array($ext, $ignoreExtension)) {

                        // filter extension
                        if (!empty($filterExtension)) {
                            if (in_array($ext, $filterExtension)) {
                                $allFiles[] = $fullPath;
                            }
                        } else {
                            $allFiles[] = $fullPath;
                        }

                        // is file list enough
                        if ($fileCount && ($fileCount > 0) && (count($allFiles) >= $fileCount)) break;
                    } else if ($recursive && is_dir($fullPath)) {
                        $allFiles = array_merge($allFiles, self::getDirectoriesFiles($fullPath, $filterExtension, $ignoreExtension, $fileCount, $recursive));
                    }
                }
            }
            $handle && @closedir($handle);
        }
        return $allFiles;
    }


    /**
     * Get an array containing the path of all files in this repository
     * @param string $path
     * @param string $ext
     * @return array An array, item is a file
     */
    public static function getDirectoryFilesByExtension($path = '', $ext = 'json')
    {
        $files = [];
        $_files = glob($path . '*.' . $ext);
        foreach ($_files as $file) $files[] = str_replace('.' . $ext, '', basename($file));
        return $files;
    }


    /**
     * @param string $filename
     * @return string
     */
    static function getExtension($filename = '')
    {
        return (strpos($filename, '.')) ? Array1::getLastItem(explode(".", $filename)) : $filename;
    }


    /**
     * Mime Type
     * @param string $extension
     * @return mixed|string
     */
    static function getMimeType($extension = '')
    {
        $mimes = array('hqx' => 'application/mac-binhex40', 'cpt' => 'application/mac-compactpro', 'doc' => 'application/msword', 'bin' => 'application/macbinary', 'dms' => 'application/octet-stream', 'lha' => 'application/octet-stream', 'lzh' => 'application/octet-stream', 'exe' => 'application/octet-stream', 'class' => 'application/octet-stream', 'psd' => 'application/octet-stream', 'so' => 'application/octet-stream', 'sea' => 'application/octet-stream', 'dll' => 'application/octet-stream', 'oda' => 'application/oda', 'pdf' => 'application/pdf', 'ai' => 'application/postscript', 'eps' => 'application/postscript', 'ps' => 'application/postscript', 'smi' => 'application/smil', 'smil' => 'application/smil', 'mif' => 'application/vnd.mif', 'xls' => 'application/vnd.ms-excel', 'ppt' => 'application/vnd.ms-powerpoint', 'pptx' => 'application/vnd.ms-powerpoint', 'wbxml' => 'application/vnd.wap.wbxml', 'wmlc' => 'application/vnd.wap.wmlc', 'dcr' => 'application/x-director', 'dir' => 'application/x-director', 'dxr' => 'application/x-director', 'dvi' => 'application/x-dvi', 'gtar' => 'application/x-gtar', 'php' => 'application/x-httpd-php', 'php4' => 'application/x-httpd-php', 'php3' => 'application/x-httpd-php', 'phtml' => 'application/x-httpd-php', 'phps' => 'application/x-httpd-php-source', 'js' => 'application/x-javascript', 'swf' => 'application/x-shockwave-flash', 'sit' => 'application/x-stuffit', 'tar' => 'application/x-tar', 'tgz' => 'application/x-tar', 'xhtml' => 'application/xhtml+xml', 'xht' => 'application/xhtml+xml', 'zip' => 'application/zip', 'mid' => 'audio/midi', 'midi' => 'audio/midi', 'mpga' => 'audio/mpeg', 'mp2' => 'audio/mpeg', 'mp3' => 'audio/mpeg', 'aif' => 'audio/x-aiff', 'aiff' => 'audio/x-aiff', 'aifc' => 'audio/x-aiff', 'ram' => 'audio/x-pn-realaudio', 'rm' => 'audio/x-pn-realaudio', 'rpm' => 'audio/x-pn-realaudio-plugin', 'ra' => 'audio/x-realaudio', 'rv' => 'video/vnd.rn-realvideo', 'wav' => 'audio/x-wav', 'bmp' => 'image/bmp', 'gif' => 'image/gif', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'jpe' => 'image/jpeg', 'png' => 'image/png', 'tiff' => 'image/tiff', 'tif' => 'image/tiff', 'css' => 'text/css', 'html' => 'text/html', 'htm' => 'text/html', 'shtml' => 'text/html', 'txt' => 'text/plain', 'text' => 'text/plain', 'log' => 'text/plain', 'rtx' => 'text/richtext', 'rtf' => 'text/rtf', 'xml' => 'text/xml', 'xsl' => 'text/xml', 'mpeg' => 'video/mpeg', 'mpg' => 'video/mpeg', 'mpe' => 'video/mpeg', 'qt' => 'video/quicktime', 'mov' => 'video/quicktime', 'avi' => 'video/x-msvideo', 'movie' => 'video/x-sgi-movie', 'doc' => 'application/msword', 'docx' => 'application/msword', 'word' => 'application/msword', 'xl' => 'application/excel', 'xls' => 'application/excel', 'xlsx' => 'application/excel', 'eml' => 'message/rfc822');
        return (!isset($mimes[strtolower($extension)])) ? 'application/octet-stream' : $mimes[strtolower($extension)];
    }


    /**
     * Force Download FIle
     * @param $url
     * @throws Exception
     */
    static function downloadFile($url)
    {
        $realFileInfo = pathinfo($url);
        if (strlen(trim($realFileInfo['basename'])) < 2) throw new Exception($realFileInfo['basename'] . ' - is Invalid Download FilePath');

        ob_start();

        if (ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');
        header('Accept-Ranges: bytes');
        header("Cache-control: private");
        header('Pragma: private');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        header("Cache-Control: public, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: " . FileManager1::getMimeType($realFileInfo['extension']));
        //header("Content-Length: " . (string)(@filesize($model['download_file_url'])));
        header('Content-Disposition: attachment; filename="' . $realFileInfo['basename'] . '"');
        header("Content-Transfer-Encoding: binary\n");
        ob_end_clean();
        readfile($url);
        die('downloading in progress...');
        return true;
    }

    /**
     * File Icon
     * @param $filePath
     * @param array|null $returnKeyMap
     * @return string
     */
    static function getFileAndType($filePath, array $returnKeyMap = null)
    {
        $returnKeyMap = !$returnKeyMap ? [
            // media
            'media_picture' => 'fa fa-picture',
            'media_music' => 'fa fa-music',
            'media_video' => 'fa fa-video',
            // code
            'code_css' => 'fa fa-code-o',
            'code_php' => 'fa fa-php',
            'code_aspx' => 'fa fa-file-aspx',
            'code_xml' => 'fa fa-html',
            'code_database' => 'fa fa-database',
            // file
            'file_text' => 'fa fa-file-text-o',
            'file_web' => 'fa fa-code',
            'file_archive' => 'fa fa-file-archive-o',
            'file_excel' => 'fa fa-file-excel-o',
            'file_word' => 'fa fa-file-word',
            'file_powerpoint' => 'fa fa-file-powerpoint',
            'file_font' => 'fa fa-font',
            'file_pdf' => 'fa fa-file-pdf',
            'file_graphics' => 'fa fa-file-image-o',
            'file_application' => 'fa fa-file-o',
            'file_default' => 'fa fa-file-o',
            'picture' => 'fa fa-file-excel',
        ] : $returnKeyMap;

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
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
                $img = $returnKeyMap['media_picture'];
                break;
            case"passwd":
            case"ftpquota":
            case"sql":
            case"js":
            case"json":
            case"sh":
            case"config":
            case"twig":
            case"tpl":
            case"md":
            case"gitignore":
            case"c":
            case"cpp":
            case"cs":
            case"py":
            case"map":
            case"lock":
            case"dtd":
                $img = $returnKeyMap['file_web'];
                break;
            case"txt":
            case"ini":
            case"conf":
            case"log":
            case"htaccess":
                $img = $returnKeyMap['file_text'];
                break;
            case"css":
            case"less":
            case"sass":
            case"scss":
                $img = $returnKeyMap['code_css'];
                break;
            case"zip":
            case"rar":
            case"gz":
            case"tar":
            case"7z":
                $img = $returnKeyMap['file_archive'];
                break;
            case"php":
            case"php4":
            case"php5":
            case"phps":
            case"phtml":
                $img = $returnKeyMap['code_php'];
                break;
            case"htm":
            case"html":
            case"shtml":
            case"xhtml":
            case"xml":
            case"xsl":
            case"xslx":
                $img = $returnKeyMap['code_xml'];
                break;
            case"wav":
            case"mp3":
            case"mp2":
            case"m4a":
            case"aac":
            case"ogg":
            case"oga":
            case"wma":
            case"mka":
            case"flac":
            case"ac3":
            case"tds":
            case"m3u":
            case"m3u8":
            case"pls":
            case"cue":
                $img = $returnKeyMap['media_music'];
                break;
            case"avi":
            case"mpg":
            case"mpeg":
            case"mp4":
            case"m4v":
            case"flv":
            case"f4v":
            case"ogm":
            case"ogv":
            case"mov":
            case"mkv":
            case"3gp":
            case"asf":
            case"wmv":
                $img = $returnKeyMap['media_video'];
                break;
            case"xls":
            case"xlsx":
                $img = $returnKeyMap['file_excel'];
                break;
            case"asp":
            case"aspx":
                $img = $returnKeyMap['code_aspx'];
                break;
            case"sql":
            case"mda":
            case"myd":
            case"dat":
            case"sql.gz":
                $img = $returnKeyMap['code_database'];
                break;
            case"doc":
            case"docx":
                $img = $returnKeyMap['file_word'];
                break;
            case"ppt":
            case"pptx":
                $img = $returnKeyMap['file_powerpoint'];
                break;
            case"ttf":
            case"ttc":
            case"otf":
            case"woff":
            case"woff2":
            case"eot":
            case"fon":
                $img = $returnKeyMap['file_font'];
                break;
            case"pdf":
                $img = $returnKeyMap['file_pdf'];
                break;
            case"psd":
            case"ai":
            case"eps":
            case"fla":
            case"swf":
                $img = $returnKeyMap['file_graphics'];
                break;
            case"exe":
            case"msi":
                $img = $returnKeyMap['file_application'];
                break;
            default:
                $img = $returnKeyMap['file_default'];
        }
        return $img;
    }


    /**
     * Get File Name
     * @param string $filePath
     * @return mixed|string
     */
    static function getName($filePath = '')
    {
        $filePath = rtrim($filePath, '/\\');
        $filePath = Array1::last(explode('/', $filePath));
        $filePath = Array1::last(explode('\\', $filePath));
        return $filePath;
    }

    /**
     * File Extension
     * @param bool $commonPictureImage
     * @return array
     */
    static function getImageExtension($commonPictureImage = false)
    {
        return Picture1::getExtensionList($commonPictureImage);
    }

    /**
     * File Extension
     * @param bool $common
     * @return array
     */
    static function getDocumentExtension($common = false)
    {
        $commonData = ['txt', 'xls', 'xlsx', 'doc', 'docx', 'pdf'];
        return $common ? $commonData : array_merge(['pptx'], $commonData);
    }


    /**
     * is File Image
     * @param $filename
     * @param bool $commonPictureImage
     * @return bool
     */
    static function isImageFile($filename, $commonPictureImage = false)
    {
        return in_array(strtolower(self::getExtension($filename)), self::getImageExtension($commonPictureImage)) ? true : false;
    }


    /**
     * @param $path
     * @return null|string
     */
    static function getFileName($path)
    {
        $path = rtrim($path, '/\\');
        return preg_replace('/^.+[\\\\\\/]/', '', $path);
    }

    /**
     * @param $path
     * @return bool|string
     */
    static function getOnlyFileName($path)
    {
        $path = rtrim($path, '/\\');
        return substr(self::getFileName($path), 0, (strlen(self::getFileName($path)) - strlen(self::getExtension($path))) - 1);
    }


    /**
     * @param $path
     * @return bool|string
     */
    static function getOnlyFilePath($path)
    {
        return substr($path, 0, (strlen($path) - strlen(self::getOnlyFileName($path)) - 1));
    }

    /**
     * @param $fileName
     * @return bool
     */
    static function delete($fileName)
    {
        @unlink($fileName);
        @rmdir($fileName);
        return @unlink($fileName);
    }

    /**
     * Verify if url exists or use default
     * @param string $url
     * @param string $default
     * @return string
     */
    static function urlPathExistsOr($url = '', $default = '')
    {
        // path normalizer
        if (String1::startsWith(strtolower($url), 'http')) $path = Url1::urlToPath($url);
        else {
            $path = $url;
            $url = Url1::pathToUrl($url);
        }
        return file_exists($path) ? $url : $default;
    }

    /**
     *
     * @param $path
     * @return bool
     */
    static function deleteDirectory($path)
    {
        return exec("rm -rf " . escapeshellarg($path));
    }

    /**
     * Delete all files in directory recursively
     * @param $directory
     * @param bool $deleteDirectory
     * @return bool
     */
    static function deleteAll($directory, $deleteDirectory = false)
    {
        if (substr($directory, -1) == "/") $directory = substr($directory, 0, -1);
        if (!file_exists($directory) || !is_dir($directory)) return false;
        else if (!is_readable($directory)) return false;
        else {
            $directoryHandle = opendir($directory);
            while ($contents = readdir($directoryHandle)) {
                if ($contents != '.' && $contents != '..') {
                    $path = $directory . "/" . $contents;
                    if (is_dir($path)) {
                        static::deleteAll($path);
                    } else {
                        @unlink($path);
                    }
                }
            }
            closedir($directoryHandle);
            if ($deleteDirectory) if (!rmdir($directory)) {
                return false;
            }
            return true;
        }
    }

    static function normalizeFilePathSeparator($file, $separator = '/')
    {
        return preg_replace("#([\\\\/]+)#", $separator, $file);
    }


    /**
     * Return relative path between two sources
     * @param $fromHalfPath
     * @param $toFullPath
     * @param string $separator
     * @return string
     *
     *  $relative = getRelativePath('/var/www/example.com','/var/www/example.com/media/test.jpg');
     *  Function will return /media/test.jpg.
     */
    static function relativePath($fromHalfPath, $toFullPath, $separator = DIRECTORY_SEPARATOR)
    {
        $fromHalfPath = str_replace(array('/', '\\'), $separator, $fromHalfPath);
        $toFullPath = str_replace(array('/', '\\'), $separator, $toFullPath);

        $arFrom = explode($separator, rtrim($fromHalfPath, $separator));
        $arTo = explode($separator, rtrim($toFullPath, $separator));
        while (count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
            array_shift($arFrom);
            array_shift($arTo);
        }
        return str_pad("", count($arFrom) * 3, '..' . $separator) . implode($separator, $arTo);
    }


    /**
     * @param $source_url
     * @param $destination
     * @param bool $shouldCompressIfCompressible
     * @param array $compress_config
     * @return bool
     */
    static function upload($source_url, $destination, $shouldCompressIfCompressible = true, $compress_config = ['width' => 320, 'height' => 320, 'quality' => 80, 'watermark_source' => false])
    {
        if (!Picture1::isImage($source_url)) return move_uploaded_file($source_url, $destination);
        $compress_config = array_merge(['width' => 400, 'height' => 400, 'quality' => 80, 'watermark_source' => false], $compress_config);
        return $shouldCompressIfCompressible ? Picture1::resize($source_url, $destination, $compress_config['width'], $compress_config['height'], $compress_config['quality'], $compress_config['watermark_source']) : Picture1::upload($source_url, $destination, $shouldCompressIfCompressible);
    }


    /**
     * Generate directory structure
     * @param string $basePath , path where all directories will be created in
     * @param array $relativePathList , recursive array in structure of directories
     * @return void
     */
    static function generateDirectories($basePath = '\\', array $relativePathList = ['web' => ['js', 'css']])
    {
        //If array, unfold it
        foreach ($relativePathList as $key => $path) {
            $folderName = is_numeric($key) ? '' : '\\' . $key;
            self::createDirectory($basePath . $folderName . '\\' . $path);
        }
    }


    static function createDirectory($path = '\\')
    {
        return is_dir($path) || mkdir($path, 0777, true);
    }

    /**
     * Writes data to the filesystem.
     * @param string $path The absolute file path to write to
     * @param string $contents The contents of the file to write
     * @return boolean          Returns true if write was successful, false if not.
     */
    public static function write($path, $contents)
    {
        $fp = fopen($path, 'w+');
        if (!flock($fp, LOCK_EX)) return false;
        $result = fwrite($fp, $contents);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $result !== false;
    }

    /**
     * read file
     * @param $path
     * @return bool|string
     */
    public static function read($path)
    {
        if (!file_exists($path)) return false;
        $file = fopen($path, 'r');
        $contents = fread($file, filesize($path));
        fclose($file);
        return $contents;
    }


    static function loadComposerPackage($dir)
    {
        $composer = json_decode(file_get_contents($dir . "/composer.json"), 1);
        $loadByPSR = function ($namespaces, $psr4) use ($dir) {
            // Foreach namespace specified in the composer, load the given classes
            foreach ($namespaces as $namespace => $classpaths) {
                if (!is_array($classpaths)) {
                    $classpaths = array($classpaths);
                }
                spl_autoload_register(function ($classname) use ($namespace, $classpaths, $dir, $psr4) {
                    // Check if the namespace matches the class we are looking for
                    if (preg_match("#^" . preg_quote($namespace) . "#", $classname)) {
                        // Remove the namespace from the file path since it's psr4
                        if ($psr4) {
                            $classname = str_replace($namespace, "", $classname);
                        }
                        $filename = preg_replace("#\\\\#", "/", $classname) . ".php";
                        foreach ($classpaths as $classpath) {
                            $fullpath = $dir . "/" . $classpath . "/$filename";
                            if (file_exists($fullpath)) {
                                include_once $fullpath;
                            }
                        }
                    }
                });
            }
        };

        (isset($composer['autoload']['psr-4']) && $loadByPSR($composer['autoload']['psr-4'], true));
        (isset($composer['autoload']['psr-0']) && $loadByPSR($composer['autoload']['psr-0'], false));
    }


    /**
     * Validates the name of the file to ensure it can be stored in the
     * filesystem.
     *
     * @param string $name The name to validate against
     * @param boolean $convertToSafeFilenameIfFailed Allows filename to be converted if fails validation
     * @return string Returns true if valid. Throws an exception if not.
     * @throws Exception
     */
    public static function validateFileName($name, $convertToSafeFilenameIfFailed = false)
    {
        if (!preg_match('/^[0-9A-Za-z\_\-]{1,63}$/', $name)) {
            if ($convertToSafeFilenameIfFailed) {
                // rename the file
                $name = preg_replace('/[^0-9A-Za-z\_\-]/', '', $name);
                // limit the file name size
                $name = substr($name, 0, 63);
            } else  throw new \Exception(sprintf('`%s` is not a valid file name.', $name));
        }
        return $name;
    }


    private static function __autoClassRecursiveLoaderExecutor($class, $dir = null, $initPath = null)
    {
        if (is_null($dir)) $dir = $initPath;
        try{
            foreach (scandir($dir) as $file) {
                // directory?
                if (is_dir($dir . $file) && substr($file, 0, 1) !== '.') self::__autoClassRecursiveLoaderExecutor($class, $dir . $file . '/');
                // php file?
                if (substr($file, 0, 2) !== '._' && preg_match("/.php$/i", $file)) {
                    // filename matches class?
                    if (str_replace('.php', '', $file) == $class || str_replace('.class.php', '', $file) == $class) include $dir . $file;
                }
            }
        }catch (Exception $err){
            // Console1::log("Error reading directory [$dir] (".$err->getMessage().")");
        }
    }

    /**
     * @param array|string $initPathList
     * @return void
     */
    static function autoClassRecursiveLoad($initPathList = ['app'])
    {
        //$dff =[];
        foreach (Array1::toArray($initPathList) as $initPath) {
            $autoLoad = function ($class, $dir = null) use ($initPath) {
                FileManager1::__autoClassRecursiveLoaderExecutor($class, $dir, $initPath);
            };
            spl_autoload_register($autoLoad);
            //$dff[] = $initPath;
        }
        //return $dff;
    }
}
