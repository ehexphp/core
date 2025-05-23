<?php
/**
 * Created by PhpStorm.
 * Author: Samtax01
 * Date: 08/07/2018
 * Time: 7:47 AM
 * @link https://github.com/jenssegers/blade
 */






    /************************************************
     *  Blade
     * @see https://github.com/jenssegers/blade
     *
     ************************************************/
    // your views file path, it's an array
    $view_path = [
        PATH_RESOURCE.'views',    // app view
    ];

    $view_cachePath = BASE_PATH.'resources'.DS.'cache';     // compiled version
    FileManager1::createDirectory($view_cachePath);

//    $view_file = new Filesystem;
//    $view_compiler = new BladeCompiler($view_file, $view_cachePath);
//
//    // you can add a custom directive if you want
//    $view_compiler->directive('datetime', function($timestamp) {
/*        return preg_replace('/(\(\d+\))/', '<?php echo date("Y-m-d H:i:s", $1); ?>', $timestamp);*/
//    });
//
//    $view_resolver = new EngineResolver;
//    $view_resolver->register('blade', function () use ($view_compiler) {
//        return new CompilerEngine($view_compiler);
//    });
//
//    // get an instance of factory
//    $view = new Factory($view_resolver, new FileViewFinder($view_file, $view_path));
//    // if your views file extension is not php or blade.php, use this to add it
//    //$views->addExtension('tpl', 'blade');

    //echo $blade->make('homepage', ['name' => 'John Doe'])->render();


use Jenssegers\Blade\Blade;

Global1::set('$bladeView', new Blade($view_path, $view_cachePath));




    /**
     * @param $view_name
     * @param array $param
     * @param bool $noPageAutoWrap
     * @return string
     * render the template file, stop the page and echo the view
     */
    function view($view_name, $param = [], $noPageAutoWrap = false){
        $bladeView = Global1::get('$bladeView');

        $usePageWrap = !$noPageAutoWrap && Config1::AUTO_PAGE_WRAPPER;
        if($usePageWrap) { Page1::start(); }  // Page Wrapper Start

        $sharedData = @array_merge(Array1::makeArray(Config1::onPageStart()), String1::isset_or($_SESSION['__SHARED_VARIABLE'], []), Array1::makeArray($param));
        $newView = $bladeView->make($view_name, $sharedData)->render();

        // delete old request
        if(!Page1::$FLAG_KEEP_OLD_REQUEST) { unset($_SESSION['__old']); Session1::delete('__old'); }   // delete if reload

        // Page Wrapper End
        if($usePageWrap) { Page1::end(); }
        Config1::onPageEnd();
        return $newView;
    }




    /**
     * @param $view_name
     * @param array $param
     * @return string
     *
     * render the template file and just return it
     * Could be use for sending e-mails, or rendering multiple view
     */
    function view_make($view_name, $param = []){
        $bladeView = Global1::get('$bladeView');
        return $bladeView->make($view_name, $param)->render();
    }

    /**
     * Compile a view string to html
     * @param string $blade_string
     * @param array $param
     * @return string
     */
    function view_maker($blade_string = "sum is {{ 2 + 3 }}", $param = []){
        $tempPath = resources_path_view().'/__generated_view.blade.php';
        file_put_contents($tempPath, $blade_string);
        $view_data = view_make(path_to_viewpath($tempPath), $param);
        FileManager1::delete($tempPath);
        return $view_data;
    }

    /**
     * Validate if View Path exists
     * @param $view_name
     * @return bool
     */
    function view_exists($view_name){
        $bladeView = Global1::get('$bladeView');
        return $bladeView->exists($view_name);
    }


    /**
     * @param $full_filename
     * @return mixed
     *
       Convert :
       "/Applications/MAMP/htdocs/Project-Ehex/easy-task@framework/resources/views/layouts/w3/pages/mail.blade.php"
       To :
       "layouts.w3.pages.mail"
     *
     */
    function path_to_viewpath($full_filename){
        $full_filename = FileManager1::normalizeFilePathSeparator($full_filename, DS);
        if(String1::contains('/resources/views/', $full_filename)){
            $delimiter = '/resources/views/';
            $full_filename = explode($delimiter, $full_filename)[1];
        }
        $full_filename = \String1::replaceEnd($full_filename, '.blade.php', '');
        return \String1::replace($full_filename, DIRECTORY_SEPARATOR, '.');
    }

    /**
     * Get View full path
     * @param $full_view_path
     * @param bool $validate (turn off if is directory)
     * @return string
     */
    function viewpath_to_path($full_view_path, $validate = true){
        if(!$validate){
            $fullPath = get_valid_view_path($full_view_path);
            return $fullPath? $fullPath: Console1::println("View Path [$full_view_path] Not Found in either AppView or SharedView", true);
        }
        $bladeView = Global1::get('$bladeView');
        return $bladeView->getViewFullPath($full_view_path);
    }
    /**
     * Get View full path
     * @param $full_viewfile_path
     * @return string
     */


    /**
     * @param string $full_filename
     * @param bool $view_name_only
     * @param bool $recursive
     * @return array|null Fetch all view from  :
     */
    function get_all_view_in_directory($full_filename = '/Applications/MAMP/htdocs/ex/', $view_name_only = false, $recursive = false){
        return array_map(
            function($path) use ($view_name_only) {
                if($view_name_only) {
                    $last = explode('/', $path);
                    $path = end($last);
                }
                return path_to_viewpath($path);
            }, FileManager1::getDirectoriesFiles($full_filename, [], [], -1, $recursive));
    }






    // directory list
    function resources_path($isShared = false){
        return $isShared? PATH_SHARED_RESOURCE: PATH_RESOURCE;
    }

    /**
     * Automatic lookup for view in either app view folder or shared view folder. return null if folder not exist
     * @param string $view_path (passed in view name. e.g layouts.bootstrap)
     * @param bool $searchCurrentAppViewFirstThenSharedView ( choose if you preferred searching app view folder first or shared view folder)
     * @return null|string
     */
    function get_valid_view_path($view_path = '', $searchCurrentAppViewFirstThenSharedView = true){
        $full_view_path = String1::replace($view_path, '.', DIRECTORY_SEPARATOR);
        $lookupPath1 = view . resources_path_view(!$searchCurrentAppViewFirstThenSharedView) . DIRECTORY_SEPARATOR .$full_view_path;
        $lookupPath2 = view . resources_path_view($searchCurrentAppViewFirstThenSharedView) . DIRECTORY_SEPARATOR .$full_view_path;
        if(  is_dir($lookupPath1)  ) return $lookupPath1;
        else if(is_dir($lookupPath2)  ) return $lookupPath2;
        return null;
    }
    function resources_path_view($isShared = false){ return resources_path($isShared) . 'views'; }
    function resources_path_view_layout($isShared = false){ return resources_path($isShared) . 'views/layouts'; }
    function resources_path_plugin($isShared = false){ return resources_path($isShared).'plugins' ; }
    function resources_path_view_cache(){ global $view_cachePath; return $view_cachePath; }
    function resources_path_cache(){ return resources_path(false).'/cache'; }
    function resources_path_asset($isShared = false){ return resources_path($isShared).'/assets';  }