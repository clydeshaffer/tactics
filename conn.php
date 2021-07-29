<?php

$servername = "localhost";
$username = $_SERVER["TACTICS_SQL_USER"];;
$password = $_SERVER["TACTICS_SQL_PASS"];;
$db = "tank_tactics";

$conn = new mysqli($servername, $username, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>