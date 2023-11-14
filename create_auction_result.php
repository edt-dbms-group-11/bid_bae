<?php include_once("header.php")?>
<?php include_once("create_auction.php")?>
<?php include_once("database.php"); ?>

<div class="container my-5">

<?php

    session_start();
    global $connection;
    if (!isset($_SESSION) || $_SESSION == null) {
    echo('<div class="text-center">You\'re not logged in. Please re-login if this was a mistake</div>');
    header('refresh:3;url=browse.php');
    }

    // This function takes the form data and adds the new auction to the database.

    // $mysqli = connectToDatabase();

    // Check if the form was submitted
    if (isset($_POST["auctionsubmit"])) {
   
    // Extract form data into variables. 
    $title = $_POST['auctionTitle'];
    $details = $_POST['auctionDetails'];
    $startPrice = $_POST['auctionStartPrice'];
    $reservePrice = $_POST['auctionReservePrice'];
    $endDate = $_POST['auctionEndDate'];
    $formattedEndDate = date('Y-m-d H:i:s', strtotime($endDate));

    // print_r($_SESSION);
    $seller_id = $_SESSION['id'];

    // TODO #2.1: Perform data validation.
    $errors = [];

    // Check if title is not empty
    if (empty($title)) {
        $errors[] = "Title cannot be empty.";
    }

    // Check if details are not empty
    // if (empty($details)) {
    //     $errors[] = "Details cannot be empty.";
    // }

    //  Validate category - you may want to check if the selected category exists in the database
    // if (!is_numeric($category) || $category <= 0) {
    //     $errors[] = "Invalid category.";
    // }

    // Check if start price is a positive number
    if (!is_numeric($startPrice) || $startPrice <= 0) {
        $errors[] = "Start price must be a positive number.";
    }

    // Check if reserve price is a positive number (if provided)
    if (!isset($reservePrice) || trim($reservePrice) === '') {
        // If reserve price is blank, assign the start price to it
        $reservePrice = $startPrice;
    } elseif (!is_numeric($reservePrice) || $reservePrice <= 0) {
        // If reserve price is provided but not a positive number, show an error
        $errors[] = "Reserve price must be a positive number.";
    }

    // Validate end date - you may want to check if the date is in the future
    if (empty($endDate) || strtotime($endDate) <= time()) {
        $errors[] = "Invalid end date.";
    }

    // If there are validation errors, display them
    if (!empty($errors)) {
        echo('<div class="text-center text-danger">Error creating auction. Please fix the following issues:<br>');
        foreach ($errors as $error) {
            echo('- ' . $error . '<br>');
        }
        echo('</div>');
    } else {

        // If everything looks good, make the appropriate call to insert data into the database. */
        $query = "INSERT INTO Auction (title, description, seller_id, start_price, reserved_price, current_price, end_time) 
                VALUES ('$title', '$details', $seller_id,  $startPrice, $reservePrice, $startPrice, '$formattedEndDate')";

        // Execute the query
        $result = mysqli_query($connection, $query);
        // $connection->query($query); 

        if ($result) {
            // If the insertion was successful, get the auction ID
            $auction_id = mysqli_insert_id($connection);

            // Now, add rows to the Auction_Product table
            foreach ($_POST['selectedItems'] as $item_id) {
                $insertProductQuery = "INSERT INTO Auction_Product (item_id, auction_id) VALUES ('$item_id', '$auction_id')";
                $insertProductResult = mysqli_query($connection, $insertProductQuery);

                if (!$insertProductResult) {
                    // Handle error if any
                    echo('<div class="text-center text-danger">Error adding items to the Auction Product table. Please try again.</div>');
                    echo "error: " . mysqli_error($connection);
                    exit();
                }
            }
            // Notify the user that the auction and associated items were added successfully
            echo('<div class="text-center">Auction and associated items successfully created! <a href="FIXME">View your new listing.</a></div>');
        } else {
            // If there was an error, provide an error message
            echo('<div class="text-center text-danger">Error creating auction. Please try again.</div>');
            echo "error:" .mysqli_error($connection);
        }
    }
}

// Close the database connection
// $connection->close();

?>

</div>

<?php include_once("footer.php")?>