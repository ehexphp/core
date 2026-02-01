<?php

/**
 * Interface Controller1RouteInterface
 * Use for (new RouteSystem)->resource('', '', [])
 */
interface Controller1RouteInterface
{
    /**
     * Return First Page interface. Like Home
     * Access with url('/{model}/')
     * @return mixed
     */
    static function index();

    /**
     * View Search Model
     * Access with url('/{model}/search')
     * @param $text
     * @return mixed
     */
    //static function  search($text = '');

    /**
     * Return Show View interface
     * Access with url('/{model}/model_id_or_slug')
     * @param $id
     * @return mixed
     */
    static function show($id);

    /**
     * Return Manage View interface
     * Access with url('/{model}/manage')
     * @return mixed
     */
    static function manage();

    /**
     * Return Edit View interface
     * Access with url('/{model}/{model_id_or_slug}/edit')
     * @param $id
     * @return mixed
     */
    static function edit($id);

    /**
     * Return Create View interface
     * Access with url('/{model}/create')
     * @return mixed
     */
    static function create();


    /**
     * Update Model Information
     * Access with <form action="Form1::callController(Model1::class, 'processSave()')" > <?= form_token() ?>  ... </form>
     * @param $id
     */
    static function processSave($id = null);

    /**
     * Delete Model
     * Access with <form action="Form1::callController(Model1::class, 'processDestroy()')" > <?= form_token() ?> ... </form>
     * @param $id
     */
    static function processDestroy($id);
}
