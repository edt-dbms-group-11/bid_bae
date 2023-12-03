<?php
include_once('database.php');

// Function to get seller's available items
function getSellerItems($seller_id)
{
    global $connection;
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

function getAuctionItems($auction_id)
{
    global $connection;

    // Implement SQL query to fetch items based on auction_id
    $sql = "SELECT i.id, i.name
            FROM Item AS i
            JOIN Auction_Product AS ap ON i.id = ap.item_id
            WHERE ap.auction_id = $auction_id";

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        // Fetch items and store them in an array
        $items = array();
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        return $items;
    } else {
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

function createItem($user_id, $itemTitle, $itemDesc, $category_n, $imageurl)
{
    global $connection;
    $sql = "INSERT INTO item (user_id,name,description,category_id,image_url) values('$user_id','$itemTitle','$itemDesc', '$category_n', '$imageurl')";

    if (mysqli_query($connection, $sql)) {
        echo "<script type='text/javascript'>window.top.location='./create_item_success.php';</script>";
        exit;
    } else {
        echo "error:" . mysqli_error($connection);
    }
}

function getCategoriesFromDatabase()
{
    global $connection;

    $sql = "SELECT id, name FROM category";
    $result = $connection->query($sql);

    // Check if there are rows returned
    if ($result->num_rows > 0) {
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


function getAuctionsFromDatabaseWithParameters($order_by, $category_id, $keyword, $status, $page_num, $page_size)
{
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
    if ($category_id != 'all') {
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

function getRowCount()
{ // This function should be called almost immediately after the execution of SQL_CALC_FOUND_ROWS
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

function validateAuctionData($title, $selectedItems, $startPrice, $reservePrice, $startDate, $endDate, $description)
{
    global $connection;
    $errors = [];

    // Check if title is not empty
    if (empty($title)) {
        $errors[] = "Title cannot be empty.";
    }

    // Validate if each item ID belongs to the user
    foreach ($selectedItems as $itemId) {
        $validateItemQuery = "SELECT user_id FROM Item WHERE id = $itemId";
        $validateItemResult = mysqli_query($connection, $validateItemQuery);

        if (!$validateItemResult) {
            echo "Database error: " . mysqli_error($connection);
            exit;
        }

        $row = mysqli_fetch_assoc($validateItemResult);

        if ($row['user_id'] != $_SESSION['id']) {
            $errors[] = "Invalid item selection.";
        }
    }

    // Check if start price is a positive number
    $startPriceInt = filter_var($startPrice, FILTER_VALIDATE_INT);
    if ((!$startPriceInt || $startPriceInt < 1 || $startPrice != $startPriceInt)) {
        $errors[] = "Start price must be a positive number.";
    }

    // Check if reserve price is a positive number (if provided)
    $reservePriceInt = filter_var($reservePrice, FILTER_VALIDATE_INT);
    if (!$reservePriceInt || $reservePriceInt < 1 || $reservePrice != $reservePriceInt) {
        // If reserve price is provided but not a positive number, show an error
        $errors[] = "Reserve price must be a positive number.";
    } elseif ($reservePrice < $startPrice) {
        // If reserve price is lower than start price, show an error
        $errors[] = "Reserve price cannot be lower than the start price.";
    }

    // Validate start date - you may want to check if the date is in the future
    if (empty($startDate) || strtotime($startDate) <= time()) {
        $errors[] = "Invalid start date.";
    }

    // Validate end date - you may want to check if the date is in the future, and after the start date
    if (empty($endDate) || strtotime($endDate) <= time()) {
        $errors[] = "Invalid end date.";
    } elseif (strtotime($endDate) <= strtotime($startDate)) {
        $errors[] = "Auction cannot end before it begins.";
    }

    return $errors;
}

function updateAuctionDetails($auction_id, $title, $description, $start_price, $reserve_price, $start_date, $end_date)
{
    global $connection; // Assuming you have a database connection

    // TODO: Add any necessary validation or sanitization for user inputs

    // Use prepared statements to prevent SQL injection
    $sql = "UPDATE Auction
            SET title = ?, description = ?, start_price = ?, reserve_price = ?, start_time = ?, end_time = ?
            WHERE id = ?";

    $stmt = $connection->prepare($sql);

    if (!$stmt) {
        return false; // Failed to prepare statement
    }

    // Bind parameters
    $stmt->bind_param("ssddssi", $title, $description, $start_price, $reserve_price, $start_date, $end_date, $auction_id);

    // Execute the statement
    $result = $stmt->execute();

    // Close the statement
    $stmt->close();

    return $result;
}

function getPagedAuctionHistory($user_id, $page_num, $page_size)
{
    global $connection;
    $offset_value = ($page_num - 1) * $page_size;

    $auction_history_query = "SELECT SQL_CALC_FOUND_ROWS
        b.id AS bid_id,
        b.bid_price AS bid_price,
        u.display_name AS seller_name,
        a.seller_id AS seller_id,
        a.title AS auction_title,
        a.id AS auction_id,
        a.start_time AS auction_start_time,
        a.end_time AS auction_end_time,
        a.status AS auction_status,
        (
            b.bid_price = a.current_price AND b.bid_price = a.end_price AND a.status = 'DONE'
        ) AS is_winner
    FROM
        `Bid` AS b
    INNER JOIN(
        SELECT
            MAX(id) AS max_id,
            auction_id
        FROM
            `Bid`
        WHERE
            user_id = $user_id
        GROUP BY
            auction_id
    ) AS max_bid
    ON
        b.id = max_bid.max_id
    INNER JOIN `Auction` AS a
    ON
        b.auction_id = a.id
    INNER JOIN `User` AS u
    ON
        a.seller_id = u.id
    ORDER BY
        b.id DESC
    LIMIT ?, ?;";

    $stmt = $connection->prepare($auction_history_query);
    $stmt->bind_param("ii", $offset_value, $page_size);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function queryUserById($user_id)
{
    global $connection;
    $query = "SELECT * FROM User WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt === false) {
        die('Error on statement: ' . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $user_data;
}

function addBalance($user_id, $amt)
{
    global $connection;

    $user_detail = queryUserById($user_id);
    $current_balance = $user_detail['balance'];
    $new_balance = $current_balance + $amt;

    $update_query = "UPDATE User SET balance = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $update_query);

    if ($stmt == false) {
        die('Error on statement: ' . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "ii", $new_balance, $user_id);
    $update_result = mysqli_stmt_execute($stmt);

    if ($update_result == false) {
        die('Error executing the statement: ' . mysqli_error($connection));
    }

    mysqli_stmt_close($stmt);
}


function findInitStateAuction()
{
    global $connection;

    error_log('findInitAuction');
    $find_init_query = "SELECT * FROM Auction WHERE status = 'INIT' AND start_time >= NOW();";
    $result = mysqli_query($connection, $find_init_query);

    if (!$result) {
        die('Error querying init auctions: ' . mysqli_error($connection));
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $auction_id = $row['id'];

        $updateInitAuction = "UPDATE Auction SET status = 'IN_PROGRESS' WHERE id = $auction_id";
        $updateAuctionResult = mysqli_query($connection, $updateInitAuction);

        if (!$updateAuctionResult) {
            die('Error updating auction to start: ' . mysqli_error($connection));
        }
    }
}

//used in mylistings.php to retrieve auctions as per user's desired filter
function getUserAuctionsByFilter($user_id, $filter)
{
    global $connection;

    // Ensure $filter is a safe value to prevent SQL injection
    $allowed_filters = ['live', 'ended', 'all', 'not_started'];
    if (!in_array($filter, $allowed_filters)) {
        throw new InvalidArgumentException("Invalid filter value provided.");
        return [];
    }

    $sql_query = "SELECT auc.id AS auction_id, auc.title, auc.description, auc.status, auc.current_price, auc.end_time, count(bid.id) as num_bids
  FROM Auction AS auc
  LEFT JOIN Bid as bid on bid.auction_id = auc.id
  WHERE auc.seller_id = $user_id";

    // Add filter conditions to the query
    switch ($filter) {
        case 'live':
            $sql_query .= " AND auc.start_time < NOW() AND auc.end_time > NOW()";
            break;
        case 'ended':
            $sql_query .= " AND auc.end_time <= NOW()";
            break;
        case 'not_started':
            $sql_query .= " AND auc.start_time > NOW()";
        default:
            break;
    }

    $sql_query .= " GROUP BY auc.id";

    $result = $connection->query($sql_query);
    $user_auctions = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $user_auctions[] = $row;
        }
    }

    return $user_auctions;
}

// Function to get auction details by auction_id
function getAuctionDetailsById($auction_id)
{
    global $connection;

    $sql_query = "SELECT * FROM Auction WHERE id = $auction_id";
    $result = $connection->query($sql_query);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function getRecommedationsForUser($user_id, $mode, $page_num, $page_size)
{
    global $connection;
    $offset_value = ($page_num - 1) * $page_size;

    $drop_query = "DROP TABLE IF EXISTS user_$mode, other_$mode;";
    $create_temp_table_one_query = "
    CREATE TEMPORARY TABLE user_$mode (
        user_id INT NOT NULL,
        auction_id INT NOT NULL
    )
    SELECT user_id, auction_id
    FROM $mode
    WHERE user_id = $user_id;";
    
    $create_temp_table_two_query = "
    CREATE TEMPORARY TABLE other_$mode (
        user_id INT NOT NULL,
        auction_id INT NOT NULL
    )
    SELECT user_id, auction_id
    FROM $mode
    WHERE user_id != $user_id
    AND auction_id IN (SELECT auction_id FROM user_$mode);";

    $get_recommendation_query = "
    SELECT SQL_CALC_FOUND_ROWS auc.id, auc.title, auc.description, auc.current_price, COUNT(bid.id) as bid_count, auc.end_time
    FROM Auction AS auc
    LEFT JOIN Bid AS bid ON bid.auction_id = auc.id
    JOIN (
        SELECT DISTINCT($mode.auction_id)
        FROM $mode
        JOIN other_$mode
        ON $mode.user_id=other_$mode.user_id
        WHERE $mode.auction_id NOT IN (SELECT auction_id from user_$mode)
        ) as recs
    ON recs.auction_id = auc.id
    GROUP BY auc.id
    LIMIT $page_size
    OFFSET $offset_value;";

    $connection->query($drop_query);
    $connection->query($create_temp_table_one_query);
    $connection->query($create_temp_table_two_query);
    $result = $connection->query($get_recommendation_query);
    
    // mysqli_query($connection, $formatted_sql_query);
    $auctions = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $auctions[] = $row;
        }
    }
    return $auctions;
}

?>