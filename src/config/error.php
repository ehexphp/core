<?php
/**
 * Created by PhpStorm.
 * Author: Samtax01
 * Date: 08/07/2018
 * Time: 7:47 AM
 */


/************************************************
 *  Whoop Error Config
 *  https://github.com/filp/whoops
 ************************************************/
if(is_debug_mode()){
    $whoops = new \Whoops\Run;
    $handler = new \Whoops\Handler\PrettyPageHandler();
    $handler->setPageTitle('EHEX Framework error detected!');

    // Add a custom table to the layout:
    $handler->addDataTableCallback('Autoload Classes', function(\Whoops\Exception\Inspector $inspector) {
        $data = array();
        $exception = $inspector->getException();
        //  if ($exception instanceof SomeSpecificException) {
        //      $data['Important exception data'] = $exception->getSomeSpecificData();
        //  }
        foreach (app_class_list() as $class){
            $data[$class] = "typeof ".get_parent_class($class);
        }

        $data["---------------"] = "--------------------------------------";
        $data["Manage Database"] = Url1::getCurrentUrl(false)."?db_help";
        return $data;
    });



    $whoops->pushHandler($handler);
    $whoops->register();
}

