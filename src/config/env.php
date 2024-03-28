<?php


/**
 * Load ENV base on the provided hostName in the config
 */
function loadEnv(){

    $isForceHTTPS = function(){
        return (isset($server['HTTPS']) && $server['HTTPS'] == 'on') ||
            (isset($server['HTTP_X_FORWARDED_PROTO']) && $server['HTTP_X_FORWARDED_PROTO'] == 'https');
    };
    $protocol = $isForceHTTPS ? 'https': 'http' ;
    $currentFullUrl = "$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $currentUrl = parse_url($currentFullUrl, PHP_URL_HOST);
    $currentHost = "$protocol://$currentUrl";
    $urls = ["currentHost"=> $currentHost];
    $isEnvFound = false;

    // Search for .env based on the environment
    foreach (Config1::ENVs as $env=>$hostList){
        foreach ($hostList as $host){

            $targetUrl = parse_url($host, PHP_URL_HOST);
            $urls["configuredHost"][] = $host;

            if($currentUrl === $targetUrl){
                try {
                    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH, $env);
                    $dotenv->safeLoad();
                    $isEnvFound = true;
                    break 2;
                }catch (Exception $err){
                    die("<h1>Oops! Invalid character in your <code>$env</code> file </h1>");
                }
            }
        }
    }


    // Is Error Occurred?
    if(!$isEnvFound){
        $urls = json_encode($urls);
        echo "<script> console.error('No .env found for this url', $urls) </script>";
        $urls = str_replace("\/", "/", $urls);
        die(
        "<div style='background: #c8c7c7; padding: 20px; display: inline-block; border-radius: 15px; font-size: 2em;color: #3c3b3b;'>
    <p style='text-align: center;'>
        Oops! No .env found for this url.<br/>
        Update your <strong>'.config.php'</strong> file to fix this issue.<br/>
        Add <strong>'$currentHost'</strong> to the approriate Config::ENVs[] var.<br/>
        e.g
    </p>
    <small>
       
        <pre style='background: #eceaea; padding: 20px; display: inline-block; border-radius: 15px;'>
        <code>
         const ENVs = [
                '.env'=>['$currentHost'],
                '.prod.env'=>['&lt;!-- your prod domain --&gt;']
         ];
         </code>
        </pre>
    </small>
</div>
");
    }

    // Merge Config1 with $_SERVER and $_ENV
    $configs = (new ReflectionClass(Config1::class))->getConstants();
    $newEnv = array_merge($configs, $_ENV, $_SERVER);
    $_SERVER = $newEnv;
    $_ENV = $newEnv;
    return $newEnv;
}

loadEnv();