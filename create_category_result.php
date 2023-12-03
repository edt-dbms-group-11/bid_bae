<?php include_once("create_category.php") ?>
<?php include_once("header.php") ?>
<?php include_once('database.php') ?>
<?php include_once("database_functions.php") ?>

<div class="container my-5">

  <?php

  $categories = getCategoriesFromDatabase();

  if (isset($_POST["categorysubmit"])) {
    $categoryTitle = $categoryDesc = "";

    $categoryDesc = $_POST['categoryDesc'];

    if (empty($_POST["categoryTitle"])) {
      $catErr = "Category Name is required";
      echo "<script>alert('$categErr')</script>";
    } else {
      $categoryTitle = $_POST["categoryTitle"];
    }

    foreach ($categories as $category):
      if (strtolower($category['name']) == strtolower($categoryTitle)) {
        echo "<script>alert('Category Name already exists')</script>";
        exit;
      }
    endforeach;

    if (!empty($categoryTitle)) {
      createCategory($categoryTitle, $categoryDesc);
    }
  }
  ?>
</div>
<?php include_once("footer.php") ?>