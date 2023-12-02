<?php include_once("header.php") ?>
<?php include_once("create_auction.php") ?>
<?php include_once("database.php"); ?>
<?php include_once("database_functions.php"); ?>

<div class="container my-5">

  <?php
  function handleGeneralError($customMsg)
  {
    $errors[] = $customMsg || "Error adding items to the Auction Product table. Please try again.";
    $_SESSION['errors'] = $errors;
    echo '<script>window.location.href = "create_auction.php";</script>';
    exit();
  }
  ?>

  <?php
  global $connection;
  if (!isset($_SESSION) || $_SESSION == null) {
    echo ('<div class="text-center">You\'re not logged in. Please re-login if this was a mistake</div>');
    header('refresh:3;url=browse.php');
  }

  // Check if the form was submitted
  if (isset($_POST["auctionsubmit"])) {
    // Extract form data into variables.
    $title = $_POST['auctionTitle'];
    $details = $_POST['auctionDetails'];
    $startPrice = $_POST['auctionStartPrice'];
    $reservePrice = $_POST['auctionReservePrice'];
    $startDate = $_POST['auctionStartDate'];
    $endDate = $_POST['auctionEndDate'];

    $errors = validateAuctionData($title, $selectedItems, $startPrice, $reservePrice, $startDate, $endDate, $description);
    $success = [];

    if (!empty($errors)) {
      $_SESSION['errors'] = $errors;
      echo '<script>window.location.href = "create_auction.php";</script>';
      exit();
    } else {
      $query = "INSERT INTO Auction (title, description, seller_id, start_price, reserved_price, current_price, end_time) 
      VALUES ('$title', '$details', $seller_id,  $startPrice, $reservePrice, $startPrice, '$formattedEndDate')";

      // Execute the query
      $result = mysqli_query($connection, $query);

      if ($result) {
        $auction_id = mysqli_insert_id($connection);

        foreach ($_POST['selectedItems'] as $item_id) {
          $insertProductQuery = "INSERT INTO Auction_Product (item_id, auction_id) VALUES ('$item_id', '$auction_id')";
          $insertProductResult = mysqli_query($connection, $insertProductQuery);

          if ($insertProductResult) {
            foreach ($_POST['selectedItems'] as $itemId) {
              $validateItemQuery = "SELECT user_id FROM Item WHERE id = $itemId";
              $validateItemResult = mysqli_query($connection, $validateItemQuery);
              $itemData = mysqli_fetch_assoc($validateItemResult);

              if (!$validateItemResult) {
                handleGeneralError('');
              }

              if ($itemData['user_id'] !== $_SESSION['id']) {
                error_log("The item with ID $itemId does not belong to the current user.");
                handleGeneralError("You are unauthorized to perform this action");
              }

              $updateQuery = "UPDATE Item SET is_available = 0 WHERE id = $itemId";
              $updateResult = mysqli_query($connection, $updateQuery);

              if (!$updateResult) {
                handleGeneralError('');
              }

              // All series success
              $_SESSION['success'] = 'auction_create_success';
              echo '<script>window.location.href = "create_auction.php";</script>';
              exit();

            }
          } else {
            handleGeneralError('');
          }
        }
      } else {
        handleGeneralError('');
      }
    }
  } else {
    // If there was an error, provide an error message
    echo ('<div class="text-center text-danger">Error creating auction. Please try again.</div>');
    echo "error:" . mysqli_error($connection);
  }
  ?>

</div>

<?php include_once("footer.php") ?>