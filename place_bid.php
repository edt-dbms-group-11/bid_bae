<?php
  include_once("database.php");
  include_once("session_check.php");
  session_start();

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if bid is not empty
    if (empty($_POST["bid_amount"])) {
        echo "Bid is required";
        refreshBack();
    } else {
      $bid = test_input(($_POST["bid_amount"]));
      $auction_id = test_input(($_POST["auction_id"]));
      $user_id = test_input(($_POST["user_id"]));
      $current_bid = test_input(isset($_POST["current_bid"]));

      if (!preg_match("/^[0-9]*$/",$bid)) {
        echo "Only numbers allowed";
        refreshBack();
      } else if ($bid < $current_bid) {
        echo "Bid must be higher than current bid";
      } else {
        insertBid($bid, $auction_id, $user_id, $current_bid);
      }
    }
}

  function insertBid($bid, $auction_id, $user_id, $current_bid) {
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
      echo json_encode(['status' => 'error', 'message' => 'Please retry, bid failed to place']);
    }

      mysqli_stmt_close($stmt);
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

  function refreshBack() {
    header("refresh:1;url=listing.php?auction_id=$auction_id.php");
  }

?>