<?php
/*
  $Id: header_tags_popup_help.php,v 1.0 2005/09/22 
   
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce
  
  Released under the GNU General Public License
*/
?>
<style type="text/css">
.popupText {color: #000; font-size: 12px; } 
</style>
<table border="0" cellpadding="0" cellspacing="0" class="popupText">
  <tr><td><hr class="solid"></td></tr>
  <tr>
   <td class="popupText"><p><b>What are HTTA, HTDA, HTKA and HTCA used for?</b><br><br>
    Header Tags comes with a default set of tags. You can create your own
    set of tags for each page (it comes with some set up, like for index
    and product pages).

<pre>
HT = Header Tags  
T  = Title 
A  = All 
D  = Description
K  = Keywords
C  = Categories *
</pre> 

HTTA, HTDA, HTKA are used to append the default site tags instead of using them 
only if the fields are blank.<br><br>

<b>* Note:</b> The HTCA is a special option applicable only to index, product_info, 
& pages (informational pages). It causes the name of the specific category, manufacturer, 
product, or info page currently being displayed to be prepended to the title & 
keywords. In addition, the first 200 characters of the description content are prepended 
to the description tag. This is useful in case tags have yet to be entered in for all 
of your pages.<br><br>

If HTTA is set on (checked), this will append the Header Tags Title All (default site 
title) to the title you set up for the page.<br><br>

So if you have the option checked, both titles will be displayed.
Let's say your title is Mysite and the default title is osCommerce.<br>
<pre>
With HTTA on, the title is
 Mysite Oscommerce
With HTTA off, the title is
 Mysite
</pre>
</p>
  </td>
 </tr> 
</table>
