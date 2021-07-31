<?php

include_once 'crud.php';

$realm = 'TankTactics';

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="'.$realm.'"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
} else {

    $myUserInfo = fetch_user_by_login_name($_SERVER['PHP_AUTH_USER']);

    if($myUserInfo) {
        header('HTTP/1.1 401 Unauthorized');
        die('Login name is taken');
    }

    create_user($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    header("Status: 303 See Other");
    header('Location: index.php');
    
}

?>