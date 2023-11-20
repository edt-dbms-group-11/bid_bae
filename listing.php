<?php 
  include_once("header.php");
  include_once("session_check.php");
  include_once("database.php");
?>
<?php require("utilities.php")?>

<?php
  session_start();

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
  } else {
    echo("Invalid auction data");
    header("refresh:3;url=browse.php");
  } 

  if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
  }

  $auctionData = queryAuctionDetail($connection, $auction_id);

  $auction_id = $auctionData[0];
  $auction_title = $auctionData[1];
  $auction_description = $auctionData[2];
  $auction_reserved_price = $auctionData[3];
  $auction_start_price = $auctionData[4];
  $auction_end_price = $auctionData[5];
  $auction_current_price = $auctionData[6];
  $auction_end_time = $auctionData[7];
  $auction_start_time = $auctionData[8];
  $auction_status = $auctionData[9];
  $item_url = $auctionData[10];
  $item_id = $auctionData[11];
  $item_name = $auctionData[12];

  // TODO(paul): move query to database fn file
  function queryAuctionDetail($connection, $auction_id) {
    $auction_detail_query = "SELECT Auction.id as auction_id, Auction.title as title, Auction.description as auction_description, reserved_price, start_price, end_price, current_price, end_time, start_time, Auction.status as auction_status, Item.image_url, item_id, Item.name as item_name, User.display_name FROM Auction_Product
    JOIN Auction ON Auction_Product.auction_id = Auction.id
    JOIN Item ON Auction_Product.item_id = Item.id
    JOIN User ON User.id = Item.user_id
    WHERE auction_id = '$auction_id';";

    $auction_detail_item = mysqli_query($connection, $auction_detail_query);
    if (!$auction_detail_item) {
        die('Invalid query: ' . mysqli_error($connection));
    }

    return mysqli_fetch_array($auction_detail_item);
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


<div class="container py-4">

  <div class="row flex border border-secondary rounded px-4 pb-3">
  <div class="left col-6">
    <h2 class="my-3"><?php echo($auction_title); ?></h2>
    <img src="<?php echo $auction_image; ?>" class="img-fluid" alt="Auction Image" style="height: 500px; object-fit: cover;">
  </div>
  <div class="right col-6">
    <div class="auction-right align-content-around my-1 row">
    <div class="py-3 justify-content-between align-content-between ">
      <div class="auction-detail">
        <p class="lead">Detail</p>
        <p class="small"><?php echo($auction_description); ?></p>
        <div class="badge badge-pill badge-info py-2 px-4 mb-2" role="alert">
          Current bid: £<?php echo(number_format($auction_current_price, 2)) ?>
        </div>
      </div>
      <div class="auction-buttons">
        <p class="h4">Make this yours</p>
        <div class="auction-butons-top row d-flex">
          <div class="col-10">
            <div class="form-group">
              <div>
                <div class="input-group mb-1">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">£</span>
                  </div>
                  <input oninput="onBidInput()" type="text" class="form-control" placeholder="Your bid" id="user-bid-input" aria-describedby="basic-addon2">
                  <div class="input-group-append">
                  <button onclick="submitBid()" id="btn-place-bid" class="btn btn-outline-secondary disabled" disabled type="button">Place</button>
                </div>
                <div id="bid-alert-container"></div>
                <div class="auction-history">

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bottom-content">
        <div class="row d-flex justify-content-evenly px-3">
          <?php
            /* The following watchlist functionality uses JavaScript, but could
              just as easily use PHP as in other places in the code */
            if ($now < $auction_end_time):
          ?>
          <!-- [WIP] TODO: continue watchlist fn -->
            <div id="watch_nowatch" <?php if ($is_logged_in && $watching) echo('style="display: none"');?> >
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
            </div>
            <div id="watch_watching" <?php if (!$is_logged_in || !$watching) echo('style="display: none"');?> >
              <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
              <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
            </div>
          <?php endif ?>

        </div>
        <div class="bottom-time pt-4">
          <?php if ($now > $auction_end_time_converted): ?>
            <div class="alert alert-warning" role="alert">
              This auction ended at <?php echo(date_format($auction_end_time, 'j M H:i')) ?>
            </div>        
          <?php else: ?>
            <div class="alert alert-warning" role="alert">
              This auction will end in <?php echo(date_format($auction_end_time, 'j M H:i') . $time_remaining) ?>
            </div>  
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>
  </div>


<?php include_once("footer.php")?>


<script> 
  let bidInput = document.getElementById('user-bid-input');
  let bidBtn = document.getElementById('btn-place-bid');

  function addToWatchlist(button) {
    console.log("These print statements are helpful for debugging btw");

    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>]},

      success: 
        function (obj, textstatus) {
          // Callback function for when call is successful and returns obj
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

  } // End of addToWatchlist func

  function removeFromWatchlist(button) {
    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>]},

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
    }); // End of AJAX call

  } // End of addToWatchlist func

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
    let currBid = <?php echo($auction_current_price); ?>;
    let bidAlert = '<div class="badge badge-danger px-4">Bid must be higher than current bid</div>';
    
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
          }, 1000);
        }
      })
      },
      error: function(xhr, status, error) {
        console.log(error);
      }
    });
  }

</script>