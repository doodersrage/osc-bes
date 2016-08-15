<?php

// Current EP Version
$curver = '3.00-MS2';

/*
  $Id: easypopulate.php,v 3.00 (Based on 2.78, 2.80 and 2.81b)
*/

//
//*******************************
//*******************************
// C O N F I G U R A T I O N
// V A R I A B L E S
//*******************************
//*******************************


// **** Temp directory ****
// if you changed your directory structure from stock and do not have /catalog/temp/, then you'll need to change this accordingly.
//
$tempdir = "admin/temp/";
$tempdir2 = "admin/temp/";

//**** File Splitting Configuration ****
// we attempt to set the timeout limit longer for this script to avoid having to split the files
// NOTE:  If your server is running in safe mode, this setting cannot override the timeout set in php.ini
// uncomment this if you are not on a safe mode server and you are getting timeouts
// set_time_limit(330);

// if you are splitting files, this will set the maximum number of records to put in each file.
// if you set your php.ini to a long time, you can make this number bigger
global $maxrecs;
$maxrecs = 300; // default, seems to work for most people.  Reduce if you hit timeouts
//$maxrecs = 4; // for testing

//**** Image Defaulting ****
global $default_images, $default_image_manufacturer, $default_image_product, $default_image_category;

// set them to your own default "We don't have any picture" gif
//$default_image_manufacturer = 'no_image_manufacturer.gif';
//$default_image_product = 'no_image_product.gif';
//$default_image_category = 'no_image_category.gif';

// or let them get set to nothing
$default_image_manufacturer = '';
$default_image_product = '';
$default_image_category = '';

//**** Status Field Setting ****
// Set the v_status field to "Inactive" if you want the status=0 in the system
// Set the v_status field to "Delete" if you want to remove the item from the system <- THIS IS NOT WORKING YET!
// If zero_qty_inactive is true, then items with zero qty will automatically be inactive in the store.
global $active, $inactive, $zero_qty_inactive, $deleteit;
$active = 'Active';
$inactive = 'Inactive';
//$deleteit = 'Delete'; // not functional yet
$zero_qty_inactive = false;

//**** Size of products_model in products table ****
// set this to the size of your model number field in the db.  We check to make sure all models are no longer than this value.
// this prevents the database from getting fubared.  Just making this number bigger won't help your database!  They must match!
global $modelsize;
$modelsize = 64;

//**** Price includes tax? ****
// Set the v_price_with_tax to
// 0 if you want the price without the tax included
// 1 if you want the price to be defined for import & export including tax.
global $price_with_tax;
$price_with_tax = false;

// **** Quote -> Escape character conversion ****
// If you have extensive html in your descriptions and it's getting mangled on upload, turn this off
// set to 1 = replace quotes with escape characters
// set to 0 = no quote replacement
global $replace_quotes;
$replace_quotes = false;

// **** Field Separator ****
// change this if you can't use the default of tabs
// Tab is the default, comma and semicolon are commonly supported by various progs
// Remember, if your descriptions contain this character, you will confuse EP!
global $separator;
$separator = "\t"; // tab is default
//$separator = ","; // comma
//$separator = ";"; // semi-colon
//$separator = "~"; // tilde
//$separator = "-"; // dash
//$separator = "*"; // splat

// **** Max Category Levels ****
// change this if you need more or fewer categories
global $max_categories;
$max_categories = 3; // 7 is default

// VJ product attributes begin
// **** Product Attributes ****
// change this to false, if do not want to download product attributes
global $products_with_attributes;
$products_with_attributes = true; 

// change this to true, if you use QTYpro and want to set attributes stock with EP.
global $products_attributes_stock;
$products_attributes_stock = false;

// change this if you want to download selected product options
// this might be handy, if you have a lot of product options, and your output file exceeds 256 columns (which is the max. limit MS Excel is able to handle)
global $attribute_options_select;
// $attribute_options_select = array('Size', 'Model'); // uncomment and fill with product options name you wish to download // comment this line, if you wish to download all product options
// VJ product attributes end




// ****************************************
// Froogle configuration variables
// -- YOU MUST CONFIGURE THIS!  IT WON'T WORK OUT OF THE BOX!
// ****************************************

// **** Froogle product info page path ****
// We can't use the tep functions to create the link, because the links will point to the admin, since that's where we're at.
// So put the entire path to your product_info.php page here
global $froogle_product_info_path;
$froogle_product_info_path = HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'product_info.php';

// **** Froogle product image path ****
// Set this to the path to your images directory
global $froogle_image_path;
$froogle_image_path = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES;

// **** Froogle - search engine friendly setting
// if your store has SEARCH ENGINE FRIENDLY URLS set, then turn this to true
// I did it this way because I'm having trouble with the code seeing the constants
// that are defined in other places.
global $froogle_SEF_urls;
$froogle_SEF_urls = false;


// ****************************************
// End Froogle configuration variables
// ****************************************


//*******************************
//*******************************
// E N D
// C O N F I G U R A T I O N
// V A R I A B L E S
//*******************************
//*******************************


//*******************************
//*******************************
// S T A R T
// INITIALIZATION
//*******************************
//*******************************


require('includes/application_top.php');
require('includes/database_tables.php');

// >>> BEGIN REGISTER_GLOBALS
link_get_variable('download');
link_get_variable('dltype');
link_get_variable('split');

link_post_variable('MAX_FILE_SIZE');
link_post_variable('buttoninsert');
link_post_variable('buttonsplit');
link_post_variable('localfile');

// WARNING: I'm not sure about this line - maybe this is why 'Upload EP File' doesn't work (fixed by splautz)
link_files_variable('usrfl');
// <<< END REGISTER_GLOABLS

//*******************************
// If you are running a pre-Nov1-2002 snapshot of OSC, then we need this include line to avoid
// errors like:
//   undefined function tep_get_uploaded_file
 if (!function_exists(tep_get_uploaded_file)){
        include (DIR_WS_FUNCTIONS . 'easypopulate_functions.php');
 }
//*******************************

// VJ product attributes begin
global $attribute_options_array;
$attribute_options_array = array();

if ($products_with_attributes == true) {
        if (is_array($attribute_options_select) && (count($attribute_options_select) > 0)) {
                foreach ($attribute_options_select as $value) {
                        $attribute_options_query = "select distinct products_options_id from " . TABLE_PRODUCTS_OPTIONS . " where products_options_name = '" . $value . "'";

                        $attribute_options_values = tep_db_query($attribute_options_query);

                        if ($attribute_options = tep_db_fetch_array($attribute_options_values)){
                                $attribute_options_array[] = array('products_options_id' => $attribute_options['products_options_id']);
                        }
                }
        } else {
                $attribute_options_query = "select distinct products_options_id from " . TABLE_PRODUCTS_OPTIONS . " order by products_options_id";

                $attribute_options_values = tep_db_query($attribute_options_query);

                while ($attribute_options = tep_db_fetch_array($attribute_options_values)){
                        $attribute_options_array[] = array('products_options_id' => $attribute_options['products_options_id']);
                }
        }
}
// VJ product attributes end

global $filelayout, $filelayout_count, $filelayout_sql, $langcode, $fileheaders;

// these are the fields that will be defaulted to the current values in the database if they are not found in the incoming file
global $default_these;
$default_these = array(
        'v_products_image',
        #'v_products_mimage',
        #'v_products_bimage',
        #'v_products_subimage1',
        #'v_products_bsubimage1',
        #'v_products_subimage2',
        #'v_products_bsubimage2',
        #'v_products_subimage3',
        #'v_products_bsubimage3',
        'v_categories_id',
        'v_products_price',
        'v_products_quantity',
        'v_products_free_shipping',
        'v_products_weight',
        'v_date_added',
        'v_date_avail',
        'v_status',
        'v_tax_class_id', 
        'v_tax_class_title',
        'v_manufacturers_name',
        'v_manufacturers_id',
        #'v_products_dim_type',
        #'v_products_length',
        #'v_products_width',
        #'v_products_height',
        #'v_products_hide_from_groups',
        'v_products_name',
        'v_products_info',
        'v_products_description',
        'v_products_url',
        'v_products_head_title_tag',
        'v_products_head_desc_tag',
        'v_products_head_keywords_tag',
        'v_products_h1',
        'v_surls_name',
        'v_products_img_alt',
        'v_products_affiliate_url'
        );

//elari check default language_id from configuration table DEFAULT_LANGUAGE
$epdlanguage_query = tep_db_query("select languages_id, name from " . TABLE_LANGUAGES . " where code = '" . DEFAULT_LANGUAGE . "'");
if (tep_db_num_rows($epdlanguage_query)) {
        $epdlanguage = tep_db_fetch_array($epdlanguage_query);
        $epdlanguage_id   = $epdlanguage['languages_id'];
        $epdlanguage_name = $epdlanguage['name'];
} else {
        Echo 'Strange but there is no default language to work... That may not happen, just in case... ';
}

$langcode = ep_get_languages();

if ( $dltype != '' ){
        // if dltype is set, then create the filelayout.  Otherwise it gets read from the uploaded file
        ep_create_filelayout($dltype); // get the right filelayout for this download
}

//*******************************
//*******************************
// E N D
// INITIALIZATION
//*******************************
//*******************************


if ( $download == 'stream' or  $download == 'tempfile' ){
        //*******************************
        //*******************************
        // DOWNLOAD FILE
        //*******************************
        //*******************************
        $filestring = ""; // this holds the csv file we want to download
        $result = tep_db_query($filelayout_sql);
        $row =  tep_db_fetch_array($result);

// EP Optimization - SETUP ATTRIBUTES ARRAY - START 

  $attribute_values_sql = "select  products_id, options_id, options_values_id, options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " ORDER BY products_attributes_id";
  $attributes_query= tep_db_query($attribute_values_sql);

  $attributes_array=array();
  while($row_attributes= tep_db_fetch_array($attributes_query)){
  $attributes_array[$row_attributes["products_id"]."_".$row_attributes["options_id"]."_".$row_attributes["options_values_id"]]=array("products_id"=>$row_attributes["products_id"],
																						"options_id"=>$row_attributes["options_id"],
																						"options_values_id"=>$row_attributes["options_values_id"],
																						"options_values_price"=>$row_attributes["options_values_price"],
																						"price_prefix"=>$row_attributes["price_prefix"]
																						);
  }

  $attributes_values_sql= "select products_options_values_id, products_options_values_name, language_id from " . TABLE_PRODUCTS_OPTIONS_VALUES;
  $attributes_values_query = tep_db_query($attributes_values_sql);
  $attributes_values_array=array();
  while($row_attributes_values= tep_db_fetch_array($attributes_values_query)){
  $attributes_values_array[$row_attributes_values["products_options_values_id"]."_".$row_attributes_values["language_id"]]=array("products_options_values_id"=>$row_attributes_values["products_options_values_id"],
																						"products_options_values_name"=>$row_attributes_values["products_options_values_name"]
																						);
  }

  $attribute_options_lang_sql= "select products_options_id, products_options_name, language_id from " . TABLE_PRODUCTS_OPTIONS;
  $attribute_options_lang_query = tep_db_query($attribute_options_lang_sql);
  $attribute_options_lang_array=array();
  while($row_attributes_lang= tep_db_fetch_array($attribute_options_lang_query)){
  $attribute_options_lang_array[$row_attributes_lang["products_options_id"]."_".$row_attributes_lang["language_id"]]=array("products_options_id"=>$row_attributes_lang["products_options_id"],
																						"products_options_name"=>$row_attributes_lang["products_options_name"]
																						);
  }


// EP Optimization - SETUP ATTRIBUTES ARRAY - END

        // Here we need to allow for the mapping of internal field names to external field names
        // default to all headers named like the internal ones
        // the field mapping array only needs to cover those fields that need to have their name changed
        if ( count($fileheaders) != 0 ){
                $filelayout_header = $fileheaders; // if they gave us fileheaders for the dl, then use them
        } else {
                $filelayout_header = $filelayout; // if no mapping was spec'd use the internal field names for header names
        }
        //We prepare the table heading with layout values
        foreach( $filelayout_header as $key => $value ){
                $filestring .= $key . $separator;
        }
        // now lop off the trailing tab
        $filestring = substr($filestring, 0, strlen($filestring)-1);

        // set the type
        if ( $dltype == 'froogle' ){
                $endofrow = "\n";
        } else {
                // default to normal end of row
                $endofrow = $separator . 'EOREOR' . "\n";
        }
        $filestring .= $endofrow;

        $num_of_langs = count($langcode);
        while ($row){


                // if the filelayout says we need a products_name, get it
                // build the long full froogle image path
                $row['v_products_fullpath_image'] = $froogle_image_path . $row['v_products_image'];
                // Other froogle defaults go here for now
                $row['v_froogle_instock']                 = 'Y';
                $row['v_froogle_shipping']                 = '';
                $row['v_froogle_upc']                         = '';
                $row['v_froogle_color']                        = '';
                $row['v_froogle_size']                        = '';
                $row['v_froogle_quantitylevel']                = '';
                $row['v_froogle_manufacturer_id']        = '';
                $row['v_froogle_exp_date']                = '';
                $row['v_froogle_product_type']                = 'OTHER';
                $row['v_froogle_delete']                = '';
                $row['v_froogle_currency']                = 'USD';
                $row['v_froogle_offer_id']                = $row['v_products_model'];
                $row['v_froogle_product_id']                = $row['v_products_model'];

                // names and descriptions require that we loop thru all languages that are turned on in the store
                foreach ($langcode as $key => $lang){
                        $lid = $lang['id'];

                        // for each language, get the description and set the vals
                        $sql2 = "SELECT *
                                FROM ".TABLE_PRODUCTS_DESCRIPTION." pd left join ".TABLE_SEO_URLS." on products_surls_id = surls_id
                                WHERE
                                        pd.products_id = " . $row['v_products_id'] . " AND
                                        pd.language_id = '" . $lid . "'
                                ";
                        $result2 = tep_db_query($sql2);
                        $row2 =  tep_db_fetch_array($result2);

                        // I'm only doing this for the first language, since right now froogle is US only.. Fix later!
                        // adding url for froogle, but it should be available no matter what
                        if ($froogle_SEF_urls){
                                // if only one language
                                if ($num_of_langs == 1){
                                        $row['v_froogle_products_url_' . $lid] = $froogle_product_info_path . '/products_id/' . $row['v_products_id'];
                                } else {
                                        $row['v_froogle_products_url_' . $lid] = $froogle_product_info_path . '/products_id/' . $row['v_products_id'] . '/language/' . $lid;
                                }
                        } else {
                                if ($num_of_langs == 1){
                                        $row['v_froogle_products_url_' . $lid] = $froogle_product_info_path . '?products_id=' . $row['v_products_id'];
                                } else {
                                        $row['v_froogle_products_url_' . $lid] = $froogle_product_info_path . '?products_id=' . $row['v_products_id'] . '&language=' . $lid;
                                }
                        }

                        $row['v_products_name_' . $lid]         = $row2['products_name'];
                        $row['v_products_info_' . $lid]         = $row2['products_info'];
                        $row['v_products_description_' . $lid]         = $row2['products_description'];
                        $row['v_products_url_' . $lid]                 = $row2['products_url'];

                        // froogle advanced format needs the quotes around the name and desc
                        $row['v_froogle_products_name_' . $lid] = '"' . strip_tags(str_replace('"','""',$row2['products_name'])) . '"';
                        $row['v_froogle_products_description_' . $lid] = '"' . strip_tags(str_replace('"','""',$row2['products_description'])) . '"';

                        /* // support for Linda's Header Controller 2.0 here
                        if(isset($filelayout['v_products_head_title_tag_' . $lid])){ */
                                $row['v_products_head_title_tag_' . $lid]         = $row2['products_head_title_tag'];
                                $row['v_products_head_desc_tag_' . $lid]         = $row2['products_head_desc_tag'];
                                $row['v_products_head_keywords_tag_' . $lid]         = $row2['products_head_keywords_tag'];
                        /* }
                        // end support for Header Controller 2.0 */
                        $row['v_products_h1_' . $lid]         = $row2['products_h1'];
                        $row['v_surls_name_' . $lid]         = $row2['surls_name'];
                        $row['v_products_img_alt_' . $lid]         = $row2['products_img_alt'];
                        $row['v_products_affiliate_url_' . $lid]                 = $row2['products_affiliate_url'];
                }

                // for the categories, we need to keep looping until we find the root category

                // start with v_categories_id
                // Get the category description
                // set the appropriate variable name
                // if parent_id is not null, then follow it up.
                // we'll populate an aray first, then decide where it goes in the
                $thecategory_id = $row['v_categories_id'];
                $fullcategory = ''; // this will have the entire category stack for froogle
                for( $categorylevel=1; $categorylevel<$max_categories+1; $categorylevel++){
                        if ($thecategory_id){
                                $sql2 = "SELECT categories_name
                                        FROM ".TABLE_CATEGORIES_DESCRIPTION."
                                        WHERE        
                                                categories_id = " . $thecategory_id . " AND
                                                language_id = " . $epdlanguage_id ;

                                $result2 = tep_db_query($sql2);
                                $row2 =  tep_db_fetch_array($result2);
                                // only set it if we found something
                                $temprow['v_categories_name_' . $categorylevel] = $row2['categories_name'];
                                // now get the parent ID if there was one
                                $sql3 = "SELECT parent_id
                                        FROM ".TABLE_CATEGORIES."
                                        WHERE
                                                categories_id = " . $thecategory_id;
                                $result3 = tep_db_query($sql3);
                                $row3 =  tep_db_fetch_array($result3);
                                $theparent_id = $row3['parent_id'];
                                if ($theparent_id != ''){
                                        // there was a parent ID, lets set thecategoryid to get the next level
                                        $thecategory_id = $theparent_id;
                                } else {
                                        // we have found the top level category for this item,
                                        $thecategory_id = false;
                                }
                                //$fullcategory .= " > " . $row2['categories_name'];
                                $fullcategory = $row2['categories_name'] . " > " . $fullcategory;
                        } else {
                                $temprow['v_categories_name_' . $categorylevel] = '';
                        }
                }
                // now trim off the last ">" from the category stack
                $row['v_category_fullpath'] = substr($fullcategory,0,strlen($fullcategory)-3);

                // temprow has the old style low to high level categories.
                $newlevel = 1;
// Modified by Andy - $categorylevel=6 to categorylevel=ep_max_cat
                // let's turn them into high to low level categories
                for( $categorylevel=$max_categories; $categorylevel>0; $categorylevel--){
                        if ($temprow['v_categories_name_' . $categorylevel] != ''){
                                $row['v_categories_name_' . $newlevel++] = $temprow['v_categories_name_' . $categorylevel];
                        }
                }
                // if the filelayout says we need a manufacturers name, get it
                if (isset($filelayout['v_manufacturers_name'])){
                        if ($row['v_manufacturers_id'] != ''){
                                $sql2 = "SELECT manufacturers_name
                                        FROM ".TABLE_MANUFACTURERS."
                                        WHERE
                                        manufacturers_id = " . $row['v_manufacturers_id']
                                        ;
                                $result2 = tep_db_query($sql2);
                                $row2 =  tep_db_fetch_array($result2);
                                $row['v_manufacturers_name'] = $row2['manufacturers_name'];
                        }
                }


                // If you have other modules that need to be available, put them here

                // VJ product attribs begin
                if (isset($filelayout['v_attribute_options_id_1'])){
                        $languages = tep_get_languages();

                        $attribute_options_count = 1;
      foreach ($attribute_options_array as $attribute_options) {
                                $row['v_attribute_options_id_' . $attribute_options_count]         = $attribute_options['products_options_id'];

                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                        $lid = $languages[$i]['id'];

// EP Optimization - SETUP ATTRIBUTES ARRAY - START
                                        // $attribute_options_languages_query = "select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$attribute_options['products_options_id'] . "' and language_id = '" . (int)$lid . "'";

                                        // $attribute_options_languages_values = tep_db_query($attribute_options_languages_query);

                                        // $attribute_options_languages = tep_db_fetch_array($attribute_options_languages_values);

                                           $attribute_options_languages=$attribute_options_lang_array[(int)$attribute_options['products_options_id']."_".(int)$lid];
// EP Optimization - SETUP ATTRIBUTES ARRAY - END

                                        $row['v_attribute_options_name_' . $attribute_options_count . '_' . $lid] = $attribute_options_languages['products_options_name'];
                                }

                                $attribute_values_query = "select products_options_values_id from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$attribute_options['products_options_id'] . "' order by products_options_values_id";

                                $attribute_values_values = tep_db_query($attribute_values_query);

                                $attribute_values_count = 1;
                                while ($attribute_values = tep_db_fetch_array($attribute_values_values)) {
                                        $row['v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count]         = $attribute_values['products_options_values_id'];

// EP Optimization - SETUP ATTRIBUTES ARRAY - START
                                        // $attribute_values_price_query = "select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$row['v_products_id'] . "' and options_id = '" . (int)$attribute_options['products_options_id'] . "' and options_values_id = '" . (int)$attribute_values['products_options_values_id'] . "'";

                                        // $attribute_values_price_values = tep_db_query($attribute_values_price_query);

                                        // $attribute_values_price = tep_db_fetch_array($attribute_values_price_values);

					                       $attribute_values_price=$attributes_array[(int)$row['v_products_id']."_".(int)$attribute_options['products_options_id']."_".(int)$attribute_values['products_options_values_id']];
// EP Optimization - SETUP ATTRIBUTES ARRAY - END

                                        $row['v_attribute_values_price_' . $attribute_options_count . '_' . $attribute_values_count]         = $attribute_values_price['price_prefix'] . $attribute_values_price['options_values_price'];

        //// attributes stock add start        
        if ( $products_attributes_stock        == true ) {   
                   $stock_attributes = $attribute_options['products_options_id'].'-'.$attribute_values['products_options_values_id'];
                   
                   $stock_quantity_query = tep_db_query("select products_stock_quantity from " . TABLE_PRODUCTS_STOCK . " where products_id = '" . (int)$row['v_products_id'] . "' and products_stock_attributes = '" . $stock_attributes . "'");
           $stock_quantity = tep_db_fetch_array($stock_quantity_query);
                   
                   $row['v_attribute_values_stock_' . $attribute_options_count . '_' . $attribute_values_count] = $stock_quantity['products_stock_quantity'];
         }
        //// attributes stock add end  
                                                                               
                                        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                                $lid = $languages[$i]['id'];

// EP Optimization - SETUP ATTRIBUTES ARRAY - START
                                                // $attribute_values_languages_query = "select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$attribute_values['products_options_values_id'] . "' and language_id = '" . (int)$lid . "'";

                                                // $attribute_values_languages_values = tep_db_query($attribute_values_languages_query);

                                                // $attribute_values_languages = tep_db_fetch_array($attribute_values_languages_values);

						                           $attribute_values_languages=$attributes_values_array[(int)$attribute_values['products_options_values_id']."_".(int)$lid];
// EP Optimization - SETUP ATTRIBUTES ARRAY - END

                                                $row['v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $lid] = $attribute_values_languages['products_options_values_name'];
                                        }

                                        $attribute_values_count++;
                                }

                                $attribute_options_count++;
                        }
                }
                // VJ product attribs end

                // this is for the separate price per customer module
                if (isset($filelayout['v_customer_price_1'])){
                        $sql2 = "SELECT
                                        customers_group_price,
                                        customers_group_id
                                FROM
                                        ".TABLE_PRODUCTS_GROUPS."
                                WHERE
                                products_id = " . $row['v_products_id'] . "
                                ORDER BY
                                customers_group_id"
                                ;
                        $result2 = tep_db_query($sql2);
                        $ll = 1;
                        $row2 =  tep_db_fetch_array($result2);
                        while( $row2 ){
                                $row['v_customer_group_id_' . $ll]         = $row2['customers_group_id'];
                                $row['v_customer_price_' . $ll]         = $row2['customers_group_price'];
                                $row2 = tep_db_fetch_array($result2);
                                $ll++;
                        }
                }
                if ($dltype == 'froogle'){
                        // For froogle, we check the specials prices for any applicable specials, and use that price
                        // by grabbing the specials id descending, we always get the most recently added special price
                        // I'm checking status because I think you can turn off specials
                        $sql2 = "SELECT
                                        specials_new_products_price
                                FROM
                                        ".TABLE_SPECIALS."
                                WHERE
                                products_id = " . $row['v_products_id'] . " and
                                status = 1 and
                                expires_date < CURRENT_TIMESTAMP
                                ORDER BY
                                        specials_id DESC"
                                ;
                        $result2 = tep_db_query($sql2);
                        $ll = 1;
                        $row2 =  tep_db_fetch_array($result2);
                        if( $row2 ){
                                // reset the products price to our special price if there is one for this product
                                $row['v_products_price']         = $row2['specials_new_products_price'];
                        }
                }

                //elari -
                //We check the value of tax class and title instead of the id
                //Then we add the tax to price if $price_with_tax is set to 1
                $row_tax_multiplier                 = tep_get_tax_class_rate($row['v_tax_class_id']);
                $row['v_tax_class_title'] 	= tep_get_tax_class_title($row['v_tax_class_id']);
                $row['v_products_price']         = round($row['v_products_price'] +
                                ($price_with_tax * $row['v_products_price'] * $row_tax_multiplier / 100),2);
                if (!empty($row['v_specials_new_products_price']))
                  $row['v_specials_new_products_price']         = round($row['v_specials_new_products_price'] +
                                ($price_with_tax * $row['v_specials_new_products_price'] * $row_tax_multiplier / 100),2);


                // Now set the status to a word the user specd in the config vars
                if ( $row['v_status'] == '1' ){
                        $row['v_status'] = $active;
                } else {
                        $row['v_status'] = $inactive;
                }

                // remove any bad things in the texts that could confuse EasyPopulate
                $therow = '';
                foreach( $filelayout as $key => $value ){
                        //echo "The field was $key<br>";

                        $thetext = $row[$key];
                        // kill the carriage returns and tabs in the descriptions, they're killing me!
                        $thetext = str_replace("\r",' ',$thetext);
                        $thetext = str_replace("\n",' ',$thetext);
                        $thetext = str_replace("\t",' ',$thetext);
                        // and put the text into the output separated by tabs
                        $therow .= $thetext . $separator;
                }

                // lop off the trailing tab, then append the end of row indicator
                $therow = substr($therow,0,strlen($therow)-1) . $endofrow;

                $filestring .= $therow;
                // grab the next row from the db
                $row =  tep_db_fetch_array($result);
        }

        #$EXPORT_TIME=time();
        $EXPORT_TIME = strftime('%Y%b%d-%H%I');
        if ($dltype=="froogle"){
                $EXPORT_TIME = "FroogleEP" . $EXPORT_TIME;
        } else {
                $EXPORT_TIME = "EP" . $EXPORT_TIME;
        }

        // now either stream it to them or put it in the temp directory
        if ($download == 'stream'){
                //*******************************
                // STREAM FILE
                //*******************************
                header("Content-type: application/vnd.ms-excel");
                header("Content-disposition: attachment; filename=$EXPORT_TIME.txt");
// Changed if using SSL, helps prevent program delay/timeout (add to backup.php also)
        //        header("Pragma: no-cache");
if ($request_type== 'NONSSL'){
header("Pragma: no-cache");
 } else {
header("Pragma: ");
}
                header("Expires: 0");
                echo $filestring;
                die();
        } else {
                //*******************************
                // PUT FILE IN TEMP DIR
                //*******************************
                $tmpfname = DIR_FS_DOCUMENT_ROOT . $tempdir . "$EXPORT_TIME.txt";
                //unlink($tmpfname);
                $fp = fopen( $tmpfname, "w+");
                fwrite($fp, $filestring);
                fclose($fp);
                echo "You can get your file in the Tools/Files under " . $tempdir . "EP" . $EXPORT_TIME . ".txt";
                die();
        }
}
//*******************************
// E N D
// DOWNLOAD FILE
//*******************************
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
<tr>
<td width="<?php echo BOX_WIDTH; ?>" valign="top" height="27">
<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<?php require(DIR_WS_INCLUDES . 'column_left.php');?>
</table></td>
<td class="pageHeading" valign="top"><?php
echo "Easy Populate $curver - Default Language : " . $epdlanguage_name . '(' . $epdlanguage_id .')' ;
?>

<p class="smallText">

<?php
        //*******************************
        //*******************************
        // UPLOAD AND INSERT FILE
        //*******************************
        //*******************************

if ($localfile or (is_uploaded_file($usrfl) && $split==0)) {
        if ($usrfl){
                // move the file to where we can work with it
                $file = tep_get_uploaded_file('usrfl');
                if (is_uploaded_file($file['tmp_name'])) {
                        tep_copy_uploaded_file($file, DIR_FS_DOCUMENT_ROOT . $tempdir);
                }

                echo "<p class=smallText>";
                echo "File uploaded. <br>";
                echo "Temporary filename: " . $usrfl . "<br>";
                echo "User filename: " . $usrfl_name . "<br>";
                echo "Size: " . $usrfl_size . "<br>";

                // get the entire file into an array
                $readed = file(DIR_FS_DOCUMENT_ROOT . $tempdir . $usrfl_name);
        }
        if ($localfile){
                // move the file to where we can work with it
                $file = tep_get_uploaded_file('usrfl');                        $attribute_options_query = "select distinct products_options_id from " . TABLE_PRODUCTS_OPTIONS . " order by products_options_id";

                        $attribute_options_values = tep_db_query($attribute_options_query);

                        $attribute_options_count = 1;
                        //while ($attribute_options = tep_db_fetch_array($attribute_options_values)){
                if (is_uploaded_file($file['tmp_name'])) {
                        tep_copy_uploaded_file($file, DIR_FS_DOCUMENT_ROOT . $tempdir);
                }

                echo "<p class=smallText>";
                echo "Filename: " . $localfile . "<br>";

                // get the entire file into an array
                $readed = file(DIR_FS_DOCUMENT_ROOT . $tempdir . $localfile);
        }

        // now we string the entire thing together in case there were carriage returns in the data
        $newreaded = "";
        foreach ($readed as $read){
                $newreaded .= $read;
        }

        // now newreaded has the entire file together without the carriage returns.
        // if for some reason excel put qoutes around our EOREOR, remove them then split into rows
        $newreaded = str_replace('"EOREOR"', 'EOREOR', $newreaded);
        $readed = explode( $separator . 'EOREOR',$newreaded);


        // Now we'll populate the filelayout based on the header row.
        $theheaders_array = explode( $separator, $readed[0] ); // explode the first row, it will be our filelayout
        $lll = 0;
        $filelayout = array();
        foreach( $theheaders_array as $header ){
                $cleanheader = str_replace( '"', '', $header);
        //        echo "Fileheader was $header<br><br><br>";
                $filelayout[ $cleanheader ] = $lll++; //
        }
        unset($readed[0]); //  we don't want to process the headers with the data

        // now we've got the array broken into parts by the expicit end-of-row marker.
        array_walk($readed, 'walk');

}


        //*******************************
        //*******************************
        // UPLOAD AND SPLIT FILE
        //*******************************
        //*******************************

      if (is_uploaded_file($usrfl) && $split==1) {
        // move the file to where we can work with it
        $file = tep_get_uploaded_file('usrfl');
        //echo "Trying to move file...";
        if (is_uploaded_file($file['tmp_name'])) {
                tep_copy_uploaded_file($file, DIR_FS_DOCUMENT_ROOT . $tempdir);
        }

        $infp = fopen(DIR_FS_DOCUMENT_ROOT . $tempdir . $usrfl_name, "r");

        //toprow has the field headers
        $toprow = fgets($infp,32768);

        $filecount = 1;

        echo "Creating file EP_Split" . $filecount . ".txt ...  ";
        $tmpfname = DIR_FS_DOCUMENT_ROOT . $tempdir . "EP_Split" . $filecount . ".txt";
        $fp = fopen( $tmpfname, "w+");
        fwrite($fp, $toprow);

        $linecount = 0;
        $line = fgets($infp,32768);
        while ($line){
                // walking the entire file one row at a time
                // but a line is not necessarily a complete row, we need to split on rows that have "EOREOR" at the end
                $line = str_replace('"EOREOR"', 'EOREOR', $line);
                fwrite($fp, $line);
                if (strpos($line, 'EOREOR')){
                        // we found the end of a line of data, store it
                        $linecount++; // increment our line counter
                        if ($linecount >= $maxrecs){
                                echo "Added $linecount records and closing file... <Br>";
                                $linecount = 0; // reset our line counter
                                // close the existing file and open another;
                                fclose($fp);
                                // increment filecount
                                $filecount++;
                                echo "Creating file EP_Split" . $filecount . ".txt ...  ";
                                $tmpfname = DIR_FS_DOCUMENT_ROOT . $tempdir . "EP_Split" . $filecount . ".txt";
                                //Open next file name
                                $fp = fopen( $tmpfname, "w+");
                                fwrite($fp, $toprow);
                        }
                }
                $line=fgets($infp,32768);
        }
        echo "Added $linecount records and closing file...<br><br> ";
        fclose($fp);
        fclose($infp);

        echo "You can download your split files in the Tools/Files under /catalog/temp/";

}

?>
      </p>

      <table width="75%" border="2">
        <tr>
          <td width="75%">
           <FORM ENCTYPE="multipart/form-data" ACTION="easypopulate.php?split=0" METHOD=POST>
              <p>
                <div align = "left">
                <p><b>Upload EP File</b></p>
                <p>
                  <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="100000000">
                  <p></p>
                  <input name="usrfl" type="file" size="50">
                <input type="submit" name="buttoninsert" value="Insert into db">
                <br>
                </p>
              </div>

              </form>

           <FORM ENCTYPE="multipart/form-data" ACTION="easypopulate.php?split=1" METHOD=POST>
              <p>
                <div align = "left">
                <p><b>Split EP File</b></p>
                <p>
                  <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="1000000000">
                  <p></p>
                  <input name="usrfl" type="file" size="50">
                <input type="submit" name="buttonsplit" value="Split file">
                <br>
                </p>
              </div>

             </form>

           <FORM ENCTYPE="multipart/form-data" ACTION="easypopulate.php" METHOD=POST>
              <p>
                <div align = "left">
                <p><b>Import from Temp Dir (<? echo $tempdir; ?>)</b></p>
                <p class="smallText">
                <INPUT TYPE="text" name="localfile" size="50">
                  <input type="submit" name="buttoninsert" value="Insert into db">
                  <br>
                </p>
              </div>

             </form>




                <p><b>Download EP Files</b></p>

              <!-- Download file links -  Add your custom fields here -->
          <a href="easypopulate.php?download=stream&dltype=full">Download <b>Complete</b> tab-delimited .txt file to edit</a><br>

<!-- Self Modification (Customization) - START -->
          <a href="easypopulate.php?download=stream&dltype=customized">Download <b>Customized</b> tab-delimited .txt file to edit</a><br>
<!-- Self Modification (Customization) - END -->

          <a href="easypopulate.php?download=stream&dltype=priceqty">Download <b>Model/Price/Qty</b> tab-delimited .txt file to edit</a><br>
          <a href="easypopulate.php?download=stream&dltype=category">Download <b>Model/Category</b> tab-delimited .txt file to edit</a><br>

                        <!-- VJ product attributes begin //-->
<?php
  if ($products_with_attributes == true) {
?>
          <a href="easypopulate.php?download=stream&dltype=attrib">Download <b>Model/Attributes</b> tab-delimited .txt file</a><br>
<?php
  }
?>
                        <!-- VJ product attributes end //-->

<!-- Self Modification (Extra Field) - START -->
          <a href="easypopulate.php?download=stream&dltype=extra_field">Download <b>Extra Field (CREATE THE EXTRA FIELD FIRST BEFORE USING THIS FEATURE)</b> tab-delimited .txt file to edit</a><br>
<!-- Self Modification (Extra Field) - END -->
<?php /*
          <a href="easypopulate.php?download=stream&dltype=froogle">Download <b>Froogle</b> tab-delimited .txt file to edit</a><br>
*/ ?>
                <p><b>Create EP Files in Temp Dir (<? echo $tempdir; ?>)</b></p>
          <a href="easypopulate.php?download=tempfile&dltype=full">Create Complete tab-delimited .txt file in temp dir</a><br>

<!-- Self Modification (Customziation) - START -->
          <a href="easypopulate.php?download=tempfile&dltype=customized">Create Customized tab-delimited .txt file in temp dir</a><br>
<!-- Self Modification (Customziation) - END -->

          <a href="easypopulate.php?download=tempfile&dltype=priceqty"">Create Model/Price/Qty tab-delimited .txt file in temp dir</a><br>
          <a href="easypopulate.php?download=tempfile&dltype=category">Create Model/Category tab-delimited .txt file in temp dir</a><br>

                        <!-- VJ product attributes begin //-->
          <a href="easypopulate.php?download=tempfile&dltype=attrib">Create Model/Attributes tab-delimited .txt file in temp dir</a><br>
                        <!-- VJ product attributes end //-->

<!-- Self Modification (Extra Field) - START -->
          <a href="easypopulate.php?download=tempfile&dltype=extra_field">Create Extra Field (CREATE THE EXTRA FIELD FIRST BEFORE USING THIS FEATURE) tab-delimited .txt file in temp dir</a><br>
<!-- Self Modification (Extra Field) - END -->
<?php /*
          <a href="easypopulate.php?download=tempfile&dltype=froogle">Create Froogle tab-delimited .txt file in temp dir</a><br>
*/ ?>
          </td>
        </tr>
      </table>
    </td>
 </tr>
</table>

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

<p>�</p>
<p>�</p><p><br>
</p></body>
</html>

<?php

function ep_get_languages() {
        $languages_query = tep_db_query("select languages_id, code from " . TABLE_LANGUAGES . " order by sort_order");
        // start array at one, the rest of the code expects it that way
        $ll =1;
        while ($ep_languages = tep_db_fetch_array($languages_query)) {
                //will be used to return language_id en language code to report in product_name_code instead of product_name_id
                $ep_languages_array[$ll++] = array(
                                        'id' => $ep_languages['languages_id'],
                                        'code' => $ep_languages['code']
                                        );
        }
        return $ep_languages_array;
};

function tep_get_tax_class_rate($tax_class_id) {
        $tax_multiplier = 0;
        $tax_query = tep_db_query("select SUM(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " WHERE  tax_class_id = '" . $tax_class_id . "' GROUP BY tax_priority");
        if (tep_db_num_rows($tax_query)) {
                while ($tax = tep_db_fetch_array($tax_query)) {
                        $tax_multiplier += $tax['tax_rate'];
                }
        }
        return $tax_multiplier;
};

function tep_get_tax_title_class_id($tax_class_title) {
        $classes_query = tep_db_query("select tax_class_id from " . TABLE_TAX_CLASS . " WHERE tax_class_title = '" . $tax_class_title . "'" );
        $tax_class_array = tep_db_fetch_array($classes_query);
        $tax_class_id = $tax_class_array['tax_class_id'];
        return $tax_class_id ;
}

function print_el( $item2 ) {
        echo " | " . substr(strip_tags($item2), 0, 10);
};

function print_el1( $item2 ) {
        echo sprintf("| %'.4s ", substr(strip_tags($item2), 0, 80));
};
function ep_create_filelayout($dltype){
        global $filelayout, $filelayout_count, $filelayout_sql, $langcode, $fileheaders, $max_categories;
        // depending on the type of the download the user wanted, create a file layout for it.
        $fieldmap = array(); // default to no mapping to change internal field names to external.
        switch( $dltype ){
        case 'full':
                // The file layout is dynamically made depending on the number of languages
                $iii = 0;
                $filelayout = array(
                        'v_products_model'                => $iii++,
                        'v_products_image'                => $iii++,
                        );

                foreach ($langcode as $key => $lang){
                        $l_id = $lang['id'];
                        // uncomment the head_title, head_desc, and head_keywords to use
                        // Linda's Header Tag Controller 2.0
                        //echo $langcode['id'] . $langcode['code'];
                        $filelayout  = array_merge($filelayout , array(
                                        'v_products_name_' . $l_id                => $iii++,
                                        'v_products_info_' . $l_id        => $iii++,
                                        'v_products_description_' . $l_id        => $iii++,
                                        'v_products_url_' . $l_id        => $iii++,
                                        'v_products_head_title_tag_'.$l_id        => $iii++,
                                        'v_products_head_desc_tag_'.$l_id        => $iii++,
                                        'v_products_head_keywords_tag_'.$l_id        => $iii++,
                                        'v_products_h1_' . $l_id        => $iii++,
                                        'v_surls_name_' . $l_id        => $iii++,
                                        'v_products_img_alt_' . $l_id        => $iii++,
                                        'v_products_affiliate_url_' . $l_id        => $iii++,
                                        ));
                }


                // uncomment the customer_price and customer_group to support multi-price per product contrib

    // VJ product attribs begin
     $header_array = array(
                        'v_products_price'                => $iii++,
// Self Modification (For SPPC) - START
//'v_customer_price_1' => $iii++,
//'v_customer_group_id_1' => $iii++,
// Self Modification (For SPPC) - END

// Self Modification (For Specials) - START
'v_specials_new_products_price'                => $iii++,
'v_specials_expires_date'                => $iii++,
'v_specials_status'                => $iii++,
//'v_customers_group_id'                => $iii++,
// Self Modification (For Specials) - END
                        'v_products_weight'                => $iii++,
                        'v_date_avail'                        => $iii++,
                        'v_date_added'                        => $iii++,
                        'v_products_quantity'                => $iii++,
                        'v_products_free_shipping'                => $iii++,
                        // 'v_products_hide_from_groups'		=> $iii++,
                        );

                        $languages = tep_get_languages();

      global $attribute_options_array;

      $attribute_options_count = 1;
      foreach ($attribute_options_array as $attribute_options_values) {
                                $key1 = 'v_attribute_options_id_' . $attribute_options_count;
                                $header_array[$key1] = $iii++;

        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $l_id = $languages[$i]['id'];

                                        $key2 = 'v_attribute_options_name_' . $attribute_options_count . '_' . $l_id;
                                        $header_array[$key2] = $iii++;
                                }

                                $attribute_values_query = "select products_options_values_id  from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$attribute_options_values['products_options_id'] . "' order by products_options_values_id";

                                $attribute_values_values = tep_db_query($attribute_values_query);

                                $attribute_values_count = 1;
                                while ($attribute_values = tep_db_fetch_array($attribute_values_values)) {
                                        $key3 = 'v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count;
                                        $header_array[$key3] = $iii++;

                                        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                                $l_id = $languages[$i]['id'];

                                                $key4 = 'v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $l_id;
                                                $header_array[$key4] = $iii++;
                                        }

                                        $key5 = 'v_attribute_values_price_' . $attribute_options_count . '_' . $attribute_values_count;
                                        $header_array[$key5] = $iii++;
        
//// attributes stock add start        
        if ( $products_attributes_stock        == true ) { 
                                        $key6 = 'v_attribute_values_stock_' . $attribute_options_count . '_' . $attribute_values_count;
                                        $header_array[$key6] = $iii++;
        }                                
//// attributes stock add end                 
                                        
                                        $attribute_values_count++;
                                }

                                $attribute_options_count++;
     }

    $header_array['v_manufacturers_name'] = $iii++;

    $filelayout = array_merge($filelayout, $header_array);
    // VJ product attribs end

                // build the categories name section of the array based on the number of categores the user wants to have
                for($i=1;$i<$max_categories+1;$i++){
                        $filelayout = array_merge($filelayout, array('v_categories_name_' . $i => $iii++));
                }

                $filelayout = array_merge($filelayout, array(
                        'v_tax_class_title'                => $iii++,
                        'v_status'                        => $iii++,
                        ));

                $filelayout_sql = "SELECT
                        p.products_id as v_products_id,
                        p.products_model as v_products_model,
                        p.products_image as v_products_image,
                        p.products_price as v_products_price,
                        sp.specials_new_products_price as v_specials_new_products_price,
                        sp.expires_date as v_specials_expires_date,
                        sp.status as v_specials_status,
                        p.products_weight as v_products_weight,
                        p.products_date_available as v_date_avail,
                        p.products_date_added as v_date_added,
                        p.products_tax_class_id as v_tax_class_id,
                        p.products_quantity as v_products_quantity,
                        p.products_free_shipping as v_products_free_shipping,
                        p.manufacturers_id as v_manufacturers_id,
                        subc.categories_id as v_categories_id,
                        p.products_status as v_status
                        FROM
                        ".TABLE_PRODUCTS." as p left join ".TABLE_SPECIALS." as sp on p.products_id = sp.products_id,
                        ".TABLE_CATEGORIES." as subc,
                        ".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
                        WHERE
                        p.products_id = ptoc.products_id AND
                        ptoc.categories_id = subc.categories_id
                        ";

                break;

// Self Modification (Customization) - START
        case 'customized':
                // The file layout is dynamically made depending on the number of languages
                $iii = 0;
                $filelayout = array(
                        'v_products_model'                => $iii++,
                        'v_products_image'                => $iii++,
                        );

                foreach ($langcode as $key => $lang){
                        $l_id = $lang['id'];
                        // uncomment the head_title, head_desc, and head_keywords to use
                        // Linda's Header Tag Controller 2.0
                        // echo $langcode['id'] . $langcode['code'];
                        $filelayout  = array_merge($filelayout , array(
                                        'v_products_name_' . $l_id                => $iii++,
                                        'v_products_info_' . $l_id        => $iii++,
                                        'v_products_description_' . $l_id        => $iii++,
                                        'v_products_url_' . $l_id        => $iii++,
                                        'v_products_head_title_tag_'.$l_id        => $iii++,
                                        'v_products_head_desc_tag_'.$l_id        => $iii++,
                                        'v_products_head_keywords_tag_'.$l_id        => $iii++,
                                        'v_products_h1_' . $l_id        => $iii++,
                                        'v_surls_name_' . $l_id        => $iii++,
                                        'v_products_img_alt_' . $l_id        => $iii++,
                                        'v_products_affiliate_url_' . $l_id        => $iii++,
                                        ));
                }


                // uncomment the customer_price and customer_group to support multi-price per product contrib

    // VJ product attribs begin
     $header_array = array(
                        'v_products_price'                => $iii++,

// Self Modification (For SPPC) - START
//'v_customer_price_1' => $iii++,
//'v_customer_group_id_1' => $iii++,
// Self Modification (For SPPC) - END

// Self Modification (For Specials) - START
'v_specials_new_products_price'                => $iii++,
'v_specials_expires_date'                => $iii++,
'v_specials_status'                => $iii++,
//'v_customers_group_id'                => $iii++,
// Self Modification (For Specials) - END

                        'v_products_weight'                => $iii++,
                        // 'v_date_avail'                        => $iii++,
                        // 'v_date_added'                        => $iii++,
                        // 'v_products_quantity'                => $iii++,
                        // 'v_products_free_shipping'                => $iii++,
                        // 'v_products_hide_from_groups'		=> $iii++,
                        );

                        $languages = tep_get_languages();

      global $attribute_options_array;

      $attribute_options_count = 1;
      foreach ($attribute_options_array as $attribute_options_values) {
                                $key1 = 'v_attribute_options_id_' . $attribute_options_count;
                                $header_array[$key1] = $iii++;

        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $l_id = $languages[$i]['id'];

                                        $key2 = 'v_attribute_options_name_' . $attribute_options_count . '_' . $l_id;
                                        $header_array[$key2] = $iii++;
                                }

                                $attribute_values_query = "select products_options_values_id  from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$attribute_options_values['products_options_id'] . "' order by products_options_values_id";

                                $attribute_values_values = tep_db_query($attribute_values_query);

                                $attribute_values_count = 1;
                                while ($attribute_values = tep_db_fetch_array($attribute_values_values)) {
                                        $key3 = 'v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count;
                                        $header_array[$key3] = $iii++;

                                        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                                $l_id = $languages[$i]['id'];

                                                $key4 = 'v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $l_id;
                                                $header_array[$key4] = $iii++;
                                        }

                                        $key5 = 'v_attribute_values_price_' . $attribute_options_count . '_' . $attribute_values_count;
                                        $header_array[$key5] = $iii++;
        
//// attributes stock add start        
        if ( $products_attributes_stock        == true ) { 
                                        $key6 = 'v_attribute_values_stock_' . $attribute_options_count . '_' . $attribute_values_count;
                                        $header_array[$key6] = $iii++;
        }                                
//// attributes stock add end                 
                                        
                                        $attribute_values_count++;
                                }

                                $attribute_options_count++;
     }

    // $header_array['v_manufacturers_name'] = $iii++;

    $filelayout = array_merge($filelayout, $header_array);
    // VJ product attribs end

                // build the categories name section of the array based on the number of categores the user wants to have
                for($i=1;$i<$max_categories+1;$i++){
                        $filelayout = array_merge($filelayout, array('v_categories_name_' . $i => $iii++));
                }

                $filelayout = array_merge($filelayout, array(
                        // 'v_tax_class_title'                => $iii++,
                        'v_status'                        => $iii++,
                        ));

                $filelayout_sql = "SELECT
                        p.products_id as v_products_id,
                        p.products_model as v_products_model,
                        p.products_image as v_products_image,
                        p.products_price as v_products_price,
                        sp.specials_new_products_price as v_specials_new_products_price,
                        sp.expires_date as v_specials_expires_date,
                        sp.status as v_specials_status,".
//                      sp.customers_group_id as v_customers_group_id,
                       "p.products_weight as v_products_weight,
                        p.products_date_available as v_date_avail,
                        p.products_date_added as v_date_added,
                        p.products_tax_class_id as v_tax_class_id,
                        p.products_quantity as v_products_quantity,
                        p.products_free_shipping as v_products_free_shipping,".
//                      p.products_hide_from_groups as v_products_hide_from_groups,
                       "p.manufacturers_id as v_manufacturers_id,
                        subc.categories_id as v_categories_id,
                        p.products_status as v_status
                        FROM
                        ".TABLE_PRODUCTS." as p left join ".TABLE_SPECIALS." as sp on p.products_id = sp.products_id,
                        ".TABLE_CATEGORIES." as subc,
                        ".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
                        WHERE
                        p.products_id = ptoc.products_id AND
                        ptoc.categories_id = subc.categories_id
                        ";

                break;
// Self Modification (Customization) - END

        case 'priceqty':
                $iii = 0;
                // uncomment the customer_price and customer_group to support multi-price per product contrib
                $filelayout = array(
                        'v_products_model'                => $iii++,
                        'v_products_price'                => $iii++,
                        'v_products_quantity'                => $iii++,
                        'v_products_free_shipping'                => $iii++,
                        #'v_customer_price_1'                => $iii++,
                        #'v_customer_group_id_1'                => $iii++,
                        #'v_customer_price_2'                => $iii++,
                        #'v_customer_group_id_2'                => $iii++,
                        #'v_customer_price_3'                => $iii++,
                        #'v_customer_group_id_3'                => $iii++,
                        #'v_customer_price_4'                => $iii++,
                        #'v_customer_group_id_4'                => $iii++,
                                );
                $filelayout_sql = "SELECT
                        p.products_id as v_products_id,
                        p.products_model as v_products_model,
                        p.products_price as v_products_price,
                        p.products_tax_class_id as v_tax_class_id,
                        p.products_quantity as v_products_quantity,
                        p.products_free_shipping as v_products_free_shipping
                        FROM
                        ".TABLE_PRODUCTS." as p
                        ";

                break;

        case 'category':
                // The file layout is dynamically made depending on the number of languages
                $iii = 0;
                $filelayout = array(
                        'v_products_model'                => $iii++,
                );

                // build the categories name section of the array based on the number of categores the user wants to have
                for($i=1;$i<$max_categories+1;$i++){
                        $filelayout = array_merge($filelayout, array('v_categories_name_' . $i => $iii++));
                }


                $filelayout_sql = "SELECT
                        p.products_id as v_products_id,
                        p.products_model as v_products_model,
                        subc.categories_id as v_categories_id
                        FROM
                        ".TABLE_PRODUCTS." as p,
                        ".TABLE_CATEGORIES." as subc,
                        ".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc                        
                        WHERE
                        p.products_id = ptoc.products_id AND
                        ptoc.categories_id = subc.categories_id
                        ";
                break;

// start EP for product extra field ============================= DEVSOFTVN - 10/20/2005 	
	case 'extra_field':
		$iii = 0;
		// uncomment the customer_price and customer_group to support multi-price per product contrib
		// Mofificata Davide Duca
		$filelayout = array(
			'v_products_model'		=> $iii++,
			'v_products_extra_fields_name'		=> $iii++, 
			'v_products_extra_fields_id'		=> $iii++,
			'v_products_id'		=> $iii++,
			'v_products_extra_fields_value'		=> $iii++,
						);
	
		$filelayout_sql = "SELECT
                        p.products_id as v_products_id,
                        p.products_model as v_products_model,
						subc.products_extra_fields_id as v_products_extra_fields_id,
						subc.products_extra_fields_value as v_products_extra_fields_value,
						ptoc.products_extra_fields_name as v_products_extra_fields_name
                        FROM
                        ".TABLE_PRODUCTS." as p,
                        ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." as subc,
						".TABLE_PRODUCTS_EXTRA_FIELDS." as ptoc
                        WHERE
                        p.products_id = subc.products_id AND
						ptoc.products_extra_fields_id = subc.products_extra_fields_id
                        ";	
// Fine modifica
		
		break;
// end of EP for extra field code ======= DEVSOFTVN================       

        case 'froogle':
                // this is going to be a little interesting because we need
                // a way to map from internal names to external names
                //
                // Before it didn't matter, but with froogle needing particular headers,
                // The file layout is dynamically made depending on the number of languages
                $iii = 0;
                $filelayout = array(
                        'v_froogle_products_url_1'                        => $iii++,
                        );
                //
                // here we need to get the default language and put
                $l_id = 1; // dummy it in for now.
//                foreach ($langcode as $key => $lang){
//                        $l_id = $lang['id'];
                        $filelayout  = array_merge($filelayout , array(
                                        'v_froogle_products_name_' . $l_id                => $iii++,
                                        'v_froogle_products_description_' . $l_id        => $iii++,
                                        ));
//                }
                $filelayout  = array_merge($filelayout , array(
                        'v_products_price'                => $iii++,
                        'v_products_fullpath_image'        => $iii++,
                        'v_category_fullpath'                => $iii++,
                        'v_froogle_offer_id'                => $iii++,
                        'v_froogle_instock'                => $iii++,
                        'v_froogle_ shipping'                => $iii++,
                        'v_manufacturers_name'                => $iii++,
                        'v_froogle_ upc'                => $iii++,
                        'v_froogle_color'                => $iii++,
                        'v_froogle_size'                => $iii++,
                        'v_froogle_quantitylevel'        => $iii++,
                        'v_froogle_product_id'                => $iii++,
                        'v_froogle_manufacturer_id'        => $iii++,
                        'v_froogle_exp_date'                => $iii++,
                        'v_froogle_product_type'        => $iii++,
                        'v_froogle_delete'                => $iii++,
                        'v_froogle_currency'                => $iii++,
                                ));
                $iii=0;
                $fileheaders = array(
                        'product_url'                => $iii++,
                        'name'                        => $iii++,
                        'description'                => $iii++,
                        'price'                        => $iii++,
                        'image_url'                => $iii++,
                        'category'                => $iii++,
                        'offer_id'                => $iii++,
                        'instock'                => $iii++,
                        'shipping'                => $iii++,
                        'brand'                        => $iii++,
                        'upc'                        => $iii++,
                        'color'                        => $iii++,
                        'size'                        => $iii++,
                        'quantity'                => $iii++,
                        'product_id'                => $iii++,
                        'manufacturer_id'        => $iii++,
                        'exp_date'                => $iii++,
                        'product_type'                => $iii++,
                        'delete'                => $iii++,
                        'currency'                => $iii++,
                        );
                $filelayout_sql = "SELECT
                        p.products_id as v_products_id,
                        p.products_model as v_products_model,
                        p.products_image as v_products_image,
                        p.products_price as v_products_price,
                        p.products_weight as v_products_weight,
                        p.products_date_added as v_date_avail,
                        p.products_tax_class_id as v_tax_class_id,
                        p.products_quantity as v_products_quantity,
                        p.manufacturers_id as v_manufacturers_id,
                        subc.categories_id as v_categories_id
                        FROM
                        ".TABLE_PRODUCTS." as p,
                        ".TABLE_CATEGORIES." as subc,
                        ".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
                        WHERE
                        p.products_id = ptoc.products_id AND
                        ptoc.categories_id = subc.categories_id
                        ";
                break;

// VJ product attributes begin
        case 'attrib':
                $iii = 0;
                $filelayout = array(
                        'v_products_model'                => $iii++
                        );

    $header_array = array();

                $languages = tep_get_languages();

    global $attribute_options_array;

    $attribute_options_count = 1;
    foreach ($attribute_options_array as $attribute_options_values) {
                        $key1 = 'v_attribute_options_id_' . $attribute_options_count;
                        $header_array[$key1] = $iii++;

                        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                $l_id = $languages[$i]['id'];

                                $key2 = 'v_attribute_options_name_' . $attribute_options_count . '_' . $l_id;
                                $header_array[$key2] = $iii++;
                        }

                        $attribute_values_query = "select products_options_values_id  from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$attribute_options_values['products_options_id'] . "' order by products_options_values_id";

                        $attribute_values_values = tep_db_query($attribute_values_query);

                        $attribute_values_count = 1;
                                while ($attribute_values = tep_db_fetch_array($attribute_values_values)) {
                                        $key3 = 'v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count;
                                        $header_array[$key3] = $iii++;

                                        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                                $l_id = $languages[$i]['id'];

                                                $key4 = 'v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $l_id;
                                                $header_array[$key4] = $iii++;
                                        }

                                        $key5 = 'v_attribute_values_price_' . $attribute_options_count . '_' . $attribute_values_count;
                                        $header_array[$key5] = $iii++;
        
//// attributes stock add start        
        if ( $products_attributes_stock        == true ) { 
                                        $key6 = 'v_attribute_values_stock_' . $attribute_options_count . '_' . $attribute_values_count;
                                        $header_array[$key6] = $iii++;
        }                                
//// attributes stock add end                 
                                        
                                        $attribute_values_count++;
                                }

                        $attribute_options_count++;
    }

    $filelayout = array_merge($filelayout, $header_array);

                $filelayout_sql = "SELECT
                        p.products_id as v_products_id,
                        p.products_model as v_products_model
                        FROM
                        ".TABLE_PRODUCTS." as p
                        ";

                break;
// VJ product attributes end
        }
        $filelayout_count = count($filelayout);

}


function walk( $item1 ) {
        global $filelayout, $filelayout_count, $modelsize;
        global $active, $inactive, $langcode, $default_these, $deleteit, $zero_qty_inactive;
        global $epdlanguage_id, $price_with_tax, $replace_quotes;
        global $default_images, $default_image_manufacturer, $default_image_product, $default_image_category;
        global $separator, $max_categories;
        // first we clean up the row of data

        // chop blanks from each end
        $item1 = ltrim(rtrim($item1));

        // blow it into an array, splitting on the tabs
        $items = explode($separator, $item1);

        // make sure all non-set things are set to '';
        // and strip the quotes from the start and end of the stings.
        // escape any special chars for the database.
        foreach( $filelayout as $key=> $value){
                $i = $filelayout[$key];
                if (isset($items[$i]) == false) {
                        $items[$i]='';
                } else {
                        // Check to see if either of the magic_quotes are turned on or off;
                        // And apply filtering accordingly.
                        if (function_exists('ini_get')) {
                                //echo "Getting ready to check magic quotes<br>";
                                if (ini_get('magic_quotes_runtime') == 1){
                                        // The magic_quotes_runtime are on, so lets account for them
                                        // check if the last character is a quote;
                                        // if it is, chop off the quotes.
                                        if (substr($items[$i],-1) == '"'){
                                                $items[$i] = substr($items[$i],2,strlen($items[$i])-4);
                                        }
                                        // now any remaining doubled double quotes should be converted to one doublequote
                                        $items[$i] = str_replace('\"\"',"&#34",$items[$i]);
                                        if ($replace_quotes){
                                                $items[$i] = str_replace('\"',"&#34",$items[$i]);
                                                $items[$i] = str_replace("\'","&#39",$items[$i]);
                                        }
                                } else { // no magic_quotes are on
                                        // check if the last character is a quote;
                                        // if it is, chop off the 1st and last character of the string.
                                        if (substr($items[$i],-1) == '"'){
                                                $items[$i] = substr($items[$i],1,strlen($items[$i])-2);
                                        }
                                        // now any remaining doubled double quotes should be converted to one doublequote
                                        $items[$i] = str_replace('""',"&#34",$items[$i]);
                                        if ($replace_quotes){
                                                $items[$i] = str_replace('"',"&#34",$items[$i]);
                                                $items[$i] = str_replace("'","&#39",$items[$i]);
                                        }
                                }
                        }
                }
        }


/*
        if ( $items['v_status'] == $deleteit ){
                // they want to delete this product.
                echo "Deleting product " . $items['v_products_model'] . " from the database<br>";
                // Get the ID

                // kill in the products_to_categories

                // Kill in the products table

                return; // we're done deleteing!
        }
*/


// EP for product extra fields Contrib by minhmaster DEVSOFTVN ==========
		$v_products_extra_fields_id = $items[$filelayout['v_products_extra_fields_id']];
		$v_products_id	=	$items[$filelayout['v_products_id']];
		$v_products_extra_fields_value	=	$items[$filelayout['v_products_extra_fields_value']];

	if (isset($v_products_extra_fields_id) ){					
				$sql_exist	=	"SELECT products_extra_fields_value FROM ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS. " WHERE (products_id ='".$v_products_id. "') AND (products_extra_fields_id ='".$v_products_extra_fields_id ."')";
				if (tep_db_num_rows(tep_db_query($sql_exist)) <= 0) {
					$sql_extra_field	=	"INSERT INTO ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS."(products_id,products_extra_fields_id,products_extra_fields_value) VALUES ('".$v_products_id."','".$v_products_extra_fields_id."','".$v_products_extra_fields_value."')";
					$str_err_report= " $v_products_extra_fields_id | $v_products_id  | $v_products_extra_fields_value | <b><font color=blue><?php echo INSERED_EP; ?></font></b><br>";					
				} else {
					$sql_extra_field	=	"UPDATE ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." SET products_extra_fields_value='".$v_products_extra_fields_value."' WHERE (products_id ='".$v_products_id. "') AND (products_extra_fields_id ='".$v_products_extra_fields_id ."')";
					$str_err_report= " $v_products_extra_fields_id | $v_products_id  | $v_products_extra_fields_value | <b><font color=blue><?php echo UPDATE_EP; ?></font></b><br>";					
				}
				

				$result = tep_db_query($sql_extra_field);
				//echo $sql_extra_field;
				echo $str_err_report;
				
	} else  {
//============ EP for product extra fields Contrib by minhmt DEVSOFTVN off============

        // now do a query to get the record's current contents
        $sql = "SELECT
                p.products_id as v_products_id,
                p.products_model as v_products_model,
                p.products_image as v_products_image,
                p.products_price as v_products_price,
                sp.specials_new_products_price as v_specials_new_products_price,
                sp.expires_date as v_specials_expires_date,
                sp.status as v_specials_status,
                p.products_weight as v_products_weight,
                p.products_date_added as v_date_added,
                p.products_date_available as v_date_avail,
                p.products_tax_class_id as v_tax_class_id,
                p.products_quantity as v_products_quantity,
                p.products_free_shipping as v_products_free_shipping, ".
//              p.products_hide_from_groups as v_products_hide_from_groups,
               "p.manufacturers_id as v_manufacturers_id,
                subc.categories_id as v_categories_id,
                p.products_status as v_status
                FROM
                ".TABLE_PRODUCTS." as p left join ".TABLE_SPECIALS." as sp on p.products_id = sp.products_id,
                ".TABLE_CATEGORIES." as subc,
                ".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
                WHERE
                p.products_id = ptoc.products_id AND
                p.products_model = '" . $items[$filelayout['v_products_model']] . "' AND
                ptoc.categories_id = subc.categories_id
                ";

        $result = tep_db_query($sql);
        $row =  tep_db_fetch_array($result);


        while ($row){
                // OK, since we got a row, the item already exists.
                // Let's get all the data we need and fill in all the fields that need to be defaulted to the current values
                // for each language, get the description and set the vals
                foreach ($langcode as $key => $lang){
                        //echo "Inside defaulting loop";
                        //echo "key is $key<br>";
                        //echo "langid is " . $lang['id'] . "<br>";
//                        $sql2 = "SELECT products_name, products_description
//                                FROM ".TABLE_PRODUCTS_DESCRIPTION."
//                                WHERE
//                                        products_id = " . $row['v_products_id'] . " AND
//                                        language_id = '" . $lang['id'] . "'
//                                ";
                        $sql2 = "SELECT *
                                FROM ".TABLE_PRODUCTS_DESCRIPTION." pd left join ".TABLE_SEO_URLS." on products_surls_id = surls_id
                                WHERE
                                        pd.products_id = " . $row['v_products_id'] . " AND
                                        pd.language_id = '" . $lang['id'] . "'
                                ";
                        $result2 = tep_db_query($sql2);
                        $row2 =  tep_db_fetch_array($result2);
                        // Need to report from ......_name_1 not ..._name_0
                        $row['v_products_name'][$lang['id']]                 = $row2['products_name'];
                        $row['v_products_info'][$lang['id']]         = $row2['products_info'];
                        $row['v_products_description'][$lang['id']]         = $row2['products_description'];
                        $row['v_products_url'][$lang['id']]                 = $row2['products_url'];

                        /* // support for Linda's Header Controller 2.0 here
                        if(isset($filelayout['v_products_head_title_tag_' . $lang['id'] ])){ */
                                $row['v_products_head_title_tag'][$lang['id']]         = $row2['products_head_title_tag'];
                                $row['v_products_head_desc_tag'][$lang['id']]         = $row2['products_head_desc_tag'];
                                $row['v_products_head_keywords_tag'][$lang['id']]         = $row2['products_head_keywords_tag'];
                        /* }
                        // end support for Header Controller 2.0 */
                        $row['v_products_h1'][$lang['id']]         = $row2['products_h1'];
                        $row['v_surls_name'][$lang['id']]         = $row2['surls_name'];
                        $row['v_products_img_alt'][$lang['id']]         = $row2['products_img_alt'];
                        $row['v_products_affiliate_url'][$lang['id']]         = $row2['products_affiliate_url'];
                }

                // start with v_categories_id
                // Get the category description
                // set the appropriate variable name
                // if parent_id is not null, then follow it up.
                $thecategory_id = $row['v_categories_id'];

                for( $categorylevel=1; $categorylevel<$max_categories+1; $categorylevel++){
                        if ($thecategory_id){
                                $sql2 = "SELECT categories_name
                                        FROM ".TABLE_CATEGORIES_DESCRIPTION."
                                        WHERE
                                                categories_id = " . $thecategory_id . " AND
                                                language_id = " . $epdlanguage_id ;

                                $result2 = tep_db_query($sql2);
                                $row2 =  tep_db_fetch_array($result2);
                                // only set it if we found something
                                $temprow['v_categories_name_' . $categorylevel] = $row2['categories_name'];
                                // now get the parent ID if there was one
                                $sql3 = "SELECT parent_id
                                        FROM ".TABLE_CATEGORIES."
                                        WHERE
                                                categories_id = " . $thecategory_id;
                                $result3 = tep_db_query($sql3);
                                $row3 =  tep_db_fetch_array($result3);
                                $theparent_id = $row3['parent_id'];
                                if ($theparent_id != ''){
                                        // there was a parent ID, lets set thecategoryid to get the next level
                                        $thecategory_id = $theparent_id;
                                } else {
                                        // we have found the top level category for this item,
                                        $thecategory_id = false;
                                }
                        } else {
                                        $temprow['v_categories_name_' . $categorylevel] = '';
                        }
                }
                // temprow has the old style low to high level categories.
                $newlevel = 1;
                // let's turn them into high to low level categories
                for( $categorylevel=$max_categories+1; $categorylevel>0; $categorylevel--){
                        if ($temprow['v_categories_name_' . $categorylevel] != ''){
                                $row['v_categories_name_' . $newlevel++] = $temprow['v_categories_name_' . $categorylevel];
                        }
                }

                if ($row['v_manufacturers_id'] != ''){
                        $sql2 = "SELECT manufacturers_name
                                FROM ".TABLE_MANUFACTURERS."
                                WHERE
                                manufacturers_id = " . $row['v_manufacturers_id']
                                ;
                        $result2 = tep_db_query($sql2);
                        $row2 =  tep_db_fetch_array($result2);
                        $row['v_manufacturers_name'] = $row2['manufacturers_name'];
                }

                //elari -
                //We check the value of tax class and title instead of the id
                //Then we add the tax to price if $price_with_tax is set to true
                $row_tax_multiplier = tep_get_tax_class_rate($row['v_tax_class_id']);
                $row['v_tax_class_title'] = tep_get_tax_class_title($row['v_tax_class_id']);
                if ($price_with_tax){
                        $row['v_products_price'] = round($row['v_products_price'] + ($row['v_products_price'] * $row_tax_multiplier / 100),2);
                        if (!empty($row['v_specials_new_products_price']))
                          $row['v_specials_new_products_price'] = round($row['v_specials_new_products_price'] + ($row['v_specials_new_products_price'] * $row_tax_multiplier / 100),2);
                }

                // now create the internal variables that will be used
                // the $$thisvar is on purpose: it creates a variable named what ever was in $thisvar and sets the value
                foreach ($default_these as $thisvar){
                        $$thisvar        = $row[$thisvar];
                }
                $v_status = $v_status ? $active : $inactive;

                $row =  tep_db_fetch_array($result);
        }

        // this is an important loop.  What it does is go thru all the fields in the incoming file and set the internal vars.
        // Internal vars not set here are either set in the loop above for existing records, or not set at all (null values)
        // the array values are handled separatly, although they will set variables in this loop, we won't use them.
        foreach( $filelayout as $key => $value ){
                if (strpos($key,'v_categories_name_') !== 0 && strpos($key,'v_attribute_values_') !== 0 && strpos($key,'v_attribute_options_') !== 0 && preg_match('/(.+)_(\d+)/', $key, $match)) {
                        ${$match[1]}[$match[2]] = $items[ $value ];
                } else $$key = $items[ $value ];
        }

        /*
        // so how to handle these?  we shouldn't built the array unless it's been giving to us.
        // The assumption is that if you give us names and descriptions, then you give us name and description for all applicable languages
        foreach ($langcode as $lang){
                //echo "Langid is " . $lang['id'] . "<br>";
                $l_id = $lang['id'];
                if (isset($filelayout['v_products_name_' . $l_id ])){
                        //we set dynamically the language values
                        $v_products_name[$l_id]         = $items[$filelayout['v_products_name_' . $l_id]];
                        $v_products_info[$l_id]         = $items[$filelayout['v_products_info_' . $l_id ]];
                        $v_products_description[$l_id]         = $items[$filelayout['v_products_description_' . $l_id ]];
                        $v_products_url[$l_id]                 = $items[$filelayout['v_products_url_' . $l_id ]];
                        // support for Linda's Header Controller 2.0 here
                        if(isset($filelayout['v_products_head_title_tag_' . $l_id])){
                                $v_products_head_title_tag[$l_id]         = $items[$filelayout['v_products_head_title_tag_' . $l_id]];
                                $v_products_head_desc_tag[$l_id]         = $items[$filelayout['v_products_head_desc_tag_' . $l_id]];
                                $v_products_head_keywords_tag[$l_id]         = $items[$filelayout['v_products_head_keywords_tag_' . $l_id]];
                        }
                        // end support for Header Controller 2.0
                        $v_products_h1[$l_id]         = $items[$filelayout['v_products_h1_' . $l_id ]];
                        $v_surls_name[$l_id]         = $items[$filelayout['v_surls_name_' . $l_id ]];
                        $v_products_img_alt[$l_id]         = $items[$filelayout['v_products_img_alt_' . $l_id ]];
                        $v_products_affiliate_url[$l_id]                 = $items[$filelayout['v_products_affiliate_url_' . $l_id ]];
                }
        }
        */
        //elari... we get the tax_clas_id from the tax_title
        //on screen will still be displayed the tax_class_title instead of the id....
        if ( isset( $v_tax_class_title) ){
                $v_tax_class_id          = tep_get_tax_title_class_id($v_tax_class_title);
        }
        //we check the tax rate of this tax_class_id
        $row_tax_multiplier = tep_get_tax_class_rate($v_tax_class_id);

        //And we recalculate price without the included tax...
        //Since it seems display is made before, the displayed price will still include tax
        //This is same problem for the tax_clas_id that display tax_class_title
        if ($price_with_tax){
                $v_products_price        = round( $v_products_price / (1 + ( $row_tax_multiplier * $price_with_tax/100) ), 4);
                if (!empty($v_specials_new_products_price))
                  $v_specials_new_products_price = round( $v_specials_new_products_price / (1 + ( $row_tax_multiplier * $price_with_tax/100) ), 4);
        }

        // if they give us one category, they give us all 6 categories
        unset ($v_categories_name); // default to not set.


//*********************** Modified by Andy - categorylevel=6 to categorylevel=ep_max_cat
        if ( isset( $filelayout['v_categories_name_1'] ) ){
                $newlevel = 1;
                for( $categorylevel=$max_categories; $categorylevel>0; $categorylevel--){
                        if ( $items[$filelayout['v_categories_name_' . $categorylevel]] != ''){
                                $v_categories_name[$newlevel++] = $items[$filelayout['v_categories_name_' . $categorylevel]];
                        }
                }
                while( $newlevel < $max_categories+1){
                        $v_categories_name[$newlevel++] = ''; // default the remaining items to nothing
                }
        }

        if (ltrim(rtrim($v_products_quantity)) == '') {
                $v_products_quantity = 1;
        }
        if ($v_date_avail == '') {
//                $v_date_avail = "CURRENT_TIMESTAMP";
                $v_date_avail = "NULL";
        } else {
                // we put the quotes around it here because we can't put them into the query, because sometimes
                //   we will use the "current_timestamp", which can't have quotes around it.
                $v_date_avail = '"' . $v_date_avail . '"';
        }

        if ($v_date_added == '') {
                $v_date_added = "CURRENT_TIMESTAMP";
        } else {
                // we put the quotes around it here because we can't put them into the query, because sometimes
                //   we will use the "current_timestamp", which can't have quotes around it.
                $v_date_added = '"' . $v_date_added . '"';
        }


        // default the stock if they spec'd it or if it's blank
        $v_db_status = '1'; // default to active
        if ($v_status == $inactive){
                // they told us to deactivate this item
                $v_db_status = '0';
        }
        if ($zero_qty_inactive && $v_products_quantity == 0) {
                // if they said that zero qty products should be deactivated, let's deactivate if the qty is zero
                $v_db_status = '0';
        }

        if ($v_manufacturer_id==''){
                $v_manufacturer_id="NULL";
        }

        if (trim($v_products_image)==''){
                $v_products_image = $default_image_product;
        }

        if (strlen($v_products_model) > $modelsize ){
                echo "<font color='red'>" . strlen($v_products_model) . $v_products_model . "... ERROR! - Too many characters in the model number.<br>
                        12 is the maximum on a standard OSC install.<br>
                        Your maximum product_model length is set to $modelsize<br>
                        You can either shorten your model numbers or increase the size of the field in the database.</font>";
                die();
        }

        // OK, we need to convert the manufacturer's name into id's for the database
        if ( isset($v_manufacturers_name) && $v_manufacturers_name != '' ){
                $sql = "SELECT man.manufacturers_id
                        FROM ".TABLE_MANUFACTURERS." as man
                        WHERE
                                man.manufacturers_name = '" . $v_manufacturers_name . "'";
                $result = tep_db_query($sql);
                $row =  tep_db_fetch_array($result);
                if ( $row != '' ){
                        foreach( $row as $item ){
                                $v_manufacturer_id = $item;
                        }
                } else {
                        // to add, we need to put stuff in categories and categories_description
                        $sql = "SELECT MAX( manufacturers_id) max FROM ".TABLE_MANUFACTURERS;
                        $result = tep_db_query($sql);
                        $row =  tep_db_fetch_array($result);
                        $max_mfg_id = $row['max']+1;
                        // default the id if there are no manufacturers yet
                        if (!is_numeric($max_mfg_id) ){
                                $max_mfg_id=1;
                        }

                        // Uncomment this query if you have an older 2.2 codebase
                        /*
                        $sql = "INSERT INTO ".TABLE_MANUFACTURERS."(
                                manufacturers_id,
                                manufacturers_name,
                                manufacturers_image
                                ) VALUES (
                                $max_mfg_id,
                                '$v_manufacturers_name',
                                '$default_image_manufacturer'
                                )";
                        */

                        // Comment this query out if you have an older 2.2 codebase
                        $sql = "INSERT INTO ".TABLE_MANUFACTURERS."(
                                manufacturers_id,
                                manufacturers_name,
                                manufacturers_image,
                                sort_order,
                                manufacturers_status,
                                date_added,
                                last_modified
                                ) VALUES (
                                $max_mfg_id,
                                '$v_manufacturers_name',
                                '$default_image_manufacturer',
                                NULL,
                                1,
                                CURRENT_TIMESTAMP,
                                CURRENT_TIMESTAMP
                                )";
                        $result = tep_db_query($sql);
                        $sql = "INSERT INTO ".TABLE_MANUFACTURERS_INFO."(
                                        manufacturers_id,
                                        languages_id,
                                        manufacturers_url
                                ) VALUES (
                                        $max_mfg_id,
                                        '$epdlanguage_id',
                                        ''
                                )";
                        $result = tep_db_query($sql);
                        $v_manufacturer_id = $max_mfg_id;
                }
        }
        // if the categories names are set then try to update them
        if ( isset($v_categories_name_1)){
                // start from the highest possible category and work our way down from the parent
                $v_categories_id = 0;
                $theparent_id = 0;
                for ( $categorylevel=$max_categories+1; $categorylevel>0; $categorylevel-- ){
                        $thiscategoryname = $v_categories_name[$categorylevel];
                        if ( $thiscategoryname != ''){
                                // we found a category name in this field

                                // now the subcategory
                                $sql = "SELECT cat.categories_id
                                        FROM ".TABLE_CATEGORIES." as cat, 
                                             ".TABLE_CATEGORIES_DESCRIPTION." as des
                                        WHERE
                                                cat.categories_id = des.categories_id AND
                                                des.language_id = $epdlanguage_id AND
                                                cat.parent_id = " . $theparent_id . " AND
                                                des.categories_name = '" . addslashes($thiscategoryname) . "'";
                                $result = tep_db_query($sql);
                                $row =  tep_db_fetch_array($result);
                                if ( $row != '' ){
                                        foreach( $row as $item ){
                                                $thiscategoryid = $item;
                                        }
                                } else {
                                        // to add, we need to put stuff in categories and categories_description
                                        $sql = "SELECT MAX( categories_id) max FROM ".TABLE_CATEGORIES;
                                        $result = tep_db_query($sql);
                                        $row =  tep_db_fetch_array($result);
                                        $max_category_id = $row['max']+1;
                                        if (!is_numeric($max_category_id) ){
                                                $max_category_id=1;
                                        }
                                        $thiscategoryname = addslashes($thiscategoryname);
                                        $sql = "INSERT INTO ".TABLE_CATEGORIES."(
                                                categories_id,
                                                categories_image,
                                                parent_id,
                                                sort_order,
                                                categories_status,
                                                date_added,
                                                last_modified
                                                ) VALUES (
                                                $max_category_id,
                                                '$default_image_category',
                                                $theparent_id,
                                                NULL,
                                                1,
                                                CURRENT_TIMESTAMP
                                                ,CURRENT_TIMESTAMP
                                                )";
                                        $result = tep_db_query($sql);
                                        $sql = "INSERT INTO ".TABLE_CATEGORIES_DESCRIPTION."(
                                                        categories_id,
                                                        language_id,
                                                        categories_name
                                                ) VALUES (
                                                        $max_category_id,
                                                        '$epdlanguage_id',
                                                        '$thiscategoryname'
                                                )";
                                        $result = tep_db_query($sql);
                                        $thiscategoryid = $max_category_id;
                                }
                                // the current catid is the next level's parent
                                $theparent_id = $thiscategoryid;
                                $v_categories_id = $thiscategoryid; // keep setting this, we need the lowest level category ID later
                        }
                }
        }


        if ($v_products_model != "") {
                //   products_model exists!
                array_walk($items, 'print_el');

                // First we check to see if this is a product in the current db.
                $result = tep_db_query("SELECT products_id FROM ".TABLE_PRODUCTS." WHERE (products_model = '". $v_products_model . "')");

                if (tep_db_num_rows($result) == 0)  {
                        //   insert into products

                        $sql = "SHOW TABLE STATUS LIKE '".TABLE_PRODUCTS."'";
                        $result = tep_db_query($sql);
                        $row =  tep_db_fetch_array($result);
                        $max_product_id = $row['Auto_increment'];
                        if (!is_numeric($max_product_id) ){
                                $max_product_id=1;
                        }
                        $v_products_id = $max_product_id;
                        echo "<font color='green'> !New Product!</font><br>";

                        $query = "INSERT INTO ".TABLE_PRODUCTS." (
                                        products_image,
                                        products_model,
                                        products_price,
                                        products_status,
                                        products_last_modified,
                                        products_date_added,
                                        products_date_available,
                                        products_tax_class_id,
                                        products_weight,
                                        products_quantity,
                                        products_free_shipping, ".
//                                      products_hide_from_groups,
                                       "manufacturers_id)
                                                VALUES (
                                                        '$v_products_image',";

                        // unmcomment these lines if you are running the image mods
                        /*
                                $query .=                . $v_products_mimage . '", "'
                                                        . $v_products_bimage . '", "'
                                                        . $v_products_subimage1 . '", "'
                                                        . $v_products_bsubimage1 . '", "'
                                                        . $v_products_subimage2 . '", "'
                                                        . $v_products_bsubimage2 . '", "'
                                                        . $v_products_subimage3 . '", "'
                                                        . $v_products_bsubimage3 . '", "'
                        */

                        $query .="                                '$v_products_model',
                                                                '$v_products_price',
                                                                '$v_db_status',
                                                                CURRENT_TIMESTAMP,
                                                                $v_date_added,
                                                                $v_date_avail,
                                                                '$v_tax_class_id',
                                                                '$v_products_weight',
                                                                '$v_products_quantity',
                                                                '$v_products_free_shipping', ".
//                                                              '$v_products_hide_from_groups',
                                                               "'$v_manufacturer_id')
                                                        ";
                                $result = tep_db_query($query);
                } else {
                        // existing product, get the id from the query
                        // and update the product data

                        $row =  tep_db_fetch_array($result);
                        $v_products_id = $row['products_id'];
                        echo "<font color='black'> Updated</font><br>";
                        $query = 'UPDATE '.TABLE_PRODUCTS.'
                                        SET
                                        products_price="'.$v_products_price.
                                        '" ,products_image="'.$v_products_image;

                        // uncomment these lines if you are running the image mods
/*
                                $query .=
                                        '" ,products_mimage="'.$v_products_mimage.
                                        '" ,products_bimage="'.$v_products_bimage.
                                        '" ,products_subimage1="'.$v_products_subimage1.
                                        '" ,products_bsubimage1="'.$v_products_bsubimage1.
                                        '" ,products_subimage2="'.$v_products_subimage2.
                                        '" ,products_bsubimage2="'.$v_products_bsubimage2.
                                        '" ,products_subimage3="'.$v_products_subimage3.
                                        '" ,products_bsubimage3="'.$v_products_bsubimage3;
*/

                        $query .= '", products_weight="'.$v_products_weight .
                                        '", products_tax_class_id="'.$v_tax_class_id . 
                                        '", products_date_available= ' . $v_date_avail .
                                        ', products_date_added= ' . $v_date_added .
                                        ', products_last_modified=CURRENT_TIMESTAMP
                                        , products_quantity="' . $v_products_quantity .  
                                        '", products_free_shipping="' . $v_products_free_shipping .  
                                        '", manufacturers_id=' . $v_manufacturer_id . 
                                        ', products_status=' . $v_db_status . '
                                        WHERE
                                                (products_id = "'. $v_products_id . '")';

                        $result = tep_db_query($query);
                }

                // added by splautz to ensure special price is updated
                if (isset($v_specials_new_products_price) || isset($v_specials_status) || isset($v_specials_expires_date)) {
                  $specials_query = tep_db_query("select * from " . TABLE_SPECIALS . " where products_id = '" . (int)$v_products_id . "'");
                  if ($specials = tep_db_fetch_array($specials_query)) $specials_id = $specials['specials_id'];
                  else $specials_id = NULL;
                  if (!isset($v_specials_new_products_price)) $v_specials_new_products_price = $specials_id?$specials['specials_new_products_price']:'';
                  if (!isset($v_specials_status) || $v_specials_status === '') $v_specials_status = $specials_id?$specials['status']:'1';
                  if (!isset($v_specials_expires_date)) $v_specials_expires_date = $specials_id?$specials['expires_date']:'';

                  if ($v_specials_new_products_price === '') {
                    if ($specials_id) tep_db_query("delete from " . TABLE_SPECIALS . " where specials_id = '" . $specials_id . "'");
                  } elseif ($specials_id) {
                    tep_db_query("update " . TABLE_SPECIALS . " set specials_new_products_price = '" . tep_db_input($v_specials_new_products_price) . "', specials_last_modified = now(), expires_date = '" . tep_db_input($v_specials_expires_date) . "', status = '" . tep_db_input($v_specials_status) . "' where specials_id = '" . $specials_id . "'");
                  } else {
                    tep_db_query("insert into " . TABLE_SPECIALS . " (products_id, specials_new_products_price, specials_date_added, expires_date, status) values ('" . (int)$v_products_id . "', '" . tep_db_input($v_specials_new_products_price) . "', now(), '" . tep_db_input($v_specials_expires_date) . "', '" . tep_db_input($v_specials_status) . "')");
                  }
                }

                // the following is common in both the updating an existing product and creating a new product
                if (is_array($v_products_name)){
                        foreach( $v_products_name as $key => $name){
                                                        if ($name!=''){
                                        $sql = "SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE
                                                        products_id = $v_products_id AND
                                                        language_id = " . $key;
                                        $result = tep_db_query($sql);
                                        $row = tep_db_fetch_array($result);

                                        // added by splautz to ensure TABLE_SEO_SURLS is updated
                                        if (isset($row['products_surls_id']) && is_numeric($surls_id=$row['products_surls_id'])) {
                                          if ($v_surls_name[$key]) {
                                            if (!tep_check_dup_surl(false, $surls_id, str_replace("'","''",tep_db_prepare_input($v_surls_name[$key])))) {
                                              $sql_data_array = array('surls_name' => str_replace("'","''",tep_db_prepare_input($v_surls_name[$key])));
                                              tep_db_perform(TABLE_SEO_URLS, $sql_data_array, 'update', "surls_id = '" . (int)$surls_id . "'");
                                            } else echo "<font color=red>DUP SURL: {$v_surls_name[$key]} </font>";
			                              } else {
                                            tep_remove_surl($surls_id);
                                            $surls_id = NULL;
                                          }
		                                } elseif ($v_surls_name[$key]) {
                                          if (tep_check_dup_surl(false, '', str_replace("'","''",tep_db_prepare_input($v_surls_name[$key])), 'product_info.php', 'products_id=' . (int)$v_products_id, $key)) {  // dup exists
                                            echo "<font color=red>DUP SURL: {$v_surls_name[$key]} </font>";
                                            $surls_id = NULL;
                                          } else {  // dup not found, ok to insert
                                            $sql_data_array = array('surls_name' => str_replace("'","''",tep_db_prepare_input($v_surls_name[$key])),
                                              'surls_script' => 'product_info.php',
                                              'surls_param' => 'products_id=' . (int)$v_products_id,
                                              'language_id' => $key);
                                            tep_db_perform(TABLE_SEO_URLS, $sql_data_array);
                                            $surls_id = tep_db_insert_id();
                                          }
		                                } else $surls_id = NULL;

                                        if (!$row) { // (tep_db_num_rows($result) == 0) {
                                                // nope, this is a new product description
                                                /* $sql =
                                                        "INSERT INTO ".TABLE_PRODUCTS_DESCRIPTION."
                                                                (products_id,
                                                                language_id,
                                                                products_name,
                                                                products_description,
                                                                products_url)
                                                                VALUES (
                                                                        '" . $v_products_id . "',
                                                                        " . $key . ",
                                                                        '" . $name . "',
                                                                        '". $v_products_description[$key] . "',
                                                                        '". $v_products_url[$key] . "'
                                                                        )";
                                                // support for Linda's Header Controller 2.0
                                                if (isset($v_products_head_title_tag)){
                                                        // override the sql if we're using Linda's contrib */
                                                        $sql =
                                                                "INSERT INTO ".TABLE_PRODUCTS_DESCRIPTION."
                                                                        (products_id,
                                                                        language_id,
                                                                        products_name,
                                                                        products_info,
                                                                        products_description,
                                                                        products_url,
                                                                        products_head_title_tag,
                                                                        products_head_desc_tag,
                                                                        products_head_keywords_tag,
                                                                        products_h1,
                                                                        products_surls_id,
                                                                        products_img_alt,
                                                                        products_affiliate_url)
                                                                        VALUES (
                                                                                '" . $v_products_id . "',
                                                                                " . $key . ",
                                                                                '" . str_replace("'","''",tep_db_prepare_input($name)) . "',
                                                                                '". str_replace("'","''",tep_db_prepare_input($v_products_info[$key])) . "',
                                                                                '". str_replace("'","''",tep_db_prepare_input($v_products_description[$key])) . "',
                                                                                '". $v_products_url[$key] . "',
                                                                                '". str_replace("'","''",tep_db_prepare_input($v_products_head_title_tag[$key])) . "',
                                                                                '". str_replace("'","''",tep_db_prepare_input($v_products_head_desc_tag[$key])) . "',
                                                                                '". str_replace("'","''",tep_db_prepare_input($v_products_head_keywords_tag[$key])) . "',
                                                                                '". str_replace("'","''",tep_db_prepare_input($v_products_h1[$key])) . "',
                                                                                 ". ($surls_id===NULL?'NULL':"'$surls_id'") . ",
                                                                                '". str_replace("'","''",$v_products_img_alt[$key]) . "',
                                                                                '". $v_products_affiliate_url[$key] . "')";
                                                /* }
                                                // end support for Linda's Header Controller 2.0 */
                                                $result = tep_db_query($sql);
                                        } else {
                                                // already in the description, let's just update it
                                                /* $sql =
                                                        "UPDATE ".TABLE_PRODUCTS_DESCRIPTION." SET
                                                                products_name='$name',
                                                                products_description='".$v_products_description[$key] . "',
                                                                products_url='" . $v_products_url[$key] . "'
                                                        WHERE
                                                                products_id = '$v_products_id' AND
                                                                language_id = '$key'";
                                                // support for Lindas Header Controller 2.0
                                                if (isset($v_products_head_title_tag)){
                                                        // override the sql if we're using Linda's contrib */
														$v_products_description = str_replace("'","''",$v_products_description[$key]);
                                                        $sql =
                                                                "UPDATE ".TABLE_PRODUCTS_DESCRIPTION." SET
                                                                        products_name = '".str_replace("'","''",tep_db_prepare_input($name))."',
                                                                        products_info = '".str_replace("'","''",tep_db_prepare_input($v_products_info[$key])) . "',
                                                                        products_description = '".$v_products_description . "',
                                                                        products_url = '" . $v_products_url[$key] ."',
                                                                        products_head_title_tag = '" . str_replace("'","''",tep_db_prepare_input($v_products_head_title_tag[$key])) ."',
                                                                        products_head_desc_tag = '" . str_replace("'","''",tep_db_prepare_input($v_products_head_desc_tag[$key])) ."',
                                                                        products_head_keywords_tag = '" . str_replace("'","''",tep_db_prepare_input($v_products_head_keywords_tag[$key])) ."',
                                                                        products_h1 = '" . str_replace("'","''",tep_db_prepare_input($v_products_h1[$key])) ."',
                                                                        products_surls_id = " . ($surls_id===NULL?'NULL':"'$surls_id'") .",
                                                                        products_img_alt = '" . str_replace("'","''",$v_products_img_alt[$key]) ."',
                                                                        products_affiliate_url = '" . $v_products_affiliate_url[$key] ."'
                                                                WHERE
                                                                        products_id = '$v_products_id' AND
                                                                        language_id = '$key'";
                                                /* }
                                                // end support for Linda's Header Controller 2.0 */
                                                $result = tep_db_query($sql);
                                        }
                                }
                        }
                }
                if (isset($v_categories_id)){
                        //find out if this product is listed in the category given
                        $result_incategory = tep_db_query('SELECT
                                                '.TABLE_PRODUCTS_TO_CATEGORIES.'.products_id,
                                                '.TABLE_PRODUCTS_TO_CATEGORIES.'.categories_id
                                                FROM
                                                        '.TABLE_PRODUCTS_TO_CATEGORIES.'
                                                WHERE
                                                '.TABLE_PRODUCTS_TO_CATEGORIES.'.products_id='.$v_products_id.' AND
                                                '.TABLE_PRODUCTS_TO_CATEGORIES.'.categories_id='.$v_categories_id);

                        if (tep_db_num_rows($result_incategory) == 0) {
                                // nope, this is a new category for this product
                                $res1 = tep_db_query('INSERT INTO '.TABLE_PRODUCTS_TO_CATEGORIES.' (products_id, categories_id, sort_order)
                                                        VALUES ("' . $v_products_id . '", "' . $v_categories_id . '", NULL)');
                        } else {
                                // already in this category, nothing to do!
                        }
                }
                // for the separate prices per customer module
                $ll=1;

                if (isset($v_customer_price_1)){
                        
                        if (($v_customer_group_id_1 == '') AND ($v_customer_price_1 != ''))  {
                                echo "<font color=red>ERROR - v_customer_group_id and v_customer_price must occur in pairs</font>";
                                die();
                        }
                        // they spec'd some prices, so clear all existing entries
                        $result = tep_db_query('
                                                DELETE
                                                FROM
                                                        '.TABLE_PRODUCTS_GROUPS.'
                                                WHERE
                                                        products_id = ' . $v_products_id
                                                );
                        // and insert the new record
                        if ($v_customer_price_1 != ''){
                                $result = tep_db_query('
                                                        INSERT INTO
                                                                '.TABLE_PRODUCTS_GROUPS.'
                                                        VALUES
                                                        (
                                                                ' . $v_customer_group_id_1 . ',
                                                                ' . $v_customer_price_1 . ',
                                                                ' . $v_products_id . '
                                                                )'
                                                        );
                        }
                        if ($v_customer_price_2 != ''){
                                $result = tep_db_query('
                                                        INSERT INTO
                                                                '.TABLE_PRODUCTS_GROUPS.'
                                                        VALUES
                                                        (
                                                                ' . $v_customer_group_id_2 . ',
                                                                ' . $v_customer_price_2 . ',
                                                                ' . $v_products_id . '
                                                                )'
                                                        );
                        }
                        if ($v_customer_price_3 != ''){
                                $result = tep_db_query('
                                                        INSERT INTO
                                                                '.TABLE_PRODUCTS_GROUPS.'
                                                        VALUES
                                                        (
                                                                ' . $v_customer_group_id_3 . ',
                                                                ' . $v_customer_price_3 . ',
                                                                ' . $v_products_id . '
                                                                )'
                                                        );
                        }
                        if ($v_customer_price_4 != ''){
                                $result = tep_db_query('
                                                        INSERT INTO
                                                                '.TABLE_PRODUCTS_GROUPS.'
                                                        VALUES
                                                        (
                                                                ' . $v_customer_group_id_4 . ',
                                                                ' . $v_customer_price_4 . ',
                                                                ' . $v_products_id . '
                                                                )'
                                                        );
                        }

                }

                // VJ product attribs begin
                if (isset($v_attribute_options_id_1)){
                        $attribute_rows = 1; // master row count

                        $languages = tep_get_languages();

                        // product options count
                        $attribute_options_count = 1;
                        $v_attribute_options_id_var = 'v_attribute_options_id_' . $attribute_options_count;

                        while (isset($$v_attribute_options_id_var) && !empty($$v_attribute_options_id_var)) {
                                // remove product attribute options linked to this product before proceeding further
                                // this is useful for removing attributes linked to a product
                                $attributes_clean_query = "delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$v_products_id . "' and options_id = '" . (int)$$v_attribute_options_id_var . "'";

                                tep_db_query($attributes_clean_query);

                                $attribute_options_query = "select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$$v_attribute_options_id_var . "'";

                                $attribute_options_values = tep_db_query($attribute_options_query);

                                // option table update begin
                                if ($attribute_rows == 1) {
                                        // insert into options table if no option exists
                                        if (tep_db_num_rows($attribute_options_values) <= 0) {
                                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                                        $lid = $languages[$i]['id'];

                                                  $v_attribute_options_name_var = 'v_attribute_options_name_' . $attribute_options_count . '_' . $lid;

                                                        if (isset($$v_attribute_options_name_var)) {
                                                                $attribute_options_insert_query = "insert into " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, language_id, products_options_name) values ('" . (int)$$v_attribute_options_id_var . "', '" . (int)$lid . "', '" . $$v_attribute_options_name_var . "')";

                                                                $attribute_options_insert = tep_db_query($attribute_options_insert_query);
                                                        }
                                                }
                                        } else { // update options table, if options already exists
                                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                                        $lid = $languages[$i]['id'];

                                                        $v_attribute_options_name_var = 'v_attribute_options_name_' . $attribute_options_count . '_' . $lid;

                                                        if (isset($$v_attribute_options_name_var)) {
                                                                $attribute_options_update_lang_query = "select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$$v_attribute_options_id_var . "' and language_id ='" . (int)$lid . "'";

                                                                $attribute_options_update_lang_values = tep_db_query($attribute_options_update_lang_query);

                                                                // if option name doesn't exist for particular language, insert value
                                                                if (tep_db_num_rows($attribute_options_update_lang_values) <= 0) {
                                                                        $attribute_options_lang_insert_query = "insert into " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, language_id, products_options_name) values ('" . (int)$$v_attribute_options_id_var . "', '" . (int)$lid . "', '" . $$v_attribute_options_name_var . "')";

                                                                        $attribute_options_lang_insert = tep_db_query($attribute_options_lang_insert_query);
                                                                } else { // if option name exists for particular language, update table
                                                                        $attribute_options_update_query = "update " . TABLE_PRODUCTS_OPTIONS . " set products_options_name = '" . $$v_attribute_options_name_var . "' where products_options_id ='" . (int)$$v_attribute_options_id_var . "' and language_id = '" . (int)$lid . "'";

                                                                        $attribute_options_update = tep_db_query($attribute_options_update_query);
                                                                }
                                                        }
                                                }
                                        }
                                }
                                // option table update end

                                // product option values count
                                $attribute_values_count = 1;
                                $v_attribute_values_id_var = 'v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count;

                                while (isset($$v_attribute_values_id_var) && !empty($$v_attribute_values_id_var)) {
                                        $attribute_values_query = "select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$$v_attribute_values_id_var . "'";

                                        $attribute_values_values = tep_db_query($attribute_values_query);

                                        // options_values table update begin
                                        if ($attribute_rows == 1) {
                                                // insert into options_values table if no option exists
                                                if (tep_db_num_rows($attribute_values_values) <= 0) {
                                                        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                                                $lid = $languages[$i]['id'];

                                                                $v_attribute_values_name_var = 'v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $lid;

                                                                if (isset($$v_attribute_values_name_var)) {
                                                                        $attribute_values_insert_query = "insert into " . TABLE_PRODUCTS_OPTIONS_VALUES . " (products_options_values_id, language_id, products_options_values_name) values ('" . (int)$$v_attribute_values_id_var . "', '" . (int)$lid . "', '" . $$v_attribute_values_name_var . "')";

                                                                        $attribute_values_insert = tep_db_query($attribute_values_insert_query);
                                                                }
                                                        }


                                                        // insert values to pov2po table
                                                        $attribute_values_pov2po_query = "insert into " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " (products_options_id, products_options_values_id) values ('" . (int)$$v_attribute_options_id_var . "', '" . (int)$$v_attribute_values_id_var . "')";

                                                        $attribute_values_pov2po = tep_db_query($attribute_values_pov2po_query);
                                                } else { // update options table, if options already exists
                                                        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                                                $lid = $languages[$i]['id'];

                                                                $v_attribute_values_name_var = 'v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $lid;

                                                                if (isset($$v_attribute_values_name_var)) {
                                                                        $attribute_values_update_lang_query = "select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$$v_attribute_values_id_var . "' and language_id ='" . (int)$lid . "'";

                                                                        $attribute_values_update_lang_values = tep_db_query($attribute_values_update_lang_query);

                                                                        // if options_values name doesn't exist for particular language, insert value
                                                                        if (tep_db_num_rows($attribute_values_update_lang_values) <= 0) {
                                                                                $attribute_values_lang_insert_query = "insert into " . TABLE_PRODUCTS_OPTIONS_VALUES . " (products_options_values_id, language_id, products_options_values_name) values ('" . (int)$$v_attribute_values_id_var . "', '" . (int)$lid . "', '" . $$v_attribute_values_name_var . "')";

                                                                                $attribute_values_lang_insert = tep_db_query($attribute_values_lang_insert_query);
                                                                        } else { // if options_values name exists for particular language, update table
                                                                                $attribute_values_update_query = "update " . TABLE_PRODUCTS_OPTIONS_VALUES . " set products_options_values_name = '" . $$v_attribute_values_name_var . "' where products_options_values_id ='" . (int)$$v_attribute_values_id_var . "' and language_id = '" . (int)$lid . "'";

                                                                                $attribute_values_update = tep_db_query($attribute_values_update_query);
                                                                        }
                                                                }
                                                        }
                                                }
                                        }
                                        // options_values table update end

                                        // options_values price update begin
                                  $v_attribute_values_price_var = 'v_attribute_values_price_' . $attribute_options_count . '_' . $attribute_values_count;

                                        if (isset($$v_attribute_values_price_var) && ($$v_attribute_values_price_var != '')) {
                                                $attribute_prices_query = "select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$v_products_id . "' and options_id ='" . (int)$$v_attribute_options_id_var . "' and options_values_id = '" . (int)$$v_attribute_values_id_var . "'";

                                                $attribute_prices_values = tep_db_query($attribute_prices_query);

                                                $attribute_values_price_prefix = ($$v_attribute_values_price_var < 0) ? '-' : '+';

                                                // options_values_prices table update begin
                                                // insert into options_values_prices table if no price exists
                                                if (tep_db_num_rows($attribute_prices_values) <= 0) {
                                                        $attribute_prices_insert_query = "insert into " . TABLE_PRODUCTS_ATTRIBUTES . " (products_id, options_id, options_values_id, options_values_price, price_prefix) values ('" . (int)$v_products_id . "', '" . (int)$$v_attribute_options_id_var . "', '" . (int)$$v_attribute_values_id_var . "', '" . (float)$$v_attribute_values_price_var . "', '" . $attribute_values_price_prefix . "')";

                                                        $attribute_prices_insert = tep_db_query($attribute_prices_insert_query);
                                                } else { // update options table, if options already exists
                                                        $attribute_prices_update_query = "update " . TABLE_PRODUCTS_ATTRIBUTES . " set options_values_price = '" . $$v_attribute_values_price_var . "', price_prefix = '" . $attribute_values_price_prefix . "' where products_id = '" . (int)$v_products_id . "' and options_id = '" . (int)$$v_attribute_options_id_var . "' and options_values_id ='" . (int)$$v_attribute_values_id_var . "'";

                                                        $attribute_prices_update = tep_db_query($attribute_prices_update_query);
                                                }
                                        }
                                        // options_values price update end

//////// attributes stock add start
                $v_attribute_values_stock_var = 'v_attribute_values_stock_' . $attribute_options_count . '_' . $attribute_values_count;

                if (isset($$v_attribute_values_stock_var) && ($$v_attribute_values_stock_var != '')) {
        
                $stock_attributes = $$v_attribute_options_id_var.'-'.$$v_attribute_values_id_var;
                
                $attribute_stock_query = tep_db_query("select products_stock_quantity from " . TABLE_PRODUCTS_STOCK . " where products_id = '" . (int)$v_products_id . "' and products_stock_attributes ='" . $stock_attributes . "'");                
                
                // insert into products_stock_quantity table if no stock exists
                if (tep_db_num_rows($attribute_stock_query) <= 0) {
                        $attribute_stock_insert_query =tep_db_query("insert into " . TABLE_PRODUCTS_STOCK . " (products_id, products_stock_attributes, products_stock_quantity) values ('" . (int)$v_products_id . "', '" . $stock_attributes . "', '" . (int)$$v_attribute_values_stock_var . "')");
                                
                } else { // update options table, if options already exists
                        $attribute_stock_insert_query = tep_db_query("update " . TABLE_PRODUCTS_STOCK. " set products_stock_quantity = '" . (int)$$v_attribute_values_stock_var . "' where products_id = '" . (int)$v_products_id . "' and products_stock_attributes = '" . $stock_attributes . "'");
                                            
                        // turn on stock tracking on products_options table
                    $stock_tracking_query = tep_db_query("update " . TABLE_PRODUCTS_OPTIONS . " set products_options_track_stock = '1' where products_options_id = '" . (int)$$v_attribute_options_id_var . "'");
                
                }
        }
//////// attributes stock add end                                        
                                      
                                        $attribute_values_count++;
                                        $v_attribute_values_id_var = 'v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count;
                                }

                                $attribute_options_count++;
                                $v_attribute_options_id_var = 'v_attribute_options_id_' . $attribute_options_count;
                        }

                        $attribute_rows++;
                }
                // VJ product attribs end

        } else {
                // this record was missing the product_model
                array_walk($items, 'print_el');
                echo "<p class=smallText>No products_model field in record. This line was not imported <br>";
                echo "<br>";
        }
// end of row insertion code
  } // end of EP for extra filed 
}


function ep_datoriser($date_time) {
	global $ep_date_format; // d-m-y etc..
	global $ep_raw_time; // user's prefered time (eg for specials to start) if no time in upload
	
	$raw_date_exist = preg_match("/^([0-2]0[0-9]{2}-[0-1][0-9]-[0-3][0-9])( [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$)?/", $date_time);
	if (!$raw_date_exist) {
		// not raw... we can only assume it is an excel date..
		// separate dates from times
		$exist_time = preg_match("/^2?0?[0-9]?[0-9][\.\/-][0-3]?[0-9][\.\/-]2?0?[0-9]?[0-9] ([0-2]?[0-9]:[0-5][0-9]).*$/", $date_time, $excel_time); // no seconds..
		$exist_date = preg_match("/^(2?0?[0-9]?[0-9][\.\/-][0-3]?[0-9][\.\/-]2?0?[0-9]?[0-9]).*$/", $date_time, $excel_date);
		//echo $excel_time[1] . '<br >';
		//echo $excel_date[1] . '<br ><br />';
		// if (!zen_not_null($exist_date)) // we fail to get a date! error msg rqd, and/or substitute action??
		
		// check for which of 3 possible date separators we have..
		// this sucks, I know... but it works for now
		if (zen_not_null(strpos($excel_date[1], '-'))) $separator = '-';
		if (zen_not_null(strpos($excel_date[1], '.'))) $separator = '.';
		if (zen_not_null(strpos($excel_date[1], '/'))) $separator = '/';
		
		//echo 'separator is: ' . $separator . '<br />';
		$format_bits = explode('-', $ep_date_format);
		$date_bits = explode($separator, $excel_date[1]);
		foreach ($format_bits as $key => $bit) {
			$$bit = $date_bits[$key]; // $y = 05 or 2005, $m = 09 or 9, $d = 03 or 3 for eg. Can only work if d,m,y order from excel is same as config
			$$bit = strlen($$bit) < 2 ? '0' . $$bit : $$bit; // 4 is now 04 for eg. - expand this as a rudimentary check - should never occur on $y var
			$$bit = strlen($$bit) > 2 ? substr($$bit,-2, 2) : $$bit; // 2005 is now 05 - expand this as a rudimentary check - should only occur on $y var
			//echo $$bit . '<br />';
			// another rudimentary check could be for $m vals > 12 = error too!
		}
		// create default raw time... if user left space off, put it on..
		if (substr($ep_raw_time,0, 1) != ' ') $ep_raw_time = ' ' . $ep_raw_time;
		// is it really a raw time? if not, make it midnight..
		$exist_raw_time = preg_match("/ [0-2][0-9]:[0-5][0-9]:[0-5][0-9]/", $ep_raw_time); // true if is raw time
		$ep_raw_time = zen_not_null($exist_raw_time) ? $ep_raw_time : ' 00:00:00';
		
		// if time supplied from excel, use it instead..
		$ep_raw_time = zen_not_null($exist_time) ? ' ' . $excel_time[1] : $ep_raw_time;
		
		//echo '<br />'.$ep_raw_time . '<br />';
		$raw_date = '20' . $y . '-' . $m . '-' . $d . $ep_raw_time; // needs updating at the end of the century ;-)
		//echo $raw_date . '<br /><br />';
	} else {
		// the date is raw, so return it
		$raw_date = $date_time;
		//echo $date . ' is raw...<br />';
	}
	return $raw_date;
}

function write_debug_log($string) {
	global $ep_debug_log_path;
	$logFile = $ep_debug_log_path . 'ep_debug_log.txt';
  $fp = fopen($logFile,'ab');
  fwrite($fp, $string);
  fclose($fp);
  return;
}

function ep_query($query) {
	global $ep_debug_logging;
	global $ep_debug_logging_all;
	global $ep_stack_sql_error;
	$result = mysql_query($query);
	if (mysql_errno()) {
		$ep_stack_sql_error = true;
		echo "<BR> MySQL error ".mysql_errno().": ".mysql_error()."\nWhen executing:\n$query\n <BR>";
		if ($ep_debug_logging == true) {
			// langer - will add time & date..
			$string = "MySQL error ".mysql_errno().": ".mysql_error()."\nWhen executing:\n$query\n";
			write_debug_log($string);
		}
	} elseif ($ep_debug_logging_all == true) {
		$string = "MySQL PASSED\nWhen executing:\n$query\n";
		write_debug_log($string);
	}
	return $result;
}


require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>






		

