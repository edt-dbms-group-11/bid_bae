<?php
include_once("header.php");
include_once("utilities.php");
include_once("database_functions.php");
?>

<div class="container">

<h2 class="my-3">Browse all auctions</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php">
  <div class="row d-flex">
    <div class="col-md-4 align-self-end pr-0">
      <div class="form-group">
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" name="keyword" class="form-control border-left-0" id="keyword" placeholder='Search for keywords' value=<?php echo (!isset($_GET['keyword'])) ? '' : $_GET['keyword']; ?>>
        </div>
      </div>
    </div>
    <div class="col-md-3 align-self-end pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <?php $categories = getCategoriesFromDatabase(); ?>
        <select class="form-control" id="cat" name="cat">
          <option <?php echo (!isset($_GET['cat']) || $_GET['cat'] === 'all') ? 'selected' : ''; ?> value="all">All categories</option>
          <?php foreach ($categories as $category): ?>
            <option <?php echo (isset($_GET['cat']) && $_GET['cat'] === $category['id']) ? 'selected' : ''; ?> value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="col-md-2 align-self-center pr-0">
      <div class="form-group">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" id="order_by" name="order_by">
          <option <?php echo (!isset($_GET['order_by']) || $_GET['order_by'] === 'date') ? 'selected' : ''; ?> value="date">Soonest expiry</option>
          <option <?php echo (isset($_GET['order_by']) && $_GET['order_by'] === 'pricelow') ? 'selected' : ''; ?> value="pricelow">Price (low to high)</option>
          <option <?php echo (isset($_GET['order_by']) && $_GET['order_by'] === 'pricehigh') ? 'selected' : ''; ?> value="pricehigh">Price (high to low)</option>
        </select>
      </div>
    </div>
    <div class="col-md-2 align-self-center pr-0">
      <div class="form-group">
        <label class="mx-2" for="status">Auction Status:</label>
        <select class="form-control" id="status" name="status">
          <option <?php echo (!isset($_GET['status']) || $_GET['status'] === 'running') ? 'selected' : ''; ?> value="running">In Progress</option>
          <option <?php echo (isset($_GET['status']) && $_GET['status'] === 'tostart') ? 'selected' : ''; ?> value="tostart">Yet to start</option>
          <option <?php echo (isset($_GET['status']) && $_GET['status'] === 'ended') ? 'selected' : ''; ?> value="ended">Ended</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 align-self-end mb-3 pr-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>

<?php
  // Retrieve these from the URL
  if (!isset($_GET['keyword'])) {
    $keyword = '';
  }
  else {
    $keyword = $_GET['keyword'];
  }

  if (!isset($_GET['cat'])) {
    $category = 'all';  // Handled in the DB function
  }
  else {
    $category = $_GET['cat'];
  }
  
  if (!isset($_GET['order_by'])) {
    $ordering = 'date';
  }
  else {
    $ordering = $_GET['order_by'];
  }
  
  if (!isset($_GET['status'])) {
    $status = 'running';
  }
  else {
    $status = $_GET['status'];
  }

  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }
  
  $results_per_page = 10;

  $queriedAuctions = getAuctionsFromDatabaseWithParameters($ordering, $category, $keyword, $status, $curr_page, $results_per_page);

  $row_count = getRowCount();
  $num_results = $row_count;  
  $max_page = ceil($num_results / $results_per_page);
?>

<div class="container mt-5">
<ul class="list-group">

<?php

  if ($row_count == 0) {
    echo '<div class="alert alert-warning mt-3" role="alert">';
    echo 'Sorry, no results found. We apologize for the inconvenience.';
    echo '</div>';
  }
  else {
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
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
    
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }
    
    // Do this in any case
    echo('
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>


</div>

<?php include_once("footer.php")?>