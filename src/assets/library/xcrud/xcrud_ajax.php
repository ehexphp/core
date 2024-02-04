<?php

include('xcrud.php');
header('Content-Type: text/html; charset=' . Xcrud_config::$mbencoding);

// XAMTAX EDIT
//const BASE_PATH = __DIR__."/../../../";
//const PATH_LIB_ASSETS = BASE_PATH;
//include_once (PATH_LIB_ASSETS."Ehex.php");


@define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
@define("PATH_LIB_ASSETS", BASE_PATH."vendor/ehexphp/core/src/");

//die(BASE_PATH."vendor/ehexphp/core/src/");

include(BASE_PATH."vendor/ehexphp/core/src/".'Ehex.php');
echo Xcrud::get_requested_instance();
