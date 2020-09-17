<?php
// 可限制domain，目前全開
header("Access-Control-Allow-Origin: *");
$path = explode('?', $_SERVER['REQUEST_URI'])[0];

$checkApi = preg_match('|^/api/.*|', $path, $match);

if ( $checkApi ) {
    require_once 'api.php';
}

switch ( $path ) {
    case '/':
        break;
    default:
        http_response_code(404);
        echo 'Not found file <br>';
        exit;
}