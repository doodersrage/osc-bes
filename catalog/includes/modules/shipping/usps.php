<?php
/*
  $Id: usps.php,v 1.47 2003/04/08 23:23:42 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
  
  This version modified by Alastair Young alastair@redhunter.com 1/20/05
  Uses domxml module to parse the xml responses
  Supports USPS V2 API for domestic
  Reworked International
  No longer prone to random USPS text and service changes within major categories
*/

  class usps {
    var $code, $title, $description, $icon, $enabled, $countries;

// class constructor
    function usps() {
      global $order;

      $this->code = 'usps';
      $this->title = MODULE_SHIPPING_USPS_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_USPS_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_USPS_SORT_ORDER;
      $this->icon = DIR_WS_ICONS . 'shipping_usps.gif';
      $this->tax_class = MODULE_SHIPPING_USPS_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_USPS_STATUS == 'True') ? true : false);

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_USPS_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_USPS_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
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

// Domestic types in request do not match MailService elements in response. There are multiple MailServices to each type.
// We query these individually. This should make usps_method integration easier
// in the mean time, comment out the unwanted ones
      $this->types = array(	
      						'Express' => 'Express Mail (next day)',
							'First Class' => 'First Class Mail',
							'Priority' => 'Priority Mail (2-3 days)',
							'Parcel' => 'Parcel Post',
//							'BPM' => 'Bound Printed Matter',
//							'Library' => 'Library Mail',
//							'Media' => 'Media Mail'
							);

      // make queries for these specific package types, as well as none
      $this->containers = array (	'Express'  => array('None'), // array('Flat Rate Envelope'),
      								'Priority' => array('None')); // array('Flat Rate Envelope','Flat Rate Box'));
      									
      $this->intl_mailtypes = array(	'package',
//									'envelope',
//									'postcards or aerogrammes',
//									'matter for the blind'
                               );

      $this->intl_types = array('GXG Document' => 'Global Express Guaranteed Document Service',
                                'GXG Non-Document' => 'Global Express Guaranteed Non-Document Service',
                                'Express' => 'Global Express Mail (EMS)',
                                'Priority Lg' => 'Global Priority Mail - Flat-rate Envelope (large)',
                                'Priority Sm' => 'Global Priority Mail - Flat-rate Envelope (small)',
                                'Priority Var' => 'Global Priority Mail - Variable Weight Envelope (single)',
                                'Airmail Letter' => 'Airmail Letter Post',
                                'Airmail Parcel' => 'Airmail Parcel Post',
                                'Surface Letter' => 'Economy (Surface) Letter Post',
                                'Surface Post' => 'Economy (Surface) Parcel Post');

// added by splautz for free shipping
      $this->free_types = preg_split("/\W+/",MODULE_SHIPPING_USPS_FREE,-1,PREG_SPLIT_NO_EMPTY);
      foreach($this->free_types as $i => $type) $this->free_types[$i] = preg_replace('/\-/',' ',$type);

      $this->countries = $this->country_list();
    }

// class methods
    function quote($method = '') {
      global $order, $shipping_weight_x, $shipping_num_boxes_x, $shipping_count_x;
      global $orig_code;  // added by splautz for support of non-freight shipping

      if ($method) $this->_setService($method);

      $this->_setMachinable('True');
      $this->_setContainer('');
      $this->_setSize('REGULAR');

// modified by splautz for free shipping
      $uspsQuote = array();
      $free_shipping_rates = array();
      $max_discount = 0;
      $spounds = floor ($shipping_weight_x[0]);
      $sounces = round(16 * ($shipping_weight_x[0] - $spounds));
      // get quote (0 => full quote)
      $this->_setWeight($spounds, $sounces);
      $uquote[0] = $this->_getQuote();
      if (!is_array($uquote[0]) || isset($uquote[0]['error'])) $uspsQuote = $uquote[0];
      else {
        if (!$shipping_count_x[1] || $shipping_weight_x[0] != $shipping_weight_x[1]) {
          if ($shipping_count_x[1]) {
            $spounds = floor ($shipping_weight_x[1]);
            $sounces = round(16 * ($shipping_weight_x[1] - $spounds));
      // get quote (1 => quote excluding free items)
            $this->_setWeight($spounds, $sounces);
            $uquote[1] = $this->_getQuote();
            // get free shipping rates
            if (is_array($uquote[1]) && !isset($uquote[1]['error'])) foreach($uquote[1] as $urate) {
              list($qtype,$qcost) = each($urate);
              $qcost *= $shipping_num_boxes_x[1];
              if (in_array($qtype,$this->free_types)) $free_shipping_rates[$qtype] = $qcost;
            }
          } else foreach($this->free_types as $qtype) $free_shipping_rates[$qtype] = 0;
          // find max discount
          foreach($uquote[0] as $urate) {
            list($qtype,$qcost) = each($urate);
            $qcost *= $shipping_num_boxes_x[0];
            if (isset($free_shipping_rates[$qtype])) $max_discount = max($max_discount, $qcost - $free_shipping_rates[$qtype]);
          }
        }
        // rebuild quote with free shipping reductions
        foreach($uquote[0] as $urate) {
          list($qtype,$qcost) = each($urate);
          $qcost *= $shipping_num_boxes_x[0];
          if (isset($free_shipping_rates[$qtype]))  // take free shipping rate if present
            $qcost = $free_shipping_rates[$qtype];
          elseif (in_array($qtype,$this->free_types) || !is_numeric(MODULE_SHIPPING_USPS_FREE_DISC))  // if still a free rate or limit undefined, reduce by max discount
            $qcost -= $max_discount;
          else // for all others reduce by max discount up to limit if appropriate
            $qcost -= min($max_discount,MODULE_SHIPPING_USPS_FREE_DISC);
          if ($qcost < 0) $qcost = 0;
          $uspsQuote[] = array($qtype => $qcost);
        }
      }

      if (is_array($uspsQuote)) {
        if (isset($uspsQuote['error'])) {
          $this->quotes = array('module' => $this->title,
                                'error' => $uspsQuote['error']);
        } else {
          $this->quotes = array('id' => $this->code,
                                'module' => $this->title . ' (' . $shipping_num_boxes_x[0] . ' x ' . $shipping_weight_x[0] . 'lbs)');

          $methods = array();
          $size = sizeof($uspsQuote);
          for ($i=0; $i<$size; $i++) {
            list($type, $cost) = each($uspsQuote[$i]);

            $methods[] = array('id' => $type,
                               'title' => ((isset($this->types[$type])) ? $this->types[$type] : $type),
                               'cost' => $cost + MODULE_SHIPPING_USPS_HANDLING * $shipping_num_boxes_x[1]);
          }

          $this->quotes['methods'] = $methods;

          if ($this->tax_class > 0) {
            $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          }
        }
      } else {
        $this->quotes = array('module' => $this->title,
                              'error' => MODULE_SHIPPING_USPS_TEXT_ERROR);
      }

      if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_USPS_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable USPS Shipping', 'MODULE_SHIPPING_USPS_STATUS', 'True', 'Do you want to offer USPS shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Enter the USPS User ID', 'MODULE_SHIPPING_USPS_USERID', 'NONE', 'Enter the USPS USERID assigned to you.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Enter the USPS Password', 'MODULE_SHIPPING_USPS_PASSWORD', 'NONE', 'See USERID, above.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Which server to use', 'MODULE_SHIPPING_USPS_SERVER', 'production', 'An account at USPS is needed to use the Production server', '6', '0', 'tep_cfg_select_option(array(\'test\', \'production\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Handling Fee', 'MODULE_SHIPPING_USPS_HANDLING', '0', 'Handling fee for this shipping method.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_USPS_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_USPS_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_USPS_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Allow Free Shipping?', 'MODULE_SHIPPING_USPS_FREE', 'Parcel', 'Allow free shipping for these rates. Parcel, Priority, First-Class, Express', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Free Shipping Discount', 'MODULE_SHIPPING_USPS_FREE_DISC', '', 'All other rates will be reduced by up to this amount on free shipping items.  Leave blank to reduce by value of highest free rate.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_SHIPPING_USPS_STATUS', 'MODULE_SHIPPING_USPS_USERID', 'MODULE_SHIPPING_USPS_PASSWORD', 'MODULE_SHIPPING_USPS_SERVER', 'MODULE_SHIPPING_USPS_HANDLING', 'MODULE_SHIPPING_USPS_TAX_CLASS', 'MODULE_SHIPPING_USPS_ZONE', 'MODULE_SHIPPING_USPS_SORT_ORDER', 'MODULE_SHIPPING_USPS_FREE', 'MODULE_SHIPPING_USPS_FREE_DISC');
    }

    function _setService($service) {
      $this->service = $service;
    }

    function _setWeight($pounds, $ounces=0) {
      if ($pounds == 0 && $ounces < 1) $ounces = 1;  // added by splautz since usps doesnt accept zero weight
      $this->pounds = $pounds;
      $this->ounces = $ounces;
    }

    function _setContainer($container) {
      $this->container = $container;
    }

    function _setSize($size) {
      $this->size = $size;
    }

    function _setMachinable($machinable) {
      $this->machinable = $machinable;
    }
    
    function _getQuote() {
      global $order;
      global $orig_code;  // added by splautz for support of non-freight shipping

// modified by splautz for support of non-freight shipping
      if ($order->delivery['country']['id'] == SHIPPING_ORIGIN_COUNTRY) {
        $request  = '<RateV2Request USERID="' . MODULE_SHIPPING_USPS_USERID . '" PASSWORD="' . MODULE_SHIPPING_USPS_PASSWORD . '">';
        $services_count = 0;

        $dest_zip = str_replace(' ', '', $order->delivery['postcode']);
        if ($order->delivery['country']['iso_code_2'] == 'US') $dest_zip = substr($dest_zip, 0, 5);

        foreach (array_keys($this->types) as $type) {
          $containers = array('');
          if (array_key_exists($type,$this->containers)) $containers=array_merge($containers,$this->containers[$type]);

          foreach ($containers as $container) {			 
            $request .= '<Package ID="' . $services_count . '">' .
                        '<Service>' . $type . '</Service>' .
// modified by splautz for support of non-freight shipping
//                      '<ZipOrigination>' . SHIPPING_ORIGIN_ZIP . '</ZipOrigination>' .
                        '<ZipOrigination>' . tep_get_location_code($orig_code, 2) . '</ZipOrigination>' .
                        '<ZipDestination>' . $dest_zip . '</ZipDestination>' .
                        '<Pounds>' . $this->pounds . '</Pounds>' .
                        '<Ounces>' . $this->ounces . '</Ounces>';
            if ($container != 'None') $request .= '<Container>' . $container . '</Container>';
            $request .= '<Size>' . $this->size . '</Size>' .
                        '<Machinable>' . $this->machinable . '</Machinable>' .
                        '</Package>';
            $services_count++;
          }
        }
        $request .= '</RateV2Request>';
        $request = 'API=RateV2&XML=' . urlencode($request);
      } else {
        $request  = '<IntlRateRequest USERID="' . MODULE_SHIPPING_USPS_USERID . '" PASSWORD="' . MODULE_SHIPPING_USPS_PASSWORD . '">';
        for ($services_count=0; $services_count < count($this->intl_mailtypes); $services_count++ ) {
           $request .= '<Package ID="' . $services_count . '">' .
                    '<Pounds>' . $this->pounds . '</Pounds>' .
                    '<Ounces>' . $this->ounces . '</Ounces>' .
                    '<MailType>' . $this->intl_mailtypes[$services_count] . '</MailType>' .
                    '<Country>' . $this->countries[$order->delivery['country']['iso_code_2']] . '</Country>' .
                    '</Package>';
        }
        $request .= '</IntlRateRequest>';
        $request = 'API=IntlRate&XML=' . urlencode($request);
      }

      switch (MODULE_SHIPPING_USPS_SERVER) {
        case 'production': $usps_server = 'production.shippingapis.com';
                           $api_dll = 'shippingapi.dll';
                           break;
        case 'test':
        default:           $usps_server = 'testing.shippingapis.com';
                           $api_dll = 'ShippingAPITest.dll';
                           break;
      }

      $body = '';

      $http = new httpClient();
      if ($http->Connect($usps_server, 80)) {
        $http->addHeader('Host', $usps_server);
        $http->addHeader('User-Agent', 'osCommerce');
        $http->addHeader('Connection', 'Close');
        if ($http->Get('/' . $api_dll . '?' . $request)) $body = $http->getBody();

        $http->Disconnect();
      } else {
        return false;
      }

      if (($end = strpos($body, '<Package ID=')) && ($response = $this->_getXMLcontent(substr($body, 0, $end), 'Error'))) {
        $number = ereg('<Number>(.*)</Number>', $response[0], $regs);
        $number = $regs[1];
        $description = ereg('<Description>(.*)</Description>', $response[0], $regs);
        $description = $regs[1];

        return array('error' => $number . ' - ' . $description);
      }
 
      $response = array();
      while (true) {
        if ($start = strpos($body, '<Package ID=')) {
          $body = substr($body, $start);
          $end = strpos($body, '</Package>');
          $response[] = substr($body, 0, $end+10);
          $body = substr($body, $end+9);
        } else {
          break;
        }
      }

      $rates = array();
// modified by splautz for support of non-freight shipping
      if ($order->delivery['country']['id'] == SHIPPING_ORIGIN_COUNTRY) {

        $n = sizeof($response);
        for ($i=0; $i<$n; $i++) {
          if ($postages = $this->_getXMLcontent($response[$i], 'Postage')) foreach($postages as $postage) {
            if (ereg('<Rate>(.*)</Rate>', $postage, $regs)) {
              $rate = $regs[1];
              if (ereg('<MailService>(.*)</MailService>', $postage, $regs)) {
                // strip size info. On domestic these do not change rate and inch " quotes screw up display with backslashes
                $service = preg_replace('/\s*\(.*\).*$/','',$regs[1]);
                foreach (array_keys($this->types) as $type) if (strpos($service,$type) === 0) $service = $type;
                if (isset($this->service) && ($service != $this->service) ) continue;

                $rates[$service] = array($service => $rate); // trick to remove duplicates
              }
            }
          }
        }
      } else {
        $n = sizeof($response);
        for ($i=0; $i<$n; $i++) {
          $body = $response[$i];
          $services = array();
          while (true) {
            if ($start = strpos($body, '<Service ID=')) {
              $body = substr($body, $start);
              $end = strpos($body, '</Service>');
              $services[] = substr($body, 0, $end+10);
              $body = substr($body, $end+9);
            } else {
              break;
            }
          }
        }
        $size = sizeof($services);
        for ($i=0, $n=$size; $i<$n; $i++) {
          if (ereg('<Postage>(.*)</Postage>', $services[$i], $regs)) {
            $postage = $regs[1];
            if (ereg('<SvcDescription>(.*)</SvcDescription>', $services[$i], $regs)) {
              $service = $regs[1];
              if (!in_array($service,$this->intl_types)) continue;
              if (ereg('<SvcCommitments>(.*)</SvcCommitments>', $services[$i], $regs)) $service .= ' (' . $regs[1] . ')';
              if (isset($this->service) && ($service != $this->service)) continue;

              $rates[$service] = array($service => $postage); // trick to remove duplicates
            }
          }
        }
      }
      return ((sizeof($rates) > 0) ? $rates : false);
    }

    function country_list() {
      $list = array('AF' => 'Afghanistan',
                    'AL' => 'Albania',
                    'DZ' => 'Algeria',
                    'AD' => 'Andorra',
                    'AO' => 'Angola',
                    'AI' => 'Anguilla',
                    'AG' => 'Antigua and Barbuda',
                    'AR' => 'Argentina',
                    'AM' => 'Armenia',
                    'AW' => 'Aruba',
                    'AU' => 'Australia',
                    'AT' => 'Austria',
                    'AZ' => 'Azerbaijan',
                    'BS' => 'Bahamas',
                    'BH' => 'Bahrain',
                    'BD' => 'Bangladesh',
                    'BB' => 'Barbados',
                    'BY' => 'Belarus',
                    'BE' => 'Belgium',
                    'BZ' => 'Belize',
                    'BJ' => 'Benin',
                    'BM' => 'Bermuda',
                    'BT' => 'Bhutan',
                    'BO' => 'Bolivia',
                    'BA' => 'Bosnia-Herzegovina',
                    'BW' => 'Botswana',
                    'BR' => 'Brazil',
                    'VG' => 'British Virgin Islands',
                    'BN' => 'Brunei Darussalam',
                    'BG' => 'Bulgaria',
                    'BF' => 'Burkina Faso',
                    'MM' => 'Burma',
                    'BI' => 'Burundi',
                    'KH' => 'Cambodia',
                    'CM' => 'Cameroon',
                    'CA' => 'Canada',
                    'CV' => 'Cape Verde',
                    'KY' => 'Cayman Islands',
                    'CF' => 'Central African Republic',
                    'TD' => 'Chad',
                    'CL' => 'Chile',
                    'CN' => 'China',
                    'CX' => 'Christmas Island (Australia)',
                    'CC' => 'Cocos Island (Australia)',
                    'CO' => 'Colombia',
                    'KM' => 'Comoros',
                    'CG' => 'Congo (Brazzaville),Republic of the',
                    'ZR' => 'Congo, Democratic Republic of the',
                    'CK' => 'Cook Islands (New Zealand)',
                    'CR' => 'Costa Rica',
                    'CI' => 'Cote d\'Ivoire (Ivory Coast)',
                    'HR' => 'Croatia',
                    'CU' => 'Cuba',
                    'CY' => 'Cyprus',
                    'CZ' => 'Czech Republic',
                    'DK' => 'Denmark',
                    'DJ' => 'Djibouti',
                    'DM' => 'Dominica',
                    'DO' => 'Dominican Republic',
                    'TP' => 'East Timor (Indonesia)',
                    'EC' => 'Ecuador',
                    'EG' => 'Egypt',
                    'SV' => 'El Salvador',
                    'GQ' => 'Equatorial Guinea',
                    'ER' => 'Eritrea',
                    'EE' => 'Estonia',
                    'ET' => 'Ethiopia',
                    'FK' => 'Falkland Islands',
                    'FO' => 'Faroe Islands',
                    'FJ' => 'Fiji',
                    'FI' => 'Finland',
                    'FR' => 'France',
                    'GF' => 'French Guiana',
                    'PF' => 'French Polynesia',
                    'GA' => 'Gabon',
                    'GM' => 'Gambia',
                    'GE' => 'Georgia, Republic of',
                    'DE' => 'Germany',
                    'GH' => 'Ghana',
                    'GI' => 'Gibraltar',
                    'GB' => 'Great Britain and Northern Ireland',
                    'GR' => 'Greece',
                    'GL' => 'Greenland',
                    'GD' => 'Grenada',
                    'GP' => 'Guadeloupe',
                    'GT' => 'Guatemala',
                    'GN' => 'Guinea',
                    'GW' => 'Guinea-Bissau',
                    'GY' => 'Guyana',
                    'HT' => 'Haiti',
                    'HN' => 'Honduras',
                    'HK' => 'Hong Kong',
                    'HU' => 'Hungary',
                    'IS' => 'Iceland',
                    'IN' => 'India',
                    'ID' => 'Indonesia',
                    'IR' => 'Iran',
                    'IQ' => 'Iraq',
                    'IE' => 'Ireland',
                    'IL' => 'Israel',
                    'IT' => 'Italy',
                    'JM' => 'Jamaica',
                    'JP' => 'Japan',
                    'JO' => 'Jordan',
                    'KZ' => 'Kazakhstan',
                    'KE' => 'Kenya',
                    'KI' => 'Kiribati',
                    'KW' => 'Kuwait',
                    'KG' => 'Kyrgyzstan',
                    'LA' => 'Laos',
                    'LV' => 'Latvia',
                    'LB' => 'Lebanon',
                    'LS' => 'Lesotho',
                    'LR' => 'Liberia',
                    'LY' => 'Libya',
                    'LI' => 'Liechtenstein',
                    'LT' => 'Lithuania',
                    'LU' => 'Luxembourg',
                    'MO' => 'Macao',
                    'MK' => 'Macedonia, Republic of',
                    'MG' => 'Madagascar',
                    'MW' => 'Malawi',
                    'MY' => 'Malaysia',
                    'MV' => 'Maldives',
                    'ML' => 'Mali',
                    'MT' => 'Malta',
                    'MQ' => 'Martinique',
                    'MR' => 'Mauritania',
                    'MU' => 'Mauritius',
                    'YT' => 'Mayotte (France)',
                    'MX' => 'Mexico',
                    'MD' => 'Moldova',
                    'MC' => 'Monaco (France)',
                    'MN' => 'Mongolia',
                    'MS' => 'Montserrat',
                    'MA' => 'Morocco',
                    'MZ' => 'Mozambique',
                    'NA' => 'Namibia',
                    'NR' => 'Nauru',
                    'NP' => 'Nepal',
                    'NL' => 'Netherlands',
                    'AN' => 'Netherlands Antilles',
                    'NC' => 'New Caledonia',
                    'NZ' => 'New Zealand',
                    'NI' => 'Nicaragua',
                    'NE' => 'Niger',
                    'NG' => 'Nigeria',
                    'KP' => 'North Korea (Korea, Democratic People\'s Republic of)',
                    'NO' => 'Norway',
                    'OM' => 'Oman',
                    'PK' => 'Pakistan',
                    'PA' => 'Panama',
                    'PG' => 'Papua New Guinea',
                    'PY' => 'Paraguay',
                    'PE' => 'Peru',
                    'PH' => 'Philippines',
                    'PN' => 'Pitcairn Island',
                    'PL' => 'Poland',
                    'PT' => 'Portugal',
                    'QA' => 'Qatar',
                    'RE' => 'Reunion',
                    'RO' => 'Romania',
                    'RU' => 'Russia',
                    'RW' => 'Rwanda',
                    'SH' => 'Saint Helena',
                    'KN' => 'Saint Kitts (St. Christopher and Nevis)',
                    'LC' => 'Saint Lucia',
                    'PM' => 'Saint Pierre and Miquelon',
                    'VC' => 'Saint Vincent and the Grenadines',
                    'SM' => 'San Marino',
                    'ST' => 'Sao Tome and Principe',
                    'SA' => 'Saudi Arabia',
                    'SN' => 'Senegal',
                    'YU' => 'Serbia-Montenegro',
                    'SC' => 'Seychelles',
                    'SL' => 'Sierra Leone',
                    'SG' => 'Singapore',
                    'SK' => 'Slovak Republic',
                    'SI' => 'Slovenia',
                    'SB' => 'Solomon Islands',
                    'SO' => 'Somalia',
                    'ZA' => 'South Africa',
                    'GS' => 'South Georgia (Falkland Islands)',
                    'KR' => 'South Korea (Korea, Republic of)',
                    'ES' => 'Spain',
                    'LK' => 'Sri Lanka',
                    'SD' => 'Sudan',
                    'SR' => 'Suriname',
                    'SZ' => 'Swaziland',
                    'SE' => 'Sweden',
                    'CH' => 'Switzerland',
                    'SY' => 'Syrian Arab Republic',
                    'TW' => 'Taiwan',
                    'TJ' => 'Tajikistan',
                    'TZ' => 'Tanzania',
                    'TH' => 'Thailand',
                    'TG' => 'Togo',
                    'TK' => 'Tokelau (Union) Group (Western Samoa)',
                    'TO' => 'Tonga',
                    'TT' => 'Trinidad and Tobago',
                    'TN' => 'Tunisia',
                    'TR' => 'Turkey',
                    'TM' => 'Turkmenistan',
                    'TC' => 'Turks and Caicos Islands',
                    'TV' => 'Tuvalu',
                    'UG' => 'Uganda',
                    'UA' => 'Ukraine',
                    'AE' => 'United Arab Emirates',
                    'UY' => 'Uruguay',
                    'UZ' => 'Uzbekistan',
                    'VU' => 'Vanuatu',
                    'VA' => 'Vatican City',
                    'VE' => 'Venezuela',
                    'VN' => 'Vietnam',
                    'WF' => 'Wallis and Futuna Islands',
                    'WS' => 'Western Samoa',
                    'YE' => 'Yemen',
                    'ZM' => 'Zambia',
                    'ZW' => 'Zimbabwe');

      return $list;
    }

    function _getXMLcontent($str, $tag) {
      if (preg_match_all("/\<".$tag."\>(.*?)\<\/".$tag."\>/is", $str, $templist)) return $templist[1];
      else return array();
    }
  }
?>
