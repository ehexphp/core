<?php

/**
 * Display in Admin Dashboard
 * Interface ModelAdmin
 */
interface Model1PageInterface
{
    /**
     * Save  Model Page Information
     * Simply Call PageModel1Class::saveDefault($_POST)
     *  e.g Session1::setStatusFrom(static::saveDefault($_POST)? ['Updated', 'Page Updated!', 'success']: ['Failed', 'Failed to Updated Page', 'error']);
     */
    static function processUpdatePage();


    /**
     * Manage PageModel1Class with HtmlForm1 or xcrud
     * simply  call.
     *  e.g  return static::getDefault()->form([])->setFieldAttribute([ 'address'=>['type'=>'textarea'], ]);
     * @return mixed|Xcrud|HtmlForm1
     */
    static function manage();
}
