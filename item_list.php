<?php
  include_once('header.php');
  include_once('database.php');
  session_start();
  if (!isset($_SESSION) || $_SESSION == null) {
    echo('<div class="text-center">You\'re not logged in. Please re-login if this was a mistake</div>');
    header('refresh:3;url=browse.php');
  }

  $user_id = $_SESSION['id'];
  $items_query = "SELECT Item.id, Item.name, Item.description, Item.image_url, Category.name as category_name FROM Item JOIN Category ON Item.category_id = Category.id WHERE user_id = $user_id;";
  $result_item = mysqli_query($connection, $items_query);
  if (!$result_item) {
    die('Invalid query: ' . mysqli_error($connection));
  }
  $items = mysqli_fetch_all($result_item);

  // var_dump($items);
?>

<?php 
  $items_per_page = 6;
  $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
  $offset = ($current_page - 1) * $items_per_page;
  $items_for_current_page = array_slice($items, $offset, $items_per_page);
  $total_pages = ceil(count($items) / $items_per_page);

  // items list
  echo '<div class="container py-4">';
  echo '<div><h1>My Products</h1></div>';
  echo '<div>For now, in order to add items to auction, you need to create auction first then add the items from the page</div>';
  echo '<div class="row py-4 h-75 ">';
  foreach ($items_for_current_page as $item) {
      echo '<div class="col-md-4 p-2">';
      echo '<div class="card justify-content-between  align-content-around " style="height: 300px;">';
      echo '<img class="card-img-top" src="' . $item[3] . '" alt="' . $item[1] . '" style="height: 150px; object-fit: cover;">';
      echo '<div class="card-body">';
      echo '<h5 class="card-title">' . $item[1] . '</h5>';
      echo '<p class="card-text text-truncate">' . $item[2] . '</p>';
      echo '<p class="card-text">' . $item[4] . '</p>';
      echo '</div>';
      echo '</div>';
      echo '</div>';
  }
  echo '</div>';

  // pagination
  echo '<nav aria-label="Page navigation example">';
  echo '<ul class="pagination py-4">';
  for ($i = 1; $i <= $total_pages; $i++) {
      echo '<li class="page-item' . ($i == $current_page ? ' active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
  }
  echo '</ul>';
  echo '</nav>';
  echo '</div>';
?>
