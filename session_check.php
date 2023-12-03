<?php
if (!isset($_SESSION) || $_SESSION == null || !isset($_SESSION['id'])) { ?>

  <div class="container my-4">
    <div class="text-center">
      <h2 class="display-4 -">You're not logged in</h2>
      <p>You'll be redirected to homepage. Please re-login if this was a mistake</p>
    </div>
  </div>

  <?php
  header('refresh:1;url=browse.php');
  exit;
}
?>