<?php
require('includes/application_top.php');
if (!$HTTP_GET_VARS['submit'])
{
	?>
	<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//DE">
	<html <?php echo HTML_PARAMS; ?>>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#CCCC33">
	<!-- header //-->
	<?php require(DIR_WS_INCLUDES.'header.php'); ?>
	<!-- header_eof //-->

	<!-- body //-->
	 <table border="0" width="100%" cellspacing="2" cellpadding="2" >
	  <tr>
	    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
	<!-- left_navigation //-->
	<?php require(DIR_WS_INCLUDES.'column_left.php'); ?>
	<!-- left_navigation_eof //-->
	    </table></td><td>
	<!-- body_text //-->

	<?php
	echo "Export and Save Customer Data onto your Local Machine ";
	echo '<form action="'. $PHP_SELF .'">';
	echo '<input type="submit" value="Export" name="submit"></form>';
	?>
	</td>
	</table>
	<!-- footer //-->
	<center><font color="#666666" size="2"></font></center>
	<!-- footer_eof //-->
	<br>
	</body>
	</html>
	<?php
}
else
{
	$contents="PWA,Firstname,Lastname,Gender,DOB,Company,Address,Address2,Zip,City,State,Country,Phone,Email,Fax,Last_Order_Date,Products_Purchased\n";
	$user_query = tep_db_query('select adb.*, c.* from customers as c left join address_book as adb on c.customers_default_address_id = adb.address_book_id');
	while($row = tep_db_fetch_array($user_query)) 
	{
		$order_query = tep_db_query('select o.*, op.* from orders as o left join orders_products as op on o.orders_id = op.orders_id where o.customers_id = '.$row[customers_id].' order by o.date_purchased desc');
		$last_order_date = '';
		$products = array();
		while($order = tep_db_fetch_array($order_query)) {
			if (!strlen($last_order_date)) $last_order_date = $order[date_purchased];
			$products[$order[products_id]] = $order[products_id];
		}
		$products_purchased = implode(' ',$products);

		if ($row[customers_gender]=="m")
		{ 
			$gender = "Male"; 
		}
		elseif ($row[customers_gender]=="f")
		{ 
			$gender = "Female"; 
		}
		else
		{ 
			$gender = ""; 
		}

		if ($row[entry_country_id] == 81)
		{
			$land="Deutschland";
		}
		else 
		{
			$country_query = tep_db_query("select countries_name from countries where countries_id = '" . $row[entry_country_id] . "';");
			$landq = tep_db_fetch_array($country_query);
			$land=$landq[countries_name];
		}
		if ($row[entry_zone_id] != 0)
		{
			$state_query = tep_db_query("select zone_name from zones where zone_id = '" . $row[entry_zone_id ] . "';");
			$stateq = tep_db_fetch_array($state_query);
			$state =$stateq[zone_name];
		}

		$contents.=$row[purchased_without_account].",";
		$contents.=$row[customers_firstname].",";
		$contents.=$row[customers_lastname].",";
		$contents.=$gender.",";
		$contents.=($row[customers_dob]=='0000-00-00 00:00:00'?"":$row[customers_dob]).",";

		$contents.=$row[entry_company].",";
		$contents.=$row[entry_street_address].",";
		$contents.=$row[entry_suburb].",";
		$contents.=$row[entry_postcode].",";
		$contents.=$row[entry_city].",";
		//$contents.=$row[entry_state].",";
		$contents.=$state.",";
		$contents.=$land.",";

		$contents.=$row[customers_telephone].",";
		$contents.=$row[customers_email_address].",";
		$contents.=$row[customers_fax].",";
		$contents.=$last_order_date.",";
		$contents.=$products_purchased."\n";
	}
	Header("Content-Disposition: attachment; filename=export.csv");
	print $contents;
}
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>