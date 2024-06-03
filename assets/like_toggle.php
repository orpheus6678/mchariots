<?php

session_start();

if (is_null($_SESSION['username'])) {
  header('Location: login.php');
  die;
}

require 'db.php';
require 'util.php';

$username = $_SESSION['username'];
$postid = $_GET['id'];

[ 'liked' => $liked ] = mysqli_fetch_assoc(mysqli_execute_query(
  $mysqli,
  'SELECT EXISTS(SELECT * FROM `likes` WHERE `postid` = ? AND `username` = ?) AS `liked`',
  [$postid, $username]
));

$query = ($liked)? 'DELETE FROM `likes` WHERE `username` = ? AND `postid` = ?'
                 : 'INSERT INTO `likes` VALUES (?, ?)';

mysqli_execute_query(
  $mysqli,
  $query,
  [$username, $postid]
);

header('Location: ' . $_SERVER['HTTP_REFERER']);
die;

?>
