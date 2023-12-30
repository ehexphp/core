<?php
/**
 * Created by PhpStorm.
 * Author: Samtax01
 * Date: 08/07/2018
 * Time: 7:47 AM
 */


/************************************************
 *  Fixed Action
 ************************************************/
if(is_debug_mode()){
    Config1::onDebug();
    // Database Setup
    if(isset($_REQUEST['db_help'])) {
        Db1::help();
    }
}



/************************************************
 *  Route Config
 ************************************************/
require PATH_LIB_LIBRARY."route/route.php";
exRoute1::initRouter();
$route = exRoute1::instance();





/************************************************
 *  Route Init/Include
 ************************************************/
$FORM_ACTION_SHOULD_REDIRECT = true;

/**
 * @param $route exRoute1
 */
function api_and_form_default_route($route){
    $route->any('/form/$class', function ($class){
        global $FORM_ACTION_SHOULD_REDIRECT;
        Session1::set('old', $_REQUEST);

        // render function
        \ServerRequest1::callFunction(urldecode($class), ',', false);

        if(!$FORM_ACTION_SHOULD_REDIRECT) return $FORM_ACTION_SHOULD_REDIRECT = true;
        else return Url1::redirect(String1::isset_or($_REQUEST['redirect_to'], Url1::backUrl()));
    });

    $route->get('/api/$class', function (){
        // render result
        echo json_encode(\Api1::callFunction(urldecode(Url1::getPageName()), ',', true));
    });
}




/**
 * @param $name
 * @param $actionFunction
 */
function make_route($name, $actionFunction) {
    global $route; $route->any($name, $actionFunction);
}

/**
 * @param string $onLoginFound_redirectTo
 * @param array $errorMessage
 */
function make_default_route($onLoginFound_redirectTo = '/', $errorMessage = ['Welcome Back', 'You have Logged In Already, Please Logout out first and try again', 'error']) { //routes()['dashboard']
    exRoute1::instance()->get('/forgot_password', 'pages.auth.forgot_password');
    exRoute1::instance()->get('/reset_password', 'pages.auth.reset_password');
    exRoute1::instance()->any('/register',        function() use ($onLoginFound_redirectTo, $errorMessage){ if(User::isLoginExist()){  Url1::redirect(url($onLoginFound_redirectTo), $errorMessage); } else { exRoute1::instance()->view('pages.auth.register'); } });
    exRoute1::instance()->any('/login',           function() use ($onLoginFound_redirectTo, $errorMessage){ if(User::isLoginExist()){  Url1::redirect(url($onLoginFound_redirectTo), $errorMessage); } else { exRoute1::instance()->view('pages.auth.login'); } });
    exRoute1::instance()->any('/logout',          function() { return User::logout(); });
    exRoute1::instance()->get('/delete_account',  function() { (User::getLogin(false))->delete(); });
}


$route->end();


