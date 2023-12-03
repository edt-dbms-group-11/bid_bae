<?php
session_start();
include_once("database.php");
include("database_functions.php");

$user_id = $_SESSION['id'];
$random_amount = rand(50, 3000);

addBalance($user_id, $random_amount);
echo ('<div class="text-center">Topup Successful!</div>');
header('refresh:0.7;url=browse.php');

exit();
?>