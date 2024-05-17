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
