<?php
session_start();
if (!isset($_SESSION) || $_SESSION == null) {
    $_SESSION['is_logged_in'] = false;
    $_SESSION['email'] = null;
    $_SESSION['username'] = null;
    $_SESSION['display_name'] = null;
} elseif ($_SESSION['username'] != null) {
    $username = $_SESSION['username'];
    $display_name = $_SESSION['display_name'];
    $email = $_SESSION['email'];
    $is_logged_in = true;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap and FontAwesome CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Custom CSS file -->
    <link rel="stylesheet" href="css/custom.css">

    <title>BidBae</title>
</head>

<body>

    <!-- Navbars -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
        <a class="navbar-brand" href="#">BidBae</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <?php
                if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] == true) {
                    echo "<div class='container'>
                      <div class='row flex'>
                          <div class='align-middle pt-2'>Hi, $display_name</div>
                          <div class='col-4'>
                            <a class='nav-link' href='logout.php'>Logout</a>
                          </div>
                      </div>
                  </div>";
                } else {
                    echo "<button type='button' class='btn nav-link' data-toggle='modal' data-target='#loginModal'>Login</button>";
                }
                ?>
            </li>
        </ul>
    </nav>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <ul class="navbar-nav align-middle">
            <li class="nav-item mx-1">
                <a class="nav-link" href="browse.php">Browse</a>
            </li>
        </ul>
        <?php
        if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] == true) {
            echo '<ul class="navbar-nav align-middle">
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="item_list.php">My Products</a>
                    </li>
                    <li class="nav-item ml-3">
      <a class="nav-link btn border-light" href="create_auction.php">+ Create auction</a>
    </li>
    <li class="nav-item ml-4">
    <a class="nav-link btn border-light" href="create_item.php">+ Create Item</a>
  </li> </ul>';
        } else {
            echo "<div></div>";
        }
        ?>
    </nav>

    <!-- Login modal -->
    <div class="modal fade" id="loginModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Login</h4>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form method="POST" action="login_result.php">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-primary form-control">Sign in</button>
                    </form>
                    <div class="text-center">or <a href="register.php">create an account</a></div>
                </div>

            </div>
        </div>
    </div> <!-- End modal -->

    <!-- Bootstrap and jQuery JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>