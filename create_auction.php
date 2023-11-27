<?php 
include_once("header.php");
include_once("database.php"); 
include_once("database_functions.php"); 
?>


<?php
// If user is not logged in, they should not be able to use this page.
if (!isset($_SESSION) || $_SESSION == null)  {
  echo('<div class="text-center">You\'re not logged in. Please re-login if this was a mistake</div>');
  header('refresh:3;url=browse.php');
}

$seller_id = $_SESSION['id'];
?>

<div class="container">

<!-- Create auction form -->
<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Create new auction</h2>
  <?php 
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
      echo('<div class="alert alert-danger" role="alert">Error creating auction. Please fix the following issues:<br>');
      foreach ($_SESSION['errors'] as $error) {
          echo('- ' . $error . '<br>');
      }
      echo('</div>');

      unset($_SESSION['errors']);
  }?>
  <div class="card">
    <div class="card-body">
      <form method="post" action="create_auction_result.php">
        <div class="form-group row">
          <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="auctionTitle" name="auctionTitle" placeholder="e.g. Black mountain bike">
            <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A short description of the item you're selling, which will display in listings.</small>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label text-right">Select Items</label>
          <div class="col-sm-10">
              <?php
              //$seller_id = "5";
              // Retrieve seller's items
              $seller_items = getSellerItems($seller_id);
              // Display checkboxes for each item
              foreach ($seller_items as $item) {
                  echo '<div class="form-check">';
                  echo '<input class="form-check-input" type="checkbox" name="selectedItems[]" value="' . $item['id'] . '">';
                  echo '<label class="form-check-label">' . $item['name'] . '</label>';
                  echo '</div>';
              }
              ?>
              <small id="selectHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Items already listed in other auctions are not available.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="auctionDetails" name="auctionDetails" rows="4"></textarea>
            <small id="detailsHelp" class="form-text text-muted">Full details of the listing to help bidders decide if it's what they're looking for.</small>
          </div>
        </div>
        <!-- <div class="form-group row">
          <label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category</label>
          <div class="col-sm-10">
            // Load categories dynamically from the database 
            <?php
            $categories = getCategoriesFromDatabase(); // Implement a function to fetch categories
            ?>
            <select class="form-control" id="auctionCategory" name="auctionCategory">
              <option selected>Choose...</option>
              <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
              <?php endforeach; ?>
            </select>
            <small id="categoryHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select a category for this item.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionImages" class="col-sm-2 col-form-label text-right">Item Images</label>
          <div class="col-sm-10">
            <input type="file" class="form-control-file" id="auctionImages" multiple accept="image/*">
            <small id="imagesHelp" class="form-text text-muted">Upload images of the item. You can select multiple images.</small>
          </div>
        </div> -->
        <div class="form-group row">
          <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
          <div class="col-sm-10">
          <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input min="1" type="number" class="form-control" id="auctionStartPrice" name="auctionStartPrice">
            </div>
            <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Initial bid amount.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input min="1" type="number" class="form-control" id="auctionReservePrice" name="auctionReservePrice">
            </div>
            <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate">
            <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
          </div>
        </div>
        <!-- <input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>"> -->
        <button type="submit" name= "auctionsubmit" class="btn btn-primary form-control">Create Auction</button>
      </form>
    </div>
  </div>
</div>

</div>


<?php include_once("footer.php")?>