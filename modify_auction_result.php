<?php
include_once("header.php");
include_once("utilities.php");
include_once("database_functions.php");

$auction_id = $_GET['auction_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitChanges'])) {

    // Retrieve updated details from the form
    $updated_title = $_POST['auctionTitle'];
    $selectedItems = $_POST['selectedItems'];
    $updated_details = $_POST['auctionDetails'];
    $updated_startPrice = $_POST['auctionStartPrice'];
    $updated_reservePrice = $_POST['auctionReservePrice'];
    $updated_startDate = $_POST['auctionStartDate'];
    $updated_endDate = $_POST['auctionEndDate'];

    // Validate the data using the existing function
    $errors = validateAuctionData($updated_title, $selectedItems, $updated_details, $updated_startPrice, $updated_reservePrice, $updated_startDate, $updated_endDate);

    if (empty($errors)) {
        // No validation errors, proceeding with updating auction details

        // SQL update statement
        $update_query = "UPDATE Auction 
                        SET title = '$updated_title', description = '$updated_details', start_price = '$updated_startPrice', 
                        reserved_price = '$updated_reservePrice', current_price = '$updated_startPrice', 
                        start_time = '$updated_startDate', end_time = '$updated_endDate'
                        WHERE id = $auction_id";
        
        // Execute the update query
        $result = mysqli_query($connection, $update_query);

        if ($result) {
            // Redirect back to mylistings.php after modification
            echo(
                '<div class="text-center">Auction successfully updated!</div>'
            ); 
            header("refresh:2;url=mylistings.php");
            //header("Location: mylistings.php");
            exit();
        } else {
            // Handle update failure
            echo "Failed to update auction details. Please try again.";
        }
    } else {
        // Display validation errors
        echo '<div class="container mt-5">';
        echo '<div class="alert alert-danger" role="alert">';
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
        echo '</div>';
        echo '</div>';
    }
}

?>

<?php include_once("footer.php"); ?>
