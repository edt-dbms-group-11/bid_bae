<?php
include_once("database.php");
include("database_functions.php");
include_once("send_mail.php");

$auction_id = test_input(($_POST["auction_id"]));
$user_detail;
$seller_id;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if bid is not empty
  if (empty($_POST["bid_amount"])) {
    echo "Bid is required";
    refreshBack($auction_id);
  } else {
    $bid = test_input(($_POST["bid_amount"]));
    $user_id = test_input(($_POST["user_id"]));
    $current_bid = test_input(isset($_POST["current_bid"]));
    $seller_id = test_input(isset($_POST["seller_id"]));

    if (!preg_match("/^[0-9]*$/", $bid)) {
      echo "Only numbers allowed";
      refreshBack($auction_id);
    } else if ($bid <= $current_bid) {
      echo "Bid must be higher than current bid";
    } else {
      insertBid($bid, $auction_id, $user_id, $current_bid, $seller_id);
    }
  }
}

function validateSufficientBalance($bid_amt)
{
  global $connection, $user_detail;
  return $user_detail['balance'] >= $bid_amt;
}

function validateSelfOwnBid()
{
  global $connection, $user_detail, $seller_id;
  if ($user_detail['id'] === $seller_id) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'You cannot bid on your own auction!']);
    exit();
  }
}

function insertBid($bid, $auction_id, $user_id, $current_bid, $seller_id)
{
  global $user_detail;
  $user_detail = queryUserById($user_id);
  $isBalanceSuffice = validateSufficientBalance($bid);
  validateSelfOwnBid();

  if ($isBalanceSuffice) {
    global $connection;
    updateCurrentUserBalances($bid);
    updateHighestBidderBalances($auction_id);
    $insert_bid_query = "INSERT INTO Bid (auction_id, user_id, bid_price) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insert_bid_query);
    if ($stmt === false) {
      die('Error preparing the statement: ' . mysqli_error($connection));
    }
    mysqli_stmt_bind_param($stmt, "iid", $auction_id, $user_id, $bid);
    $insert_bid_result = mysqli_stmt_execute($stmt);
    if ($insert_bid_result === false) {
      die('Error executing the statement: ' . mysqli_error($connection));
    }

    if (mysqli_affected_rows($connection) > 0) {
      updateCurrentAuction($bid, $auction_id);
    } else {
      http_response_code(400);
      echo json_encode(['status' => 'error', 'message' => 'Please retry, bid failed to place']);
    }

    mysqli_stmt_close($stmt);

  } else {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'You have insufficient unlocked balance. Please top up first!']);
    exit();
  }
}

function updateCurrentUserBalances($bidAmount)
{
  global $connection, $user_detail;

  $new_unlocked_balance = $user_detail['balance'] - $bidAmount; // Guaranteed to be positive integer since we verify the balance > bidAmount
  $new_locked_balance = $user_detail['locked_balance'] + $bidAmount;
  $update_user_balances_query = "UPDATE User SET balance = ?, locked_balance = ? WHERE id = ?";

  $stmt = mysqli_prepare($connection, $update_user_balances_query);
  if ($stmt === false) {
    die('Error preparing the statement: ' . mysqli_error($connection));
  }
  mysqli_stmt_bind_param($stmt, "ddi", $new_unlocked_balance, $new_locked_balance, $user_detail['id']);
  $update_balance_result = mysqli_stmt_execute($stmt);
  if ($update_balance_result === false) {
    die('Error executing the statement: ' . mysqli_error($connection));
  }

}

function updateHighestBidderBalances($auctionID)
{
  global $connection;

  $highest_bidder_query = "SELECT user_id, bid_price FROM Bid WHERE auction_id = ? ORDER BY bid_timestamp DESC LIMIT 1";

  $stmt = mysqli_prepare($connection, $highest_bidder_query);
  if ($stmt === false) {
    die('Error preparing the statement: ' . mysqli_error($connection));
  }
  mysqli_stmt_bind_param($stmt, "i", $auctionID);
  $highest_bidder_result = mysqli_stmt_execute($stmt);
  if ($highest_bidder_result === false) {
    die('Error executing the statement: ' . mysqli_error($connection));
  }
  mysqli_stmt_bind_result($stmt, $col1, $col2);
  $highest_bidder_user_id = NULL;
  $last_highest_bid = NULL;

  while (mysqli_stmt_fetch($stmt)) {
    $highest_bidder_user_id = $col1;
    $last_highest_bid = $col2;
  }

  $highest_bidder_details = queryUserById($highest_bidder_user_id);

  $new_unlocked_balance = $highest_bidder_details['balance'] + $last_highest_bid;
  $new_locked_balance = $highest_bidder_details['locked_balance'] - $last_highest_bid;

  $update_user_balances_query = "UPDATE User SET balance = ?, locked_balance = ? WHERE id = ?";

  $stmt = mysqli_prepare($connection, $update_user_balances_query);
  if ($stmt === false) {
    die('Error preparing the statement: ' . mysqli_error($connection));
  }
  mysqli_stmt_bind_param($stmt, "ddi", $new_unlocked_balance, $new_locked_balance, $highest_bidder_user_id);
  $update_balance_result = mysqli_stmt_execute($stmt);

  // Email previous bidder they have been outbid
  sendOutbidEmail($highest_bidder_details['display_name'], $highest_bidder_details['email'], $highest_bidder_details['opt_in_email'], $last_highest_bid, $auctionID);

  if ($update_balance_result === false) {
    die('Error executing the statement: ' . mysqli_error($connection));
  }

}

function updateCurrentAuction($bid, $auction_id)
{
  global $connection;

  $update_current_auction_query = "UPDATE Auction SET current_price = ? WHERE id = ?";
  $stmt = mysqli_prepare($connection, $update_current_auction_query);
  if ($stmt === false) {
    die('Error preparing the statement: ' . mysqli_error($connection));
  }
  mysqli_stmt_bind_param($stmt, "ii", $bid, $auction_id);
  $update_current_auction_result = mysqli_stmt_execute($stmt);
  if ($update_current_auction_result === false) {
    die('Error executing the statement: ' . mysqli_error($connection));
  }

  if (mysqli_affected_rows($connection) > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Thank you! Bid successfully placed!']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Please retry, bid failed to place']);
  }

  mysqli_stmt_close($stmt);
}

function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function refreshBack($auction_id)
{
  header("refresh:1;url=listing.php?auction_id=$auction_id.php");
}

?>