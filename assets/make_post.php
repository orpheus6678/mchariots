<?php

require 'db.php';

session_start();

if (is_null($_SESSION['username']))
  die;

$username = $_SESSION['username'];
$text = mysqli_real_escape_string($mysqli, $_POST['text']);

for ($i = 0; $i < count($_FILES['images']['name']); $i++)
{
  $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
  $pfppath = uniqid("{$username}_") . ".$ext";
  $dest = __DIR__ . "\\img\\$pfppath";

  move_uploaded_file($_FILES['images']['tmp_name'][$i], $dest);
  $pfppath_arr[] = mysqli_real_escape_string($mysqli, $pfppath);
}

$username = mysqli_real_escape_string($mysqli, $_SESSION['username']);
$datetime = date("Y-m-d H:i:s");

mysqli_multi_query($mysqli,
  'SET @id = UUID_SHORT();'
  . "INSERT INTO `posts` VALUES (@id, '$text', '$datetime', '$username', NULL);"
  . "SELECT @id AS `id`;"
);

mysqli_next_result($mysqli);
mysqli_next_result($mysqli);
['id' => $id ] = mysqli_fetch_assoc(mysqli_store_result($mysqli));
mysqli_next_result($mysqli);

foreach ($pfppath_arr as $pfppath)
{
  mysqli_multi_query(
    $mysqli,
      'SET @id = UUID_SHORT();'
    . "INSERT INTO `images` VALUES (@id, '$pfppath');"
    . "INSERT INTO `posts_images` VALUES ($id, @id);"
  );

  mysqli_next_result($mysqli);
  mysqli_next_result($mysqli);
  mysqli_next_result($mysqli);
}

header('Location: ../profile.php');
die;
