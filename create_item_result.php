<?php include_once("create_item.php")?>
<?php include_once("database_functions.php")?>

<div class="container my-5">

<?php
$conn = new mysqli("localhost", "root", "", "auction_system");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }

    if (isset($_POST["itemsubmit"])) {
        $username = $itemTitle = $itemDesc = $imageurl = $category_n = "";
    // collect value of input field


    $itemDesc = $_POST['itemDesc'];

    if (empty($_POST['username'])) {
        $usernameErr = "Username is required";
        //echo "<script>alert('$usernameErr')</script>";
      } else {
        $username = test_input($_POST["username"]);
      }
    
      if (empty($_POST["itemTitle"])) {
        $itemErr = "Item Name is required";
        //echo "<script>alert('$itemErr')</script>";
      } else {
        $itemTitle = test_input($_POST["itemTitle"]);
      }
    
      if (empty($_POST["auctionCategory"])) {
        $categoryErr = "Category is required";
        //echo "<script>alert('$categoryErr')</script>";
      } else {
        $category_n = test_input($_POST["auctionCategory"]);
      }
    
      if ((empty($_POST["imageurl"])) or (!filter_var($_POST["imageurl"], FILTER_VALIDATE_URL) === true)) {
        $imageErr = "Valid Image URL is required";
        //echo "<script>alert('$imageErr')</script>";
      } else {
        $imageurl = test_input($_POST["imageurl"]);
      }
    
      if (!empty($username) & !empty($itemTitle) & !empty($category_n) & !empty($imageurl)){
    $sql = "INSERT INTO item (username,name,description,category_id,image_url) values('$username','$itemTitle','$itemDesc', '$category_n', '$imageurl')";
      
    if(mysqli_query($conn,$sql))
    {
        echo "<script>alert('new record inserted')</script>";
        //header("Location: create_item_success.php");
        echo "<script type='text/javascript'>window.top.location='./create_item_success.php';</script>"; exit;
        //echo('<div class="text-center">Item successfully created! <a href="FIXME">View your new listing.</a></div>');
    }
    else{
        echo "error:" .mysqli_error($conn);
    }
    mysqli_close($conn);
    }
}   

// Closing the connection.
// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */


/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */


/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
            

// If all is successful, let user know.



?>
</div>
<?php include_once("footer.php")?>