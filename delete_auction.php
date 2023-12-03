<?php
include_once("header.php");
include_once("utilities.php");
include_once("database_functions.php");

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
    // Execute the delete query directly
    $delete_query = "DELETE FROM Auction WHERE id = $auction_id";
    $delete_result = mysqli_query($connection, $delete_query);

    if ($delete_result) {
        // Redirect to mylistings.php after deletion
        echo (
            '<div class="text-center">Auction successfully deleted!</div>'
        );
        header("refresh:2;url=mylistings.php");
        //header("Location: mylistings.php");
        exit();
    } else {
        // Handle delete failure
        echo "Failed to delete the auction. Please try again.";
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