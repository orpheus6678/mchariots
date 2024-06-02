<?php

require 'db.php';

session_start();

if (is_null($_SESSION['username']))
  die;

$username = $_SESSION['username'];
$ext = pathinfo($_FILES['pfp']['name'], PATHINFO_EXTENSION);
$pfppath = uniqid("{$username}_") . ".$ext";
$dest = __DIR__ . "\\img\\$pfppath";

move_uploaded_file($_FILES['pfp']['tmp_name'], $dest);

$username = mysqli_real_escape_string($mysqli, $username);
$pfppath = mysqli_real_escape_string($mysqli, $pfppath);

mysqli_multi_query($mysqli,
    'SET @id = UUID_SHORT();'
  . "INSERT INTO `images` VALUES (@id, '$pfppath');"
  . "UPDATE `users` SET `pfp` = @id WHERE `username` = '$username';"
);

header('Location: ../profile.php');
die;
