<?php
/*
  $Id: wishlist.php,v 3.0  2005/08/24 Dennis Blake
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
*/

  class wishlist {
	var $wishID;


	function wishlist() {
      	$this->reset();
	}

	function restore_wishlist() {
	global $customer_id;

		if (!tep_session_is_registered('customer_id')) return false;

	// merge current wishlist items in database
		if (is_array($this->wishID)) {
        	reset($this->wishID);

			while (list($wishlist_id, ) = each($this->wishID)) {
				$wishlist_query = tep_db_query("select products_id from " . TABLE_WISHLIST . " where customers_id = '" . $customer_id . "' and products_id = '" . $wishlist_id . "'");
				if (!tep_db_num_rows($wishlist_query)) {
		   			tep_db_query("insert into " . TABLE_WISHLIST . " (customers_id, products_id) values ('" . $customer_id . "', '" . tep_db_input($wishlist_id) . "')");
					if (isset($this->wishID[$wishlist_id]['attributes'])) {
	              		reset($this->wishID[$wishlist_id]['attributes']);
						while (list($option, $value) = each($this->wishID[$wishlist_id]['attributes'])) {
                          //clr 031714 udate query to include attribute value. This is needed for text attributes.
                          $attr_value = $this->wishID[$wishlist_id]['attributes_values'][$option];
                          tep_db_query("insert into " . TABLE_WISHLIST_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id, products_options_value_text) values ('" . (int)$customer_id . "', '" . tep_db_input($wishlist_id) . "', '" . (int)$option . "', '" . (int)$value . "', '" . tep_db_input($attr_value) . "')");
                          // tep_db_query("insert into " . TABLE_WISHLIST_ATTRIBUTES . " (customers_id, products_id, products_options_id , products_options_value_id) values ('" . $customer_id . "', '" . tep_db_input($wishlist_id) . "', '" . $option . "', '" . $value . "' )");
			    		}
					}
				}
			}
		}

		// reset session contents
		unset($this->wishID);

		$wishlist_session = tep_db_query("select products_id from " . TABLE_WISHLIST . " where customers_id = '" . $customer_id . "'");
		while($wishlist = tep_db_fetch_array($wishlist_session)) {
			$this->wishID[$wishlist['products_id']] = array($wishlist['products_id']);
		// attributes
            //CLR 020606 update query to pull attribute value_text. This is needed for text attributes.
            $attributes_query = tep_db_query("select products_options_id, products_options_value_id, products_options_value_text from " . TABLE_WISHLIST_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($wishlist['products_id']) . "'");
       		// $attributes_query = tep_db_query("select products_options_id, products_options_value_id from " . TABLE_WISHLIST_ATTRIBUTES . " where customers_id = '" . $customer_id . "' and products_id = '" . tep_db_input($wishlist['products_id']) . "'");
       		while ($attributes = tep_db_fetch_array($attributes_query)) {
                $this->wishID[$wishlist['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
                //CLR 020606 if text attribute, then set additional information
                if ($attributes['products_options_value_id'] == PRODUCTS_OPTIONS_VALUE_TEXT_ID)
                  $this->wishID[$wishlist['products_id']]['attributes_values'][$attributes['products_options_id']] = $attributes['products_options_value_text'];
       		}
		}
	}

	function add_wishlist($wishlist_id, $attributes_id) {
      global $customer_id;

		$wishlist_id = tep_get_uprid($wishlist_id, $attributes_id);

		if(!$this->in_wishlist($wishlist_id)) {

			// Insert into session
//          $this->wishID[$wishlist_id] = array($wishlist_id);  splautz => I'm unsure what this was for
			$this->wishID[$wishlist_id] = array();

			if (tep_session_is_registered('customer_id')) {
			// Insert into database
	   			tep_db_query("insert into " . TABLE_WISHLIST . " (customers_id, products_id) values ('" . $customer_id . "', '" . tep_db_input($wishlist_id) . "')");
			}

	   		// Read array of options and values for attributes in id[]
			if (is_array($attributes_id)) {
				reset($attributes_id);
				while (list($option, $value) = each($attributes_id)) {
                  //CLR 020606 check if input was from text box.  If so, store additional attribute information
                  //CLR 020708 check if text input is blank, if so do not add to attribute lists
                  //CLR 030228 add htmlspecialchars processing.  This handles quotes and other special chars in the user input.
                  $attr_value = NULL;
                  $blank_value = FALSE;
                  if (strstr($option, TEXT_PREFIX)) {
                    if (trim($value) == NULL)
                    {
                      $blank_value = TRUE;
                    } else {
                      $option = substr($option, strlen(TEXT_PREFIX));
                      $attr_value = htmlspecialchars(stripslashes($value), ENT_QUOTES);
                      $value = PRODUCTS_OPTIONS_VALUE_TEXT_ID;
                      $this->wishID[$wishlist_id]['attributes_values'][$option] = $attr_value;
                    }
                  }

                  if (!$blank_value)
                  {
                    $this->wishID[$wishlist_id]['attributes'][$option] = $value;
		   			// Add to customers_wishlist_attributes table
                    //CLR 020606 update db insert to include attribute value_text. This is needed for text attributes.
                    //CLR 030228 add tep_db_input() processing
                    if (tep_session_is_registered('customer_id')) tep_db_query("insert into " . TABLE_WISHLIST_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id, products_options_value_text) values ('" . (int)$customer_id . "', '" . tep_db_input($wishlist_id) . "', '" . (int)$option . "', '" . (int)$value . "', '" . tep_db_input($attr_value) . "')");
					// if (tep_session_is_registered('customer_id')) tep_db_query("insert into " . TABLE_WISHLIST_ATTRIBUTES . " (customers_id, products_id, products_options_id , products_options_value_id) values ('" . $customer_id . "', '" . tep_db_input($wishlist_id) . "', '" . $option . "', '" . $value . "' )");
                  }
	    		}
		  	}
		}
	}

	function remove($wishlist_id) {
	global $customer_id;

        //CLR 030228 add call tep_get_uprid to correctly format wishlist ids containing quotes
        $wishlist_id = tep_get_uprid($wishlist_id, $attributes);

		// Remove from session
		unset($this->wishID[$wishlist_id]);

		//remove from database
		if (tep_session_is_registered('customer_id')) {
			tep_db_query("delete from " . TABLE_WISHLIST . " where products_id = '" . tep_db_input($wishlist_id) . "' and customers_id = '" . $customer_id . "'");
			tep_db_query("delete from " . TABLE_WISHLIST_ATTRIBUTES . " where products_id = '" . tep_db_input($wishlist_id) . "' and customers_id = '" . $customer_id . "'");
		}
	}


	function clear() {
	global $customer_id;

		// Remove all from database
  		if (tep_session_is_registered('customer_id')) {
 	  		$wishlist_products_query = tep_db_query("select products_id from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "'");
	  		while($wishlist_products = tep_db_fetch_array($wishlist_products_query)) {
				tep_db_query("delete from " . TABLE_WISHLIST . " where products_id = '" . tep_db_input($wishlist_products[products_id]) . "' and customers_id = '" . $customer_id . "'");
				tep_db_query("delete from " . TABLE_WISHLIST_ATTRIBUTES . " where products_id = '" . tep_db_input($wishlist_products[products_id]) . "' and customers_id = '" . $customer_id . "'");
	  		}
		}
	}

	function reset($reset_database = false) {
      global $customer_id;

		// Remove all from database
		if (tep_session_is_registered('customer_id') && ($reset_database == true)) {
        	tep_db_query("delete from " . TABLE_WISHLIST . " where customers_id = '" . $customer_id . "'");
        	tep_db_query("delete from " . TABLE_WISHLIST_ATTRIBUTES . " where customers_id = '" . $customer_id . "'");
      	}

		// reset session contents
		unset($this->wishID);		
    }

	function in_wishlist($wishlist_id) {
	global $customer_id;

		if (isset($this->wishID[$wishlist_id])) {
        	return true;
      	} else {
        	return false;
      	}
	}


	function get_att($wishlist_id) {
    	$pieces = explode('{', $wishlist_id);

	    return $pieces[0];
	}

    function count_wishlist() {  // get total number of items in wishlist 
      $total_items = 0;
      if (is_array($this->wishID)) {
        reset($this->wishID);
        while (list($wishlist_id, ) = each($this->wishID)) {
          $total_items++;
        }
      }

      return $total_items;
    }

  }

?>