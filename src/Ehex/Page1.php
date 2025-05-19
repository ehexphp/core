<?php

/**
 * Class Page1
 * This is created for jquery $(document).ready
 * and can be used as
 * JQuery version > 2
 * $(function(){
 * alert('page loaded');
 * });
 *
 *
 * JQuery version < 2+
 * (function($){
 * alert('alert');
 * })($);
 *
 */
class Page1
{


    public static $FLAG_SHOW_LOAD_TIME = false;
    public static $FLAG_KEEP_OLD_REQUEST = false;
    private static $is_page_wrapper_set = false;


    /**
     * Add Global Variable to Page
     * @param $variable
     * @param string $value
     */
    public static $_VARIABLE = [];

    public static function setVariable($variable, $value = '')
    {
        return static::$_VARIABLE[$variable] = $value;
    }

    public static function getVariable($variable, $defaultValue = null)
    {
        return isset(static::$_VARIABLE[$variable]) ? static::$_VARIABLE[$variable] : $defaultValue;
    }

    public static function deleteVariable($variable)
    {
        unset(static::$_VARIABLE[$variable]);
    }

    static function saveSharedVariable($data = [])
    {
        if (empty($data)) return;
        $_SESSION['__SHARED_VARIABLE'] = $data;
    }

    /**
     * @param string $data
     * @param null $uniqueSaveKey
     */
    static function printOnce($data = '', $uniqueSaveKey = null)
    {
        if (!self::$is_page_wrapper_set) die('Page1::start() and Page1::stop() not included at the beginning of your script. Or Enable Config1::AUTO_PAGE_WRAPPER');
        $hashCode = $uniqueSaveKey ? $uniqueSaveKey : md5($data);
        if (!isset($_SESSION[Session1::$NAME][Url1::getPageFullUrl_noGetParameter()]['print_once'][$hashCode])) echo $data;
        $_SESSION[Session1::$NAME][Url1::getPageFullUrl_noGetParameter()]['print_once'][$hashCode] = true;
    }

    /**
     * Open Page Wrapper for JQuery
     * @param array $styleOrScriptList
     * @param array $sharedVariable
     */
    static function start(array $styleOrScriptList = [], $sharedVariable = [])
    {
        Global1::set('pageStartTime', microtime(true), false);
        $jqueryBuffer = '<!DOCTYPE html>';
        $jqueryBuffer .= '<script> 
                            window.q = []; 
                            window.$ = function(f){ 
                                q.push(f) 
                            };
                            window.pageStartTime = performance.now();
                            console.time("[JS]"); 
                         </script>';
        $jqueryBuffer .= implode(' ', $styleOrScriptList);
        self::$is_page_wrapper_set = true;

        // set shared data
        $shareData = isset($_SESSION['__SHARED_VARIABLE']) ? $_SESSION['__SHARED_VARIABLE'] : [];
        $shareData = @array_merge($shareData, !empty($sharedVariable) ? $sharedVariable : []);
        foreach (Array1::makeArray($shareData) as $key => $value) {
            global ${$key};
            $GLOBALS["$key"] = $value;
        }

        // easy js
        $jqueryBuffer .= PHP_EOL . '<script src="' . Url1::pathToUrl(PATH_LIB_ASSETS . "js/ehex.min.js?v=2.1") . '"></script>' . PHP_EOL . '<!-- Ehex -->' . PHP_EOL . PHP_EOL;
        echo $jqueryBuffer;
        echo "<script>Cookie1.set('url_hash', window.location.hash.replace('#', ''), 1)</script>";
    }


    /**
     * End Page Wrapper for jQUERY
     * @param array $scriptOrStyleList
     * @param bool $enableToast
     */
    static function end(array $scriptOrStyleList = [], $enableToast = true)
    {
        $executionTime = (microtime(true) - Global1::get('pageStartTime')) . ' ms';

        echo "<script>!window.jQuery && document.write('<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"><\/script>');</script>";
        echo '<script type="text/javascript">
                    $(function(){
                        console.log("[PHP] ' . $executionTime . '" );
                        console.timeEnd("[JS]");
                    });

                    if(window.$.each){
                        $.each(q, function(index, f) {
                            $(f)
                        });
                    } else{
                        console.error("[Ehex] Jquery Failed. ehex.min.js file requires jquery")
                    }
               </script>';
        echo implode(' ', $scriptOrStyleList);

        if (static::$FLAG_SHOW_LOAD_TIME) {
            $loadTime = "\n\n[PHP] $executionTime \n[JS] <script> document.writeln( (performance.now() - window.pageStartTime) + ' ms' ) </script>";
            Console1::println('<h3 align="center"> Pageload Time' . $loadTime . ' </h3><hr/><h6 align="center"><strong>Current Url : </strong>' . Url1::getPageFullUrl() . '</h6>');
        }
        unset($_SESSION[Session1::$NAME][Url1::getPageFullUrl_noGetParameter()]['print_once']);
        unset($_SESSION['__SHARED_VARIABLE']);
        // popup status
        if ($enableToast) Session1::popupStatus()->toToast();
    }

    /**
     * Get Ehex EasyCore Assets Path
     * @return string
     *
     */
    static function getEhexCoreAssetsPath()
    {
        return PATH_LIB_ASSETS;
    }


    /**
     * Add Pagination to page
     * Used in MySql1::makeLimitQueryAndPagination, Model::paginateRender()
     *
     * @param string $total_pages
     * @param string $templateClass
     * @param string $requestPageKeyName
     * @return string
     */
    static function renderPagination($total_pages = 'ceil($total / $limit)', $templateClass = BootstrapPaginationTemplate::class, $requestPageKeyName = 'page')
    {
        /**
         * Use Template for Current, Next and Previous
         * i.e Convert This to Template
         * $pagLink = "<div class='pagination'>";
         * for ($i=1; $i<=$total_pages; $i++) {
         * $pagLink .= "<a href='index.php?page=".$i."'>".$i."</a>";
         * };
         * echo $pagLink . "</div>";
         */
        if ($total_pages <= 1) return "";
        $current_page = String1::isset_or($_REQUEST[$requestPageKeyName], 1);
        $pageLink = $templateClass::getContainerOpen();
        if (($current_page - 1) > 0) $pageLink .= $templateClass::getPreviousItem($templateClass::$previousClass, Url1::getPageFullUrl([$requestPageKeyName => ($current_page - 1)]));
        $pageMore = Math1::getSurroundingValues($total_pages, $current_page);
        foreach ($pageMore as $i) {
            if (+$i === +$current_page) {
                $class = $templateClass::$activeClass . ' ' . $templateClass::$disableClass;
                $link = 'javascript:void(0)';
            } else {
                $class = '';
                $link = Url1::getPageFullUrl([$requestPageKeyName => ($i)]);
            }
            $pageLink .= $templateClass::getActiveItem($class, $link, $i);
        }
        if (($current_page + 1) <= $total_pages) $pageLink .= $templateClass::getNextItem($templateClass::$nextClass, Url1::getPageFullUrl([$requestPageKeyName => ($current_page + 1)]));
        $pageLink .= $templateClass::getContainerClose();
        return $pageLink;
    }



//    /**
//     * @param array $dataList
//     * @deprecated @use Page1::start() instead
//     */
//    static function pasteAfterHeader(array $dataList = []){ self::start($dataList); }
//
//    /**
//     * @param array $dataList
//     * @deprecated @use Page1::end() instead
//     */
//    static function pasteAfterFooter(array $dataList = []){ self::end($dataList); }

    static function isMobile()
    {
        $useragent=$_SERVER['HTTP_USER_AGENT'];
        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
      $device = FileManager1::getDatasetFile("device_regex.json", true);
//      return (preg_match($device['d1'], $_SERVER['HTTP_USER_AGENT']) || preg_match($device['d2'], substr($_SERVER['HTTP_USER_AGENT'], 0, 4)));
    }
}