<?php

session_start();

if (is_null($_SESSION['username'])) {
  header('Location: login.php');
  die;
}

require 'assets/db.php';
require 'assets/util.php';

?>

<!DOCTYPE html>
<html>
  <head>
    <link rel=stylesheet href=global.css>
    <title>Home | MChariots</title>

    <style>
      h1 { text-align: center; }
      main { display: flex; }
      .query {
        display: grid;
        grid-template-columns: auto auto;
        align-content: start;
        row-gap: .5em;
        margin-right: 1em;
      }

      .query > label { grid-column: 1; }
      .query > select { 
        justify-self: center;
        width: 100px;
      }

      .query > button {
        margin-top: 1em;
        justify-self: start;
        padding: 1em;
      }
    </style>
  </head>
  <body>
    <?php require 'assets/navbar.html'; ?>
    <h1>Welcome to Modern Chariots Homepage</h1>
      <main>
        <form class=query method=post>
          <h3>Filter results by</h3>
            <label for=filter-user>Specific User</label>
            <input type=text name=filter-user id=filter-user>

            <label for=car-model>Car Model</label>
            <select name=car id=car-model>
              <option value="">--Choose--</option>
              <?php
              $result = mysqli_execute_query($mysqli, 'SELECT `name` FROM `cars`');
              
              foreach ($result as $row)
                echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
              ?>
            </select>
          
            <label for=filter-manf>Manufacturer</label>
            <select name=manufacturer id=filter-manf>
            <option value="">--Choose--</option>
            <?php
            $result = mysqli_execute_query($mysqli, 'SELECT `name` FROM `manufacturers`');
            
            foreach ($result as $row)
              echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
            ?>
            </select>
          
            <label for=filter-origin>Origin</label>
            <select name=origin id=filter-origin>
            <option value="">--Choose--</option>
            <?php
            $result = mysqli_execute_query($mysqli, 'SELECT DISTINCT `origin` FROM `manufacturers`');
            
            foreach ($result as $row)
              echo '<option value="' . $row['origin'] . '">' . $row['origin'] . '</option>';
            ?>
            </select>
          
            <label for=drive-train>Drive Train</label>
            <select name=drive-train id=drive-train>
            <option value="">--Choose--</option>
            <?php
            $result = mysqli_execute_query($mysqli, 'SELECT DISTINCT `drive_train` FROM `cars`');
            
            foreach ($result as $row)
              echo '<option value="' . $row['drive_train'] . '">' . $row['drive_train'] . '</option>';
            ?>
            </select>

            <label for=car-type>Car Type</label>
            <select name=car-type id=car-type>
            <option value="">--Choose--</option>
            <?php
            $result = mysqli_execute_query($mysqli, 'SELECT DISTINCT `type` FROM `cars`');
            
            foreach ($result as $row)
              echo '<option value="' . $row['type'] . '">' . $row['type'] . '</option>';
            ?>
            </select>
          
            <button>Filter!</button>
        </form>
        <div class=posts>
<?php

$query = 'SELECT `posts`.`id` FROM `posts` ';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
  $where = false;
  $joined_cars = false;
  $join_cars_query = ' JOIN `cars` ON `posts`.`car` = `cars`.`name` ';

  if (
       !empty($_POST['manufacturer'])
    OR !empty($_POST['origin'])
    OR !empty($_POST['drive-train'])
    OR !empty($_POST['car-type']))
  {
    $query .= $join_cars_query;
    $joined_cars = true;
  }

  if (!empty($_POST['manufacturer']) OR !empty($_POST['origin']))
    $query .= ' JOIN `manufacturers` ON `manufacturers`.`name` = `cars`.`manufacturer` ';

  if (!empty($_POST['filter-user'])) {
    $filter_user = mysqli_real_escape_string($mysqli, $_POST['filter-user']);
    $query .= " WHERE `username` = '$filter_user' ";
    $where = true;
  }

  if (!empty($_POST['car'])) {
    $car = mysqli_real_escape_string($mysqli, $_POST['car']);
    $query_part = "`car` = '$car'";
    $query .= ($where)? " AND $query_part " : " WHERE $query_part ";
    $where = true;
  }

  if (!empty($_POST['manufacturer'])) {
    $manufacturer = mysqli_real_escape_string($mysqli, $_POST['manufacturer']);
    $query_part = "`manufacturer` = '$manufacturer'";
    $query .= ($where)? " AND $query_part " : " WHERE $query_part ";
    $where = true;
  }

  if (!empty($_POST['origin'])) {
    $origin = mysqli_real_escape_string($mysqli, $_POST['origin']);
    $query_part = "`origin` = '$origin'";
    $query .= ($where)? " AND $query_part " : " WHERE $query_part ";
    $where = true;
  }

  if (!empty($_POST['drive-train'])) {
    $drive_train = mysqli_real_escape_string($mysqli, $_POST['drive-train']);
    $query_part = "`drive_train` = '$drive_train'";
    $query .= ($where)? " AND $query_part " : " WHERE $query_part ";
    $where = true;
  }

  if (!empty($_POST['car-type'])) {
    $car_type = mysqli_real_escape_string($mysqli, $_POST['car-type']);
    $query_part = "`type` = '$car_type'";
    $query .= ($where)? " AND $query_part " : " WHERE $query_part ";
    $where = true;
  }
}

$result =
  mysqli_execute_query($mysqli, "$query ORDER BY `created` DESC");

foreach ($result as $row)
  show_post($row['id'], show_user: true);

?>
        </div>
      </main>
  </body>
</html>
