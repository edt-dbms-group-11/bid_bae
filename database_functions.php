<?php

// Function to get categories from the database
function getCategoriesFromDatabase()
{
    // Database connection parameters
    // $servername = "127.0.0.1";
    // $username = "root";
    // $password = "";
    // $dbname = "auction_system";

    // Create connection
    $conn = new mysqli("localhost", "root", "", "auction_system");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch categories from the category table
    $sql = "SELECT id, name FROM category";

    // Execute the query
    $result = $conn->query($sql);

    // Check if there are rows returned
    if ($result->num_rows > 0) {
        // Fetch categories and store them in an array
        $categories = array();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        // Close the database connection
        $conn->close();

        return $categories;
    } else {
        // If no categories are found, return an empty array
        $conn->close();
        return array();
    }
}

?>