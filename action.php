<?php

include_once 'auth.php';
include_once 'crud.php';

function redir_to_game()
{
    header("Status: 303 See Other");
    header('Location: game.php?id=' . $_GET["id"]);
}

$gameInfo = fetch_game_by_id($_GET["id"]);

if(!$gameInfo) {
    header('HTTP/1.1 404 Not found');
    die('Game not found!');
}

$gameAgeHours = floor((strtotime("now") - strtotime($gameInfo["StartDate"])) / (60*60));
$actionPointsGiven = floor($gameAgeHours / $gameInfo["ActionHours"]);

$tanks = fetch_tanks_by_game($_GET["id"]);
$myTank = fetch_tank_by_game_and_player($_GET["id"], $myUserInfo["PlayerID"]);

if($myTank["HP"] <= 0) {
    header('HTTP/1.1 400 Bad request');
    die("omae wa mou shindeiru");
}

$coords = explode("-", $_GET["gridpos"]);
$dist = abs($coords[0] - $myTank["X"]) + abs($coords[1] - $myTank["Y"]);

$targetedTank = fetch_tank_by_location($_GET["id"], $coords[0], $coords[1]);

$myActionPoints = $actionPointsGiven + $myTank["ExtraAP"] - $myTank["SpentAP"];

if($myActionPoints <= 0) {
    header('HTTP/1.1 400 Bad request');
    die("You don't have any action points!");
}

switch ($_GET["action_type"]) {
    case 'move':
        if($targetedTank) {
            header('HTTP/1.1 400 Bad request');
            die("That space is occupied");
        }
        if($dist > 4) {
            header('HTTP/1.1 400 Bad request');
            die("That space is too far");
        }
        move_tank($myTank["TankID"], $coords[0], $coords[1]);
        redir_to_game();
        break;
    case 'attack':
        if($myTank["TankID"] == $targetedTank["TankID"]) {
            header('HTTP/1.1 400 Bad request');
            die("Of course I know him, he's me!");
        }
        if(!$targetedTank) {
            header('HTTP/1.1 400 Bad request');
            die("There's nobody there");
        }
        if($dist > 5) {
            header('HTTP/1.1 400 Bad request');
            die("That space is too far");
        }
        if(attack_tank($myTank["TankID"], $targetedTank["TankID"])) {
            if($targetedTank["HP"] <= 1) {
                terminate_tank($targetedTank["TankID"]);
            }
        }
        redir_to_game();
        break;
    case 'gift':
        if($myTank["TankID"] == $targetedTank["TankID"]) {
            header('HTTP/1.1 400 Bad request');
            die("Of course I know him, he's me!");
        }
        if(!$targetedTank) {
            header('HTTP/1.1 400 Bad request');
            die("There's nobody there");
        }
        gift_action_point($myTank["TankID"], $targetedTank["TankID"]);
        redir_to_game();
        break;
    default:
        header('HTTP/1.1 400 Bad request');
        die("Nyot a vawid action type~");
        break;
}


$conn->close();
?>