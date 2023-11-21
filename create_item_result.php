<?php include_once("create_item.php")?>
<?php include_once("header.php")?>
<?php include_once('database.php') ?>
<?php include_once("database_functions.php")?>

<div class="container my-5">

<?php
      
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
        $itemTitle = $_POST["itemTitle"];
      }
    
      if (empty($_POST["auctionCategory"])) {
        $categoryErr = "Category is required";
        echo "<script>alert('$categoryErr')</script>";
      } else {
        $category_n = $_POST["auctionCategory"];
      }
    
      if ((empty($_POST["imageurl"])) or (!filter_var($_POST["imageurl"], FILTER_VALIDATE_URL) === true)) {
        $imageErr = "Valid Image URL is required";
        echo "<script>alert('$imageErr')</script>";
      } else {
        $imageurl = $_POST["imageurl"];
      }
    
      if (!empty($username) && !empty($itemTitle) && !empty($category_n) && !empty($imageurl)){
           createItem($user_id,$itemTitle,$itemDesc, $category_n, $imageurl);
    }
}   
?>
</div>
<?php include_once("footer.php")?>