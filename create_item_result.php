<?php include_once("create_item.php")?>
<?php include_once("header.php")?>
<?php include_once('database.php') ?>
<?php include_once("database_functions.php")?>

<div class="container my-5">

<?php

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }
      
      session_start();
      if (!isset($_SESSION) || $_SESSION == null) {
        echo('<div class="text-center">You\'re not logged in. Please re-login if this was a mistake</div>');
        header('refresh:3;url=browse.php');
      }
    
      $user_id = $_SESSION['id'];

    if (isset($_POST["itemsubmit"])) {
        $itemTitle = $itemDesc = $imageurl = $category_n = "";

    $itemDesc = $_POST['itemDesc'];
    
      if (empty($_POST["itemTitle"])) {
        $itemErr = "Item Name is required";
        echo "<script>alert('$itemErr')</script>";
      } else {
        $itemTitle = test_input($_POST["itemTitle"]);
      }
    
      if (empty($_POST["auctionCategory"])) {
        $categoryErr = "Category is required";
        echo "<script>alert('$categoryErr')</script>";
      } else {
        $category_n = test_input($_POST["auctionCategory"]);
      }
    
      if ((empty($_POST["imageurl"])) or (!filter_var($_POST["imageurl"], FILTER_VALIDATE_URL) === true)) {
        $imageErr = "Valid Image URL is required";
        echo "<script>alert('$imageErr')</script>";
      } else {
        $imageurl = test_input($_POST["imageurl"]);
      }
    
      if (!empty($username) && !empty($itemTitle) && !empty($category_n) && !empty($imageurl)){
    $sql = "INSERT INTO item (user_id,name,description,category_id,image_url) values('$user_id','$itemTitle','$itemDesc', '$category_n', '$imageurl')";
      
    if(mysqli_query($connection,$sql))
    {
        echo "<script>alert('new record inserted')</script>";
        echo "<script type='text/javascript'>window.top.location='./create_item_success.php';</script>"; exit;
    }
    else{
        echo "error:" .mysqli_error($connection);
    }
    }
}   
?>
</div>
<?php include_once("footer.php")?>