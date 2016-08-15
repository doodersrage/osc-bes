<?php
// path to your banned IP file - alter as required
$sandtrap = file('home/boatequipmentsuperstore.com/banned/IP_Trapped.txt');
$ua = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
$ip = "$REMOTE_ADDR"."\n";

$punish = 0;

// this sets up code to ban them if they don't supply a user agent
if ( $ua == "" ) {
  $punish = 2;
}

foreach( $sandtrap as $blockip ) {
  $tester = strcmp($blockip,$ip);
  if ( $tester < 0 ) {
    continue;
  }
  if ( $tester == 0 ) {
    $punish = 1;
    break;
  }
  if ( $tester > 0 ) {
    break;
  }
}

if ( $punish != 0 ) {
// the next line is the page to redirect them to 
  header ("Location: http://www.boatequipmentsuperstore.com/**catalog**/blocked.php");
  exit;
}
?>