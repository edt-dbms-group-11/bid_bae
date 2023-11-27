<?php

include_once('database.php');

// Function to get seller's available items
function getSellerItems($seller_id) {
    global $connection;

    // Implement SQL query to fetch items based on seller_id and is_available status
    $sql = "SELECT id, name FROM Item WHERE user_id = $seller_id AND is_available = 1";
    
    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        // Fetch items and store them in an array
        $items = array();
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        // $conn->close();
        return $items;
    } else {
        // $conn->close();
        return array();
    }
}

function createCategory($categoryTitle, $categoryDesc)
{
    global $connection;
    $sql = "INSERT INTO category(name, description) VALUES ('$categoryTitle', '$categoryDesc')";

    if (mysqli_query($connection, $sql)) {
        echo "<script type='text/javascript'>window.top.location='./create_category_success.php';</script>";
        exit;
    } else {
        echo "error:" . mysqli_error($connection);
    }
}

function createItem($user_id,$itemTitle,$itemDesc, $category_n, $imageurl){
    global $connection;
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

// Function to get categories from the database
function getCategoriesFromDatabase()
{
    global $connection;

    // Query to fetch categories from the category table
    $sql = "SELECT id, name FROM category";

    // Execute the query
    $result = $connection->query($sql);

    // Check if there are rows returned
    if ($result->num_rows > 0) {
        // Fetch categories and store them in an array
        $categories = array();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    } else {
        // If no categories are found, return an empty array
        return array();
    }
}


function getAuctionsFromDatabaseWithParameters($order_by, $category_id, $keyword, $status, $page_num, $page_size) {
    global $connection;

    $offset_value = ($page_num - 1) * $page_size;

    $orderByExpression = '';

    switch ($order_by) {
        case 'pricelow':
            $orderByExpression = 'auc.current_price ASC';
            break;
        case 'pricehigh':
            $orderByExpression = 'auc.current_price DESC';
            break;
        case 'date':
            $orderByExpression = 'auc.end_time ASC';
            break;
        default:
            $orderByExpression = 'auc.end_time ASC';
            break;
    }
    $statusExpression = '';
    switch ($status) {
        case 'running':
            $statusExpression = 'IN_PROGRESS';
            break;
        case 'tostart':
            $statusExpression = 'INIT';
            break;
        case 'ended':
            $statusExpression = 'DONE';
            break;
    }

    $sql_query = "SELECT SQL_CALC_FOUND_ROWS auc.id, auc.title, auc.description, auc.current_price, COUNT(bid.id) as bid_count, auc.end_time
                    FROM Auction AS auc
                    LEFT JOIN Bid AS bid ON bid.auction_id = auc.id
                    JOIN Auction_Product AS auc_item ON auc.id = auc_item.auction_id
                    JOIN Item AS item ON auc_item.item_id = item.id
                    WHERE auc.status = '%s'
                    AND (item.description LIKE '%%%s%%'
                        OR auc.description LIKE '%%%s%%'
                        OR item.name LIKE '%%%s%%'
                        or auc.title LIKE '%%%s%%'
                        )
                    ";
    if($category_id != 'all') {
        $sql_query .= "AND item.category_id = $category_id ";
    }
    
    $sql_query .= "GROUP BY auc.id
                    ORDER BY %s
                    LIMIT %u
                    OFFSET %u;
    ";
    $formatted_sql_query = sprintf($sql_query, $statusExpression, $keyword, $keyword, $keyword, $keyword, $orderByExpression, $page_size, $offset_value);

    $result = $connection->query($formatted_sql_query);
    $auctions = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $auctions[] = $row;
        }
    }

    return $auctions;
}

function getRowCount() {  // This function should be called almost immediately after the execution of SQL_CALC_FOUND_ROWS
    global $connection;

    $sql_query = "SELECT FOUND_ROWS() AS total_rows";

    $result = $connection->query($sql_query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return $row['total_rows'];
        }
    }
    return 0;
}
?>
