<?php

/**
 * Display in Admin Dashboard
 * Interface ModelAdmin
 */
interface Model1ActionInterface
{
    /**
     * Dashboard Menu.
     * for quick implementation visit
     * @see https://ehex.github.io/ehex-docs/#/BasicUsage?id=model-dashboard
     * @return array
     */
    static function getDashboard();

    /**
     * Manage model with HtmlForm1 or xcrud.
     * for quick implementation visit
     * @see https://ehex.github.io/ehex-docs/#/BasicUsage?id=model-manage
     * @return mixed|Xcrud|HtmlForm1
     */
    static function manage();


    /**
     * Model Sidebar menu list.
     * for quick implementation visit
     * @see https://ehex.github.io/ehex-docs/#/BasicUsage?id=model-route-and-menu
     * @return mixed|array
     */
    static function getMenuList();

    /**
     * Model Route List
     * for quick implementation visit
     * @see https://ehex.github.io/ehex-docs/#/BasicUsage?id=model-route-and-menu
     * @param exRoute1 $route
     */
    static function onRoute($route);


    /**
     * Save  Model Information
     * for quick implementation visit
     * @see https://ehex.github.io/ehex-docs/#/BasicUsage?id=model-process-save
     * @param $id
     */
    static function processSave($id = null);
}
