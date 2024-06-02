<?php

$dbname = 'mchariots';
$mysqli = mysqli_connect('localhost', 'root', '', $dbname);

if (!$mysqli)
die('dberror: ' . mysqli_connect_error());
