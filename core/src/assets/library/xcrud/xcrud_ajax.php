<?php




include('xcrud.php');
// XAMTAX EDIT
include_once (__DIR__."/../../../Ehex.php");
header('Content-Type: text/html; charset=' . Xcrud_config::$mbencoding);
echo Xcrud::get_requested_instance();
