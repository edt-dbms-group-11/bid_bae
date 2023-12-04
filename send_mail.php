<?php
include_once("database.php");
include_once("utilities.php");
include_once("database_functions.php");

function sendMailAfterAuctionCreation($auction_id, $auction_title, $start_date)
{
    $seller_data = getAuctionSellerData($auction_id);
    $seller_email = $seller_data[1];
    $seller_name = $seller_data[0];
    if ($seller_data[2]) { // Only if the Seller has opted in for emails, do we proceed.
        $subject = "Your Auction has been created";
        $content = "$seller_name!<br>Your Auction: \"$auction_title\" has been created, and is set to go live at $start_date";
        sendmail($seller_email, $subject, $content);
    } else {
        error_log("Email to seller $seller_name skipped because they opted out");
    }
}

function EmailSellerBidNotPlaced($auction_id)
{
    global $connection;
    $seller_data = getAuctionSellerData($auction_id);
    $seller_email = $seller_data[1];
    $seller_name = $seller_data[0];
    if ($seller_data[2]) { // Only if the Seller has opted in for emails, do we proceed.
        $auction_data = getAuctionDetailsById($auction_id);
        $auction_title = $auction_data['title'];
        $subject = "No bids placed on your auction";
        $content = "$seller_name,<br>Oh No! Unfortunately, you have not sold the items in your auction \"$auction_title\", since no one placed a bid!";
        sendmail($seller_email, $subject, $content);
    } else {
        error_log("Email to seller $seller_name skipped because they opted out");
    }
}

function EmailSellerReservePriceNotMet($auction_id)
{
    global $connection;
    $seller_data = getAuctionSellerData($auction_id);
    $seller_email = $seller_data[1];
    $seller_name = $seller_data[0];
    if ($seller_data[2]) { // Only if the Seller has opted in for emails, do we proceed.
        $auction_data = getAuctionDetailsById($auction_id);
        $auction_title = $auction_data['title'];
        $subject = "Your auction was not sold";
        $content = "$seller_name,<br>Oh No! Unfortunately, you have not sold the items in your auction \"$auction_title\", since no bids met the reserve price!";
        sendmail($seller_email, $subject, $content);
    } else {
        error_log("Email to seller $seller_name skipped because they opted out");
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