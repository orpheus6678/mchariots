<?php

require 'db.php';

session_start();

if (is_null($_SESSION['username']))
  die;

$username = $_SESSION['username'];
$text = $_POST['text'];
$postid = $_POST['postid'];
$datetime = date("Y-m-d H:i:s");

mysqli_execute_query(
  $mysqli,
  'INSERT INTO `comments` (`created`, `postid`, `username`, `text`)
  VALUES (?, ?, ?, ?)',
  [$datetime, $postid, $username, $text]
);

header('Location: ' . $_SERVER['HTTP_REFERER']);
die;
