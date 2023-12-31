<?php


/**
 * Load ENV base on the provided hostName in the config
 */
function loadEnv(){
    $protocol = (empty($_SERVER['HTTPS']) ? 'http' : 'https') ;
    $currentFullUrl = "$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $currentUrl = parse_url($currentFullUrl, PHP_URL_HOST);
    $urls = ["currentHost"=> "$protocol://$currentUrl"];

    // Search for .env based on the environment
    foreach (Config1::ENVs as $env=>$hostList){
        foreach ($hostList as $host){

            $targetUrl = parse_url($host, PHP_URL_HOST);
            $urls["configuredHost"][] = $host;

            if($currentUrl === $targetUrl){
                try {
                    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH, $env);
                    $dotenv->safeLoad();
                    return null;
                }catch (Exception $err){
                    die("<h1>Oops! Invalid character in your <code>$env</code> file </h1>");
                }
            }
        }
    }

    // Is Error Occurred?
    $urls = json_encode($urls);
    if($urls){
        echo "<script> console.error('No .env found for this url', $urls) </script>";
        $urls = str_replace("\/", "/", $urls);
        die("<h1>Oops! No .env found for this url<br/><small>".$urls."</small> <p><small>Fix: Add the currentHost to the Config::ENVs[] var</small></p></h1>");
    }

    // Merge Config1 with $_SERVER and $_ENV
    $configs = (new ReflectionClass(Config1::class))->getConstants();
    $newEnv = array_merge($configs, $_ENV, $_SERVER);
    $_SERVER = $newEnv;
    $_ENV = $newEnv;
    return $newEnv;
}

loadEnv();