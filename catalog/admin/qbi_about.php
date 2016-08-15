<?php
/*
$Id: qbi_create.php,v 2.10 2005/05/08 al Exp $

Quickbooks Import QBI
contribution for osCommerce
ver 2.10 May 8, 2005
(c) 2005 Adam Liberman
www.libermansound.com
info@libermansound.com
Please use the osC forum for support.
Released under the GNU General Public License

    This file is part of Quickbooks Import QBI.

    Quickbooks Import QBI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Quickbooks Import QBI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Quickbooks Import QBI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/qbi_general.php');
require(DIR_WS_INCLUDES . 'qbi_version.php');
require(DIR_WS_INCLUDES . 'qbi_definitions.php');
require(DIR_WS_INCLUDES . 'qbi_page_top.php');
require(DIR_WS_INCLUDES . 'qbi_menu_tabs.php');

echo '<div class="createhead">'.ABOUT_VER." ".QBI_VER.'</div>';
echo ABOUT_DON."<br /><br />";
echo ABOUT_DON_PAYPAL."<br /><br />"; ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHHgYJKoZIhvcNAQcEoIIHDzCCBwsCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYAuNZhbYTs0HRuohXBxdiP/uYdDHQcbl9CPF+2y9BzBgbQUriMp7ukEGdOgvwa63GGYRksZDCg8hSDLaFN/lIcTqmt46h1xhSEOxwG7q3SXE6dSWeoPaUO0Xh+BjqHWFAIlffkRsSxLxZc6kZ3rsV5W4jhPO92zC98GX/8oyV/mmTELMAkGBSsOAwIaBQAwgZsGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQI0//JscBCdoqAeMt+iGuDjfPdrJLyHqXw96Jzw9lah1A6O8eQjZrYfo5lpv07eDa87zKjRA/4BN36wbrg0vm5UDBhB0geG9HJFoYHFTcIg2s3cntmxOENr8h66EBdBfEAYl0nQXf/0IVuDNGLGoANkFvXDF8/jsfshSZDw5FTVz/xGqCCA4cwggODMIIC7KADAgECAgEAMA0GCSqGSIb3DQEBBQUAMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbTAeFw0wNDAyMTMxMDEzMTVaFw0zNTAyMTMxMDEzMTVaMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAwUdO3fxEzEtcnI7ZKZL412XvZPugoni7i7D7prCe0AtaHTc97CYgm7NsAtJyxNLixmhLV8pyIEaiHXWAh8fPKW+R017+EmXrr9EaquPmsVvTywAAE1PMNOKqo2kl4Gxiz9zZqIajOm1fZGWcGS0f5JQ2kBqNbvbg2/Za+GJ/qwUCAwEAAaOB7jCB6zAdBgNVHQ4EFgQUlp98u8ZvF71ZP1LXChvsENZklGswgbsGA1UdIwSBszCBsIAUlp98u8ZvF71ZP1LXChvsENZklGuhgZSkgZEwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tggEAMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADgYEAgV86VpqAWuXvX6Oro4qJ1tYVIT5DgWpE692Ag422H7yRIr/9j/iKG4Thia/Oflx4TdL+IFJBAyPK9v6zZNZtBgPBynXb048hsP16l2vi0k5Q2JKiPDsEfBhGI+HnxLXEaUWAcVfCsQFvd2A1sxRr67ip5y2wwBelUecP3AjJ+YcxggGaMIIBlgIBATCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwCQYFKw4DAhoFAKBdMBgGCSqGSIb3DQEJAzELBgkqhkiG9w0BBwEwHAYJKoZIhvcNAQkFMQ8XDTA1MDEyMjIzNTIyNlowIwYJKoZIhvcNAQkEMRYEFMW/FmAKp+GgYFLKTY8B82FhhV0gMA0GCSqGSIb3DQEBAQUABIGATY4bIpowiokPPoswHaIbzEVwKxlbiq0uL33xN9bcuMmvlbefHcGVpipSyxJSxA+Mqb3oUIqxlVuZrfgREUgZKVKtnmxSdOas520yss02zFzohaLXxNujSVyq6pbeRAXCAMkgnytDwUFt2IeJim43A4pv36d7EHxV63tF0qVzd0A=-----END PKCS7-----
">
</form> <?php
echo "<br /><br />".ABOUT_DON_AMAZON." <a href=http://www.amazon.com>Amazon.com</a><br /><br />";
echo ABOUT_SUPPORT."<br /><br />";
echo '<a href="http://www.libermansound.com">www.libermansound.com</a><br />';
echo "adam  -AT-  libermansound  -DOT-  com<br /><br />";
echo ABOUT_LIC."<br /><br />";
?>
<br /><br /> <?php
require(DIR_WS_INCLUDES . 'qbi_page_bot.php');
?>