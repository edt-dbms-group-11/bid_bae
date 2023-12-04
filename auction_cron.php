<?php
include_once("database.php");
include_once("send_mail.php");
include_once("utilities.php");

function findInitStateAuction($connection)
{
  error_log('Executing findInitAuction()');
  $find_init_query = "SELECT * FROM Auction WHERE status = 'INIT' AND start_time <= NOW();";
  $result = mysqli_query($connection, $find_init_query);

  if (!$result) {
    die('Error querying init auctions: ' . mysqli_error($connection));
  }

  while ($row = mysqli_fetch_assoc($result)) {
    $auction_id = $row['id'];
    error_log("Auction ID: $auction_id has started, updating status");
    $updateInitAuction = "UPDATE Auction SET status = 'IN_PROGRESS' WHERE id = $auction_id";
    $updateAuctionResult = mysqli_query($connection, $updateInitAuction);

    if (!$updateAuctionResult) {
      die('Error updating auction to IN_PROGRESS status: ' . mysqli_error($connection));
    }
  }
}

function checkIfBidPlaced($auctionID, $connection)
{
  $bid_count_query = "SELECT count(bid.id) as bid_count
                      FROM Auction auc
                      LEFT JOIN Bid bid ON auc.id = bid.auction_id
                      WHERE auc.id = $auctionID
                      GROUP BY auc.id;";
  $bid_count_query_result = mysqli_query($connection, $bid_count_query);
  if ($bid_count_query_result) {
    while ($bid_count_row = mysqli_fetch_row($bid_count_query_result)) {
      if ($bid_count_row[0] > 0) {
        return true;
      } else {
        return false;
      }
    }
  } else {
    die('Bid Count Query Failed. Error: ' . mysqli_error($connection));
  }
}


function findAuctionWinner($connection, $auction_id)
{
  error_log('Executing findAuctionWinner()');
  $query = "SELECT B.user_id, U.display_name, U.email, U.opt_in_email, U.balance, U.locked_balance, MAX(B.bid_price) AS winning_bid
              FROM Bid B
              INNER JOIN User U ON B.user_id = U.id
              WHERE B.auction_id = $auction_id
              GROUP BY B.user_id, U.display_name
              ORDER BY winning_bid DESC
              LIMIT 1";

  $result = mysqli_query($connection, $query);

  if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row) {
      return [
        'user_id' => $row['user_id'],
        'display_name' => $row['display_name'],
        'email' => $row['email'],
        'email_opt_in' => $row['opt_in_email'],
        'winning_bid' => $row['winning_bid'],
        'balance' => $row['balance'],
        'locked_balance' => $row['locked_balance']
      ];
    } else {
      return null;
    }
  } else {
    die('Error querying winner: ' . mysqli_error($connection));
  }
}


function updateAuctionStatusAndWinner($connection)
{
  error_log("Executing updateAuctionStatusAndWinner()");
  $ending_auction_query = "SELECT id, end_time, reserved_price, seller_id FROM Auction WHERE end_time < NOW() AND status = 'IN_PROGRESS'";
  $result = mysqli_query($connection, $ending_auction_query);

  if ($result) {
    // For each ended auction, perform:
    while ($row = mysqli_fetch_assoc($result)) {
      $auction_id = $row['id'];
      $seller_id = $row['seller_id'];
      $reserved_price = $row['reserved_price'];

      // check if there were bids on the auction:
      if (checkIfBidPlaced($auction_id, $connection)) {
        $winner_info = findAuctionWinner($connection, $auction_id);
        if ($winner_info) {
          $winning_bid_amount = $winner_info['winning_bid'];
          $winner_balance = $winner_info['balance'];
          $winner_locked_balance = $winner_info['locked_balance'];
          $winner_user_id = $winner_info['user_id'];
          // Check if winning bid is higher than reserved price, end both auction
          if ($winning_bid_amount >= $reserved_price) {
            $querySellerBalance = "SELECT balance FROM User WHERE id = $seller_id";
            $sellerBalanceResult = mysqli_query($connection, $querySellerBalance);

            if (!$sellerBalanceResult) {
              die('Error querying seller balance: ' . mysqli_error($connection));
            }

            $sellerBalanceRow = mysqli_fetch_assoc($sellerBalanceResult);
            $seller_balance = $sellerBalanceRow['balance'];

            // new each party balance
            $new_balance_seller = $seller_balance + $winning_bid_amount;
            $new_balance_buyer = $winner_locked_balance - $winning_bid_amount;

            // update
            $updateBalanceToBuyer = "UPDATE User SET locked_balance = $new_balance_buyer WHERE id = $winner_user_id";
            $updateBalanceToSeller = "UPDATE User SET balance = $new_balance_seller WHERE id = $seller_id";

            $updateBalanceToBuyerResult = mysqli_query($connection, $updateBalanceToBuyer);
            $updateBalanceToSellerResult = mysqli_query($connection, $updateBalanceToSeller);

            // Check if balance updates were successful
            if (!$updateBalanceToBuyerResult || !$updateBalanceToSellerResult) {
              die('Error updating balances: ' . mysqli_error($connection));
            }

            $updateQuery = "UPDATE Auction SET status = 'DONE', current_price = $winning_bid_amount, end_price = $winning_bid_amount WHERE id = $auction_id";
            $updateResult = mysqli_query($connection, $updateQuery);

            if (!$updateResult) {
              die('Error updating auction: ' . mysqli_error($connection));
            }

            // Send email to both buyer and seller
            winner_email($winner_info['email'], $winner_info['email_opt_in'], $winner_info['display_name'], $winning_bid_amount, $auction_id);

          } else {
            // Update endprice 0 if no high bids
            $updateQuery = "UPDATE Auction SET status = 'DONE', current_price = $winning_bid_amount, end_price = 0 WHERE id = $auction_id";
            $updateResult = mysqli_query($connection, $updateQuery);
            if (!$updateResult) {
              die('Error updating auction: ' . mysqli_error($connection));
            }
            // Remove last bid locked balance, refund money to user
            $new_locked_balance = $winner_locked_balance - $winning_bid_amount;
            $new_balance = $winner_balance + $winning_bid_amount;
            $updateBalanceQuery = "UPDATE User SET balance = $new_balance, locked_balance = $new_locked_balance WHERE id = $winner_user_id;";
            $updateBalanceResult = mysqli_query($connection, $updateBalanceQuery);
            if (!$updateBalanceResult) {
              die("Could not update user balances");
            }
            // Send email to seller about failed auction
            EmailSellerReservePriceNotMet($auction_id);
          }
          // Set all items as unavailable under the auction 
          updateItemStatusForAuction($connection, $auction_id);
        } else {
          die("Could not fetch winner info");
        }
      } else { // If no bids were placed, email the seller that.
        EmailSellerBidNotPlaced($auction_id);

        $updateQuery = "UPDATE Auction SET status = 'DONE', current_price = 0, end_price = 0 WHERE id = $auction_id";
        $updateResult = mysqli_query($connection, $updateQuery);
        if (!$updateResult) {
          die('Error updating auction: ' . mysqli_error($connection));
        }
      }
    }
  } else {
    die('Error querying auctions: ' . mysqli_error($connection));
  }
}



function updateItemStatusForAuction($connection, $auction_id)
{
  error_log('updateItemStatusForAuction');
  $query = "SELECT item_id FROM Auction_Product WHERE auction_id = $auction_id";
  $result = mysqli_query($connection, $query);

  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $item_id = $row['item_id'];
      $updateQuery = "UPDATE Item SET is_available = 0 WHERE id = $item_id";
      $updateResult = mysqli_query($connection, $updateQuery);

      if (!$updateResult) {
        die('Error updating item availability: ' . mysqli_error($connection));
      }
    }
  } else {
    die('Error querying item IDs: ' . mysqli_error($connection));
  }
}

function main()
{
  global $connection;
  echo ("Cron Initialised.\n");

  while (true) {
    echo ("New cron execution started\n");
    // Find and updates any auction whose start date has been reached but the status is still INIT
    findInitStateAuction($connection);
    // Fund and updates any auctions that have ended (i.e. end time has been reached)
    updateAuctionStatusAndWinner($connection);

    echo ("Sleeping for 5 seconds\n");
    sleep(5); // Sleep for 5 seconds after each execution.
  }


}

// Execute main() to start cron script
main();
?>