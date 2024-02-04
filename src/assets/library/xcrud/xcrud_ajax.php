<?php

include_once('xcrud.php');
header('Content-Type: text/html; charset=' . Xcrud_config::$mbencoding);

// XAMTAX EDIT
@define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
@define("PATH_LIB_ASSETS", BASE_PATH."vendor/ehexphp/core/src/assets/");

$ehexPath = BASE_PATH."vendor/ehexphp/core/src/Ehex.php";

include_once($ehexPath);
echo Xcrud::get_requested_instance();
