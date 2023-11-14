<?php include_once('database.php') ?>

<?php
function getCategoriesFromDatabase()
{
    $sql = "SELECT id, name FROM category";
    global $connection;
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        $categories = array();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        // $connection->close();

        return $categories;
    } else {
        // $connection->close();
        return array();
    }
}

?>