<?php
session_start();
include_once('check_session.php');
include_once('database.php');

// TODO: Import database funcs from here
// include_once('database_funcs.php');

if (isset($_POST['type']) && $_POST['type'] === 'remove') {
    remove_item();
}

// TODO: Move this to database_funcs.php
function remove_item()
{
    global $connection;

    $user_id = $_SESSION['id'];
    if (isset($_POST['item_id'])) {
        $item_id = $_POST['item_id'];

        $get_user_id = "SELECT user_id FROM Item WHERE id = $item_id";
        $user_id_result = mysqli_query($connection, $get_user_id);

        if (!$user_id_result) {
            echo json_encode(['status' => 'error', 'message' => 'No such user']);
            exit();
        }

        $row_user = mysqli_fetch_array($user_id_result);

        if (!$row_user || $row_user[0] !== $user_id) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized process']);
            exit();
        }

        // Update SQL query to update the item
        $update_query = "UPDATE Item SET is_available = 0 WHERE id = $item_id";
        $result_update = mysqli_query($connection, $update_query);

        if ($result_update) {
            echo json_encode(['status' => 'success']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'item_id is not set']);
    }
}
?>