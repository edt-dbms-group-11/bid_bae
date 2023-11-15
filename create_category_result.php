<?php include_once("create_category.php")?>
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
      
      $categories = getCategoriesFromDatabase();
      
    if (isset($_POST["categorysubmit"])) {
        $categoryTitle = $categoryDesc = "";

    $categoryDesc = $_POST['categoryDesc'];
    
      if (empty($_POST["categoryTitle"])) {
        $catErr = "Category Name is required";
        echo "<script>alert('$categErr')</script>";
      } else {
        $categoryTitle = test_input($_POST["categoryTitle"]);
      }
    
        foreach ($categories as $category) : 
            if ($category['name'] == $categoryTitle){
                echo "<script>alert('Category Name already exists')</script>";
                exit;
            }
        endforeach;
      
      if (!empty($categoryTitle)){
    $sql = "INSERT INTO category (name,description) values('$categoryTitle','$categoryDesc')";
      
    if(mysqli_query($connection,$sql))
    {
        echo "<script>alert('new record inserted')</script>";
        echo "<script type='text/javascript'>window.top.location='./create_category_success.php';</script>"; exit;
    }
    else{
        echo "error:" .mysqli_error($connection);
    }
    } 
}
?>
</div>
<?php include_once("footer.php")?>