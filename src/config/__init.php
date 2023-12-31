<?php
/**
 * Ehex
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2015 - 20.., Xamtax Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	Ehex (EX)
 * @author	Samson Iyanu (Xamtax Technnology)
 * @copyright	Copyright (c) Xamtax, Inc. (https://xamtax.com/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://ehex.xamtax.com
 * @since	Version 2.0
 * @filesource
 */


// is php version 7+
if(version_compare(PHP_VERSION, '7.0.0', '<=')) die('<p>Ehex Requires (PHP VERSION 7+)</p>');



//  Define Current Location
const DS = DIRECTORY_SEPARATOR;
define('BASE_PATH', BASE_PATH ?? dirname($_SERVER['SCRIPT_FILENAME']).DS);
const PATH_EHEX_LIBRARY = EHEX_PATH ?? BASE_PATH.'vendor'.DS.'ehexphp'.DS.'core'.DS.'src'.DS;

// Define Real-Path Declaration... Changeable
const PATH_APP = BASE_PATH . 'app' . DS;
const PATH_RESOURCE = BASE_PATH . 'resources' . DS;
const PATH_LAYOUTS = PATH_RESOURCE . 'views' . DS . 'layouts' . DS;
const PATH_PLUGINS = PATH_RESOURCE . 'plugins' . DS;

// Define Include sub-folder
const PATH_LIB_ASSETS = PATH_EHEX_LIBRARY . 'assets' . DS;
const PATH_LIB_LIBRARY = PATH_EHEX_LIBRARY . 'assets' . DS . 'library' . DS;

// Define shared path
const PATH_SHARED = PATH_EHEX_LIBRARY . 'shared' . DS;
const PATH_SHARED_APP = PATH_SHARED . 'app' . DS;
const PATH_SHARED_RESOURCE = PATH_SHARED . 'resources' . DS;


/************************************************
 *  Primary files. For Debugging and ENV
 ************************************************/
include BASE_PATH . 'vendor/autoload.php';
include PATH_EHEX_LIBRARY . 'config/env.php';

/**
 * Check app debug state
 * @return bool
 */
function is_debug_mode(){
    if(env('DEBUG_MODE')) {
        return true;
    }
    foreach (json_decode(env('DEBUG_IP')) as $key) if(preg_match("/$key/", $_SERVER['REMOTE_ADDR'])) {
        return true;
    }
    return false;
}

if(is_debug_mode()){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


/************************************************
 *  Util
 ************************************************/
require PATH_LIB_LIBRARY . 'utilphp/util.php';

/************************************************
 *  Ehex Import
 ************************************************/
require PATH_EHEX_LIBRARY . 'Ehex.php';
require PATH_EHEX_LIBRARY . 'EasyDb.php';
require PATH_EHEX_LIBRARY . 'EasyDataSet.php';
require PATH_EHEX_LIBRARY . 'EasyForm.php';


/************************************************
 *  Others, like function list and Simple Html Dom
 ************************************************/
// require PATH_LIBRARY . 'html-dom/simple_html_dom.php';
require PATH_EHEX_LIBRARY . "config/headers.php";
require PATH_EHEX_LIBRARY . "config/function.php";
include PATH_EHEX_LIBRARY . 'config/session.php';

// load Helper Library
include PATH_EHEX_LIBRARY . 'config/ex1.php';                  // Extending Class1 list/ Url1x::route(), Mail1x::...
include PATH_EHEX_LIBRARY . 'config/data_query.php';           // Query Builder Config
include PATH_EHEX_LIBRARY . 'config/view.php';                 // View Config
include PATH_EHEX_LIBRARY . 'config/translator.php';           // Language Translator
include PATH_EHEX_LIBRARY . 'config/mail.php';                 // Mailer
include PATH_EHEX_LIBRARY . 'config/file.php';                 // File Session and File Database

// Ehex Classes Autoload
FileManager1::autoClassRecursiveLoad(
    array_merge(
        app_class_paths(),
        [PATH_LIB_LIBRARY.'__autoload_class/']
    )
);


/************************************************
 *  Pretty Error
 ************************************************/
include PATH_EHEX_LIBRARY . 'config/error.php';                // Error Handler
include PATH_EHEX_LIBRARY . 'config/route.php';                // Route  / and Init Route like Login/ Register


