<?php
include_once("header.php");
include_once("utilities.php");
include_once("database_functions.php");
include_once("session_check.php");

$auction_id = $_GET['auction_id'];

// Retrieve auction details from the database using $auction_id
$auction_details = getAuctionDetailsById($auction_id);

// Check if the auction exists
if (!$auction_details) {
    echo "Auction not found.";
    include_once("footer.php");
    exit();
}

// Check if the user has the right to delete the auction (you may customize this part)
if ($_SESSION['id'] !== $auction_details['seller_id']) {
    echo "You do not have permission to delete this auction.";
    include_once("footer.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmDelete'])) {

    // Get the item IDs associated with the auction
    $get_items_query = "SELECT item_id FROM Auction_Product WHERE auction_id = $auction_id";
    $get_items_result = mysqli_query($connection, $get_items_query);

    // Execute the delete query directly
    $delete_query = "DELETE FROM Auction WHERE id = $auction_id";
    $delete_result = mysqli_query($connection, $delete_query);

    if ($delete_result) {
        if ($get_items_result) {
            // Update the is_available field in the Item table for each item
            foreach ($get_items_result as $row) {
                $item_id = $row['item_id'];
                $update_item_query = "UPDATE Item SET is_available = 1 WHERE id = $item_id";
                $update_item_result = mysqli_query($connection, $update_item_query);

                if (!$update_item_result) {
                    // Handle update failure for item availability
                    echo "Failed to update item availability. Please try again.";
                    exit();
                }
            }

            // Redirect to mylistings.php after deletion and item availability update
            echo ('<div class="text-center">Auction successfully deleted!</div>');
            header("refresh:2;url=mylistings.php");
            exit();
        } else {
            // Handle query failure to get item IDs
            echo "Failed to retrieve item IDs. Please try again.";
        }
    }
}
?>

<!-- Display confirmation form for deleting auction -->
<div class="container mt-5">
    <h2>Delete Auction</h2>
    <p>Are you sure you want to delete the auction "
        <?php echo $auction_details['title']; ?>"?
    </p>

    <!-- Delete confirmation form -->
    <form method="post" action="delete_auction.php?auction_id=<?php echo $auction_id; ?>">
        <button type="submit" class="btn btn-danger" name="confirmDelete">Confirm Delete</button>
        <a href="mylistings.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include_once("footer.php"); ?>