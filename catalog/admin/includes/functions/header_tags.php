<?php
/*
  $Id: header_tags_controller.php,v 1.0 2005/04/08 22:50:52 hpdl Exp $
  Originally Created by: Jack York - http://www.oscommerce-solution.com
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
function ChangeSwitch($line, $arg)
{
  if (isset($arg))
   $line = str_replace("0", "1", $line);
  else  
   $line = str_replace("1", "0", $line);
   
  return $line; 
}

//returns true if line is a comment
function IsComment($line)
{   
  return ((strpos($line, "//") === 0) ? true : false);
}

function IsTitleSwitch($line)
{   
  if (strpos($line, "define('HTTA") === 0 && strpos($line, "define('HTTA_CAT") === FALSE)
    return true;
  else
    return false; 
}

function IsDescriptionSwitch($line)
{   
  return ((strpos($line, "define('HTDA") === 0) ? true : false);
}

function IsKeywordSwitch($line)
{   
  return ((strpos($line, "define('HTKA") === 0) ? true : false); 
}

function IsCatSwitch($line)
{   
  return ((strpos($line, "define('HTTA_CAT") === 0) ? true : false); 
}

function IsTitleTag($line)
{   
  return ((strpos($line, "define('HEAD_TITLE_TAG") === 0) ? true : false);  
}

function IsDescriptionTag($line)
{   
  return ((strpos($line, "define('HEAD_DESC_TAG") === 0) ? true : false);
}

function IsKeywordTag($line)
{   
  return ((strpos($line, "define('HEAD_KEY_TAG") === 0) ? true : false);
}

function IsH1Tag($line)
{   
  return ((strpos($line, "define('HEAD_H1_TAG") === 0) ? true : false);
}

function FileNotUsingHeaderTags($file)
{
  $file = '../'.$file;
  $fp = file($file);
  for ($i = 0; $i < count($fp); ++$i)
  {
      if (strpos($fp[$i], "Header Tags Controller") !== FALSE)
        return false;
  }
  return true;
}

function GetArgument(&$line, $arg_new, $formActive)
{
  if ($formActive)
  {
    $line = ReplaceArg($line, $arg_new);
	$arg = $arg_new;
  }
  else
  {
    $arg = '';
    $def = explode("'", $line);
  
    for ($i = 3; $i < count($def); ++$i)
    {
      if (strrpos($def[$i],"\\") === strlen($def[$i])-1) $arg .= $def[$i] . "'";
      else {
        $arg .= $def[$i];
        break;
      }
    }
  }
  
  return stripslashes($arg); 
}

function GetMainArgument(&$line, $arg, $arg2, $formActive)
{
  $def = explode("'", $line);
  for ($i = 3; $i < count($def); ++$i)
  {
      if (strrpos($def[$i],"\\") === strlen($def[$i])-1) $arg .= $def[$i] . "'";
      else {
        $arg .= $def[$i];
        break;
      }
  }
  
  if ($formActive)
  {
     $line = str_replace("'$arg'", "'$arg2'", $line);
     $arg = $arg2;
  }  

  return stripslashes($arg);  
}

function GetSectionName($line)
{
  if (strpos($line,'//') === 0) {
    $name = explode(" ", $line);
    $name[1] = trim($name[1]);
    $pos = strpos($name[1], '.');
    return (substr($name[1], 0, $pos)); 
  } else return '';
}

function GetSwitchSetting($line)
{
  return ((strpos($line, "'0'") === FALSE) ? 1 : 0);     
}

function NotDuplicatePage($fp, $pagename)  //return false if the name entered is already present
{
  for ($idx = 0; $idx < count($fp); ++$idx)   
  {
     $section = GetSectionName($fp[$idx]);
     if (! empty($section))
     {
        if (strcasecmp($section, $pagename) === 0)
          return false;
     }     
  }
  return true;
}

function ReplaceArg($line, $arg)
{
  $parts = explode("'", $line);         //break apart the line   
  $parts[3] = $arg;                     //replace the argument  
  
//  if (strpos($parts[3], "\\") === FALSE)
//    $parts[3] = addslashes($parts[3]);  
   
  $line = $parts[0] . "'" . $parts[1] . "'" . $parts[2] . "'" . $parts[3] . "');\n";
  return $line; 
}

function TotalPages($filename)
{
  $ctr = 0;
  $findTitles = false;
  $fp = file($filename);  
      
  for ($idx = 0; $idx < count($fp); ++$idx)
  { 
    $line=$fp[$idx];

    if (strpos($line, "define('HEAD_TITLE_TAG_ALL'") !== FALSE)
      continue;
    else if (strpos($line, "define('HEAD_DESC_TAG_ALL'") !== FALSE)
      continue;
    else if (strpos($line, "define('HEAD_KEY_TAG_ALL'") !== FALSE)
      continue;
    else if (strpos($line, "define('HEAD_H1_TAG_ALL'") !== FALSE)
    {
      $findTitles = true;  //enable next section
      continue;
    } 
    else if ($findTitles)
    {
      if (($pos = strpos($fp[$idx], '.php')) !== FALSE)
        $ctr++; 
    }
  }  
  return $ctr;
}

function ValidPageName($pagename)  //return false if the page name has an invalid format
{
  if (strpos($pagename, " ") !== FALSE)
   return false;
  else if (strpos($pagename, "-") !== FALSE)
   return false;
  else if (strpos($pagename, "http") !== FALSE)
   return false; 
  else if (strpos($pagename, "\\") !== FALSE)
   return false; 
  else if (strpos($pagename, "'") !== FALSE)
   return false; 
     
  return true;  
}

function WriteHeaderTagsFile($filename, $fp)
{
  if (!is_writable($filename)) 
  {
     if (!chmod($filename, 0666)) {
        echo "Cannot change the mode of file ($filename)";
        exit;
     }
  }
  $fpOut = fopen($filename, "w");
 
  if (!fpOut)
  {
     echo 'Failed to open file '.$filename;
     exit;
  }
       
  for ($idx = 0; $idx < count($fp); ++$idx)
    if (fwrite($fpOut, $fp[$idx]) === FALSE)
    {
       echo "Cannot write to file ($filename)";
       exit;
    } 
  fclose($fpOut);   
}
?>