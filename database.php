<?php
  $connection = mysqli_connect("localhost", "root", "root", "auction_db_test");

  if (mysqli_connect_errno())
    echo 'Failed to connect to the MySQL server: '. mysqli_connect_error();

?>