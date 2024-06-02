<?php

function refresh_with_cookie($cookie, $value = 'true', $location = null)
{
  $location = $location ?? $_SERVER['SCRIPT_NAME'];
  setcookie($cookie, $value);
  header("Location: $location");
  exit;
}

function delete_cookie($cookie)
{
  setcookie($cookie, null, -1);
}

function show_post($postid)
{
  require 'db.php';

  ['text' => $text ] = mysqli_fetch_assoc(mysqli_execute_query(
    $mysqli,
    'SELECT * FROM `posts` WHERE `id` = ?', [$postid]
  ));

  $result = mysqli_execute_query(
    $mysqli,
    'SELECT `path` FROM `images`
     JOIN `posts_images` ON `images`.`id` = `posts_images`.`imgid`
     JOIN `posts` ON `posts`.`id` = `posts_images`.`postid`
     WHERE `posts`.`id` = ?',
    [$postid]
  );

  echo '<div class=post>';
  echo "<p>$text</p>";
  foreach ($result as $row)
    echo '<img src="assets/img/' . $row['path'] . '">';
  echo '</div>';
}
