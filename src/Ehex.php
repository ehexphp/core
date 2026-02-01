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
@session_start();

/**
 *  Ehex Class is an helping class from XAMTAX (http://xamtax.com)
 *  It consist of different function that will ease coding php.
 *      Function contain are:
 *      String1
 *      FileManager1
 *      FormManager1
 *      Picture1
 *      Object1 or Class1
 *      Array1 e.t.c
 *  All Class ends with 1 to precent clashing with Inbuilt Class
 *  more function will be provided in next version...
 */

// Load Core Utility Classes that others depend on
require_once __DIR__ . '/Support/String1.php';
require_once __DIR__ . '/Support/Array1.php';
require_once __DIR__ . '/Support/FileManager1.php';

// Register the autoloader for all Support classes
FileManager1::autoClassRecursiveLoad([__DIR__ . '/Support/']);

// Force-load remaining frequently used core classes
require_once __DIR__ . '/Support/Class1.php';
require_once __DIR__ . '/Support/Object1.php';