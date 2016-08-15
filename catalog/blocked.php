<html>

<head>
<title>You are now banned from this web site</title>
<meta name="generator" content="">
</head>

<body bgcolor="#CC6600" text="black" link="blue" vlink="purple" alink="red">
<p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table border="3" width="952" align="center" cellspacing="0" bordercolor="#990000" bordercolordark="#990000" bordercolorlight="#990000" bgcolor="#FFFFCC">
    <tr>
        <td width="942">
            <h1>
                <center>
                    <p>&nbsp;</p>
                    <p>You are now banned from this web site</p>
                </center>
            </h1>
            <h1 align="center">You have tried to use software not allowed on this site.
<br>
From now on this is the only page you will be able to see</h1>
            <p>&nbsp;</p>
            <h2 align="center">Your IP Number</h2>
            <table border="3" width="16%" align="center" cellspacing="0" bgcolor="#FF6600" bordercolor="red" bordercolordark="red" bordercolorlight="red">
                <tr>
                    <td width="176">
		
                        <p align="center"><?php // shows IP Number on Page
echo $_SERVER['REMOTE_ADDR'];
?>
</p>
                    </td>
                </tr>
            </table>
            <p align="center"><?php // Show the user agent 
echo 'Your user agent is: <b>'.$_SERVER['HTTP_USER_AGENT'].'</b><br />';
?>
</p>
            <h1 align="center">Thank you and goodbye</h1>
        </td>
    </tr>
</table>
<h1 align="center">&nbsp;</h1>
</body>

</html>