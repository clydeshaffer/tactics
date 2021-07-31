<?php

include 'conn.php';

$sql = "INSERT INTO GameSessions () VALUES ()";

echo $conn->query($sql);

$conn->close();

header("Status: 303 See Other");
header('Location: listgames.php');

?>