<?php
// This file simulates running application_top from the catalog but without starting a session.
// Use this to ensure URL's are written correctly in external feeder scripts.
//
// written by splautz

	error_reporting(E_ALL & ~E_NOTICE);  // Set the level of error reporting

	preg_match('/^\s*define\(\x27DIR_FS_CATALOG\x27\,[^\x27]*\x27([^\x27]*)\x27[^\x27]*\)\;/ism', file_get_contents('includes/configure.php'), $match)
		or die('Unable to find catalog path!');
	chdir($match[1]);  // change working directory to catalog

	require_once('includes/configure.php');

	define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);	
	
	require_once(DIR_WS_INCLUDES . 'filenames.php');
	require_once(DIR_WS_INCLUDES . 'database_tables.php');
	require_once(DIR_WS_FUNCTIONS . 'database.php');
	require_once(DIR_WS_FUNCTIONS . 'general.php');

	tep_db_connect() or die('Unable to connect to database server!');

	$configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);

	while ($configuration = tep_db_fetch_array($configuration_query)) {
		define($configuration['cfgKey'], $configuration['cfgValue']);
	}

	include_once(DIR_WS_CLASSES . 'language.php');
	$lng = new language();
	$languages_id = $lng->language['id'];

	function tep_session_is_registered( $var ){
		return false;
	}  # end function

	function tep_session_name(){
		return false;
	} # end function

	function tep_session_id(){
		return false;

	} # end function

	require_once(DIR_WS_FUNCTIONS . 'html_output.php');

?>