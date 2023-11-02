<?php

  include_once('database.php');

  session_start();

  $email=$_POST['email'];
  $username = $_POST['username'];
  $display_name = $_POST['display_name'];
  $opt_in_email = ($_POST['opt_in_email'] === 'on') ? 1 : 0;
  $password=$_POST['password'];
  // $password_confirmation='passwordConfirmation';

  $query = "SELECT * FROM user WHERE email='$email'";
  $result = mysqli_query($connection,$query);
  $row = mysqli_fetch_array($result);

  $query_username = "SELECT * FROM user WHERE username='$username'";
  $result_username = mysqli_query($connection, $query_username);
  $row_username = mysqli_fetch_array($result_username);

  // Validate input
  if ($row) {
    echo("Email already exists");
    header("refresh:3;url=register.php");
    exit();
  }
  if ($row_username) {
    echo("Username already exists");
    header("refresh:3;url=register.php");
    exit();
  }

  $query_register = "INSERT INTO User(`username`, `password`, `email`, `display_name`, `opt_in_email`, `balance`) VALUES ('$username', SHA('$password'),'$email','$display_name', $opt_in_email, 0)";
  $result_register = mysqli_query($connection, $query_register);
  $query_id = "SELECT * FROM User WHERE username='$username'";
  $result = mysqli_query($connection, $query_id);
  if (!$result) {
    die('Invalid query: ' . mysqli_error($connection));
  }
  $row = mysqli_fetch_array($result);

  // Login after registration
  $_SESSION['is_logged_in'] = true;
  $_SESSION['username'] = $username;
  $_SESSION['display_name'] = $display_name;
  $_SESSION['id'] = $row[0];
  $_SESSION['email']= $email;

  echo('<div class="text-center">You\'re all set! You will be redirected shortly.</div>');
  header("refresh:3;url=browse.php");

?>