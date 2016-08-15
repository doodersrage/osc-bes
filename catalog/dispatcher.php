<?php
$_404PAGE="/error-page/";  // place in document root
require("includes/configure.php");

if (isset($_SERVER['REDIRECT_URL']))
{
	
	$REDIRURL=$_SERVER['REDIRECT_URL'];
	// this section will check for standard 301 category, page or product page redirect
	if (substr_count($REDIRURL,'/')>1)
	{
		list($undef,$searchurl,$restofline)=explode('/',$REDIRURL,3) ;

		$Lconn=mysql_pconnect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
		mysql_select_db(DB_DATABASE,$Lconn);
		$query="select * from seo_urls where surls_name='$searchurl' limit 1";
		$results=mysql_query($query) or die('Query failed: ' . mysql_error());
	
		if ($reshash=mysql_fetch_array($results, MYSQL_ASSOC))
		{

			$newurl=$reshash{'surls_script'}  ;
			list($nvar,$nval)=explode("=",$reshash{'surls_param'},2);
	
			if ($restofline > "")
			{
				if (preg_match('/(.+)-(.+)-(.+)\.php/',$restofline,$matches))
				{
					$newurl=$matches[1].".php";
					$passargs['page']=$matches[2];
					$passargs['sort']=$matches[3];
					$passargs[$nvar]=$nval;
				} elseif (preg_match('/(.+)-(.+)\.php/',$restofline,$matches))
				{
					$newurl=$matches[1].".php";
					$passargs['page']=$matches[2];
					$passargs[$nvar]=$nval;
				} elseif (preg_match('/(.+)/',$restofline,$matches))
				{
					$newurl=$matches[1];
					$passargs[$nvar]=$nval;
				}
			} else
			{
				$passargs[$nvar]=$nval;
			}
	
			foreach ($passargs as $ark => $arv)
			{
				$_GET[$ark]=$arv;
				$_REQUEST[$ark]=$arv;
				$HTTP_GET_VARS[$ark]=$arv;
			}
	
			$newscript=DIR_WS_HTTP_CATALOG.$newurl;
			$HTTP_SERVER_VARS['SCRIPT_NAME']=$newscript;
			$_SERVER['SCRIPT_NAME']=$newscript;
			$HTTP_SERVER_VARS['PHP_SELF']=$newscript;
			$_SERVER['PHP_SELF']=$newscript;
			$PHP_SELF=$newscript;
			mysql_close($Lconn);	
//			header("HTTP/1.1 301 Moved Permanently");
//			header("Location: http://www.dme-direct.com/".$newurl.$restofline);
			include($newurl);
			exit;
		}

		mysql_close($Lconn);
}

}

header("HTTP/1.0 404 Not Found");
header("Location: http://www.boatequipmentsuperstore.com".$_404PAGE."");
?>
