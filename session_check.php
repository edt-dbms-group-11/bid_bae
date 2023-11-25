
<?php
  session_start();

  if (!isset($_SESSION) || $_SESSION == null) {
    echo('<div class="text-center">You\'re not logged in. Please re-login if this was a mistake</div>');
    header('refresh:3;url=browse.php');
}
?>
