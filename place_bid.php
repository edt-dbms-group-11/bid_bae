<?php
  include_once("database.php");
  include("database_functions.php");

  $auction_id = test_input(($_POST["auction_id"]));
  $user_detail;
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

      if (!preg_match("/^[0-9]*$/",$bid)) {
        echo "Only numbers allowed";
        refreshBack($auction_id);
      } else if ($bid < $current_bid) {
        echo "Bid must be higher than current bid";
      } else {
        insertBid($bid, $auction_id, $user_id, $current_bid, $seller_id);
      }
    }
}

  function validateSufficientBalance ($bid_amt) {
    global $connection;
    return $user_detail['balance'] >= $bid_amt;
  }

  function validateSelfOwnBid () {
    global $connection;
    if ($user_detail['id'] === $seller_id) {
      http_response_code(400);
      header('Content-Type: application/json');
      echo json_encode(['status' => 'error', 'message' => 'You cannot bid on your own auction!']);
      exit();
    }
  }

  function insertBid($bid, $auction_id, $user_id, $current_bid, $seller_id) {
    $user_detail = queryUserById($user_id);
    $isBalanceSuffice = validateSufficientBalance($user_id, $bid);
    $isBidSelfOwned = validateSelfOwnBid($seller_id);

    if ($isBalanceSuffice) {
      global $connection;
  
      $insert_bid_query = "INSERT INTO Bid (auction_id, user_id, bid_price) VALUES (?, ?, ?)";
      $stmt = mysqli_prepare($connection, $insert_bid_query);
      if ($stmt === false) {
          die('Error preparing the statement: ' . mysqli_error($connection));
      }
      mysqli_stmt_bind_param($stmt, "iii", $auction_id, $user_id, $bid);
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
      echo json_encode(['status' => 'error', 'message' => 'You have insufficient balance. Please top up first!']);
      exit();
    }
  }



  function updateCurrentAuction($bid, $auction_id) {
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
  
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function refreshBack($auction_id) {
    header("refresh:1;url=listing.php?auction_id=$auction_id.php");
  }

?>