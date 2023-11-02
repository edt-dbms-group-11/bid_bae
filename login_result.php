<?php
  include_once('database.php');

  $email_input=$_POST['email'];
  $password_input=$_POST['password']; 
  session_start();

  if (!$email_input) {
    echo('Please enter your email');
  } elseif (!$password_input) {
    echo('Please enter your password');
  } elseif (isset($email_input) && isset($password_input)) {
    $query = "SELECT * FROM user WHERE email='$email_input' and password=SHA('$password_input')";
    $result = mysqli_query($connection,$query);
    $row = mysqli_fetch_array($result);

    if ($row) {
      echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');

      $_SESSION['is_logged_in'] = true;
      $_SESSION['username'] = $row[1];
      $_SESSION['display_name'] = $row[4];
      $_SESSION['id'] = $row[0];
    } else {
      echo('Wrong email or password. Please try again.');
      $_SESSION['is_logged_in'] = false;
    }
  }

  header("refresh:3;url=index.php");
?>