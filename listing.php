<style>
  .alert-purple {
    background-color: rgba(213, 184, 255) !important;
  }

</style>

<?php 
  include_once("header.php");
  include_once("database.php");
  include_once("bid_winner_cron.php");
?>

<?php require("utilities.php")?>
<?php
  if ($_SESSION['username'] != null) {
    $username = $_SESSION['username'];
    $display_name = $_SESSION['display_name'];
    $email = $_SESSION['email'];
    $is_logged_in = true;
  }

  $auction_id;
  $user_id;

  if (isset($_GET['auction_id'])) {
    $auction_id = $_GET['auction_id'];
    checkValidAuctionId($connection, $auction_id);
  } else {
    echo("Invalid auction data");
    header("refresh:3;url=browse.php");
  } 

  if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
  }

  $auctionData = queryAuctionDetail($connection, $auction_id);
  $auctionLogsList = queryAuctionLogs($connection, $auction_id);

  $auction_id = $auctionData['auction_id'];
  $auction_seller_id = $auctionData['seller_id'];
  $auction_title = $auctionData['title'];
  $auction_description = $auctionData['auction_description'];
  $auction_reserved_price = $auctionData['reserved_price'];
  $auction_start_price = $auctionData['start_price'];
  $auction_end_price = $auctionData['end_price'];
  $auction_current_price = $auctionData['current_price'];
  $auction_end_time = $auctionData['end_time'];
  $auction_start_time = $auctionData['start_time'];
  $auction_status = $auctionData['auction_status'];
  $auction_items = $auctionData['items'];
  $auction_user_display_name = $auctionData['user_display_name'];
  
  

  // TODO(paul): move query to database fn file
  function queryAuctionDetail($connection, $auction_id) {
    $auction_detail_query = "SELECT Auction.id as auction_id, Auction.title as title, Auction.description as auction_description, reserved_price, start_price, end_price, current_price, end_time, start_time, seller_id, Auction.status as auction_status, Item.image_url, item_id, Item.name as item_name, Item.description as item_description, User.display_name FROM Auction_Product
    JOIN Auction ON Auction_Product.auction_id = Auction.id
    JOIN Item ON Auction_Product.item_id = Item.id
    JOIN User ON User.id = Item.user_id
    WHERE auction_id = $auction_id;";

    $auction_detail_item = mysqli_query($connection, $auction_detail_query);
    if (!$auction_detail_item) {
      die('Invalid query: ' . mysqli_error($connection));
    }
    
    $auction_detail = array();

    while ($row = mysqli_fetch_assoc($auction_detail_item)) {
      $auction_detail[] = $row;
    }
    $mergedAuctions = mergeAuctionDetails($auction_detail);
    return $mergedAuctions[$auction_id];
  }

  function checkValidAuctionId($connection, $auction_id) {
    $auction_single_query = "SELECT * FROM Auction_Product WHERE auction_id=$auction_id";
    $auction_detail_item = mysqli_query($connection, $auction_single_query);
    
    if (!$auction_detail_item || mysqli_num_rows($auction_detail_item) === 0) {
        http_response_code(404);
        include("404.php");
        exit;
    }

    return true;
  }

  function mergeAuctionDetails($auctionDetails) {
      $mergedAuctions = array();
  
      foreach ($auctionDetails as $auction) {
        $auctionId = $auction['auction_id'];

        if (!isset($mergedAuctions[$auctionId])) {
          $mergedAuctions[$auctionId] = array(
            'auction_id' => $auction['auction_id'],
            'title' => $auction['title'],
            'auction_description' => $auction['auction_description'],
            'reserved_price' => $auction['reserved_price'],
            'start_price' => $auction['start_price'],
            'end_price' => $auction['end_price'],
            'current_price' => $auction['current_price'],
            'end_time' => $auction['end_time'],
            'start_time' => $auction['start_time'],
            'auction_status' => $auction['auction_status'],
            'seller_id' => $auction['seller_id'],
            'image_url' => $auction['image_url'],
            'items' => array(),
            'user_display_name' => $auction['display_name'],
          );
        }

        $mergedAuctions[$auctionId]['items'][] = array(
          'item_id' => $auction['item_id'],
          'item_name' => $auction['item_name'],
          'item_description' => $auction['item_description'],
          'image_url' => $auction['image_url'],
        );
      }
  
      return $mergedAuctions;
  }
  
  function queryAuctionLogs($connection, $auction_id) {
    $auction_log_query = "SELECT
                          Bid.id AS bid_id,
                          Bid.bid_price,
                          User.id AS user_id,
                          User.display_name
                          FROM
                            Bid
                          JOIN User ON Bid.user_id = User.id
                          WHERE
                            Bid.auction_id = $auction_id;";

    $auction_log_items = mysqli_query($connection, $auction_log_query);
    if (!$auction_log_items) {
      die('Invalid query: ' . mysqli_error($connection));
    }

    $auction_logs = array();
    
    while ($row = mysqli_fetch_object($auction_log_items)) {
      $auction_logs[] = $row;
    }
  
    // var_dump($auction_logs);
    return $auction_logs;
  }

  ?>  

<?php

  // TODO: Note: Auctions that have ended may pull a different set of data,
  //       like whether the auction ended in a sale or was cancelled due
  //       to lack of high-enough bids. Or maybe not.
  
  // Calculate time to auction end:
  $now = new DateTime('now', new DateTimeZone('UTC'));
  $auction_end_time_converted = new DateTime($auction_end_time, new DateTimeZone('UTC'));
  
  if ($now < $auction_end_time_converted) {
    $time_to_end = date_diff($now, $auction_end_time_converted);
    $time_remaining = '' . display_time_remaining($time_to_end) . '';
  }
  
  // TODO: If the user has a session, use it to make a query to the database
  //       to determine if the user is already watching this item.
  //       For now, this is hardcoded.
  $is_logged_in = true;
  $watching = false;
?>

<?php
   $is_auction_self_owned = $auction_seller_id == $user_id;
?>

<div class="container py-4">
  <div class="row flex border border-secondary rounded px-4 pb-3">
    <div class="left col-6">
      <h2 class="mt-3"><?php echo($auction_title); ?></h2>
      <div class="auction-detail">
        <div class="auction-user pb-1">
          by<strong> <?php echo($auction_user_display_name) ?></strong>
        </div>
        <p><?php echo($auction_description); ?></p>
      </div>
    </div>
    <div class="right col-6">
      <div class="auction-right align-content-around my-1 row">
        <div class="py-3 justify-content-between align-content-between ">
          <div class="auction-detail">
            <?php if ($auction_status !== 'ended' && $now <= $auction_end_time_converted): ?>
              <div class="badge badge-pill badge-info py-2 px-4 mb-2" role="alert">
                Current bid: £<?php echo(number_format($auction_current_price, 2)) ?>
            <?php endif; ?>
            </div>
          </div>
          <?php if ($is_auction_self_owned) { ?>
            <div class="alert alert-purple">
              <strong>Warning!</strong> You are not allowed to bid on your own auction.
            </div>
          <?php } else { ?>
            <div class="auction-buttons">
              <?php if ($auction_status !== 'ended' && $now <= $auction_end_time_converted): ?>
                <p class="h4">Make this yours</p>
                <div class="auction-butons-top row">
                  <div class="pl-3 form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">£</span>
                      </div>
                      <input oninput="onBidInput()" type="text" class="form-control" placeholder="Your bid" id="user-bid-input" aria-describedby="basic-addon2">
                      <div class="input-group-append">
                        <button onclick="submitBid()" id="btn-place-bid" class="btn btn-outline-secondary disabled" disabled type="button">Place</button>
                      </div>
                    </div>
                  </div>
                </div>
                <?php else: ?>
                  <?php if (intval($auction_current_price) < intval($auction_reserved_price)): ?>
                      <p class="h5 py-3">You've missed the it!</p>
                      <p>This auction ended due to lack of high-valued bids</p>
                  <?php else: ?>
                      <p class="h5 py-3">Someone's bought it!</p>
                      <p>This auction has ended. It was sold for £<?php echo number_format($auction_end_price, 2); ?></p>
                  <?php endif; ?>
                <?php endif; ?>
              <div class="pb-2" id="bid-alert-container"></div>
            </div>
          <?php } ?>
          <div class="bottom-content">
            <div class="">
              <?php if (($now < $auction_end_time_converted) && !($is_auction_self_owned)): ?>
              <!-- [WIP] TODO: continue watchlist fn -->
              <div id="watch_nowatch" <?php if ($is_logged_in && $watching) echo('style="display: none"');?>>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
              </div>
              <div id="watch_watching" <?php if (!$is_logged_in || !$watching) echo('style="display: none"');?>>
                <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
              </div>
              <?php endif ?>
            </div>
            <div class="bottom-time mt-3">
              <?php if ($now > $auction_end_time_converted): ?>
                <div class="alert alert-warning" role="alert">
                  This auction ended at <?php echo(date_format($auction_end_time_converted, 'j M H:i')) ?>
                </div>
              <?php else: ?>
                <div class="alert alert-warning" role="alert">
                  This auction will end in <?php echo(date_format($auction_end_time_converted, 'j M \a\t H:i')); ?> (<?php echo $time_remaining; ?> remaining)
                </div>
              <?php endif ?>
            </div>

            <!-- TODO(paul): Remove this dummy trigger -->
            <!-- <div class="dummy-end">
              <button id="endAuctionButton">end this auction</button>
            </div> -->
          </div>
        </div>
      </div>
      
    </div>
    <div class="bottom-item-container">
      <h4 class="my-3 lead">Items included in this auction : </h4>
      <div class="bottom-item-container--list d-flex">
        <?php foreach ($auction_items as $item): ?>
          <div class="card px-1 py-1 mx-1 my-1" style="max-width: 300px;">
            <div class="row g-0">
              <div class="col-md-4">
                <img src="<?= $item['image_url'] ?>" alt="<?= $item['item_name'] ?>" class="img-fluid" object-fit: cover; />
              </div>
              <div class="col-md-8 border-left">
                <div class="card-body">
                  <h6 class=""><?= $item['item_name'] ?></h6>
                  <p class="card-text">
                    <small class="text-muted"><?= $item['item_description'] ?></small>
                  </p>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="auction-history flex row">
      <div class="auction-history--list">
        <div class="container mt-3">
          <p class="lead text-sm">Bid History</p>
          <?php if (empty($auctionLogsList)): ?>
            <div class="alert alert-info" role="alert">
                Lucky! You're one of the first to bid! Place your bid now!
            </div>
          <?php else: ?>
            <table class="table table-sm table-bordered-sm">
                <thead>
                    <tr>
                      <th>ID</th>
                      <th>Bid Price</th>
                      <th>Display Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auctionLogsList as $log): ?>
                        <tr>
                          <td><?= $log->bid_id ?></td>
                          <td>£<?= $log->bid_price ?></td>
                          <td><?= $log->display_name ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
          <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include_once("footer.php")?>

<script> 
  let bidInput = document.getElementById('user-bid-input');
  let bidBtn = document.getElementById('btn-place-bid');

  function addToWatchlist(button) {
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {functionname: 'add_to_watchlist', arguments: [<?php echo($auction_id);?>]},

      success: 
        function (obj, textstatus) {
          console.log("Success");
          var objT = obj.trim();
  
          if (objT == "success") {
            $("#watch_nowatch").hide();
            $("#watch_watching").show();
          }
          else {
            var mydiv = document.getElementById("watch_nowatch");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
          }
        },

      error:
        function (obj, textstatus) {
          console.log("Error");
        }
    }); // End of AJAX call
  }

  function removeFromWatchlist(button) {
    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($auction_id);?>]},

      success: 
        function (obj, textstatus) {
          // Callback function for when call is successful and returns obj
          console.log("Success");
          var objT = obj.trim();
  
          if (objT == "success") {
            $("#watch_watching").hide();
            $("#watch_nowatch").show();
          }
          else {
            var mydiv = document.getElementById("watch_watching");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
          }
        },

      error:
        function (obj, textstatus) {
          console.log("Error");
        }
    });
  } 

  function onlyNum() {
    bidInput.value = bidInput.value.replace(/[^0-9]/g, '');
    handleBidBtnState()
  }

  function handleBidBtnState () {
    if (bidInput.value) {
      bidBtn.classList.remove('disabled');
      bidBtn.removeAttribute('disabled')
    } else {
      bidBtn.classList.add('disabled');
      bidBtn.setAttribute('disabled', 'disabled')
    }
  }

  function onBidInput() {
    onlyNum();
    let bidAmount = $('#user-bid-input').val().trim();
    let currBid = <?php echo isset($auction_current_price) ? $auction_current_price : ''; ?> || 0;
    let bidAlert = '<div class="badge badge-danger px-4 py-1 mb-1">Bid must be higher than current bid</div>';
    
    if (parseInt(bidAmount) < currBid) {
      bidBtn.classList.add('disabled');
      bidBtn.setAttribute('disabled', 'disabled')
      $('#bid-alert-container').html(bidAlert);
    } else {
      $('#bid-alert-container').empty();
    }
  }

  function submitBid () {
    let currBid = <?php echo($auction_current_price); ?>;
    let userId = "<?php echo $_SESSION['id']; ?>" || null
    let auctionId = <?php echo($auction_id); ?>;
    let bidAmount = $('#user-bid-input').val().trim();

    $.ajax({
      type: "POST",
      url: "place_bid.php",
      data: {
        auction_id: auctionId,
        user_id: userId,
        bid_amount: bidAmount,
        current_bid: currBid,
      },
      success: function(response) {
        Swal.fire({
          title: "Bid successfully placed!",
          text: "You will be redirected soon",
          icon: "success"
        }).then((result) => {
        if (result.isConfirmed) {
          setTimeout(() => {
            window.location.reload()
          }, 600);
        }
      })
      },
      error: function(response) {
        console.log(response);
        let errorMessage = response.responseJSON.message || "Something went wrong with the bidding process";
        Swal.fire({
          title: "Bid fails to be placed",
          text: errorMessage,
          icon: "error"
        }).then((result) => {
          setTimeout(() => {
            window.location.reload()
          }, 400);
        })
      }
    });
  }

  // TODO(paul): Remove this dummy trigger
  // document.getElementById('endAuctionButton').addEventListener('click', function() {
  //   fetch('bid_winner_cron.php')
  //     .then(response => response.text())
  //     .then(data => {
  //       // You can handle the response here if needed
  //       console.log(data);
  //     })
  //     .catch(error => {
  //       console.error('Error:', error);
  //   });
  // });
</script>

<style>
  body {
    background-color: #f8f9fa;
  }
  .container {
    max-width: 800px;
  }
  table {
    font-size: 14px;
  }
  th, td {
    padding: 0.5rem;
    text-align: center;
  }
</style>