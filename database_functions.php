<?php

include_once('database.php');

// Function to connect to the database
// function connectToDatabase() 
// {
//     // Database connection parameters
//     $servername = "localhost";
//     $username = "mamp";
//     $password = "";
//     $dbname = "auction_system";

//     // Create connection
//     $mysqli = new mysqli($servername, $username, $password, $dbname);

//     // Check connection
//     if ($mysqli->connect_error) {
//         die("Connection failed: " . $mysqli->connect_error);
//     }

//     return $mysqli;
// }

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
        echo "<script>alert('new record inserted')</script>";
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
    // Create connection
    //$connection = connectToDatabase();

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

        // Close the database connection
        // $conn->close();

        return $categories;
    } else {
        // If no categories are found, return an empty array
        // $conn->close();
        return array();
    }
}

?>