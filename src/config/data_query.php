<?php
/**
 * Created by PhpStorm.
 * Author: Samtax01
 * Date: 08/07/2018
 * Time: 7:47 AM
 */




if(!empty(env('DB_NAME'))){

    /**
     * For further documentation on using the various database facilities this library provides, consult the
     * https://github.com/illuminate/database
     * https://laravel.com/docs
     *
     */
    try{



        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection([
            "driver" => env('DB_DRIVER'),
            "host" => env('DB_HOST'),
            "port" => env('DB_PORT', '3306'),
            "database" => env('DB_NAME'),
            "username" => env('DB_USER'),
            "password" => env('DB_PASSWORD')
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

    } catch (Exception $ex){

        $errorMessage = $ex->getMessage();
        if(String1::contains('many connections', strtolower($errorMessage))){
            Session1::setStatus('Reload Page', 'Multiple connection to Db, Reload to re-connect');
        }else{
            pre(['<hr/>Database Connection Error..., <hr/>Solution <hr/>[1> Verity Model Query. <hr/>[2> Run Db1::databaseCreate() in "config onDebug(){...}" and Refresh!]<hr/>'. $errorMessage]);
        }
    };

}

/************************************************
 *  XCRUD
 *  call model1->xcrud()->render()
 *
 * @see http://xcrud.com/
 * @see http://xcrud.com/documentation/{{ url('/') }}
 *
 ************************************************/
function Xcrud_load(){
    include_once PATH_LIB_LIBRARY . 'xcrud/xcrud.php';
    Xcrud_config::$upload_folder_def = path_asset().'/uploads/xcrud';  //include Page1::getEhexCoreAssetsPath(). '/library/xcrud/xcrud.php';
}




/************************************************
 *  Paginator
 *  example paginate( model1->query()  )
 *  example paginate( [1,2,3...10]  )
 *
 * where Records Could be Any of this

 *  $records = [1,2,3,4...100];                             // Php Array
 *  $records = $qb->select('*')->from('sample', 'sample');  // Doctrine
 *  $records = User::select('*')->from('sample');           // Laravel
 *
$strana = new \Strana\Paginator();
$paginator = $strana->perPage(10)->make($records);
foreach ($paginator as $item) echo $item['field'] . '<br>';
echo $paginator;
 *
 * @see https://github.com/usmanhalalit/strana
 */
function paginate($records, $perPage = 10, $asInfiniteLoad = false, $infiniteLoadConfig = [], $adapterType = null, $config = [],  $paginationTemplateClass = DefaultPaginationTemplate::class, $pageKeyName = 'page') {
    //$infiniteLoadConfig = ['loaderDelay' => 600,  'loader' => '<img src="images/loader.gif"/>']
    $strana = new \Strana\Paginator();
    if($asInfiniteLoad) $strana->infiniteScroll($infiniteLoadConfig);
    return  $strana->perPage($perPage)->make($records, $paginationTemplateClass, $adapterType, $config, $pageKeyName);
}
