 <?php
include_once('database.php');
session_start();
if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Extract arguments from the POST variables:
$auction_id = $_POST['arguments'][0];
$user_id = $_POST["user_id"];

if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
  $sql_add_to_watchlist = "INSERT INTO watchlist(auction_id, user_id) VALUES ($auction_id,$user_id)";
  $result_add_to_watchlist = mysqli_query($connection,$sql_add_to_watchlist);

  $res = "success";
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.

  $res = "success";
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

?>