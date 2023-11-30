<style>
  .bg-winner {
    background-color: rgba(201, 242, 155, 0.3) !important;
  }
  .bg-loser {
    background-color: rgba(255, 148, 120, 0.3) !important;
  }
</style>

<?php
  include_once("header.php");
  include_once("utilities.php");
?>

<div class="container pt-2 px-4">
  <h2 class="my-3">My Bids</h2>
</div>

<?php
  $user_id = $_SESSION['id'];
  $curr_page = isset($_GET['page']) ? $_GET['page'] : 1;
  $results_per_page = 2;
  $auctionHistoryList = getPagedAuctionHistory($user_id, $curr_page, $results_per_page);

  $row_count = getRowCount();
  $num_results = $row_count;  
  $max_page = ceil($num_results / $results_per_page);
?>

<?php
  if ($row_count == 0) {
    echo('<div class="container py-3 px-4">');
    echo '<div class="alert alert-info mt-3" role="alert">';
    echo 'You haven\'t participated in any auction! Bid on some, shall we?';
    echo '</div>';
    echo '</div>';
  } else {
    echo('<div class="container py-3 px-4">');
    echo '<div class="list-group">';
    foreach ($auctionHistoryList as $auction) {
      $badgeTheme = getBadgeClass($auction['auction_status']);
      $stat = getAuctionStatusName($auction['auction_status']);
      $wording = getWinnerWording($auction['is_winner']);
      $winnerClass = getWinnerClass($auction['is_winner']);
      $winnerBadge = getWinnerBadge($auction['is_winner']);
      echo('
        <a href="listing.php?auction_id='. $auction['auction_id'].'" class="list-group-item list-group-item-action py-3 px-4 my-2 border rounded ' . $winnerClass . '">
          <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">' . $auction['auction_title'] . '</h5>
            <h6>Sold by: ' . $auction['seller_name'] . '</h6>
          </div>
          <p class="mb-1"><strong>Your highest bid: £' . $auction['bid_price'] . '</strong></p>
          <small>Ends at: ' . $auction['auction_end_time'] . '</small>
          <br>
          <span class="badge badge-pill px-4 py-1 mt-2 ' . $badgeTheme . '">' . $stat . '</span>
          <span class="badge badge-pill px-4 py-1 mt-2 ' . $winnerBadge . '">' . $wording . '</span>
        </a>
      ');
    }
    echo '</div>';
    echo '</div>';
  }
?>

<!-- pagination -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center"> 
<?php
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }

  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
  
  if ($curr_page != 1) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left pb-1"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
    
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      echo('
    <li class="page-item active">');
    }
    else {
      echo('
    <li class="page-item">');
    }
    
    echo('
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right pb-1"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>


<?php include_once("footer.php")?>
