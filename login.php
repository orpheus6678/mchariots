<?php

require 'assets/util.php';

session_start();

if (isset($_SESSION['username'])) {
  header('Location: home.php');
  die;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require 'assets/db.php';
  
  $username = $_POST['username'];
  $password = $_POST['password'];

  $result = mysqli_execute_query(
    $mysqli,
    "SELECT `hash` FROM `users` WHERE `username` = ?",
    [$username]
  );

  $row = mysqli_fetch_assoc($result);
  $hash = mysqli_num_rows($result)? $row['hash'] : null;

  if (!$hash OR !password_verify($password, $hash))
    refresh_with_cookie('incorrect');

  $_SESSION['username'] = $username;
  header('Location: home.php');

  die;
}

?>

<!DOCTYPE html>
<html>
  <head>
    <link rel=stylesheet href=normalize.css>
    <link rel=stylesheet href=global.css>
    <style>
      form {
        display: grid;
        width: max-content;
        grid-template-columns: 1fr 1.5fr;
        gap: 1em;
      }

      form > button {
        padding-inline: 1em;
        grid-column: 2;
        justify-self: end;
      }
    </style>
    <title>Login | MChariots</title>
  </head>
  <body>
    <h1>Welcome to Modern Chariots</h1>
    <form method=post>
      <label for=username>Username</label>
      <input type=text name=username id=username>

      <label for=password>Password</label>
      <input type=password name=password id=password>

      <button>Login</button>
    </form>

    <?php if (isset($_COOKIE['incorrect'])): ?>
      <p>Incorrect username or password.</p>
    <?php elseif (isset($_COOKIE['regsuccess'])): ?>
      <p>Registration successful.</p>
    <?php endif; ?>

    <p>New to Modern Chariots? <a href=register.php>Sign up.</a></p>
  </body>
</html>

<?php

delete_cookie('incorrect');
delete_cookie('regsuccess');
