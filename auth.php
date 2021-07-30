<?php

include_once 'conn.php';

$realm = 'TankTactics';

//user => password
$users = array('admin' => 'mypass', 'guest' => 'guest');


if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
    die('Text to send if user hits Cancel button');
}


// analyze the PHP_AUTH_DIGEST variable
if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']))) {
    header('HTTP/1.1 401 Unauthorized');
    die('Wrong Credentials!');
}

$myUserInfo = fetch_user_by_login_name($data['username']);

if(!$myUserInfo) {
    header('HTTP/1.1 401 Unauthorized');
    die('Wrong Credentials!');
}

// generate the valid response
//$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
$A1 = $myUserInfo["PassHash"];
$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

if ($data['response'] != $valid_response)
{
    header('HTTP/1.1 401 Unauthorized');
    die('Wrong Credentials!');
}

// function to parse the http auth header
function http_digest_parse($txt)
{
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}

function fetch_user_by_login_name($login_name)
{
    global $conn;
    $getUserQuery = $conn->prepare("SELECT PlayerID, DisplayName, PassHash FROM Players WHERE LoginName=? LIMIT 1");
    $getUserQuery->bind_param("s", $login_name);
    $getUserQuery->execute();
    $getUserQuery->bind_result($player_id, $display_name, $pass_hash);
    if($getUserQuery->fetch()) {
        return array('PlayerID'=>$player_id,
            'LoginName'=>$login_name,
            'DisplayName'=>$display_name,
            'PassHash'=>$pass_hash);
    } else {
        return null;
    }
}
?>