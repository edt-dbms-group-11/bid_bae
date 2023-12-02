<?php
include_once("header.php");
include_once("database_functions.php");

$user_id = $_SESSION['id']; // Assuming user is logged in
?>

<?php 

function modifyDeleteAuctions($auction_id, $title, $desc, $price, $num_bids, $end_time, $status) {
  // Truncate long descriptions
  if (strlen($desc) > 150) {
    $desc_shortened = substr($desc, 0, 150) . '...';
  } else {
    $desc_shortened = $desc;
  }

  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  } else {
    $bid = ' bids';
  }

  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  } else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }

  // Print HTML
  echo ('
  <li class="list-group-item row d-flex justify-content-between">
    <div class="p-2 mr-5 text-left col-6" style="max-width: 400px;">
      <h5><a href="listing.php?auction_id=' . $auction_id . '">' . $title . '</a></h5>
      <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 400px;">
        ' . $desc_shortened . '
      </div>
    </div>
    
    <div class="text-right col-4">
      <div style="font-size: 1.5em; margin-bottom: 5px;">£' . number_format($price, 2) . '</div>
      <div>' . $num_bids . $bid . '</div>
      <div>' . $time_remaining . '</div>
    </div>');
    if ($status === "INIT") {
      echo('<div class="text-center col-2">
      <a href="modify_auction.php?auction_id=' . $auction_id . '" class="btn btn-primary" style="margin:5px background-color: ##63db67; padding: 8px 25px; width: 150px">Modify</a>
      <br/> 
      <a href="delete_auction.php?auction_id=' . $auction_id . '" class="btn btn-danger submit" style="margin:5px background-color: #f56056; padding: 8px 25px; width: 150px">Delete</a>
    </div>
 </li>');
    }
    else {
      echo('<div class="text-center col-2">
      <a href="modify_auction.php?auction_id=' . $auction_id . '" class="btn btn-primary" style="margin:5px background-color: ##63db67; padding: 8px 25px; width: 150px">Modify</a>
      <br/>
      <a href="delete_auction.php?auction_id=' . $auction_id . '" class="btn btn-danger submit" style="margin:5px background-color: #f56056; padding: 8px 25px; width: 150px">Delete</a>
    </div>
 </li>');
    }
}
?>

<div class="container">

  <h2 class="my-3">My listings</h2>

  <!-- Filter options -->
  <form id="filterForm" method="get" action="mylistings.php">
    <div class="row mb-3">
      <div class="col-md-3">
        <div class="form-group">
          <select class="form-control" id="filter_by" name="filter_by">
            <option value="live"<?php echo (isset($_GET['filter_by']) && $_GET['filter_by'] === 'live') ? 'selected' : ''; ?>>Live Auctions</option>
            <option value="ended" <?php echo (isset($_GET['filter_by']) && $_GET['filter_by'] === 'ended') ? 'selected' : ''; ?>>Ended Auctions</option>
            <option value="not_started" <?php echo (isset($_GET['filter_by']) && $_GET['filter_by'] === 'not_started') ? 'selected' : ''; ?>>Future Auctions</option>
            <option value="all" <?php echo (!isset($_GET['filter_by']) || $_GET['filter_by'] === 'all') ? 'selected' : ''; ?>>All Auctions</option>
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Apply Filter</button>
      </div>
    </div>
  </form>
  <?php
    if( ($_GET['filter_by'] !== 'not_started') && getUserAuctionsByFilter($user_id, $_GET['filter_by'])) {
      echo '<div class="container mt-5">';
      echo '<div class="alert alert-danger" role="alert">';

      switch ($_GET['filter_by']) {
          case 'ended':
              echo "Ended auctions can't be modified or deleted.";
              break;
  
          case 'live':
              echo "Live auctions can't be modified or deleted.";
              break;
  
          case 'all':
              echo "Live and Ended auctions can't be modified or deleted.";
              break;
  
          default:
              echo "Live and Ended auctions can't be modified or deleted.";
              break;
      }

      echo '</div>';
      echo '</div>';
  }  
  ?>
  <div class="container mt-5">
    <ul class="list-group" id="auctions_container">
        <!-- Loop through user auctions and print a list item for each auction -->
        <?php

        $filter_by = isset($_GET['filter_by']) ? $_GET['filter_by'] : 'all';
        $user_auctions = getUserAuctionsByFilter($user_id, $filter_by);

        // Check if there are auctions to display
        if (empty($user_auctions)) {
            // Display a message or badge for no results
            echo '<div class="alert alert-info" role="alert">You have no listings.</div>';
        } else {
            // Proceed to display the list of auctions
            echo '<div class="container mt-5">';
            echo '<ul class="list-group">';

            // Loop through user auctions and print a list item for each auction
            foreach ($user_auctions as $auction) {
                $auction_id = $auction['auction_id'];
                $title = $auction['title'];
                $description = $auction['description'];
                $current_price = $auction['current_price'];
                $num_bids = $auction['num_bids'];
                $end_date = new DateTime($auction['end_time']);
                $status = $auction['status'];

                // Using the function defined in utilities.php
                modifyDeleteAuctions($auction_id, $title, $description, $current_price, $num_bids, $end_date, $status);
            }
            echo '</ul>';
            echo '</div>';
        }
        ?>
    </ul>

</div>

<?php include_once("footer.php") ?>
