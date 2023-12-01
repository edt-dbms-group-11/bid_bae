<?php
include_once("header.php");
include_once("utilities.php");
include_once("database_functions.php");

$auction_id = $_GET['auction_id'];

// Retrieve auction details from the database using $auction_id
$auction_details = getAuctionDetailsById($auction_id);

if (!$auction_details) {
    echo "Auction not found.";
    include_once("footer.php");
    exit();
}

?>

<!-- Display form for modifying auction -->
<div class="container">
    <div style="max-width: 800px; margin: 10px auto">
        <h2 class="my-3">Update the auction details:</h2>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="modify_auction_result.php?auction_id=<?php echo $auction_id; ?>">
                    <div class="form-group row">
                        <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="auctionTitle" name="auctionTitle" value="<?php echo $auction_details['title']; ?>" required>
                            <small id="titleHelp" class="form-text text-muted"><span class="text-danger">*Required.</span> A short description of the item you're selling, which will display in listings.</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right">Selected Items</label>
                        <div class="col-sm-10">
                            <ol name="selectedItems">
                                <?php
                                $auctionItems = getAuctionItems($auction_id);
                                foreach ($auctionItems as $item) {
                                    echo '<li>' . $item['name'] . '</li>';
                                }
                                ?>
                            </ol>
                            <small id="selectHelp" class="form-text text-muted">
                                You cannot add or delete items from this auction.
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="auctionDetails" name="auctionDetails"
                                rows="4"><?php echo $auction_details['description']; ?></textarea>
                            <small id="detailsHelp" class="form-text text-muted">Full details of the listing to help
                                bidders decide if it's what they're looking for.</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">£</span>
                                </div>
                                <input min="1" type="number" class="form-control" id="auctionStartPrice" name="auctionStartPrice" value="<?php echo $auction_details['start_price']; ?>">
                            </div>
                            <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">*
                                    Required.</span> Initial bid amount.</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve
                            price</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">£</span>
                                </div>
                                <input min="1" type="number" class="form-control" id="auctionReservePrice" name="auctionReservePrice" value="<?php echo $auction_details['reserved_price']; ?>">
                            </div>
                            <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="auctionStartDate" class="col-sm-2 col-form-label text-right">Start date</label>
                        <div class="col-sm-10">
                            <input type="datetime-local" class="form-control" id="auctionStartDate"
                                name="auctionStartDate" value="<?php echo $auction_details['start_time']; ?>">
                            <small id="endStartHelp" class="form-text text-muted"><span class="text-danger">*Required.</span> Day for the auction to begin.</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
                        <div class="col-sm-10">
                            <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate" value="<?php echo $auction_details['end_time']; ?>">
                            <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">*Required.</span> Day for the auction to end.</small>
                        </div>
                    </div>
                    <input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>">
                    <button type="submit" name="submitChanges" class="btn btn-primary form-control">Submit changes</button>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include_once("footer.php"); ?>