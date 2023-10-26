<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'auction_db_test');

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$dbc) {
  die('Connection failed to the database ' . mysqli_connect_error());
}
// exit();
// mysqli_close($dbc);

