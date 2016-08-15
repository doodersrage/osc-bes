<?php
/*
  $Id: yellow.php,v 1.0 2005/10/13 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class yellow {
    var $code, $title, $description, $icon, $enabled, $freight;

// class constructor
    function yellow() {
      global $order;

      $this->code = 'yellow';
      $this->title = MODULE_SHIPPING_YELLOW_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_YELLOW_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_YELLOW_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_YELLOW_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_YELLOW_STATUS == 'True') ? true : false);
      $this->freight = true;

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_YELLOW_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_YELLOW_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }

      $this->types = array('RESC' => 'Residential',
                           'RESL' => 'Residential /w liftgate',
                           'RESI' => 'Residential /w inside delivery',
                           'COMC' => 'Commercial',
                           'COML' => 'Commercial /w liftgate',
                           'COMI' => 'Commercial /w inside delivery',
                           'SUN' => 'White Glove');
    }

// class methods
    function quote($method = '') {
      global $order;

      if ( tep_not_null($method) && isset($this->types[$method]) ) {
        $this->service = $method;
      }

      $yellowQuote = $this->_getQuote($num_shipments, $shipping_weight);

      if (is_array($yellowQuote)) {
        if (isset($yellowQuote['error'])) {
          $this->quotes = array('module' => $this->title,
                                'error' => $yellowQuote['error']);
        } else {
          $this->quotes = array('id' => $this->code,
                                'module' => $this->title);  // . ($num_shipments?' (' . $shipping_weight . ' lbs; ' . $num_shipments . ' shipment' . ($num_shipments!=1?'s':'').' to '.$order->delivery['country']['iso_code_2'].':'.str_replace(' ', '', $order->delivery['postcode']).')':''));

          $methods = array();
          $size = sizeof($yellowQuote);
          foreach($yellowQuote as $type => $cost) {
            $methods[] = array('id' => $type,
                               'title' => ((isset($this->types[$type])) ? ('<a href="'.HTTP_SERVER.'/shipping-returns/#' . $type . '" target="_blank">' . $this->types[$type] . '</a>') : $type),
                               'cost' => is_numeric($cost)?($cost + ($cost?MODULE_SHIPPING_YELLOW_HANDLING:0)):$cost);
          }

          $this->quotes['methods'] = $methods;

          if ($this->tax_class > 0) {
            $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          }
        }
      } else {
        $this->quotes = array('module' => $this->title,
                              'error' => MODULE_SHIPPING_YELLOW_TEXT_ERROR);
      }

      if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_YELLOW_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable YELLOW Shipping', 'MODULE_SHIPPING_YELLOW_STATUS', 'True', 'Do you want to offer YELLOW shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Enter the YELLOW Usage ID', 'MODULE_SHIPPING_YELLOW_USAGE', 'NONE', 'Enter the USAGE portion of your API Name/Value pair combination.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Enter the YELLOW Scope ID', 'MODULE_SHIPPING_YELLOW_SCOPE', 'NONE', 'Enter the SCOPE portion of your API Name/Value pair combination.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Handling Fee', 'MODULE_SHIPPING_YELLOW_HANDLING', '0', 'Handling fee for this shipping method.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_YELLOW_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_YELLOW_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_YELLOW_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sun Origination Zipcode', 'MODULE_SHIPPING_YELLOW_SUN_ORIG', '27360', 'Enter origination zipcode for Sun Delivery to enable White Glove shipping.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sun Notification Fee', 'MODULE_SHIPPING_YELLOW_SUN_NFEE', '20.00', 'Notification fee for White Glove shipping (Sun Delivery).', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sun Insurance Fee', 'MODULE_SHIPPING_YELLOW_SUN_IFEE', '20.00', 'Insurance fee for White Glove shipping (Sun Delivery).', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sun Fuel Surcharge', 'MODULE_SHIPPING_YELLOW_SUN_FUEL', '20', 'Percent fuel surcharge for White Glove shipping (Sun Delivery).', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Free YELLOW Shipping?', 'MODULE_SHIPPING_YELLOW_FREE', 'True', 'Allow free YELLOW shipping? This includes YELLOW portion of White Glove.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Free SUN Shipping?', 'MODULE_SHIPPING_YELLOW_SUN_FREE', 'False', 'Allow free SUN shipping? This includes SUN portion of White Glove.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sun Only Categories', 'MODULE_SHIPPING_YELLOW_SUN_ONLY_CATS', '', 'Categories to allow only White Glove shipping (Sun Delivery). List category ID's separated by a space.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_SHIPPING_YELLOW_STATUS', 'MODULE_SHIPPING_YELLOW_USAGE', 'MODULE_SHIPPING_YELLOW_SCOPE', 'MODULE_SHIPPING_YELLOW_HANDLING', 'MODULE_SHIPPING_YELLOW_TAX_CLASS', 'MODULE_SHIPPING_YELLOW_ZONE', 'MODULE_SHIPPING_YELLOW_SORT_ORDER', 'MODULE_SHIPPING_YELLOW_SUN_ORIG', 'MODULE_SHIPPING_YELLOW_SUN_NFEE', 'MODULE_SHIPPING_YELLOW_SUN_IFEE', 'MODULE_SHIPPING_YELLOW_SUN_FUEL', 'MODULE_SHIPPING_YELLOW_FREE', 'MODULE_SHIPPING_YELLOW_SUN_FREE', 'MODULE_SHIPPING_YELLOW_SUN_ONLY_CATS');
    }

    function _getQuote(&$num_shipments, &$shipping_weight) {
      global $cart, $order;

      $sun_only = false;
// calculate weight for Sun delivery
      $sun_weight = 0;
      if (is_array($cart->contents) && MODULE_SHIPPING_YELLOW_SUN_ORIG && isset($this->types['SUN'])) {
        $scatids = preg_split("/\D+/",MODULE_SHIPPING_YELLOW_SUN_ONLY_CATS,-1,PREG_SPLIT_NO_EMPTY);
        reset($cart->contents);
        while (list($products_id, ) = each($cart->contents)) {
          $psun_only = false;
          if ($scatids) {  // obtain cat id's to check for sun only shipping
            $p2c_query = tep_db_query("select pc.categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " pc, " . TABLE_CATEGORIES . " c where c.categories_id = pc.categories_id and c.categories_status = 1 and pc.products_id = '". (int)$products_id . "'");
            while(!$psun_only && $p2c=tep_db_fetch_array($p2c_query))
              if (in_array($p2c['categories_id'],$scatids)) $psun_only = true;
          }
		  if (!$sun_only && $psun_only) $sun_only = true;
          $qty = $cart->contents[$products_id]['qty'];
          $product_query = tep_db_query("select p.products_weight, p.products_free_shipping, p.products_freight_class, p.products_origin_postcode, n.standard_weight from " . TABLE_PRODUCTS . " p left join " . TABLE_SUN_CLASS . " n on p.products_sun_class = n.id where p.products_id = '" . (int)$products_id . "'");
          if ($product = tep_db_fetch_array($product_query)) {
            $sun_standard_weight = $product['standard_weight']?$product['standard_weight']:'0';
            $products_weight = $product['products_weight']?$product['products_weight']:'0';
            $products_weight = max($products_weight, $sun_standard_weight);
            $products_free_shipping = $product['products_free_shipping'];
            $products_freight_class = preg_split("/\D+/",$product['products_freight_class'],-1,PREG_SPLIT_NO_EMPTY);
            if (((sizeof($products_freight_class) && is_numeric($products_freight_class[0])) || $products_weight > 150)
              && (!$products_free_shipping || (!$psun_only && MODULE_SHIPPING_YELLOW_SUN_FREE != 'True'))) {
              $sun_weight += $qty * $products_weight;
            }
          }
        }
      }

      $num_shipments = sizeof($cart->shipments[1]);
      $shipping_weight = 0;
      $dest_city = strtoupper(trim($order->delivery['city']));
      $dest_pcode = str_replace(' ', '', $order->delivery['postcode']);
      $dest_ccode = $order->delivery['country']['iso_code_2'];
      if ($dest_ccode == 'US') $dest_pcode = substr($dest_pcode, 0, 5);
      if (is_array($cart->shipments[1])) {
        $sun_error = false;
        $httpReqs = array(array(),array());
        $rates = array(array(),array());
        $server = 'www.myyellow.com';
        $uri = '/dynamic/services/servlet?CONTROLLER=com.yell.ec.inter.yfsratequote.http.controller.RateQuoteAPIController';
        $http = new httpClient($server, 443, 'SSL');
        $http->addHeader('Host', $server);
        $http->addHeader('User-Agent', 'Yellow Shipping Quote Module @ ' . STORE_NAME);
        $http->addHeader('Connection', 'keep-alive');
        foreach($cart->shipments[1] as $orig_code => $shipment) {
          $orig_pcode = tep_get_location_code($orig_code, 2);
          $request = array();
          $request['requestid'] = '1000';
          $request['USAGE'] = MODULE_SHIPPING_YELLOW_USAGE;
          $request['SCOPE'] = MODULE_SHIPPING_YELLOW_SCOPE;
          $request['destzip'] = $dest_pcode;
          if ($dest_city) {
            $request['consZipForCities'] = $dest_pcode;
            $request['consCities'] = $dest_city;
          }
          $request['originzip'] = $orig_pcode;
          $request['CF'] = '0';
          $request['accessorial0'] = '';
          $request['accessorial5'] = 'ID';
          $request['accessorial7'] = 'LG';
          $request['accessorial12'] = 'RD';
          $i = 0;
          foreach($shipment as $pfclass => $item) {
            $shipping_weight += $item['weight'];
            if (MODULE_SHIPPING_YELLOW_FREE == 'True') {
              $item['weight'] = $item['weight2'];
              $item['count'] = $item['count2'];
            }
            if ($item['count']) {
              $request['weight'.$i] = $item['weight']<2?2:ceil($item['weight']);
              $request['shipClass'.$i] = $pfclass;
              $request['pieces'.$i] = $item['count']<1?1:ceil($item['count']);
              $i++;
            }
          }
          $request['oneshipment'] = 'Y';   // $27 charged if single shipment for day less than 500 lbs
          $request['ltldd']= 'P';
          $request['payment']= 'P';
          $request['submit'] = 'Send';
          if ($i) {
            if (!$sun_only) {
              $httpReqs[0][$orig_code] = $http;
              $httpReqs[0][$orig_code]->Post($uri,$request,false);
            }
            if (MODULE_SHIPPING_YELLOW_SUN_ORIG && isset($this->types['SUN'])) {
              $request['destzip'] = MODULE_SHIPPING_YELLOW_SUN_ORIG;
              if ($dest_city) {
                unset($request['consZipForCities']); unset($request['consCities']);
              }
              $httpReqs[1][$orig_code] = $http;
              $httpReqs[1][$orig_code]->Post($uri,$request,false);
            }
          }
        }
        foreach($httpReqs as $j => $reqs) {
          foreach($reqs as $orig_code => $httpReq) {
            $status = 0;
            if (($status=$httpReq->processReply()) == 200) {
              $this->rqbody = preg_replace('/[\r\n]/','',$httpReq->getBody());
              $this->rqbody = $this->_getXMLcontent('RATEQUOTE');
// echo $this->rqbody; exit;
              $msg = urldecode($this->_getXMLcontent('STATUS'));
              $total = preg_replace('/[\$\,]/','',$this->_getXMLcontent('STANDARDGROUNDTOTAL'));
              $xservice['name'] = $this->_getXMLcontent('EXTENDEDSERVICENAME');
              $xservice['amount'] = $this->_getXMLcontent('EXTENDEDSERVICEAMOUNT');
              for($i=0; $i<count($xservice['name']); $i++) {
                switch ($xservice['name'][$i]) {
                  case 'Residential Delivery': $rtotal = preg_replace('/[\$\,]/','',$xservice['amount'][$i]); break;
                  case 'Lift Gate at Destination': $ltotal = preg_replace('/[\$\,]/','',$xservice['amount'][$i]); break;
                  case 'Inside Delivery': $itotal = preg_replace('/[\$\,]/','',$xservice['amount'][$i]); break;
                }
              }
              if ($msg || !is_numeric($total) || !is_numeric($rtotal) || !is_numeric($ltotal) || !is_numeric($itotal)) {
                if ($j) {
                  $sun_error = true;
                  break;
                } else {
                  $httpReq->Disconnect();
                  return array('error' => MODULE_SHIPPING_YELLOW_TEXT_ERROR); // . '<br>(' . ($msg?'Shipper error msg: '.$msg:'Error: invalid return') . ')');
                }
              } else foreach($this->types as $type => $service) if ($type != 'SUN') {
                if (isset($rates[$j][$type])) $rates[$j][$type] += $total; else $rates[$j][$type] = $total;
                switch ($type) {
                  case 'RESI': break;
                  case 'RESL': $rates[$j][$type] -= $itotal; break;
                  case 'RESC': $rates[$j][$type] -= $itotal + $ltotal; break;
                  case 'COMI': $rates[$j][$type] -= $rtotal; break;
                  case 'COML': $rates[$j][$type] -= $rtotal + $itotal; break;
                  case 'COMC': $rates[$j][$type] -= $rtotal + $itotal + $ltotal; break;
                }
              }

            } else {
              $httpReq->Disconnect();
              return array('error' => MODULE_SHIPPING_YELLOW_TEXT_ERROR . '<br>(status code: ' . strval($status) . ')');
            }
          }
          if (MODULE_SHIPPING_YELLOW_FREE == 'True' && !$sun_only && !sizeof($rates[$j]))
            foreach($this->types as $type => $service) if ($type != 'SUN') $rates[$j][$type] = 0;
        }
        $http->Disconnect();
      } else return false;

      $ysRates = $rates[0];
      if (MODULE_SHIPPING_YELLOW_SUN_ORIG && isset($this->types['SUN'])) {
        if ($sun_error) $ysRates['SUN'] = 'Call';
        else {
          $sun_query = tep_db_query("select r.rate1, r.rate2, r.rate3 from " . TABLE_POSTCODES . " c, " . TABLE_SUN_RATES . " r where c.postcode = '" . $dest_pcode . "' and c.country_id = '" . $order->delivery['country']['id'] . "' and c.zone_id = r.zone_id and (r.postcode = c.postcode or r.postcode = '0') and (r.areacode = c.areacode or r.areacode = '0') order by r.postcode desc, r.areacode desc");
          if ($sun = tep_db_fetch_array($sun_query)) {
            if ($sun_weight) {
              $sun_weight1 = 0; $sun_weight2 = 0; $sun_weight3 = 0;
              if ($sun_weight > 150) {
                $sun_weight1 = 150;
                if ($sun_weight > 1000) {
                  $sun_weight2 = 850;
                  $sun_weight3 = $sun_weight - 1000;
                } else $sun_weight2 = $sun_weight - 150;
              } else $sun_weight1 = $sun_weight;
              $sun_rate = ($sun['rate1']*$sun_weight1 + $sun['rate2']*$sun_weight2 + $sun['rate3']*$sun_weight3)*(floatval(MODULE_SHIPPING_YELLOW_SUN_FUEL)+100)/10000  + floatval(MODULE_SHIPPING_YELLOW_SUN_NFEE) + floatval(MODULE_SHIPPING_YELLOW_SUN_IFEE);
            } else $sun_rate = 0;
            $ysRates['SUN'] = (isset($rates[1]['COMC'])?$rates[1]['COMC']:0) + $sun_rate;
          }
        }
      }

      $shipping_weight = ceil($shipping_weight);
      if (sizeof($ysRates) > 0) {
        if (isset($this->service)) return isset($ysRates[$this->service])?array($this->service => $ysRates[$this->service]):false;
        else return $ysRates;
      } else return false;
    }

    function _getXMLcontent($tag) {
      if (preg_match_all("/\<".$tag."\>(.*?)\<\/".$tag."\>/is", $this->rqbody, $templist)) {
        if (count($templist[1]) == 1) return $templist[1][0];
        else return $templist[1];
      } else return '';
    }
  }
?>
