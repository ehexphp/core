<?php

/**
 * Task Manager
 */
class TaskManager1
{

    private static $tasks = array();

    public static function add($taskId, $func)
    {
        static::$tasks[$taskId] = $func;
    }

    public static function run()
    {
        foreach (static::$tasks as $taskId => $func) call_user_func($func);
        return true;
    }
}