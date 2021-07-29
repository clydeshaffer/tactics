<?php

include_once 'conn.php';

$realm = 'TankTactics';

//user => password
$users = array('admin' => 'mypass', 'guest' => 'guest');


if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
} else {

    $myUserInfo = fetch_user_by_login_name($_SERVER['PHP_AUTH_USER']);

    if($myUserInfo) {
        header('HTTP/1.1 401 Unauthorized');
        die('Login name is taken');
    }

    if(create_user($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
        echo 'Account created';
    } else {
        header('HTTP/1.1 401 Unauthorized');
        die('Could not create user');
    }
    
}

function fetch_user_by_login_name($login_name)
{
    $getUserQuery = $mysqli->prepare("SELECT PlayerID, DisplayName, PassHash, PassSalt FROM Players WHERE LoginName=? LIMIT 1");
    $getUserQuery->bind_param("s", $login_name);
    $getUserQuery->execute();
    $getUserQuery->bind_result($player_id, $display_name, $pass_hash, $pass_salt);
    if($getUserQuery->fetch()) {
        return array('PlayerID'=>$player_id,
            'LoginName'=>$login_name,
            'DisplayName'=>$display_name,
            'PassHash'=>$pass_hash);
    } else {
        return null;
    }
}

function create_user($login_name, $password)
{
    $makeUserQuery = $mysqli->prepare("INSERT INTO Players (LoginName, PassHash) VALUES (?, ?)");
    $A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
    $makeUserQuery->bind_param("ss", $login_name, $A1);
    $makeUserQuery->execute();
    return $getUserQuery->fetch();
}

?>