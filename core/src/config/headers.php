<?php

// access control origin
if(!empty(json_decode(env('ACCESS_CONTROL_ALLOW_ORIGINAL')))) {
    header('Access-Control-Allow-Origin: '.implode(',', json_decode(env('ACCESS_CONTROL_ALLOW_ORIGINAL'))).(isset($_SERVER['HTTP_ORIGIN'])? ','.$_SERVER['HTTP_ORIGIN']: ''));
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
}
