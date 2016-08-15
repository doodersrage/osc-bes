<?php
/*
  $Id: shipping.php,v 1.23 2003/06/29 11:22:05 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class shipping {
    var $modules;
    var $dest_code; // added by splautz for quote system

// class constructor

// modified by splautz for freight shipping
//  function shipping($module = '') {

    function shipping($modules = array()) {
      global $language, $PHP_SELF;
	  global $shippingQuotes, $order;  // added by splautz for shipping quote cache

      if (defined('MODULE_SHIPPING_INSTALLED') && tep_not_null(MODULE_SHIPPING_INSTALLED)) {
        $this->modules = explode(';', MODULE_SHIPPING_INSTALLED);

        $include_modules = array();

// modified by splautz for freight shipping
//      if ( (tep_not_null($module)) && (in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $this->modules)) ) {
//        $include_modules[] = array('class' => substr($module['id'], 0, strpos($module['id'], '_')), 'file' => substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)));
        if (sizeof($modules)) foreach($modules as $module) {
          if ( (tep_not_null($module)) && (in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $this->modules)) ) {
            $include_modules[] = array('class' => substr($module['id'], 0, strpos($module['id'], '_')), 'file' => substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)));
          }

        } else {
          reset($this->modules);
          while (list(, $value) = each($this->modules)) {
            $class = substr($value, 0, strrpos($value, '.'));
            $include_modules[] = array('class' => $class, 'file' => $value);
          }
        }

        for ($i=0, $n=sizeof($include_modules); $i<$n; $i++) {
          include(DIR_WS_LANGUAGES . $language . '/modules/shipping/' . $include_modules[$i]['file']);
          include(DIR_WS_MODULES . 'shipping/' . $include_modules[$i]['file']);

          $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];

        }

// added by splautz for shipping quote cache
        if (!tep_session_is_registered('shippingQuotes')) {
          $shippingQuotes['cartID'] = -1;
          tep_session_register('shippingQuotes');
        }

      }
// added by splautz for shipping quote cache
      $dest_pcode = str_replace(' ', '', $order->delivery['postcode']);
      $dest_ccode = $order->delivery['country']['id'];
      if ($dest_ccode == '223') $dest_pcode = substr($dest_pcode, 0, 5);
      $this->dest_code = $dest_ccode.':'.$dest_pcode;

    }

    function quote($method = '', $module = '') {
      global $total_weight, $shipping_quoted, $shipping_weight, $shipping_weight_x, $shipping_num_boxes, $shipping_num_boxes_x, $shipping_count_x;
	  global $shippingQuotes, $order, $cart, $orig_code;  // added by splautz for shipping quote cache

      $quotes_array = array();

// added by splautz for shipping quote cache
      if ($shippingQuotes['cartID'] != $cart->cartID) {
        $resetCache = true;
        $shippingQuotes['cartID'] = $cart->cartID;
        $shippingQuotes['quotes'] = array();
      } elseif (!isset($shippingQuotes['quotes'][$this->dest_code])) $resetCache = true;
      else {
        $resetCache = false;
      }

// modified by splautz for quote system
//    if (is_array($this->modules)) {
      if (!isset($include_quotes) && is_array($this->modules)) {
        $shipping_quoted = '';
        $include_quotes = array();
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($module !== '') {
            if ( ($module == $class) && ($GLOBALS[$class]->enabled) ) {
              $include_quotes[] = $class;
            }
          } elseif ($GLOBALS[$class]->enabled) {
            $include_quotes[] = $class;
          }
        }
// added by splautz for quote system
      }
      if (isset($include_quotes)) {
// modified by splautz for shipping quote cache
//      $size = sizeof($include_quotes);
//      for ($i=0; $i<$size; $i++) {
//        $quotes = $GLOBALS[$include_quotes[$i]]->quote($method);
        foreach($include_quotes as $class) {
          $freight = isset($GLOBALS[$class]->freight)?$GLOBALS[$class]->freight:0;  // added by splautz for freight shipping
          if (sizeof($cart->shipments[$freight])) {  // added by splautz for freight shipping
            if ($resetCache || !isset($shippingQuotes['quotes'][$this->dest_code][$class])) {
  // modified by splautz for non-freight shipping
              if ($freight) $tquote = $GLOBALS[$class]->quote('');
              else {
                unset($tquote);
                $num_shipments = sizeof($cart->shipments[0]);
                $total_shipping_weight = 0;
                foreach($cart->shipments[0] as $orig_code => $shipment) {
                  $shipping_weight_x[0] = (isset($shipment['weight'])?$shipment['weight']:0);
                  $shipping_weight_x[1] = (isset($shipment['weight2'])?$shipment['weight2']:0);
                  $shipping_count_x[0] = (isset($shipment['count'])?$shipment['count']:0);
                  $shipping_count_x[1] = (isset($shipment['count2'])?$shipment['count2']:0);
                  $shipping_num_boxes_x = array(1,0);
                  $total_shipping_weight += $shipment['weight'];
//                $shipping_weight = $total_weight;

                  for($i = 0; $i < 2; $i++) if ($shipping_count_x[$i] > 0) {
                    $shipping_num_boxes_x[$i] = 1;
                    if (SHIPPING_BOX_WEIGHT >= $shipping_weight_x[$i]*SHIPPING_BOX_PADDING/100) {
                      $shipping_weight_x[$i] = $shipping_weight_x[$i]+SHIPPING_BOX_WEIGHT;
                    } else {
                      $shipping_weight_x[$i] = $shipping_weight_x[$i] + ($shipping_weight_x[$i]*SHIPPING_BOX_PADDING/100);
                    }
                    if ($shipping_weight_x[$i] > SHIPPING_MAX_WEIGHT) { // Split into many boxes
                      $shipping_num_boxes_x[$i] = ceil($shipping_weight_x[$i]/SHIPPING_MAX_WEIGHT);
                      $shipping_weight_x[$i] = $shipping_weight_x[$i]/$shipping_num_boxes_x[$i];
                    }
                  }

                  $shipping_weight = $shipping_weight_x[0];  // set for compatibility with existing modules
                  $shipping_num_boxes = $shipping_num_boxes_x[0];  // set for compatibility with existing modules
                  $lquote = $GLOBALS[$class]->quote('');

                  if (isset($lquote['error'])) {
                    $tquote = $lquote;
                    $num_shipments = 0;
                    break;
                  }
                  if (isset($tquote)) {
                    for($t=0,$tsize=sizeof($tquote['methods']); $t<$tsize; $t++) {
                      for($l=0,$lsize=sizeof($lquote['methods']); $l<$lsize; $l++) {
                        if ($tquote['methods'][$t]['id'] == $lquote['methods'][$l]['id']) {
                          $tquote['methods'][$t]['cost'] += $lquote['methods'][$l]['cost'];
                          break;
                        }
                      }
                      if ($l = $lsize) unset($tquote['methods'][$t]);
                    }
                  } else $tquote = $lquote;
                }
                if (!isset($tquote['error']) && !sizeof($tquote['methods'])) {
                  $tquote['error'] = 'No rates available';
                }
                $tquote['module'] = $GLOBALS[$class]->title;  // . ' (' . $total_shipping_weight . ' lbs; ' . $num_shipments . ' shipment' . ($num_shipments!=1?'s':'') . ' to '.$order->delivery['country']['iso_code_2'].':'. substr($this->dest_code,strpos($this->dest_code,':')+1) . ')';
              }
              $shippingQuotes['quotes'][$this->dest_code][$class] = $tquote;
  // end of non-freight shipping modification

            }
            $cquotes = &$shippingQuotes['quotes'][$this->dest_code][$class];
            $quotes = $cquotes;
            if ($method !== '' && isset($cquotes['methods'])) {
              unset($quotes['methods']);
              foreach($cquotes['methods'] as $cquote) if ($cquote['id'] == $method) {
                $quotes['methods'][] = $cquote;
              }
            }
            $GLOBALS[$class]->quotes = $quotes;
// end of shipping quote cache modification

// modified by splautz for freight shipping
//          if (is_array($quotes)) $quotes_array[] = $quotes;
            if (is_array($quotes)) {
              if ($module.$method == '') {
                $quotes_array[$freight][] = $quotes;
              } else $quotes_array[] = $quotes;
            }

          }
        }
      }

      return $quotes_array;
    }

    function cheapest() {
      global $cart;  // added by splautz for freight shipping
// added by splautz for quote system
      global $shippingQuotes;

      if (is_array($this->modules)) {
        $rates = array();

        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $freight = isset($GLOBALS[$class]->freight)?$GLOBALS[$class]->freight:0;  // added by splautz for freight shipping
            if (sizeof($cart->shipments[$freight])) {  // added by splautz for freight shipping
              $quotes = $GLOBALS[$class]->quotes;
              for ($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++) {
                if (isset($quotes['methods'][$i]['cost']) && is_numeric($quotes['methods'][$i]['cost'])) {  // modified by splautz for freight shipping
                  $rates[$freight][] = array('id' => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],
                                   'title' => $quotes['module'] . ' (' . $quotes['methods'][$i]['title'] . ')',
                                   'cost' => $quotes['methods'][$i]['cost']);
                }
              }
            }
          }
        }

// modified by splautz for freight shipping
//      $cheapest = false;
//      for ($i=0, $n=sizeof($rates); $i<$n; $i++) {
//        if (is_array($cheapest)) {
//          if ($rates[$i]['cost'] < $cheapest['cost']) {
//            $cheapest = $rates[$i];
//          }
//        } else {
//          $cheapest = $rates[$i];
//        }
//      }
        $cheapest = array();
        for($freight=0; $freight<2; $freight++)
          foreach($rates[$freight] as $rate) {
            if (is_array($cheapest[$freight])) {
              if ($rate['cost'] < $cheapest[$freight]['cost']) {
                $cheapest[$freight] = $rate;
              }
            } else {
              $cheapest[$freight] = $rate;
            }
          }

        return $cheapest;
      }
    }
  }
?>
