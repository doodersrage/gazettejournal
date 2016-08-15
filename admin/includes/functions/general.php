<?PHP
// build top categories menu
function build_categories_menu() {
$category_query = mysql_query("SELECT categories_id, name FROM categories WHERE hide_category = 0 ORDER BY sort_order ASC, name ASC ;");

while ($category_result = mysql_fetch_array($category_query)) {
$categories_menu .= ' <a href="'.SITE_ADDRESS. preg_replace("/[^A-Za-z0-9]/", "-", strtolower($category_result['name'])) . '-catid-'.$category_result['categories_id'].'/">'.$category_result['name'].'</a> |';
}

return $categories_menu;
}

// write categories file to constant

function update_categories_file($new_menu) {
$myFile = ADMIN_INCLUDES_DIRECTORY . "/top_nav.php";

$fh = fopen($myFile, 'w') or die("can't open file");

$stringData =  "<?PHP" . "\r\n";
$stringData .= "define('TOP_NAVIGATION_STRING','".str_replace(array("\'","'"),"\'",$new_menu)."');" . "\r\n";
$stringData .=  "?>";

fwrite($fh, $stringData);
fclose($fh);
}

// combine two different arrays
function my_array_combine($a, $b)
{
   if(!is_array($a) or !is_array($b) or !count($a) or !count($b))
   {
      user_error(__FUNCTION__ . ': non-array or empty array supplied as parameter');
      return(FALSE);
   }
   if(count($a) != count($b))
   {
      user_error(__FUNCTION__ . ': empty array supplied as parameter');
   }
   $result = array();
   while(($key = each($a)) && ($val = each($b)))
   {
      $result[$key[1]] = $val[1];
   }
   return($result);
} 

// clean db strings for insertion
function clean_db_inserts ($dbstring) {
$replace_strings = array("'");
$replaced_with = array("''");

$new_string = str_replace($replace_strings,$replaced_with,$dbstring);

return $new_string;
}

// write breadcrumb listings
function brc($links) {
$htmlfunctions = new htmlfunctions();

$link_cnt = count($links);
$cur_link = 0;

foreach ($links as $name => $link) {
$cur_link++;
$bcstring .= $htmlfunctions->write_link($link,$name) . ($cur_link < $link_cnt ? ' &rarr; ' : '');
}

return $bcstring;
}

// store exp and start dates in proper format
function clean_date_string($date) {
$new_date_string = explode('/',$date);

$proper_format = $new_date_string[2] . '-' . $new_date_string[0] . '-' . $new_date_string[1];

return $proper_format;
}
// store exp and start dates in proper format
function clean_date_string_weddings_import($date,$source = '') {
$new_date_string = explode('/',$date);

$proper_format = $new_date_string[2] . '-' . $new_date_string[0] . '-' . $new_date_string[1];

return $proper_format;
}

// configure date for output
function out_put_date_string($date) {
$new_date_string = explode('-',$date);

$proper_format =  $new_date_string[1] . '/' . $new_date_string[2] . '/' . $new_date_string[0];

return $proper_format;
}

// parse array data for db insertion
function parse_array_data($array_val,$no_encaps = '') {
$parse_data = '';

foreach ($array_val as $value) {
$rec_count++;
if ($rec_count != 1) {
$parse_data .= "," .  ($no_encaps == 1 ? '' : "'") . clean_db_inserts($value) . ($no_encaps == 1 ? '' : "'");
} else {
$parse_data .= ($no_encaps == 1 ? '' : "'").clean_db_inserts($value) . ($no_encaps == 1 ? '' : "'");
}

}
return $parse_data;
}

//update last updated string for front page display
function update_last_updated() {
mysql_query("UPDATE store_settings SET value = NOW() WHERE constant = 'SITE_LAST_UPDATED';");
update_constants_file();
}

// set orders status text
function set_order_status($status_id) {
	  switch($status_id) {
	  case 1:
	  $product_status = 'Processing';
	  break;
	  case 2:
	  $product_status = 'Successful';
	  break;
	  case 3:
	  $product_status = 'Failed';
	  break;
	  }

return $product_status;
}

// pull customers name
function get_customers_name($customers_id) {
$customer_qry = mysql_query("SELECT fname, mi, lname FROM store_customers WHERE store_customers_id = '".$customers_id."';");
$customer_result = mysql_fetch_array($customer_qry);

$customers_name = '<a href="'.ADMIN_ADDRESS.'store-admin/customers-edit.php?mode=edit&userid='.$customers_id.'">'.$customer_result['fname'] . ' ' . $customer_result['mi'] . ' ' . $customer_result['lname'].'</a>';

return $customers_name;
}

function clear_article_session_data() {
unset($_SESSION['title']);
unset($_SESSION['author']);
unset($_SESSION['summary']);
unset($_SESSION['content']);
}

?>