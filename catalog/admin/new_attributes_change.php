<?

/*
  $Id: new_attributes_change.php 
  
 New Attribute Manager v4b, Author: Mike G.
  
  Updates for New Attribute Manager v.5.0 and multilanguage support by: Kiril Nedelchev - kikoleppard
  kikoleppard@hotmail.bg
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

if (isset($HTTP_POST_VARS['copy_product_x'])) {

  if ($dest_product_id != $current_product_id) {
    $attributes = get_attributes($current_product_id);
    delete_attributes($dest_product_id);
    insert_attributes($dest_product_id, $attributes);
  }

} elseif (isset($HTTP_POST_VARS['copy_category_x'])) {

  $attributes = get_attributes($current_product_id);
  $products_query = tep_db_query("select distinct p2c.products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p2c.categories_id = '" . (int)$dest_category_id . "'");
  while($products = tep_db_fetch_array($products_query)) {
    $product_id = $products['products_id'];
    if ($product_id != $current_product_id) {
      delete_attributes($product_id);
      insert_attributes($product_id, $attributes);
    }
  }

} elseif ($action == 'change') {

  // I found the easiest way to do this is just delete the current attributes & start over =)
  delete_attributes($current_product_id);

  // Simple, yet effective.. loop through the selected Option Values.. find the proper price & prefix.. insert.. yadda yadda yadda.
  for ($i = 0; $i < sizeof($optionValues); $i++) {
    $query = "SELECT * FROM products_options_values_to_products_options where products_options_values_id = '$optionValues[$i]'";
    $result = mysql_query($query) or die(mysql_error());
    $matches = mysql_num_rows($result);
       while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
         $optionsID = $line['products_options_id'];
       }

    $value_price =  $HTTP_POST_VARS[$optionValues[$i] . '_price'];
    $value_prefix = $HTTP_POST_VARS[$optionValues[$i] . '_prefix'];
    
    if ( $optionTypeInstalled == "1" ) {
                                       	
      $value_type = $HTTP_POST_VARS[$optionValues[$i] . '_type'];
      $value_qty = $HTTP_POST_VARS[$optionValues[$i] . '_qty'];
      $value_order = $HTTP_POST_VARS[$optionValues[$i] . '_order'];
      $value_linked = $HTTP_POST_VARS[$optionValues[$i] . '_linked'];
        
      MYSQL_QUERY( "INSERT INTO products_attributes ( products_id, options_id, options_values_id, options_values_price, price_prefix, options_type_id, options_values_qty, attribute_order, collegamento )
                   VALUES( '$current_product_id', '$optionsID', '$optionValues[$i]', '$value_price', '$value_prefix', '$value_type', '$value_qty', '$value_order', '$value_linked' )" ) or die(mysql_error());

// Linda McGrath's contribution or Forrest Miller's Product Attrib Sort 
                     
    } else if ( $optionSortCopyInstalled == "1" ) {
      $value_sort = $HTTP_POST_VARS[$optionValues[$i] . '_sort'];
      $value_sort = is_numeric($value_sort)?"'".(int)$value_sort."'":'null';
// modified to remove weight
//    $value_weight = $HTTP_POST_VARS[$optionValues[$i] . '_weight'];
//    $value_weight_prefix = $HTTP_POST_VARS[$optionValues[$i] . '_weight_prefix'];

//    MYSQL_QUERY( "INSERT INTO products_attributes ( products_id, options_id, options_values_id, options_values_price, price_prefix, products_options_sort_order, products_attributes_weight, products_attributes_weight_prefix )
//                   VALUES( '$current_product_id', '$optionsID', '$optionValues[$i]', '$value_price', '$value_prefix', '$value_sort', '$value_weight', '$value_weight_prefix' )" ) or die(mysql_error());
      MYSQL_QUERY( "INSERT INTO products_attributes ( products_id, options_id, options_values_id, options_values_price, price_prefix, products_attributes_sort_order )
                     VALUES( '$current_product_id', '$optionsID', '$optionValues[$i]', '$value_price', '$value_prefix', $value_sort )" ) or die(mysql_error());
    } else {
      MYSQL_QUERY( "INSERT INTO products_attributes ( products_id, options_id, options_values_id, options_values_price, price_prefix )
                   VALUES( '$current_product_id', '$optionsID', '$optionValues[$i]', '$value_price', '$value_prefix' )" ) or die(mysql_error());
    }             
  }

// For text input option type feature by chandra
  if ( $optionTypeTextInstalled == "1" && is_array( $HTTP_POST_VARS['optionValuesText'] )) {
   
    for ($i = 0; $i < sizeof($optionValuesText); $i++) {
                                                      	
      $value_price =  $HTTP_POST_VARS[$optionValuesText[$i] . '_price'];
      $value_prefix = $HTTP_POST_VARS[$optionValuesText[$i] . '_prefix'];
      $value_product_id = $HTTP_POST_VARS[$optionValuesText[$i] . '_options_id'];
        
      MYSQL_QUERY( "INSERT INTO products_attributes ( products_id, options_id, options_values_id, options_values_price, price_prefix )
      VALUES( '$current_product_id', '$value_product_id', '0', '$value_price', '$value_prefix' )" ) or die(mysql_error());
    }
   
  }
}

function delete_attributes($product_id) {
  // remove any associated download attributes
  $pad_query = tep_db_query("SELECT pad.products_attributes_id FROM products_attributes pa, products_attributes_download pad WHERE pa.products_attributes_id = pad.products_attributes_id and pa.products_id = '". (int)$product_id . "'");
  while($pad = tep_db_fetch_array($pad_query)) {
    MYSQL_QUERY( "DELETE FROM products_attributes_download WHERE products_attributes_id = '" . $pad['products_attributes_id'] . "'" );
  }
  // remove the product's attributes
  MYSQL_QUERY( "DELETE FROM products_attributes WHERE products_id = '" . (int)$product_id . "'" );
}

function get_attributes($product_id) {
  $attributes = array();
  $attributes_query = tep_db_query("select * from " . TABLE_PRODUCTS_ATTRIBUTES . " pa left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad on pa.products_attributes_id = pad.products_attributes_id where pa.products_id = '" . (int)$product_id . "'");
  while($attribute = tep_db_fetch_array($attributes_query)) $attributes[] = $attribute;
  return $attributes;
}

function insert_attributes($product_id, $attributes) {
  foreach($attributes as $attribute) {
    $att_sort_order = is_numeric($attribute['products_attributes_sort_order'])?"'".(int)$attribute['products_attributes_sort_order']."'":'null';
    tep_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES . " values ('', '" . (int)$product_id . "', '" . (int)$attribute['options_id'] . "', '" . (int)$attribute['options_values_id'] . "', '" . tep_db_input($attribute['options_values_price']) . "', '" . tep_db_input($attribute['price_prefix']) . "', $att_sort_order)");
    $dup_products_attributes_id = tep_db_insert_id();
    if (tep_not_null($attribute['products_attributes_filename'])) {
      tep_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " values (" . (int)$dup_products_attributes_id . ", '" . tep_db_input($attribute['products_attributes_filename']) . "', '" . tep_db_input($attribute['products_attributes_maxdays']) . "', '" . tep_db_input($attribute['products_attributes_maxcount']) . "')");
    }
  }
}
?>
