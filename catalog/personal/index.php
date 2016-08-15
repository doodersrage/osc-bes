<?php
$ip = "$REMOTE_ADDR"."\n";

// stores the banned IP addresses - change the user name
$sandtrap = file('home/boatequipmentsuperstore.com/banned/IP_Trapped.txt');
sort($sandtrap);
reset($sandtrap);

$found = 0;
foreach( $sandtrap as $blockip ) {
  $tester = strcmp($blockip,$ip);
  if ( $tester == 0 ) {
    $found = 1;
    break;
  }
  if ( $tester > 0 ) {
    array_push($sandtrap,$ip);
    sort($sandtrap);
    reset($sandtrap);
    break;
  }
}

if ( $found == 0 ) {
// path to your banned IP file - change the username
  $fp = fopen("/home/boatequipmentsuperstore.com/banned/IP_Trapped.txt","w");
  if ( $fp != 0 ) {
    foreach( $sandtrap as $blockip ) {
      fputs($fp,"$blockip");
    }
    fclose($fp);
  }
}
$ua = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
$ip = $REMOTE_ADDR;
$todaysdate = date("m/d/Y h:i:s a",time());
// the next line mails you when an IP gets banned 
mail("mindy@boatequipmentsuperstore.com", "IP Banned $todaysdate", "$ip ($ua) has been banned.\n\n","From: BANNED@boatequipmentsuperstore.com");
// the next line is the page to redirect them to after they get banned 
header ("Location: http://www.boatequipmentsuperstore.com/blocked.php");
?>
<HTML>
<HEAD>
  <meta name="robots" content="noindex,nofollow">
  <TITLE>Trapped</TITLE>
    </HEAD>
<BODY>
<center>
<h1>Trapped</h1>
</center>
</BODY>
</HTML>