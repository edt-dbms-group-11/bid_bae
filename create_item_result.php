<?php include_once("create_item.php")?>
<?php include_once("header.php")?>
<?php include_once('database.php') ?>
<?php include_once("database_functions.php")?>

<div class="container my-5">

<?php
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
    
      if (empty($_POST["fileUpload"]) && !isset($_FILES["fileUpload"])) {
        $imageErr = "Valid Image is required";
        echo "<script>alert('$imageErr')</script>";
      } else {
        $fileName = $_FILES["fileUpload"]["name"];
        $allowedTypes = ["jpg", "jpeg", "png", "gif"]; // Allowed file types
        $maxFileSize = 5 * 1024 * 1024; // 5 MB (max file size)
        if (preg_match('/\.[^.]+$/', $fileName, $matches)) {
          $fileExtension = strtolower(ltrim($matches[0], '.'));
  
          // Further processing based on the file extension
          // ...
      } else {
          echo "File name does not have an extension.";
      }
        
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileSize = $_FILES["fileUpload"]["size"];
        $fileContent = file_get_contents($_FILES["fileUpload"]["tmp_name"]); // Get file content

        // Check file type
        if (!in_array($fileType, $allowedTypes)) {
            echo "Invalid file type. Allowed types: " . implode(", ", $allowedTypes);
            exit;
          }
        if ($fileSize > $maxFileSize) {
          echo "File size exceeds the limit (5 MB).";
          exit;
        }
        $imageurl = base64_encode($fileContent);
      }
    }
    
      if (!empty($user_id) && !empty($itemTitle) && !empty($category_n) && !empty($imageurl)){
           createItem($user_id,$itemTitle,$itemDesc, $category_n, $imageurl);
    }

?>
</div>
<?php include_once("footer.php")?>