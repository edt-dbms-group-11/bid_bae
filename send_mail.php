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

function winner_email($buyer_email, $buyer_opt_in, $buyer_name, $buyer_bid, $auction_id)
{
    global $connection;
    $auction_data = getAuctionDetailsById($auction_id);
    $auction_title = $auction_data['title'];

    if ($buyer_opt_in) {
        $buyer_content = "Congratulations $buyer_name!<br>Your bid for ￡$buyer_bid on the auction \"$auction_title\" was successful, you were the highest bidder!";
        $buyer_subject = "You won!";
        sendmail($buyer_email, $buyer_subject, $buyer_content);
    } else {
        error_log("Email to seller $buyer_name skipped because they opted out");
    }

    $seller_data = getAuctionSellerData($auction_id);
    $seller_email = $seller_data[1];
    $seller_name = $seller_data[0];
    if ($seller_data[2]) { // Only if the Seller has opted in for emails, do we proceed.
        $seller_subject = "Congratulations! Your auction was sold";
        $seller_content = "$seller_name,<br>Congratulations $seller_name! You have sold the auction \"$auction_title\" for the highest bid price of ￡$buyer_bid!";
        sendmail($seller_email, $seller_subject, $seller_content);
    } else {
        error_log("Email to seller $seller_name skipped because they opted out");
    }
}

function sendOutbidEmail($bidder_name, $bidder_email, $bidder_opt_in, $bid_amount, $auction_id)
{
    global $connection;

    $auction_data = getAuctionDetailsById($auction_id);
    $auction_title = $auction_data['title'];
    if ($bidder_opt_in) {
        $outbid_content = "Dear $bidder_name<br>Unfortunately, your bid on auction \"$auction_title\" has failed. You bid for ￡$bid_amount was outbid.<br>You can check the current status <a href='https://bidbae.tech/listing.php?auction_id=$auction_id'>here</a>";
        $outbid_subject = "Oh no, you were outbid!";
        sendmail($bidder_email, $outbid_subject, $outbid_content);
    } else {
        error_log("Email to bidder $bidder_name skipped because they opted out");
    }

}
;

?>