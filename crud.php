<?php

include_once 'conn.php';

$paramTypes = array(
    'PlayerID' => 'i',
    'LoginName' => 's',
    'GameSessionID' => 'i',
    'TankID' => 'i',
    'Game' => 'i',
    'Player' => 'i',
    'HP' => 'i',
    'AP' => 'i'
);

function assoc_get_one($columns, $table, $getby, $targetvalue)
{
    global $conn;
    global $paramTypes;
    $getQuery = $conn->prepare("SELECT " . implode(", ", $columns) . " FROM " . $table . " WHERE " . $getby . "=? LIMIT 1");
    $getQuery->bind_param($paramTypes[$getby], $targetvalue);
    $getQuery->execute();
    $result = $getQuery->get_result();
    if($row = $result->fetch_assoc()) {
        return $row;
    } else {
        return null;
    }
}

function assoc_get_all($columns, $table, $getby, $targetvalue)
{
    global $conn;
    global $paramTypes;
    $getQuery = $conn->prepare("SELECT " . implode(", ", $columns) . " FROM " . $table . " WHERE " . $getby . "=?");
    $getQuery->bind_param($paramTypes[$getby], $targetvalue);
    $getQuery->execute();
    $result = $getQuery->get_result();

    $outArray = array();
    while($row = $result->fetch_assoc()) {
        array_push($outArray, $row);
    }
    return $outArray;
}

function assoc_get_one_by_two($columns, $table, $getby1, $targetvalue1, $getby2, $targetvalue2)
{
    global $conn;
    global $paramTypes;
    $getQuery = $conn->prepare("SELECT " . implode(", ", $columns) . " FROM " . $table . " WHERE " . $getby1 . "=? AND " . $getby2 . "=? LIMIT 1");
    $getQuery->bind_param($paramTypes[$getby1].$paramTypes[$getby2], $targetvalue1, $targetvalue2);
    $getQuery->execute();
    $result = $getQuery->get_result();
    if($row = $result->fetch_assoc()) {
        return $row;
    } else {
        return null;
    }
}

function fetch_tanks_by_game($game_id)
{
    return assoc_get_all(array("TankID", "Player", "X", "Y", "HP", "ExtraAP", "SpentAP"), "Tank", "Game", $game_id);
}

function fetch_tank_by_game_and_player($game_id, $player_id)
{
    return assoc_get_one_by_two(array("TankID", "Player", "X", "Y", "HP", "ExtraAP", "SpentAP"), "Tank", "Game", $game_id, "Player", $player_id);
}

function fetch_game_by_id($game_id)
{
    return assoc_get_one(array("GameSessionID", "Rounds", "ActionHours", "Winner", "StartDate", "FinishDate"), "GameSessions", "GameSessionID", $game_id);
}

function fetch_user_by_login_name($login_name)
{
    return assoc_get_one(array("PlayerID", "DisplayName", "LoginName", "PassHash"), "Players", "LoginName", $login_name);
}

function fetch_user_by_id($player_id)
{
    return assoc_get_one(array("PlayerID", "DisplayName", "LoginName", "PassHash"), "Players", "PlayerID", $player_id);
}

function create_user($login_name, $password)
{
    global $conn, $realm;
    $makeUserQuery = $conn->prepare("INSERT INTO Players (LoginName, PassHash) VALUES (?, ?)");
    $A1 = md5($login_name . ':' . $realm . ':' . $password);
    $makeUserQuery->bind_param("ss", $login_name, $A1);
    return $makeUserQuery->execute();
}

function create_tank($game_id, $player_id, $x, $y, $spent)
{
    global $conn;
    $makeUserQuery = $conn->prepare("INSERT INTO Tank (Game, Player, X, Y, SpentAP) VALUES (?, ?, ?, ?, ?)");
    $makeUserQuery->bind_param("iiiii", $game_id, $player_id, $x, $y, $spent);
    return $makeUserQuery->execute();
}

?>