<!--  -->
<?php include_once("database_functions.php")?>
<?php
/* <?php include_once("header.php")?>(Uncomment this block to redirect people without selling privileges away from this page)
  // If user is not logged in or not a seller, they should not be able to
  // use this page.
  if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
    header('Location: browse.php');
  }
*/
?>
<div class="container">

<!-- Create item form -->
<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Create new item</h2>
  <div class="card">
    <div class="card-body">
      <!-- Note: This form does not do any dynamic / client-side / 
      JavaScript-based validation of data. It only performs checking after 
      the form has been submitted, and only allows users to try once. You 
      can make this fancier using JavaScript to alert users of invalid data
      before they try to send it, but that kind of functionality should be
      extremely low-priority / only done after all database functions are
      complete. -->
      <form method="post" action="create_item_result.php">
      <div class="form-group row">
          <label for="username" class="col-sm-2 col-form-label text-right">Username</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="username" name="username">
            <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
          </div>
        </div>
        <div class="form-group row">
          <label for="itemTitle" class="col-sm-2 col-form-label text-right">Item Name</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="itemTitle" placeholder="e.g. Vase" name="itemTitle">
            <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A short description of the item being registered, which will be displayed on the item page.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="itemDesc" class="col-sm-2 col-form-label text-right">Description</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="itemDesc" rows="4" name="itemDesc"></textarea>
            <small id="detailsHelp" class="form-text text-muted">Full details of the item to help bidders understand the product.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionCategory" class="col-sm-2 col-form-label text-right" >Category</label>
          <div class="col-sm-10">
            <!-- Load categories dynamically from the database -->
            <?php
            $categories = getCategoriesFromDatabase(); // Implement a function to fetch categories
            ?>
            <select class="form-control" id="auctionCategory" name="auctionCategory">
              <option selected>Choose...</option>
              <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name'];?></option>
              <?php endforeach;?>
            </select>
            <small id="categoryHelp" class="form-text text-muted"><span class="text-danger" >* Required.</span> Select a category for this item.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="imageurl" class="col-sm-2 col-form-label text-right">Image URL</label>
          <div class="col-sm-10">
              <input type="text" class="form-control" id="imageurl" name="imageurl">
            <small id="image" class="form-text text-muted"><span class="text-danger">* Required.</span> View of the item.</small>
          </div>
        </div>
        <!-- <div class="form-group row">
          <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" id="auctionReservePrice">
            </div>
            <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" id="auctionEndDate">
            <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
          </div>
        </div> -->
        <button type="submit" name = "itemsubmit" class="btn btn-primary form-control">Create Item</button>
      </form>
    </div>
  </div>
</div>

</div>

<?php include_once("footer.php")?>