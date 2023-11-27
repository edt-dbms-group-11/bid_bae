<?php 
include_once("header.php");
include_once("database.php"); 
include_once("database_functions.php"); 
?>


<?php
session_start();

// If user is not logged in, they should not be able to use this page.
if (!isset($_SESSION) || $_SESSION == null)  {
  echo('<div class="text-center">You\'re not logged in. Please re-login if this was a mistake</div>');
  header('refresh:3;url=browse.php');
}

// Assuming seller_id is stored in the session
$seller_id = $_SESSION['id'];
?>

<div class="container">

<!-- Create auction form -->
<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Create new auction</h2>
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
              <!-- Button to trigger modal -->
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#itemSelectionModal">
                Choose
              </button>
              <!-- Hidden input to store selected items -->
              <input type="hidden" name="selectedItems" id="selectedItemsInput" value="">
              <small id="selectHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Items already listed in other auctions are not available.</small>
          </div>
        </div>

        <!-- Modal for item selection -->
        <div class="modal fade" id="itemSelectionModal" tabindex="-1" role="dialog" aria-labelledby="itemSelectionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="itemSelectionModalLabel">Select Items</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                          <?php $seller_items = getSellerItems($seller_id);
                          // Your modal content, including checkboxes for item selection
                          foreach ($seller_items as $item) {
                          echo '<div class="form-check">';
                          echo '<input class="form-check-input" type="checkbox" name="modalSelectedItems[]" value="' . $item['id'] . '">';
                          echo '<label class="form-check-label">' . $item['name'] . '</label>';
                          echo '</div>';
                        } ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="updateSelectedItems()">Save changes</button>
                        <script>
                        function updateSelectedItems() {
                            // Get all selected checkboxes
                            var selectedCheckboxes = document.querySelectorAll('input[name="modalSelectedItems[]"]:checked');
                            
                            // Extract item IDs from selected checkboxes
                            var selectedItems = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);

                            // Log the selected items to the console for debugging
                            console.log(selectedItems);

                            // Update the hidden input with the selected items
                            document.getElementById('selectedItemsInput').value = selectedItems.join(',');

                            // Close the modal
                            $('#itemSelectionModal').modal('hide');
                        }
                    </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row">
          <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="auctionDetails" name="auctionDetails" rows="4"></textarea>
            <small id="detailsHelp" class="form-text text-muted">Full details of the listing to help bidders decide if it's what they're looking for.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
          <div class="col-sm-10">
          <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" id="auctionStartPrice" name="auctionStartPrice">
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
              <input type="number" class="form-control" id="auctionReservePrice" name="auctionReservePrice">
            </div>
            <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionStartDate" class="col-sm-2 col-form-label text-right">Start date</label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" id="auctionStartDate" name="auctionStartDate">
            <small id="endStartHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to begin.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate">
            <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
          </div>
        </div>
        <input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>"> 
        <button type="submit" name= "auctionsubmit" class="btn btn-primary form-control">Create Auction</button>
      </form>
    </div>
  </div>
</div>

</div>


<?php include_once("footer.php")?>