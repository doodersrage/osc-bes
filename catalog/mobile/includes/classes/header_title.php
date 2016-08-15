<?php
  class headerTitle {
  	
    function headerTitle() {
    }
    
    function write($title = '') {
   		global $cart, $PCSITE; 	
    	$this->title =  (strlen($title) > 0)? $title : HEADING_TITLE;
    	$leftButton = " ";
		if(sizeof($cart->contents) > 0) 
			$rightButton = '<a href="' . tep_mobile_link(FILENAME_SHOPPING_CART) . '">' . tep_image(DIR_MOBILE_IMAGES . "cart.png") . '</a>';
		else {
			if(isset($PCSITE))
				$url = $PCSITE;
			else {
				$url = str_replace("mobile_", "", $_SERVER['REQUEST_URI']);
			}
			$url .= (strpos($url,'?') > 0) ? '&redirectCancelled=true' : '?redirectCancelled=true';
			$rightButton = '<a href="' . $url . '">' . tep_image(DIR_MOBILE_IMAGES . "pcsite.png") . '</a>';
		}
		echo '
	<table class="headerTitle" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td id="headerTitleLeft" class="headerTitleLeft">'  . $leftButton . '</td>
			<td id="headerTitle" class="headerTitle">' 	   . $this->title . '</td>
			<td id="headerTitleRight" class="headerTitleRight">' . $rightButton . '</td>
		</tr>
	</table>
	<!--  ajax_part_begining -->
	';
    }
}
?>
