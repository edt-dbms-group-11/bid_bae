<?php include_once("header.php") ?>
<?php require("utilities.php") ?>
<?php include_once("database.php") ?>
<?php include_once("session_check.php") ?>

<div class="container">
    <h2 class="my-3">Watchlist for you</h2>

    <?php

    $user_id = $_SESSION['id'] ?? null;

    $query = "SELECT w.auction_id AS auction_id_aliass FROM watchlist AS w WHERE w.user_id = $user_id";

    $result = mysqli_query($connection, $query);
    $watchlist_items = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $watchlist_items[] = $row;
        }
    } else {
        // Display error message if query fails
        die('Error: ' . mysqli_error($connection));
    }

    $results_per_page = 5;
    $total_items = count($watchlist_items);
    $max_page = ceil($total_items / $results_per_page);

    $curr_page = $_GET['page'] ?? 1;
    $start = ($curr_page - 1) * $results_per_page;

    // Limit items based on pagination
    $paginated_items = array_slice($watchlist_items, $start, $results_per_page);

    foreach ($paginated_items as $item) {
        $new_query = "SELECT * FROM auction WHERE id = {$item['auction_id_aliass']}";
        $new_result = mysqli_query($connection, $new_query);
        $auction_info;
        if ($new_result) {
            while ($new_row = mysqli_fetch_assoc($new_result)) {
                //var_dump($new_row);
                $auction_info = $new_row;
            }
            ;

        } else {
            // Display error message if query fails
            die('Error: ' . mysqli_error($connection));
        }


        // var_dump($auction_info);
        $bids_number_query = "SELECT count(*) FROM Bid WHERE auction_id = {$item['auction_id_aliass']}";
        $bids_result = mysqli_query($connection, $bids_number_query);

        if ($bids_result) {
            while ($bids_row = mysqli_fetch_row($bids_result)) {
                //var_dump($bids_row);
    
                //  $bid_number_row = mysqli_fetch_row($bids_result);
                $num_bids = $bids_row[0];
            }
        } else {
            $num_bids = 0;
        }

        $auction_id = $auction_info['auction_id'] ?? null;
        $title = $auction_info['title'] ?? null;
        $description = $auction_info['description'] ?? null;
        $current_price = $auction_info['current_price'] ?? null;
        $end_time = new DateTime($auction_info['end_time']) ?? null;

        print_listing_li($auction_id, $title, $description, $current_price, $num_bids, $end_time);
    }
    ?>

    <!-- Pagination -->
    <nav aria-label="Search results pages" class="mt-5">
        <ul class="pagination justify-content-center">
            <?php
            $prev_page = $curr_page - 1;
            $next_page = $curr_page + 1;

            // Previous page
            if ($curr_page > 1) {
                echo '<li class="page-item"><a class="page-link" href="new_watchlist.php?page=' . $prev_page . '">Previous</a></li>';
            }

            // Page numbers
            for ($i = 1; $i <= $max_page; $i++) {
                echo '<li class="page-item ' . ($curr_page == $i ? 'active' : '') . '"><a class="page-link" href="watchlist.php?page=' . $i . '">' . $i . '</a></li>';
            }

            // Next page
            if ($curr_page < $max_page) {
                echo '<li class="page-item"><a class="page-link" href="new_watchlist.php?page=' . $next_page . '">Next</a></li>';
            }
            ?>
        </ul>
    </nav>
</div>

<style>
    .prevIcon {
        color: #09f;
    }
</style>

<?php include_once("footer.php") ?>