<?php

require 'util.php';

session_start();

if (isset($_SESSION['username'])) {
  header('Location: home.php');
  die;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require 'db.php';

  $fullname = $_POST['fullname'] ?? '';
  $email = $_POST['email'] ?? '';
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $repassword = $_POST['repassword'] ?? '';
  $dob = $_POST['dob'] ?? '';
  $gender = $_POST['gender'] ?? '';

  if ($fullname === '')
    $fullname = null;
  else if (
    filter_var(
      $fullname,
      FILTER_VALIDATE_REGEXP,
      ['options' => ['regexp' => '/^(?=.*\S$)\S.*$/']]
    ) === false  // '0' is a valid fullname (yes)
  )
  refresh_with_cookie('invalid');
  
  if ($email === '')
    $email = null;
  else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    refresh_with_cookie('invalid');

  if (!filter_var(
    $username,
    FILTER_VALIDATE_REGEXP,
    ['options' => ['regexp' => '/^(?=.*?[a-z])\w*$/i']]
  ))
  refresh_with_cookie('invalid');

  if (!filter_var(
    $password,
    FILTER_VALIDATE_REGEXP,
    ['options' => ['regexp' => '/^(?=.*?[a-z])(?=.*?\d)\w{8,30}$/i']]
  ))
  refresh_with_cookie('invalid');

  if ($password <> $repassword)
  refresh_with_cookie('invalid');

  preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dob, $date_arr);
  list(, $yyyy, $mm, $dd) = count($date_arr)? $date_arr : [0, 0, 0, 0];
  $max_dob = date('Y-m-d', mktime(0, year: idate('Y') - 13));
  
  if ( !checkdate($mm, $dd, $yyyy)
    OR $dob < '1970-01-01'
    OR $dob > $max_dob
  )
  refresh_with_cookie('invalid');
  
  if ($gender === '')
    $gender = null;
  else if ($gender !== 'male' AND $gender !== 'female')
    refresh_with_cookie('invalid');

  $hash = password_hash($password, PASSWORD_DEFAULT);

  try {
    mysqli_execute_query(
      $mysqli,
      "INSERT INTO `users` VALUES (?, ?, ?, ?, ?, ?)",
      [$username, $hash, $email, $fullname, $dob, $gender]
    );

    refresh_with_cookie('regsuccess', location: 'login.php');
  } catch (mysqli_sql_exception $e) {
    if ($e->getCode() !== 1062)
    die('Unexpected error: ' . $e->getMessage());

    preg_match("/Duplicate entry '.*?' for key '(.*?)'/", $e->getMessage(), $e_info);
    [, $key] = $e_info;
    $key = ($key === 'PRIMARY')? 'username' : $key;
    refresh_with_cookie('exists', $key);
  }
}

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Sign Up | Modern Chariots</title>
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

      label:has(+ input[required])::after { content: ' *'; }

      .gender {
        padding-left: 0;
        margin-block: 0;
        display: flex;
        justify-content: space-around;
      }

      .requirements {
        border: 1px solid black;
        width: max-content;
        padding: 1em;
        padding-left: 2em;
      }

      .error {
        width: max-content;
        color: white;
        background-color: red;
        margin-top: 1em;
        padding-block: .2em;
        padding-inline: 1em;
      }
    </style>
  </head>
  <body>
    <h1>Become a Part of Modern Chariots</h1>

    <form method=post novalidate>
      <label for=fullname>Full Name</label>
      <input type=text name=fullname id=fullname>

      <label for=male>Gender</label>
      <ul class=gender>
        <label><input type=radio name=gender id=male value=male> Male</label>
        <label><input type=radio name=gender value=female> Female</label>
      </ul>

      <label for=dob>Date of Birth</label>
      <input type=date name=dob id=dob required>

      <label for=email>Email</label>
      <input type=email name=email id=email>

      <label for=username>Username</label>
      <input type=text name=username id=username required placeholder="eg. orpheus6678">

      <label for=password>Password</label>
      <input type=password name=password id=password required>
      
      <label for=repassword>Retype Password</label>
      <input type=password name=repassword id=repassword required>
      
      <button>Sign Up</button>
    </form>

    <!-- Didn't think I'd crave features from the latest CSS spec... -->
    <?php if (isset($_COOKIE['invalid']) OR isset($_COOKIE['exists'])): ?>
      <div class=error>
      <?php
      if (isset($_COOKIE['invalid']))
        echo "Requirements violated.\n";
      else if (isset($_COOKIE['exists']))
        echo ucfirst($_COOKIE['exists']) . " already exists.\n";
      ?>
      </div>
    <?php endif; ?>

    <p>Already a member? <a href=login.php>Login.</a></p>

    <ul class=requirements>
      <li>Required fields are indicated with an asterisk.</li>
      <li>The username and password can only contain english letters and numbers.</li>
      <li>The username and password must not contain whitespace.</li>
      <li>The username must contain a letter.</li>
      <li>The password must contain a letter, a number and must be within 8-30 characters.</li>
      <li>You must be at least 13 years old. People from the 70s are unwelcome.</li>
      <li>The fields cannot contain opening or trailing whitespace.</li>
    </ul>
  </body>
</html>

<?php

delete_cookie('invalid');
delete_cookie('exists');
