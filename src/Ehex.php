<?php /** @noinspection ALL */


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
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    Ehex (EX)
 * @author    Samson Iyanu (Xamtax Technnology)
 * @copyright    Copyright (c) Xamtax, Inc. (https://xamtax.com/)
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://ehex.xamtax.com
 * @since    Version 2.0
 * @filesource
 */


// server should keep session data for AT LEAST 1 hour
@ini_set("session.gc_maxlifetime", 360000); // 100 hours
@ini_set("session.cookie_lifetime", 360000);
//session_set_cookie_params(3600); // each client should remember their session id for EXACTLY 1 hour
//setcookie('PHPSESSID', session_id(),60*60*24);
@session_start();

require __DIR__ . '/Ehex/Array1.php';
require __DIR__ . '/Ehex/ArrayObject1.php';
require __DIR__ . '/Ehex/Class1.php';
require __DIR__ . '/Ehex/Color1.php';
require __DIR__ . '/Ehex/Console1.php';
require __DIR__ . '/Ehex/Converter1.php';
require __DIR__ . '/Ehex/Cookie1.php';
require __DIR__ . '/Ehex/DateManager1.php';
require __DIR__ . '/Ehex/exArrayObject1.php';
require __DIR__ . '/Ehex/FileManager1.php';
require __DIR__ . '/Ehex/FilePref1.php';
require __DIR__ . '/Ehex/Form1.php';
require __DIR__ . '/Ehex/Framework1.php';
require __DIR__ . '/Ehex/Function1.php';
require __DIR__ . '/Ehex/Global1.php';
require __DIR__ . '/Ehex/Header1.php';
require __DIR__ . '/Ehex/Html1.php';
require __DIR__ . '/Ehex/Number1.php';
require __DIR__ . '/Ehex/MySql1.php';
require __DIR__ . '/Ehex/Object1.php';
require __DIR__ . '/Ehex/Page1.php';
require __DIR__ . '/Ehex/Picture1.php';
require __DIR__ . '/Ehex/Popup1.php';
require __DIR__ . '/Ehex/RecursiveArrayObject1.php';
require __DIR__ . '/Ehex/RegEx1.php';
require __DIR__ . '/Ehex/ResultObject1.php';
require __DIR__ . '/Ehex/ResultStatus1.php';
require __DIR__ . '/Ehex/ServerRequest1.php';
require __DIR__ . '/Ehex/Session1.php';
require __DIR__ . '/Ehex/SessionPreferenceSave1.php';
require __DIR__ . '/Ehex/String1.php';
require __DIR__ . '/Ehex/TaskManager1.php';
require __DIR__ . '/Ehex/Url1.php';
require __DIR__ . '/Ehex/Validation1.php';
require __DIR__ . '/Ehex/Value1.php';

/**
 * Alias for the classes
 */
require __DIR__ . '/Ehex/Math1.php';
require __DIR__ . '/Ehex/Date1.php';