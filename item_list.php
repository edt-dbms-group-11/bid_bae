<?php include_once("header.php")?>
<?php
$items = array(
    array(
      'name' => 'Item 1',
      'description' => 'This is the first item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 10.99
    ),
    array(
      'name' => 'Item 2',
      'description' => 'This is the second item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 20.99
    ),
    array(
      'name' => 'Item 3',
      'description' => 'This is the third item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 30.99
    ),
    array(
      'name' => 'Item 4',
      'description' => 'This is the fourth item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 40.99
    ),
    array(
      'name' => 'Item 5',
      'description' => 'This is the fifth item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 50.99
    ),
    array(
      'name' => 'Item 6',
      'description' => 'This is the sixth item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 60.99
    ),
    array(
      'name' => 'Item 7',
      'description' => 'This is the seventh item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 70.99
    ),
    array(
      'name' => 'Item 8',
      'description' => 'This is the eighth item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 80.99
    ),
    array(
      'name' => 'Item 9',
      'description' => 'This is the ninth item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 90.99
    ),
    array(
      'name' => 'Item 10',
      'description' => 'This is the tenth item',
      'image' => 'https://via.placeholder.com/150',
      'price' => 100.99
    )
);
?>

<?php 
$items_per_page = 6;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;
$items_for_current_page = array_slice($items, $offset, $items_per_page);
$total_pages = ceil(count($items) / $items_per_page);

// Output the HTML for the items list
echo '<div class="container py-4">';
echo '<div><h1>My Products</h1></div>';
echo '<div>For now, in order to add items to auction, you need to create auction first then add the items from the page</div>';
echo '<div class="row py-4">';
foreach ($items_for_current_page as $item) {
    echo '<div class="col-md-4 p-2">';
    echo '<div class="card">';
    echo '<img class="card-img-top" src="' . $item['image'] . '" alt="' . $item['name'] . '">';
    echo '<div class="card-body">';
    echo '<h5 class="card-title">' . $item['name'] . '</h5>';
    echo '<p class="card-text">' . $item['description'] . '</p>';
    echo '<p class="card-text">' . $item['price'] . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
echo '</div>';

// Output the pagination links
echo '<nav aria-label="Page navigation example">';
echo '<ul class="pagination py-4">';
for ($i = 1; $i <= $total_pages; $i++) {
    echo '<li class="page-item' . ($i == $current_page ? ' active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
}
echo '</ul>';
echo '</nav>';
echo '</div>';

?>
