<?php include_once("header.php") ?>
<?php require("utilities.php") ?>

<div class="container">

  <h2 class="my-3">Recommendations for you</h2>

  <div id="searchSpecs">
    <!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
    <form method="get" action="recommendations.php">
      <div class="row d-flex">
        <div class="col-md-4 align-self-end pr-0">
          <div class="form-group">
            <label for="recs" class="sr-only">Generate recommendations from:</label>
            <p>Generate recommendations from:</p>
            <select class="form-control" id="recs" name="recs">
              <option <?php echo (!isset($_GET['recs']) || $_GET['recs'] === 'bid') ? 'selected' : ''; ?> value="bid">
                Your bids</option>
              <option <?php echo (isset($_GET['recs']) && $_GET['recs'] === 'watchlist') ? 'selected' : ''; ?> value="watchlist">
                Your watchlist</option>
            </select>
          </div>
        </div>
        <div class="col-md-1 align-self-end mb-3 pr-0">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </div>
    </form>
  </div>

  <?php
  $user_id = $_SESSION['id'];

  if (!isset($_GET['recs'])) {
    $mode = 'bid';
  } else {
    $mode = $_GET['recs'];
  }
  if (!isset($_GET['page'])) {
    $curr_page = 1;
  } else {
    $curr_page = $_GET['page'];
  }

  $results_per_page = 10;

  $queriedAuctions = getRecommedationsForUser($user_id, $mode, $curr_page, $results_per_page);

  $row_count = getRowCount();
  $num_results = $row_count;
  $max_page = ceil($num_results / $results_per_page);

  ?>
  <div class="container mt-5">
    <ul class="list-group">

      <?php

      if ($row_count == 0) {
        echo '<div class="alert alert-warning mt-3" role="alert">';
        echo 'Sorry, no recommendations found. Go ahead and bid on auctions or watch some so we can understand your taste.';
        echo '</div>';
      } else {
        foreach ($queriedAuctions as $auction) {
          print_listing_li($auction['id'], $auction['title'], $auction['description'], $auction['current_price'], $auction['bid_count'], $auction['end_time']);
        }
      }
      ?>

    </ul>

    <!-- Pagination for results listings -->
    <nav aria-label="Search results pages" class="mt-5">
      <ul class="pagination justify-content-center">

        <?php

        // Copy any currently-set GET variables to the URL.
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
          echo ('
    <li class="page-item">
      <a class="page-link" href="recommendations.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
        }

        for ($i = $low_page; $i <= $high_page; $i++) {
          if ($i == $curr_page) {
            // Highlight the link
            echo ('
    <li class="page-item active">');
          } else {
            // Non-highlighted link
            echo ('
    <li class="page-item">');
          }

          // Do this in any case
          echo ('
      <a class="page-link" href="recommendations.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
        }

        if ($curr_page != $max_page) {
          echo ('
    <li class="page-item">
      <a class="page-link" href="recommendations.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
        }
        ?>

      </ul>
    </nav>


  </div>

</div>