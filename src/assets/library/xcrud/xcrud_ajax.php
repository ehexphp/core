<?php

include('xcrud.php');
header('Content-Type: text/html; charset=' . Xcrud_config::$mbencoding);

// XAMTAX EDIT
const BASE_PATH = __DIR__."/../../../";
const PATH_LIB_ASSETS = BASE_PATH;
include_once (PATH_LIB_ASSETS."Ehex.php");

echo Xcrud::get_requested_instance();
