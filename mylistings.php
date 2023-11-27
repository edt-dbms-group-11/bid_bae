<?php
include_once("header.php");
include_once("utilities.php");
include_once("database_functions.php");

$user_id = $_SESSION['id']; // Assuming user is logged in
?>

<div class="container">

  <h2 class="my-3">My listings</h2>

  <!-- Filter options -->
  <form id="filterForm" method="get" action="mylistings.php">
    <div class="row mb-3">
      <div class="col-md-3">
        <div class="form-group">
          <select class="form-control" id="filter_by" name="filter_by">
            <option value="available" <?php echo ($filter_by === 'available') ? 'selected' : ''; ?>>Live Auctions</option>
            <option value="ended" <?php echo ($filter_by === 'ended') ? 'selected' : ''; ?>>Ended Auctions</option>
            <option value="all" <?php echo ($filter_by === 'all') ? 'selected' : ''; ?>>All Auctions</option>
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Apply Filter</button>
      </div>
    </div>
  </form>


  <div class="container mt-5">

    <ul class="list-group" id="auctions_container">
      <!-- Loop through user auctions and print a list item for each auction -->
      <?php

      $filter_by = isset($_GET['filter_by']) ? $_GET['filter_by'] : 'all';
      $user_auctions = getUserAuctionsByFilter($user_id, $filter_by);

      foreach ($user_auctions as $auction) {
        $item_id = $auction['item_id'];
        $title = $auction['title'];
        $description = $auction['description'];
        $current_price = $auction['current_price'];
        $num_bids = $auction['num_bids'];
        $end_date = new DateTime($auction['end_time']);

        // Use the function defined in utilities.php
        print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
      }
      ?>
    </ul>

  </div>

  <?php include_once("footer.php") ?>
