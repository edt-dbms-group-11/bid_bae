<?php

// Including database connection here
include_once("database.php");

// Helper function to help figure out what time to display
function display_time_remaining($interval) {

    if ($interval->days == 0 && $interval->h == 0) {
      // Less than one hour remaining: print mins + seconds:
      $time_remaining = $interval->format('%im %Ss');
    }
    else if ($interval->days == 0) {
      // Less than one day remaining: print hrs + mins:
      $time_remaining = $interval->format('%hh %im');
    }
    else {
      // At least one day remaining: print days + hrs:
      $time_remaining = $interval->format('%ad %hh');
    }

  return $time_remaining;

}

// This function prints an HTML <li> element containing an auction listing
function print_listing_li($auction_id, $title, $desc, $price, $num_bids, $end_time)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  }
  else {
    $desc_shortened = $desc;
  }
  
  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  }
  else {
    $bid = ' bids';
  }
  
  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  }
  else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }
  
  // Print HTML
  echo('
    <li class="list-group-item d-flex justify-content-between">
    <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened . '</div>
    <div class="text-center text-nowrap"><span style="font-size: 1.5em">£' . number_format($price, 2) . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
  </li>'
  );
}

//used in mylistings.php to retrieve auctions as per user's desired filter
function getUserAuctionsByFilter($user_id, $filter) {
  global $connection;

  // Ensure $filter is a safe value to prevent SQL injection
  $allowed_filters = ['available', 'ended', 'all'];
  if (!in_array($filter, $allowed_filters)) {
      throw new InvalidArgumentException("Invalid filter value provided.");
      return [];
  }

  $sql_query = "SELECT auc.id AS item_id, auc.title, auc.description, auc.current_price, auc.end_time, count(bid.id) as num_bids
  FROM Auction AS auc
  LEFT JOIN Bid as bid on bid.auction_id = auc.id
  WHERE auc.seller_id = $user_id";

  // Add filter conditions to the query
  switch ($filter) {
      case 'available':
        $sql_query .= " AND auc.end_time > NOW()";
        break;
      case 'ended':
        $sql_query .= " AND auc.end_time <= NOW()";
        break;
      default:
        break;
  }

  $sql_query .= " GROUP BY auc.id";

  $result = $connection->query($sql_query);
  $user_auctions = [];

  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          $user_auctions[] = $row;
      }
  }
  return $user_auctions;
}

?>