<?php include_once('database.php'); ?>
<?php include_once('create_category_result.php'); ?>

<?php
function getCategoriesFromDatabase()
{
    global $connection;
    $sql = "SELECT id, name FROM category";
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
?>
