<?php
/*
  $Id: Connector.class.php,v 3.1.4 2005/03/16 13:45:13 devosc Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  DevosC, Developing open source Code
  http://www.devosc.com

  Copyright (c) 2003 osCommerce
  Copyright (c) 2004 DevosC.com

  Released under the GNU General Public License

*/

class PayPal_Client_Connector {

  function PayPal_Client_Connector() {}

  function getResponse($domain)
  {
    global $debug;

    $response = $this->fsockopen('ssl://',$domain,'443');

    if (empty($response))
      $response = $this->curl_exec($domain);

    if (empty($response))
      $response = $this->fopen('https://'.$domain.'/cgi-bin/webscr?'.$this->response_string);

    if (empty($response))
      $response = $this->fsockopen('tcp://',$domain,'80');

    if (empty($response)) {

      $response = @file('http://'.$domain.'/cgi-bin/webscr?'.$this->response_string);

      if (!$response && ($debug->enabled))
        $debug->add(HTTP_ERROR,sprintf(HTTP_ERROR_MSG,'','','',''));

    }

    if($debug->enabled) {

      $debug->add(PAYPAL_RESPONSE,sprintf(PAYPAL_RESPONSE_MSG,$this->getVerificationResponse($response)));

      $debug->add(CONNECTION_TYPE,sprintf(CONNECTION_TYPE_MSG,$this->curl_flag,$this->transport,$domain,$this->port));

    }

    unset($this->response_string,$this->curl_flag,$this->transport,$this->port);

    return $response;
  }

  //Test both receiver email address and business ID
  function validateReceiverEmail($receiver_email,$business)
  {
    global $debug;

    if(!strcmp(strtolower($receiver_email),strtolower($this->key['receiver_email'])) && !strcmp(strtolower($business),strtolower($this->key['business']))) {

      if($debug->enabled)
        $debug->add(EMAIL_RECEIVER,sprintf(EMAIL_RECEIVER_MSG,$receiver_email,$business,$this->key['receiver_email'],$this->key['business']));

      return true;

    } else {

      if($debug->enabled)
        $debug->add(EMAIL_RECEIVER,sprintf(EMAIL_RECEIVER_ERROR_MSG,$receiver_email,$business,$this->key['receiver_email'],$this->key['business'],$this->key['txn_id']));

      return false;
    }

  }

  function validPayment($amount,$currency)
  {
    global $debug;
    $valid_payment = true;
    //check the payment currency and amount
    if ( ($this->key['mc_currency'] != $currency) || ($this->key['mc_gross'] != $amount) )
      $valid_payment = false;

    if($valid_payment === false && $debug->enabled)
      $debug->add(CART_TEST,sprintf(CART_TEST_ERR_MSG,$amount,$currency,$this->key['mc_gross'],$this->key['mc_currency']));

    return $valid_payment;
  }

  function dienice($status = '200')
  {
    switch($status) {
      case '200';
        header("HTTP/1.1 200 OK\r\n");
        break;
      case '500':
      default:
        if($this->validDigest()) {
          header("HTTP/1.1 204 No Content\r\n"); exit;
        } else {
          header("HTTP/1.1 500 Internal Server Error\r\n"); exit;
        }
        break;
    }
  }

  function digestKey()
  {
    return strrev(md5(md5(strrev(md5(MODULE_PAYMENT_PAYPAL_IPN_DIGEST_KEY)))));
  }

  function validDigest()
  {
    return (isset($this->key['digestKey']) && $this->key['digestKey'] === $this->digestKey());
  }

  function setTestMode($testMode)
  {
    switch($testMode) {
      case 'On':
        $this->testMode = 'On';
        break;
      default:
        $this->testMode = 'Off';
      break;
    }
  }

  function testMode($testMode='')
  {
    if(!empty($testMode))
      return ($this->testMode === $testMode);
    elseif (isset($this->testMode))
      return ($this->testMode === 'On');
    return false;
  }

  function getVerificationResponse($response)
  {
    if (is_array($response)) {

      return @$response[0];

    } elseif (is_string($response)) {

      $array = explode("\r\n",$response);

      return @$array[0];

    }

    return false;
  }

  function getRequestBodyContents(&$handle)
  {
    $headerdone = false;

    if ($handle) {

      while(!feof($handle)) {

        $line = @fgets($handle, 1024);

        if (!strcmp($line, "\r\n")) {

          $headerdone = true;

        } elseif ($headerdone) {

          $line = str_replace("\r\n",'',$line);

          if (in_array($line,array('VERIFIED','INVALID')))
            return $line;

        } elseif (in_array($line,array('VERIFIED','INVALID'))) {

          return $line;

        }

      }

    }

    return false;
  }

  function curl_exec($domain)
  {
    $response = '';

    $this->curl_flag = function_exists('curl_exec');

    if ( $this->curl_flag ) {

      $ch = @curl_init();

      @curl_setopt($ch,CURLOPT_URL, "https://$domain/cgi-bin/webscr");
      @curl_setopt($ch,CURLOPT_POST, 1);
      @curl_setopt($ch,CURLOPT_POSTFIELDS, $this->response_string);
      @curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
      @curl_setopt($ch,CURLOPT_HEADER, 0);
      @curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
      @curl_setopt($ch,CURLOPT_TIMEOUT, 60);

      $response = @curl_exec($ch);

      @curl_close($ch);

    }

    return $response;
  }

  function fsockopen($transport,$domain,$port)
  {
    $response = '';

    $this->transport = $transport;

    $this->port = $port;

    $fp = @fsockopen($transport.$domain,$port, $errno, $errstr, 30);

    if  ($fp) {

      $header = "POST /cgi-bin/webscr HTTP/1.1\r\n" .
                "Host: {$domain}\r\n" .
                "From: " . MODULE_PAYMENT_PAYPAL_BUSINESS_ID . "\r\n" .
                "User-Agent: PayPal_Shopping_Cart_IPN/3.1\r\n" .
                "Content-type: application/x-www-form-urlencoded\r\n" .
                "Content-length: " . strlen($this->response_string) . "\r\n" .
                "Connection: close\r\n\r\n";

      @fputs($fp, $header . $this->response_string);

      $response = $this->getRequestBodyContents($fp);

      @fclose($fp);

    }

    return $response;
  }

  function fopen($filename)
  {
    $response = '';

    $fp = @fopen($filename,'rb');

    if ($fp) {

      $response = $this->getRequestBodyContents($fp);

      @fclose($fp);

    }

    return $response;
  }

}//end class
?>