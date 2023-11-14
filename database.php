<?php
  $serverhost = getenv('DB_HOST');
  $username = getenv('DB_USER');
  $password = getenv('DB_PASS');
  $dbname = getenv('DB_NAME');
  $connection = mysqli_connect($serverhost, $username, $password, $dbname);

  if (mysqli_connect_errno())
    echo 'Failed to connect to the MySQL server: '. mysqli_connect_error();

?>
