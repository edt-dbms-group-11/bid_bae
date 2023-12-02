<?php
  include_once("database.php");
  include_once("database_functions.php");

  global $connection;

  function updateAuctionStatusAndWinner($connection) {
    error_log("updateAuctionStatusAndWinner");
    $ending_auction_query = "SELECT id, end_time, reserved_price, seller_id FROM Auction WHERE end_time < NOW() AND status = 'IN_PROGRESS'";
    $result = mysqli_query($connection, $ending_auction_query);

    if ($result) {
      while ($row = mysqli_fetch_assoc($result)) {
        $auction_id = $row['id'];
        $seller_id = $row['seller_id'];
        $reserved_price = $row['reserved_price'];

        // For each ended auction, get the winner
        $winner_info = findAuctionWinner($connection, $auction_id);

        if ($winner_info) {
          $winning_bid_amount = $winner_info['winning_bid'];
          $winner_balance = $winner_info['balance'];
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
            $new_balance_buyer = $winner_balance - $winning_bid_amount;

            // update
            $updateBalanceToBuyer = "UPDATE User SET balance = $new_balance_buyer WHERE id = $winner_user_id";
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
            
          } else {
            // Update endprice 0 if no high bids
            $updateQuery = "UPDATE Auction SET status = 'DONE', current_price = $winning_bid_amount, end_price = 0 WHERE id = $auction_id";
          }

          if (!$updateResult) {
            die('Error updating auction: ' . mysqli_error($connection));
          }

          // Set all items as unavailable under the auction 
          updateItemStatusForAuction($connection, $auction_id);
        }
      }
    } else {
      die('Error querying auctions: ' . mysqli_error($connection));
    }
  }

  function findAuctionWinner($connection, $auction_id) {
    error_log('findAuctionWinner');
    $query = "SELECT B.user_id, U.display_name, U.balance, MAX(B.bid_price) AS winning_bid
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
          'winning_bid' => $row['winning_bid'],
          'balance' => $row['balance']
        ];
      } else {
        return null;
      }
    } else {
      die('Error querying winner: ' . mysqli_error($connection));
    }
  }

  function updateItemStatusForAuction($connection, $auction_id) {
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

  error_log("bid_winner_cron running");
  updateAuctionStatusAndWinner($connection);
?>
