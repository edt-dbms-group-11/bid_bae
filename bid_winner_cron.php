<?php
  include_once("database.php");
  include_once("send_mail.php");
  include_once("utilities.php");
  global $connection;

  function updateAuctionStatusAndWinner($connection) {
    error_log("updateAuctionStatusAndWinner");
    $query = "SELECT id, end_time, reserved_price FROM Auction WHERE end_time < NOW() AND status = 'IN_PROGRESS' ORDER BY auction.id ASC";
    $result = mysqli_query($connection, $query);

    if ($result) {
      while ($row = mysqli_fetch_assoc($result)) {
        $auction_id = $row['id'];
        $reserved_price = $row['reserved_price'];
        
        // For each ended auction, get the winner
        var_dump($row);
        $winner_info = findAuctionWinner($connection, $auction_id);

        if ($winner_info) {
          $winning_bid_amount = $winner_info['winning_bid'];
          var_dump($winning_bid_amount);
          // Check if winning bid is higher than reserved price, end both auction
          if ($winning_bid_amount >= $reserved_price) {
            $updateQuery = "UPDATE Auction SET status = 'DONE', current_price = $winning_bid_amount, end_price = $winning_bid_amount WHERE id = $auction_id";
          } else {
            $updateQuery = "UPDATE Auction SET status = 'DONE', current_price = $winning_bid_amount, end_price = 0 WHERE id = $auction_id";
          }

          $updateResult = mysqli_query($connection, $updateQuery);

          if (!$updateResult) {
            die('Error updating auction: ' . mysqli_error($connection));
          }
          winner_email($winner_info['user_id'], $winner_info['winning_bid'], $auction_id);
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
    echo $auction_id;
    if(ifBidnotPlaced($auction_id)){
      return 0;
    };
    $query = "SELECT B.user_id, U.display_name, MAX(B.bid_price) AS winning_bid
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
          'winning_bid' => $row['winning_bid']
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

  updateAuctionStatusAndWinner($connection);
?>
