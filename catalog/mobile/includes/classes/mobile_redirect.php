<?php
  class mobileRedirect {
  	var $redirected;
  	var $mobileDir = "mobile_";
  	
    function mobileRedirect() {
    	$this->redirected = $this->needRedirect();
    	if($this->redirected)
    		$this->redirect();
    }
    
    function needRedirect() {
    	if($this->isCancelled()) 
    		return false;
    	if(strpos($_SERVER['SCRIPT_NAME'],$this->mobileDir . $this->mobileFile) > 0)
    		return false;
    	if(tep_browser_detect('iPhone') || tep_browser_detect('iPod'))
    		return 'iPhone';
    	if(tep_browser_detect('Blackberry'))
    		return 'Blackberry';
    	if(tep_browser_detect('Android'))
    		return 'Android';
    	if(tep_browser_detect('Nokia'))
    		return 'Nokia';
    	if(tep_browser_detect('SonyEricsson'))
    		return 'SonyEricsson';
    	if(tep_browser_detect('Opera Mobi'))
    		return 'OperaMobi';
		if(tep_browser_detect('Opera Mini'))
    		return 'OperaMini';
    	if(tep_browser_detect('MAUI_WAP_Browser'))
    		return 'GenericWAP';
    	return false;
    }
    
    function isCancelled() {
    	if (tep_session_is_registered('redirectCancelled')) 
    		return true;
    	if(isset($_GET['redirectCancelled']) && $_GET['redirectCancelled'] == 'true') {
    		tep_session_register('redirectCancelled');
    		return true;
    	}
    	return false;
    }
    
    function redirect() {
    	$path = split("/" , $_SERVER['SCRIPT_NAME']);
    	$filename = $path[sizeof($path)-1];
    	$file = $this->mobileDir . $filename;
    	$qstring = $_SERVER['QUERY_STRING'];
    	$SSL = ($_SERVER['HTTPS']) ? "SSL" : "NONSSL";
    	if (file_exists($file))
    		tep_redirect(tep_href_link($file, $qstring, $SSL, false, false));
    }
}
?>
