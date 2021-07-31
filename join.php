<?php

include_once 'auth.php';
include_once 'crud.php';

$gameInfo = fetch_game_by_id($_GET["id"]);

if(!$gameInfo) {
    header('HTTP/1.1 404 Not found');
    die('Game not found!');
}

$tankInfo = fetch_tank_by_game_and_player($_GET["id"], $myUserInfo["PlayerID"]);

if(!$tankInfo) {
    $gameAgeHours = floor((strtotime("now") - strtotime($gameInfo["StartDate"])) / (60*60));
    $actionPointsGiven = floor($gameAgeHours / $gameInfo["ActionHours"]);
    $success = false;
    while(!$success) {
        $x = rand(1, 20);
        $y = rand(1, 12);
        $success = create_tank($_GET["id"], $myUserInfo["PlayerID"], $x, $y, $actionPointsGiven);
    }
}
$conn->close();

header("Status: 303 See Other");
header('Location: game.php?' . $_SERVER['QUERY_STRING']);

?>