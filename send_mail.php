<?php
include_once("database.php");
// include_once("bid_winner_cron.php");
include_once("utilities.php");
include_once("database_functions.php");

// function sendmailbidnotplaced($auction_id)
// {
//     global $connection;
//     $time_check_query = "SELECT count(bid.id) as bid_count
//                     FROM Auction auc
//                     LEFT JOIN Bid bid ON auc.id = bid.auction_id
//                     WHERE auc.id = $auction_id
//                     GROUP BY auc.id;";
//     $time_check_result = mysqli_query($connection, $time_check_query);
//     if ($time_check_result) {
//         while ($time_check_row = mysqli_fetch_assoc($time_check_result)) {
//             if ($time_check_row['bid_count'] == 0) {
//                 email_to_seller($auction_id);
//                 return true;
//             }
//         }
//     } else {
//         // Display error message if query fails
//         die('Error: ' . mysqli_error($connection));
//     }

// }

function email_to_seller_bid_not_placed($auction_id)
{
    global $connection;
    $seller_data = getAuctionSellerData($auction_id);
    if($seller_data[2]) {  // Only if the Seller has opted in for emails, do we proceed.
        $auction_data = getAuctionDetailsById($auction_id);
    }
    


    $seller_query = "SELECT seller_id FROM auction WHERE auction.id = $auction_id";
    $seller_result = mysqli_query($connection, $seller_query);
    if ($seller_result) {

        while ($check_all = mysqli_fetch_row($seller_result)) {

            foreach ($check_all as $index => $content) {
                $auction_id = $content[0];
                $title = $content[10];
                $price = $content[4];
                $seller = $content[8];
                $sql = "SELECT email, opt_in_email
                        FROM user
                        WHERE user.id = $seller";
                $user_data = mysqli_fetch_all(mysqli_query($connection, $sql));
                $seller_email = $user_data[0][0];
                $if_opted = $user_data[0][1];
                if ($if_opted) {
                    $content56 = "<h5>OH NO! You have not sold the item " . $title . " because no one have even placed a bid! </h5><br>
                        <p>Highest offered price: ￡" . $price . "</p>";
                    sendmail($seller_email, "OH NO!", $content56);
                }
            }
        }
    } else {
        // Display error message if query fails
        die('Seller Query Failed. Error: ' . mysqli_error($connection));
    }
}

function winner_email($buyer, $bid_price, $auction_id)
{
    global $connection;
    $sql = "SELECT email, opt_in_email
            FROM user
            WHERE user.id = $buyer";
    $buyer_data = mysqli_fetch_all(mysqli_query($connection, $sql));
    $buyer_email = $buyer_data[0][0];
    $buyer_opt_in = $buyer_data[0][1];
    if ($buyer_opt_in) {
        var_dump($buyer_email);
        $item_sql = "SELECT title,seller_id
                    FROM auction
                    WHERE auction.id = $auction_id";
        $item_title = mysqli_fetch_all(mysqli_query($connection, $item_sql))[0][0];
        var_dump($item_title);
        $seller_id = mysqli_fetch_all(mysqli_query($connection, $item_sql))[0][1];
        var_dump($seller_id);
        $content2 = "<h5>Congratulations! You are the winner of the item " . $item_title . " </h5><br>
                    <p>Your offered price: ￡" . $bid_price . "</p>";
        sendmail($buyer_email, "Congratulations!", $content2);
    }
    $seller_sql = "SELECT email, opt_in_email
            FROM user
            WHERE user.id = $seller_id";
    $seller_data = mysqli_fetch_all(mysqli_query($connection, $seller_sql));
    $seller_email = $seller_data[0][0];
    $seller_opt_in = $seller_data[0][1];
    if ($seller_opt_in) {
        var_dump($seller_email);
        $content3 = "<h5>Congratulations! You have sold the item " . $item_title . " </h5><br>
                    <p>Highest offered price: ￡" . $bid_price . "</p>";
        sendmail($seller_email, "Congratulations!", $content3);
    }
}

function outbid($auction_id)
{
    global $connection;
    $outbid_sql = "SELECT user.email AS outbid_user_email, opt_in_email
            FROM user
            JOIN bid ON user.id = bid.user_id
            JOIN auction ON bid.auction_id = auction.id
            WHERE bid.auction_id = $auction_id
            AND bid.bid_price < (
            SELECT MAX(bid_price)
            FROM bid
            WHERE auction_id = $auction_id)";
    $item_sql = "SELECT title
                FROM auction
                WHERE auction.id = $auction_id";
    $item_title = mysqli_fetch_all(mysqli_query($connection, $item_sql))[0][0];
    $bid_sql = "SELECT MAX(bid_price)
                FROM bid
                WHERE bid.auction_id = $auction_id";
    $max_price = mysqli_fetch_all(mysqli_query($connection, $bid_sql))[0][0];
    $outbid_result = mysqli_query($connection, $outbid_sql);
    $email_id = $outbid_result[0]['outbid_user_email'];
    $email_opt_in = $outbid_result[0]['opt_in_email'];
    if ($outbid_result && $email_opt_in) {
        while ($outbid_all = mysqli_fetch_all($outbid_result)) {
            foreach ($outbid_all as $index => $content) {
                $content4 = "<h5>You have been outbid by another buyer for " . $item_title . " </h5><br>
                <p>The new price has become: ￡" . $max_price . "</p>";
                sendmail($content[0], "Outbid by another buyer", $content4);
            }
        }
    }
}
;

?>