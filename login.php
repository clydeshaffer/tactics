<?php

include 'auth.php';

// ok, valid username & password
echo 'You are logged in as: ' . $currentUserInfo["LoginName"];
header("Status: 303 See Other");
header('Location: listgames.php');
?>