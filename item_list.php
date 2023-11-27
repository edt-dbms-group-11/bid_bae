<!-- Fn -->
<?php

  include_once('header.php');
  include_once('database.php');

  $user_id = $_SESSION['id'];
  $items_query = "SELECT Item.id, Item.name, Item.description, Item.image_url, Item.is_available, Category.name as category_name 
                  FROM Item 
                  JOIN Category ON Item.category_id = Category.id 
                  WHERE user_id = $user_id
                  ORDER BY Item.id DESC";
$result_item = mysqli_query($connection, $items_query);
  if (!$result_item) {
    die('Invalid query: ' . mysqli_error($connection));
  }
  $items = mysqli_fetch_all($result_item);

  // var_dump($items);
?>


<!-- UI -->
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
    echo '<div id="item-' . $item[0] . '" class="col-md-4 p-2">';
    echo '<div class="card" style="min-height: 450px;">';
    echo '<img class="card-img-top" src="' . $item[3] . '" alt="' . $item[1] . '" style="height: 200px; object-fit: cover;">';
    echo '<hr class="w-100 mb-auto "></hr`>';
    echo '<div class="card-body d-flex flex-column">';
    echo '<h5 class="card-title">' . $item[1] . '</h5>';
    echo '<p class="card-text text-truncate">' . $item[2] . '</p>';
    echo '<span class="card-text badge badge-light w-50 py-2">' . $item[5] . '</span>';
    echo '<hr class="w-100 mt-auto "></hr`>';

    if ($item[4] == true) { // item.is_available
        echo '<button class="w-50 ml-auto btn btn-warning" onclick="removeItem(' . $item[0] . ')">Remove Item</button>';
    } else {
        echo '<button class="w-50 ml-auto btn btn-danger" disabled>In Auction / Sold</button>';
    }

    echo '</div>';
    echo '</div>';
    echo '</div>';
  }
  echo '</div>';

    echo '<ul class="pagination py-4">';
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<li class="page-item' . ($i == $current_page ? ' active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
    }
    echo '</ul>';
    echo '</nav>';
    echo '</div>';
?>

<!-- JS -->
<script>
    function removeItem(itemId) {
      if (confirm("Are you sure you want to remove this item?")) {
        $.ajax({
          type: "POST",
          url: "remove_item.php",
          data: { item_id: itemId, type: 'remove' },
          dataType: "json",
          success: function(response) {
            console.log('Server Response:', response);

            if (response.status === 'success') {
              alert('Item removed successfully.');
              location.reload();
            } else {
              alert('Error: ' + response.message);
            }
          },
          error: function(xhr, status, error) {
            console.log('AJAX Error:', status, error);
            alert('Error: Unable to process the request.');
          }
        });
      }
    };
</script>

<script>
</script>