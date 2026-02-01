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

/**
 * @see https://ehex.xamtax.com for Documentation
 * Created by Samson Iyanu
 * Website : https://xamtax.com
 * Required
 * class Config1{
 * const DB_NAME= 'my_db_name';
 * const DB_USER_name = 'root';
 * const DB_USER_password = 'root';
 * const DEBUG_MODE = true;
 * const MAINTENANCE_MODE = false;
 * }
 *
 *
 * This Class Consist of
 *      Db1 : re-declares mysql connection
 *      Model1 : MySqlQueryGenerator and Handler
 *      AuthModel1 : Extend Model1 and Include Auth Function
 *      DbPref1 : Extend Model1 and It Easy to Store KeyValue Data
 */
// verify if config1 exists



if (!class_exists(Config1::class)) die('<h1>APP CONFIG NOT FOUND!<br/><small>".config.php" Missing.</small></h1>');

// activate Selected Db
if (!(@include(PATH_EHEX_LIBRARY . 'config/database/' . env('DB_DRIVER') . '.php'))) {
    Console1::println('<h1>Oops!. Database DB_DRIVER not supported<br/><small>please switch to mysql or sqlite.</small></h1>', true);
}


/**
 * Database Class
 * Class Db1
 */
class Db1
{

    /**
     * @var mysqli|SQLite3 null
     */
    static $DB_HANDLER = null;

    /**
     * Open Database Connection
     * @param bool $isDatabaseAvailable
     * @return mysqli|string
     */
    static function open($isDatabaseAvailable = true)
    {
        try {
            static::$DB_HANDLER = DbAdapter1::open($isDatabaseAvailable);
            return static::$DB_HANDLER;
        } catch (Exception $ex) {
            static::$DB_HANDLER = null;
            Console1::println("<h3>Welcome to Ehex (ex).</h3> <p>App encounter error in Database</p><em>" . $ex->getMessage() . "</em>");
            Console1::println("<strong>NOTE</strong><br/><small>You can also change your app_key to <em style='font-weight: 800;color:gray'>APP_KEY = 'base64:" . password_hash(Math1::getUniqueId(), 1) . "'</em> if you have not, in .config file</small>");
            if (String1::contains('Unknown database', $ex->getMessage())) return null . die(Console1::println('<h2>Database [' . env('DB_NAME') . '] is not created yet.</h2><p>Open <em>.config.php</em> file and run <code>Db1::databaseCreate()</code> In Config1::onDebug() method. <br/><small>You can also run <em><a href="?db_help">Db1::help()</a></em> to manage model graphically.</small></p>' . exForm1::makeRunnableForm(Db1::class, 'databaseCreate()') . Session1::setStatus('Manage Model Graphically', 'to manage model, run Db1::help() in Config')));
        }
        return null;
    }

    /**
     * Close Database connection
     * @return mixed
     */
    static function close()
    {
        return static::$DB_HANDLER->close();
    }


    /**
     * is query suppose to return value
     * @param $sql
     * @return bool
     */
    static function isQueryReturningValue($sql)
    {
        return !(bool)preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX)\s/i', $sql);
    }

    static function isDatabaseExists()
    {
        try {
            new mysqli(env('DB_HOST'), env('DB_USER'), env('DB_PASSWORD'),  env('DB_NAME'), env('DB_PORT', '3306'));
            return true;
        } catch (Exception $ex) {
            return null;
        }
    }

    /*
     * Run Direct Query on Database
     */
    static function exec($query, $closeDbOnFinished = true, $isDatabaseAvailable = true, $throwError = true, $asMultipleQuery = false)
    {
        //open connection
        static::$DB_HANDLER = Db1::open($isDatabaseAvailable);
        if (!static::$DB_HANDLER) die(strtoupper(env('DB_DRIVER')) . ' DB_HANDLER not initialized' . "<br/><hr/><br/>Error in db:[" . DbAdapter1::getType() === 'mysql' ? String1::toArrayTree(static::$DB_HANDLER->error_list) : ' error_list N/A ' . "] dbString:[" . DbAdapter1::getLastErrorMessage(static::$DB_HANDLER) . "]");
        //query
        $data = DbAdapter1::exec($query, /* $asMultipleQuery */ (self::isQueryReturningValue($query) ? false : true), static::$DB_HANDLER) or ($throwError ? die("InValid Query [ " . static::errorHandlerAndSolution() . " ] ") : $data = null);
        //close connection
        if ($closeDbOnFinished && is_bool($data)) Db1::close();
        return $data;
    }


    /**
     * Error handler
     * @return mixed
     */
    public static function errorHandlerAndSolution($errorText = null)
    {
        $errorText = $errorText ? $errorText : DbAdapter1::getLastErrorMessage(static::$DB_HANDLER);
        if (!is_debug_mode()) die(Console1::println("EXDB001 Error Occured, please enable debug mode to view more"));
        $suggestion = '';

        $runLink = function ($modelClassName, $functionName) {
            return "<li>
                        <hr/>" . exForm1::makeRunnableForm($modelClassName, $functionName)
                . "<a href='" . Form1::callController($modelClassName, $functionName) . '?_token=' . token() . "'> Run </a>
                    </li>";
        };

        // Merge debug_backtrace() with method Suggestion Links
        $runFunction = function ($className, $functionName) use (&$suggestion, $runLink) {
            Object1::getExecutedClass($className, true, function ($class) use ($functionName, &$suggestion, $runLink) {
                if ($class !== Model1::class) $suggestion .= $runLink($class, $functionName);
            });
        };

        // Database Access Denied
        if (String1::contains('Access denied', $errorText)) {
            $suggestion .= '<li><h4>Database Access denied <small>(invalid user/password)</small></h4></li><li>Your Database Config DB_USER = "***", or DB_PASSWORD = "***" is wrong. Please open .config.php file and update it</li>';
        }

        // Column Not Exists Yet
        if (String1::contains('Unknown column', $errorText)) {
            $suggestion .= '<li>Please update Model Fields and run tableReset() method on Model. e.g User::tableReset() </li>';
            $runFunction(Model1::class, "tableReset()");
        } // Column Not Exists Yet
        else if (String1::containsMany(['Table', 'doesn\'t exist'], $errorText, 'and')) {
            $dbAndModel = explode('.', trim(String1::replaceEnd(String1::replaceStart($errorText, 'Table', ''), 'doesn\'t exist', ''), "\"' "));
            $modelName = isset($dbAndModel[1]) ? @array_flip(app_model_list(true, true))[$dbAndModel[1]] : null;
            $suggestion .= "<li>Please run Model $modelName::tableCreate() method in config onDebug(){...}. e.g User::tableCreate() </li>";
            if ($modelName) $suggestion .= $runLink($modelName, 'tableCreate()');
            else  $suggestion .= $runFunction(Model1, "tableCreate()");
        }

        $errorText = (empty($errorText) && empty($suggestion)) ? '<hr>Is Database Exists? If not, Please Run either<ul><li>Db1::databaseCreate();</li> <li>Db1::tableCreateAll();</li> </ul> In Your Debug Method' : $errorText;
        echo "<div class='middle' style='position: absolute !important; top:0;left:0;margin:50px;z-index: 103904208284804'>" . Console1::println(
                '<h3>Ehex Database Error</h3>' . $errorText .
                (!empty($suggestion) ? "<br/><br/><h3>Suggestion</h3><ul>$suggestion</ul><strong>Note:</strong> <small>You can also run <em><code><a href='?db_help'>Db1::help()</a></code></em> to manage your models graphically.</small>" : '')) . "</div>";
        return $errorText;
    }


    /**
     * Easy Best FUnction So Far
     * guild to create and manage db model.
     * @param bool $endPage
     */
    static function help($endPage = true)
    {
        Page1::start();
        Db1::databaseCreate();
        $style = "<style>
                    code{color:gray; font-weight: 800} 
                    code.ex_note{color:#f06956; font-weight: 800} 
                    hr{ border:1px solid #27aad6}
                    td{ border-bottom:1px solid #27aad6; padding-top:5px;}
                    .ex_right{float:right;}
                </style>";
        $script = "<script src='" . asset('default/jquery/js/jquery3.3.1.min.js') . "'></script><script>  $(function(){  $('#model_search').on('keyup', function(){ Html1.enableSearchFilter('model_search', 'model_table', 'tr'); })  }) </script>";
        Console1::println($style . $script . '<strong>Welcome to Ehex (ex). DB Smart Help</strong> <a href="' . Url1::getCurrentUrl(false) . '" style="float:right;text-decoration: none">&hookleftarrow; Go Back </a><hr/><small>You can either open this interface by adding <code>?db_help</code> to your url or <code>Db1::help();</code> to your Config::onDebug() method, located in .config.php file while your DEBUG_MODE is set to true. This interface enables you to manage your database models with ease. 
        <br/><small>Please be sure to change your APP_KEY to the below in your .config file for maximum security. <em style=\'font-weight: 800;color:gray\'><br/><strong>APP_KEY = base64:' . password_hash(Math1::getUniqueId(), 1) . '</strong></small>
        <br/><code class="ex_note">Please Note that Action Here Cannot Be Undo.</code></small>');

        // Data buffer
        $existsModel = @Db1::getExistingModels();
        $modelMethodList = ['tableCreate()', 'tableReset()', 'tableTruncate()', 'tableDestroy()', 'tableSaveBackup()', 'tableLoadBackup()', 'generateDemoData(1)'];
        $modelBuff = "<div><strong>Created Model Table : " . count($existsModel) . '/' . count(app_model_list()) . "</strong><span style='float:right'>" . HtmlForm1::addInput(null, ['placeholder' => 'search model', 'id' => 'model_search', 'style' => 'padding:10px;']) . "</span> <br><small style='color:gray;padding:3px;font-size:12px;'>If Model is red, click on <small>tableCreate()</small> Button to Create table for it</small> <div style='clear:both'></div></div><hr/>";

        // Database Method
        $modelBuff .= "<table style='width:100%' id='model_table'>";
        foreach (app_model_list() as $model) {
            $modelBuff .= "<tr><td><strong>" . (in_array($model, $existsModel) ? $model : "<code class='ex_note'>$model</code>") . "</strong></td>";
            foreach ($modelMethodList as $method) $modelBuff .= "<td>" . exForm1::makeRunnableForm($model, $method, $method) . "</td>";
            $modelBuff .= "</tr>";
        }
        $modelBuff .= "</table>";

        // Database Method
        $dbMethodList = ['tableCreateAll()', 'tableResetAll()', 'tableTruncateAll()', 'tableDestroyAll()', 'tableClearBackupAll()', 'tableSaveBackupAll()', 'tableLoadBackupAll()'];
        $modelBuff .= "<br/><br/><code class=\"ex_note\">The Function Below Is Disabled by Default for security reason, to Enable it, Make Db1::class extends Controller1.</code>";
        $modelBuff .= "<table  style='width:100%'><tr><td><strong>Database</strong></td>";
        foreach ($dbMethodList as $method) $modelBuff .= "<td disabled=''>" . exForm1::makeRunnableForm('Db1', $method, $method) . "</td>";
        $modelBuff .= "</tr></table>";

        Console1::println($modelBuff);
        Console1::println("<h4 align='center'><a href='http://ehex.xamtax.com'>Ehex Documentation</a></h4>");
        echo "<br/><br/>";


        if ($endPage) {
            Page1::end();
            exit;
        }
    }


    /*
     * Create Database
     */
    static function databaseCreate()
    {
        if (!static::exec("CREATE DATABASE IF NOT EXISTS `" . env('DB_NAME') . "`  DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci", true, false)) {
            die("Cannot create Database 'Database'. WHY? [" . (DbAdapter1::getLastErrorMessage(static::$DB_HANDLER)) . "]");
        }
    }

    /*
     * Delete Database
     */
    static function destroyDatabase()
    {
        static::exec("DROP DATABASE " . env('DB_NAME'), true);
    }

    /**
     * Create Multiple Table at once
     * @param Model1 ...$modelList
     *
     */
    static function tableCreate(...$modelList)
    {
        static::databaseCreate();
        $tableRunBuffer = '';
        $logBuffer = '';
        $logFormat = "<br/><br/><hr/><h5> Creating Table for [%s]</h5><hr/> %s";

        foreach ($modelList as $modelClass) {
            if (empty($modelClass)) continue;
            $modelInstant = (new $modelClass());
            $createQuery = '';
            if (method_exists($modelInstant, 'toTableCreateQuery')) $createQuery = $modelInstant->toTableCreateQuery();
            else $modelClass::tableCreate();
            $tableRunBuffer .= $createQuery;
            $logBuffer .= sprintf($logFormat, $modelClass, $createQuery);
        }
        if (empty($tableRunBuffer)) return;
        Session1::setStatus('Creating DB Table(s)', $logBuffer);
        Db1::exec($tableRunBuffer, true, true, true, true);
    }

    /**
     * Create table for all Available Model
     */
    static function tableCreateAll()
    {
        static::tableCreate(...app_model_list());
    }

    /*
     * Reset all Database Table [ delete and re-create with data ]
     */
    static function tableReset(...$modelList)
    {
        static::databaseCreate();
        foreach ($modelList as $model) $model::tableReset();
    }

    static function tableResetAll()
    {
        static::tableReset(...Db1::getExistingModels());
    }

    /*
     * Delete all Database Table
     */
    static function tableDestroy(...$modelList)
    {
        foreach ($modelList as $model) $model::tableDestroy();
    }

    static function tableDestroyAll()
    {
        static::tableDestroy(...Db1::getExistingModels());
    }

    static function tableTruncateAll()
    {
        foreach (Db1::getExistingModels() as $model) $model::tableTruncate();
    }

    /*
     * Delete all Database Table
     */
    static function tableSaveBackupAll($optionalBackupFolderName = 'all')
    {
        $path = path_asset("backups" . DIRECTORY_SEPARATOR . "$optionalBackupFolderName");
        $result = [];
        foreach (Db1::getExistingTables(true) as $tableName => $dbTableName) $result = $tableName::tableSaveBackup(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $tableName . '.model.json');
        Session1::setStatus("Backup Completed", "Backed Up (" . count($result) . ") Models", 'success');
        return $result;
    }


    /**
     * Restore all backup. Model Backup must end with '.model.json'
     * @param string $optionalBackupFolderName
     * @param bool $clearExistingData
     * @return array
     */
    static function tableLoadBackupAll($optionalBackupFolderName = 'all', $clearExistingData = true)
    {
        $result = FileManager1::getDirectoryFiles(path_asset("backups" . DIRECTORY_SEPARATOR . "$optionalBackupFolderName"), true, function ($path) use ($clearExistingData) {
            if (String1::endsWith($path, '.model.json')) {
                $modelNameFromFileName = Array1::getFirstItem(explode('.', FileManager1::getFileName($path)));
                return $modelNameFromFileName::tableLoadBackup($path, $clearExistingData);
            }
        });
        return $result;
    }

    /**
     * Clear all backup, for safety
     * @param string $optionalBackupFolderName
     * @return bool
     */
    static function tableClearBackupAll($optionalBackupFolderName = 'all')
    {
        return FileManager1::deleteAll(path_asset("backups" . DIRECTORY_SEPARATOR . "$optionalBackupFolderName"), true);
    }


    /**
     * create table with  - e.g createClassTable(UserInfo::getCreateQuery());
     * @param $tableQuery
     * @return bool
     *
     */
    static function createClassTable($tableQuery)
    {
        $result = static::exec($tableQuery);
        if ($result) return true;
        die("Cannot create tableInfo [ " . substr($tableQuery, 0, 37) . " ]");
    }


    /**
     * Get Existing Table Column Information.
     * @param string $tableName
     * @return array
     */
    static function getTableDbField($tableName = 'users')
    {
        return DbAdapter1::getTableDbField($tableName);
    }


    /**
     * Get Existing Table
     *  using USE db_name; SHOW TABLES;
     * @param bool $withModelName
     * @return array
     */
    static function getExistingTables($withModelName = true)
    {
        try {
            if (isset(Page1::$_VARIABLE['__getExistingTables'][$withModelName])) return Page1::$_VARIABLE['__getExistingTables'][$withModelName];


            $allTableName = [];
            $queryResult = static::exec("SHOW TABLES;", true, true, false);
            if (!$queryResult) return [];
            foreach ($queryResult as $db => $table) $allTableName[] = $table['Tables_in_' . env('DB_NAME')];
            if (!$withModelName) return Page1::$_VARIABLE['__getExistingTables'][$withModelName] = $allTableName;

            // merge model with declared table name
            $modelNameAndTableName = [];
            array_map(function ($modelName) use ($allTableName, &$modelNameAndTableName) {
                if (in_array($modelName::getTableName(), $allTableName)) $modelNameAndTableName[$modelName] = $modelName::getTableName();
            }, app_model_list());


            return Page1::$_VARIABLE['__getExistingTables'][$withModelName] = $modelNameAndTableName;
        } catch (ErrorException $ex) {
            return [];
        }
    }

    /**
     * Get Existing Table
     *  using USE db_name; SHOW TABLES;
     * @return array
     */
    static function getExistingModels()
    {
        if (isset(Page1::$_VARIABLE['__getExistingModels'])) return Page1::$_VARIABLE['__getExistingModels'];
        return Page1::$_VARIABLE['__getExistingModels'] = array_keys(static::getExistingTables(true));
    }

}
