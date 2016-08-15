<?php
session_start();
//$charset = "windows-1251";
$charset = "ISO-8859-1";
$mime = "text/html";
header("Content-Type: $mime;charset=$charset");
header('Cache-Control: no-cache');
header('Pragma: no-cache');

function convert_to_js($str)
{


//stringa kojto shte se predawa na JS triabwa da ima wytre \ pred wseki slujeben znak 
// ' -> \' 
// " -> \"
// \ -> \\
// cr -> \cr i t.n.
// dokato w samia php string niama nujda ot escape: toj si e surow

//!!! edna edinstwena cherta \ w reg.exp. tuk w php se predstawia kato 4broia \\\\ 
//!!! zashtoto 2broia \\ sa za php, za da gi widi edna \ , i sa nebhodimi 2 \\ za reg.exp. 
//za da izglejdat kato edna \ ot gledna tochka na reg.exp engina

//I spent some time fighting with this, so hopefully this will help someone else.
//Escaping a backslash (\) really involves not two, not three, but four backslashes to work properly.
//So to match a single backslash, one should use:
//preg_replace('/(\\\\)/', ...);

//Suppose you want to match '\n' (that's backslash-n, not newline). 
//The pattern you want is not /\\n/ but /\\\\n/. 
//The reason for this is that before the regex engine can interpret 
//the \\ into \, PHP interprets it. Thus, if you write the first, the regex engine sees \n, 
//which is reads as newline. Thus, you have to escape your backslashes twice: 
//once for PHP, and once for the regex engine.
$str=preg_replace("/('|\"|\\\\|\r)/", "\\\\\$1", $str);

//Windows CR LF
//Linux   LF
//Mac     CR
//The W3C has reccommended in the HTML4.0 specification that all browsers 
//normalize TEXTAREA (and I suppose TEXT input content) to CRLF format).

$str=preg_replace("/(\n)/","\\u000D\\u000A",$str);


//$str="line1\\\r\\\nLine2"; //towa ne raboti w firefox i IE
//$str="line1\\u000D\\u000ALine2"; //towa raboti
return $str;
}

function spellcheck ( $words ) 
{
   //wryshta masiw w kojto sa:
   //nomera (ot 0-la) na greshnata duma: $misspelled['word_no']
   //masiw sys predlojeniata za korekcia na greshanta duma: $misspelled['suggest']
   $misspelled = NULL;
   $int = pspell_new('en');
   $word_no=0; $i=0;
   foreach ($words as $value) 
   	{
       	//if the word is numeric , pspell incorectlly say it is misspelled 
	//if (!ctype_digit ($value)) //w RH7.3 ne e definirana
	if (!is_numeric($value))
	if (!pspell_check($int, $value)) 
           	{
		$misspelled[$i]['word_no'] = $word_no;
   	   	$misspelled[$i]['suggest'] = pspell_suggest($int, $value);
		$i++;
		}
	$word_no++;
	}
 
   return $misspelled;
}
function correct_word($miss_word_counter,&$correct_pos,$suggest)
{
//tazi funkcia deistwa naprawo wyrhu $_SESSION["temp_corrected"] i ia widoizmenia
//oswen towa i koregira i wrishta greshkata $correct_pos po address

//poluchawane nomera na dumata koiato triabwa da se koregira
$num_word = $_SESSION['misspelled'][$miss_word_counter]['word_no'];

//poluchawane statowata pozicia na greshnata duma
$start_pos=$_SESSION["words_start_pos"][$num_word]+$correct_pos;

//poluchawane dyljinata na sreshnata duma
$L=strlen($_SESSION["words"][$num_word]);

//syshtinskata korekcia 
$T=$_SESSION["temp_corrected"];
//print ("suggest: $suggest <BR>\n");
//print ("start_pos: $start_pos <BR>\n");
//print ("L: $L <BR>\n");

$_SESSION["temp_corrected"] = substr_replace( $T, $suggest, $start_pos, $L);
//printf ("temp string corrected: %s <BR>\n", $_SESSION['temp_corrected']);


//koregirane na greshkata pri korekciata
$correct_pos = $correct_pos + (strlen($suggest)-$L);
//print ("correct_pos: $correct_pos <BR>\n");
}

function display_red($miss_word_counter,$correct_pos)
{
//$err_color =	'red'; 
//$err_color =	'HotPink'; 
//$err_color =	'DeepPink';
$err_color =	'#fe7173';
$err_color2 =	'#fea1c0'; 
$start_str =	"<span style=\"background:$err_color2;\">";
$start_str_bold ="<span style=\"background:$err_color; font-weight: bold;\">";
$end_str   =  	"</span>";

//$_SESSION["temp_corrected"];

$current_pos=0; //poziciata w stringa po simwoli, an ne poziciqta na dumite
$screen_text='';

$num_elements=sizeof($_SESSION['misspelled']);
for ( $i=$miss_word_counter; $i<$num_elements; $i++) if (isset($_SESSION['misspelled'][$i]['word_no']))
	{
	$num_word = $_SESSION['misspelled'][$i]['word_no'];
	//print ("$num_word <BR>\n");
	
	//poluchawane statowata pozicia na greshnata duma
	$start_pos=$_SESSION["words_start_pos"][$num_word]+$correct_pos;
	//poluchawane kraynata pozicia na greshnata duma
	$end_pos=$_SESSION["words_start_pos"][$num_word]+strlen($_SESSION["words"][$num_word])+$correct_pos;
	
	$piece_before=substr($_SESSION["temp_corrected"], $current_pos, $start_pos - $current_pos);
	$piece_middle=substr($_SESSION["temp_corrected"], $start_pos, strlen($_SESSION["words"][$num_word]) );
	
	//printf("<BR>");
	//printf("piece_before: %s<BR>\n",$piece_before);
	//printf("piece_middle: %s<BR>\n",$piece_middle);
	
	if ($i==$miss_word_counter)
	$screen_text=$screen_text.$piece_before.$start_str_bold.$piece_middle.$end_str;
	else
	$screen_text=$screen_text.$piece_before.$start_str.$piece_middle.$end_str;
	
	$current_pos=$end_pos;
	//$i++;
	}
//return all from end of the last spell word, to the end of the whole string
$screen_text=$screen_text.substr($_SESSION["temp_corrected"], $current_pos);

//$screen_text=preg_replace("/\n/","<br>\n",$screen_text);
//towa kato che li e po prawilno na win system
$screen_text=preg_replace("/(\r\n|\n|\r)/","<br>\n",$screen_text);

print("$screen_text");
}

function display_nav($miss_word_counter)
{
print ("<table width=\"100%\" border=0 cellspacing=0 cellpadding=5>\n");
print("<TR>\n");

print("<TD width=\"10%\" align=\"left\" valign=\"top\">\n");
//moje bi triabwa da ima algoritym za opredeliane na the best match ot wsichki dumi koito aspell predlaga
$the_best_match=true;
printf("<FORM name=\"sform\" method=\"GET\" action=\"%s\">\n",$_SERVER['PHP_SELF']);

printf("
			<SELECT name=\"asuggest\" SIZE=10>\n
			<optgroup label=\"Select The Best Match\">\n");
			//printf("<OPTION>%s\n",$NULL); // i syshto da moje da se zadawa bez default suggest
			while (list( ,$value) = @each ($_SESSION['misspelled'][$miss_word_counter]['suggest']))
			{
			if ($the_best_match) {
				$the_best_match = false;
				printf("<OPTION selected=\"true\">%s\n",$value);
			} else printf("<OPTION>%s\n",$value);
			}
			printf("</optgroup>\n");
			printf("</SELECT>\n");
print("<BR>\n");
printf("<INPUT type=\"text\" name=\"csuggest\" value=\"%s\" size=\"16\" maxlength=\"24\">\n", '');
print("</TD>\n");

print("<TD align=\"left\" valign=\"top\">\n");
printf("<INPUT type=\"hidden\" name=\"miss_word_counter\" value=\"%s\">\n", $miss_word_counter);
print("<INPUT TYPE=\"submit\" name=\"correct\" value=\"Correct\">");
print("</FORM>\n");
print(" &nbsp&nbsp ");
printf("<a href=\"%s?miss_word_counter=%s&next=yes\">Skip / Next ></a>\n",$_SERVER['PHP_SELF'],$miss_word_counter);
printf("<P>\n");

//stringa kojto shte se predawa na JS triabwa da ima wytre \ pred wseki slujeben znak 
// ' -> \' 
// " -> \"
// \ -> \\
// cr -> \cr i t.n.
// dokato w samia php string niama nujda ot escape: toj si e surow

$wb_str=convert_to_js(html_entity_decode($_SESSION["temp_corrected"]));
//$wb_str='lssd \\\"gggg';

printf("<script language=\"JavaScript\">\n");
printf("<!--\n");
printf("var wb_str= \"%s\";\n",$wb_str);
printf("self.document.sform.correct.focus();\n");
printf("-->\n");
printf("</script>\n");

//izglejda che ne moje da se predade naprawo stringa na po-dolnata funkcia
//a triabwa pyrwo prez promenliwa (wij po gore)
//problema e sys " :naprimer towa ne raboti wypreki che " e escapnat!
//WriteBack('form','field', 'xxx \" yyy');
printf("<a href=\"javascript: void(0);\" onclick=\"WriteBack('%s','%s', wb_str);\">Apply / Finish</A>",$_SESSION["form_name"], $_SESSION["field_name"]);
printf("<P><BR><BR>\n");
printf("<a href=\"\" onclick=\"window.close();\">Close</A>");
printf("<P>\n");
print("</TD>\n");

print("<TD align=\"left\" valign=\"bottom\">\n");
print ("<div style=\"font-size: smaller; font-weight: normal; position: relative; bottom: -10px; right: -5px; z-index: 1; color: gray; text-align: right;\">");
printf("Design by: <a href=\"http://www.1001Line.com/\" target=\"_blank\" style=\"text-decoration: none; color: gray;\">www.1001Line.com</a>\n");
print ("</div>");
print("</TD>\n");

print("</TR>\n");
print("</TABLE>\n");

}



?>
<html>
<head>
<title>PHP Spell Check</title>

<script language="JavaScript">
<!--
function WriteBack(form_name,field_name,corrected_text) 
{
  //alert (corrected_text);
  //towa e cialosnia pyt kato imeto na formata i poleto sa twyrdo zadadeni
  //opener.document.proba.description_short.value = "child write back";
  var editor_obj;       // html editor object
  if(document.all) { editor_obj = opener.document.all("_" +field_name+  "_editor"); }
  else if(document.getElementById) { editor_obj = opener.document.getElementById("_" +field_name+  "_editor"); }

  if (editor_obj) {
    if (editor_obj.tagName.toLowerCase() == 'textarea') {
      editor_obj.value = corrected_text;
    } else {
      editor_obj.contentWindow.document.body.innerHTML = corrected_text;
    }
  }
  opener.document[form_name][field_name].value = corrected_text;
}
-->
</script>

</head>

<body>



<?php
//!!! Izglejda che POST variable ne si gubiat stojnostta sled kato swyrshi tozi script
//dokato GET var se onishtojavat
if (isset($_REQUEST['init'])) $init = $_REQUEST['init'];
else $init = '';
if (isset($_REQUEST['csuggest'])) $csuggest = $_REQUEST['csuggest'];
else $csuggest = '';
if (isset($_REQUEST['asuggest'])) $asuggest = $_REQUEST['asuggest'];
else $asuggest = '';
if (isset($_REQUEST['next'])) $next = $_REQUEST['next'];
else $next = '';
if (isset($_REQUEST['miss_word_counter'])) $miss_word_counter = $_REQUEST['miss_word_counter'];
else $miss_word_counter = 0;

if ($init=='nojs')
	{
	//perform init if JavaScript is disabled in brouser
	$_SESSION["form_name"]=$_POST['form_name']; //this var is not needed, but assigned to keep good style ...
	$_SESSION["field_name"]=$_POST['field_name']; //this var is not needed, but assigned to keep good style ...
	$_SESSION["first_time_text"] = "<div style=\"text-align: center;\"><b>This is Simple PHP Spell Checker.</b> It is used to Spell Check user input forms, text area and input fields.</div>\n
	<B>IF YOU SEE THIS TEXT</B>, you probably have <B>JavaScript turned off</B>. Click browser's 'BACK' button to return to the form and enable JavaScript in your Browser.\n
	After successfully done, this window must Popup and contains the user input text she wants to spellcheck.
	";
	unset ($_SESSION["words"]);
	unset ($_SESSION["words_start_pos"]);
	unset ($_SESSION["misspelled"]);
	$_SESSION["temp_corrected"] = $_SESSION["first_time_text"];
	$_SESSION["correct_pos"]=0;
	}

elseif ($init=='yes')
	{
	//perform init and SpellChecking via spellcheck()
	//the rest of the script, outside of this statement is just an interface 
	//print("Initialization.<BR>\n\n");
	$_SESSION["form_name"]=$_POST['form_name'];
	$_SESSION["field_name"]=$_POST['field_name'];
	$_SESSION["first_time_text"]=(get_magic_quotes_gpc() == 1)?stripslashes($_POST['first_time_text']):$_POST['first_time_text'];
	
	//NORMALIZE NEW LINES
	// Convert PC newline (CRLF)
	// to Unix newline format (LF)
	$_SESSION["first_time_text"]=preg_replace("/(\r\n)/","\n",$_SESSION["first_time_text"]);
	//Convert Mac newline (CR)
	//to Unix newline format (LF)
	$_SESSION["first_time_text"]=preg_replace("/(\r)/","\n",$_SESSION["first_time_text"]);

	$words = preg_split('/[\W]+?/',$_SESSION["first_time_text"], -1, PREG_SPLIT_NO_EMPTY);
	$_SESSION["first_time_text"]=htmlspecialchars($_SESSION["first_time_text"]);
	//za >= PHP 4.3.0 ima opcia PHP PREG_SPLIT_OFFSET_CAPTURE
	//i po dolnia cikal e nenujen no pyk poziciite shte se wyrnat w syshtia masiw kato dumite
	$i=0; $offset=0; $words_start_pos = NULL;
	foreach ($words as $value) 
		{
		$words_start_pos[$i] = strpos($_SESSION["first_time_text"], $value, $offset);
		$offset = $words_start_pos[$i]+ strlen($value);
		$i++;
		}
	$_SESSION["words"] = $words;
	$_SESSION["words_start_pos"] = $words_start_pos;
	$_SESSION["misspelled"] = spellcheck($words);
	$_SESSION["temp_corrected"] = $_SESSION["first_time_text"];
	$_SESSION["correct_pos"]=0;
	}

//Some debug. Uncoment to test
/*
print("words\n");
print("<PRE>\n");
print_r($_SESSION["words"]);
print("</PRE>\n");
print("####\n\n");
*/
/*
print("starting positions\n");
print("<PRE>\n");
print_r($_SESSION["words_start_pos"]);
print("</PRE>\n");
print("####\n\n");
*/
/*
print("misspelled\n");
print("<PRE>\n");
print_r($_SESSION["misspelled"]);
print("</PRE>\n");
print("####\n\n");
*/

//towa e greshkata pri wmakwane na nikoia duma moje da byde '-' '0' ili '+'
//this is error position, which occure when word to replace is shorter or longer then misspeled word
//$correct_pos can be +, zero or negative
//$correct_pos is corrected it self every time we made an replacement
//$correct_pos = $correct_pos + (strlen(..... ; see correct_word()
$correct_pos=$_SESSION["correct_pos"];

//!!! pyrwia pyt kogato  $miss_word_counter=0; e integer obache sled towa stawa string za 1,2,3 ...	
settype($miss_word_counter,"integer");
//$miss_word_counter e porednia nomer na geshnata duma
//naprimer 0,1,2,3,4,5

//wsichko greshno ot tekushtia nomer se izobraziawa cherweno 
//proverka dali $miss_word_counter e w dopustimite granici 0 - lastelement
//bi tribwalo da e samo isset($_SESSION['misspelled'][$miss_word_counter])
//no ne raboti prawilno: zatowa $_SESSION['misspelled'][$miss_word_counter]['word_no']
//drug wariant e da se proweri $miss_word_counter >= 0 and <max elemenet

//!!! korekcia na duma i ubelichawane na $miss_word_counter samo ako w granicite
//!!! poslednata stojnost na $miss_word_counter e +1 po goliama ot poslednia element i ne se pozwoliwa poweche uwelicenie	
if ( isset($_SESSION['misspelled'][$miss_word_counter]['word_no']) )	
{
	// $csuggest is custom suggest i e s prioritet pred $asuggest (aspell suggest)
	if ($csuggest != '')
		{
		correct_word($miss_word_counter, $correct_pos, $csuggest);
		$miss_word_counter++;
		}
	elseif ($asuggest != '')
		{
		correct_word($miss_word_counter, $correct_pos, $asuggest);
		$miss_word_counter++;
		}


	if ($next=='yes') $miss_word_counter++;
}

print("<DIV style=\"width: 100%; background:#f2f2f2; padding:10pt; border-style: none; border-width: medium; margin-bottom:0px;\">\n");
display_red($miss_word_counter,$correct_pos);
print("</DIV>\n");

print ("<div style=\"margin-top:0px; color:gray; text-align:center;\">");
print ("miss word counter: $miss_word_counter <BR>\n");
print ("</div>");

print("<div style=\"width: 100%; background:#eaeff4; padding:10pt; border-style: none; border-width: medium; margin-bottom:0px;\">\n");
display_nav($miss_word_counter);
print ("</div>");

//printf("The form_name to spell is: %s<BR>\n",$_SESSION["form_name"]);
//printf("The field_name in form to spell is: %s<BR>\n",$_SESSION["field_name"]);

$_SESSION["correct_pos"]=$correct_pos;

?>

</body>
</html>
