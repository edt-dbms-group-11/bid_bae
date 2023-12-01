<?php

require 'vendor/autoload.php';
use \SendGrid\Mail\Mail;
function sendmail($recipient, $subject, $content){
  //var_dump(openssl_get_cert_locations());
$email = new Mail();
$email->setFrom(
  'bidbae.auction@gmail.com',
  'Bid Bae'
);
$email->setSubject($subject);
// Replace the email address and name with your recipient
$email->addTo($recipient);
  
$email->addContent($content);
$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
try {
  $response = $sendgrid->send($email);
  printf("Response status: %d\n\n", $response->statusCode());

  $headers = array_filter($response->headers());
  echo "Response Headers\n\n";
  foreach ($headers as $header) {
      echo '- ' . $header . "\n";
  }
} catch (Exception $e) {
  echo 'Caught exception: '. $e->getMessage() ."\n";
}
}



// display_time_remaining:
// Helper function to help figure out what time to display
function display_time_remaining($interval) {

    if ($interval->days == 0 && $interval->h == 0) {
      // Less than one hour remaining: print mins + seconds:
      $time_remaining = $interval->format('%im %Ss');
    }
    else if ($interval->days == 0) {
      // Less than one day remaining: print hrs + mins:
      $time_remaining = $interval->format('%hh %im');
    }
    else {
      // At least one day remaining: print days + hrs:
      $time_remaining = $interval->format('%ad %hh');
    }

  return $time_remaining;

}

// This function prints an HTML <li> element containing an auction listing
function print_listing_li($auction_id, $title, $desc, $price, $num_bids, $end_time)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  }
  else {
    $desc_shortened = $desc;
  }
  
  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  }
  else {
    $bid = ' bids';
  }
  
  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  }
  else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }
  
  // Print HTML
  echo('
      <li class="list-group-item d-flex justify-content-between">
        <div class="p-2 mr-5"><h5><a href="listing.php?auction_id=' . $auction_id . '">' . $title . '</a></h5>' . $desc_shortened . '</div>
        <div class="text-center text-nowrap"><span style="font-size: 1.5em">£' . $price . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
      </li>'
    );
  }
