<?PHP
// This document stores any store related functions

// prints the header navigation used on all storefront pages
function print_store_header() {
$store_header = "<div class=\"store_man_header\"><a href=\"".SECURE_SITE_ADDRESS."store/\">Browse</a> ".(!session_check($_SESSION['store_sessionid'],$_SESSION['store_username']) ? "| <a href=\"".SECURE_SITE_ADDRESS."store/store_login.php\">Login</a> " : "| <a href=\"".SECURE_SITE_ADDRESS."store/store_login.php?logout_user=1\">Log Out</a> | <a href=\"".SECURE_SITE_ADDRESS."store/manage_account.php\">Manage Account</a> | <a href=\"".SECURE_SITE_ADDRESS."store/previous_orders.php\">Previous Orders</a> ")."| <a href=\"".SECURE_SITE_ADDRESS."store/checkout.php\">Checkout</a></div>";

return $store_header;
}

// set store item price based on customers zip
function set_item_price($def_price,$ext_price = '') {
if (!session_check($_SESSION['store_sessionid'],$_SESSION['store_username'])) {
$item_price = $def_price;
} else {
$customer_zip_query = mysql_query("SELECT zip FROM store_customers_address WHERE store_customers_id = '".$_SESSION['store_customers_id']."' ;");
$customer_zip_result = mysql_fetch_array($customer_zip_query);
$customer_zip = $customer_zip_result['zip'];
$local_zip_query = mysql_query("SELECT * FROM zip_codes WHERE zipcode = '".$customer_zip."';");
if (mysql_num_rows($local_zip_query) > 0) $item_price = $def_price; else $item_price = ($ext_price > 0 ? $ext_price : $def_price);
}

return $item_price;
}

// finds which state has been assigned by the customer
function get_customers_state($customers_id) {
$state_query = mysql_query("SELECT state FROM store_customers_address WHERE store_customers_id = '".$customers_id."';");
$state_result = mysql_fetch_array($state_query);

return $state_result['state'];
}

// checks for errors on the payment form
function payment_error_check($ccname = '',$ccnum = '',$month = '',$year = '',$cccvv = '') {
$error_cnt = 0;

if (empty($ccname)) $error_cnt++;
if (empty($ccnum)) $error_cnt++;
if (empty($month)) $error_cnt++;
if (empty($year)) $error_cnt++;
//if (empty($cccvv)) $error_cnt++;

return $error_cnt;
}

// process new order
function process_order($ccname,$ccnum,$month,$year,$cccvv='',$total,$tax = '',$sub_total = '',$order_comments = '',$gift_address = '') {
global $db;

$customer_query = mysql_query("SELECT username, email, fname, mi, lname, shipping_address, billing_address ,phone FROM store_customers WHERE store_customers_id = '".$_SESSION['store_customers_id']."';");
$customer_result = mysql_fetch_array($customer_query);

$customer_name = $customer_result['fname'] . ' ' . (!empty($customer_result['mi']) ? $customer_result['mi'] . ' ' : '') . $customer_result['lname'];

$values = array($_SESSION['store_customers_id'],$customer_name,$customer_result['shipping_address'],$customer_result['billing_address'],$tax,$sub_total,$total,$ccname,$ccnum,$month . "/" . $year,$cccvv,$order_comments,$gift_address);
$sql = "INSERT INTO orders (order_date,store_customers_id,customer_name,shipping_address,billing_address,order_status,shipping,tax,subtotal,total,payment_type,ccname,ccnum,cc_exp_date,cvv,order_comments,gift_address) VALUES (NOW(),?,?,?,?,'1','0',?,?,?,'CC',?,?,?,?,?,?);";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

$new_order_id = mysql_insert_id();

foreach ($_SESSION['cart'] as $id => $cur_cart) {
# disabled upon client request - writes classifieds order to old classifieds system and enables it by set date
#if (!empty($cur_cart['sub_category'])) {
#$current_day = date("l");
#if ($current_day == 'Tuesday') {
#$start_date = date("Y-m-d");
#$end_date = date('Y-m-d', strtotime('+'.$cur_cart['weeks'].' weeks'));
#} else {
#$start_date = date("Y-m-d", strtotime('next Tuesday'));
#$end_date = date('Y-m-d', strtotime('+'.$cur_cart['weeks'].' weeks',strtotime('next Tuesday')));
#}

#$values = array($start_date,$end_date,(!empty($cur_cart['new_ad_title']) ? clean_db_inserts($cur_cart['new_ad_title']) : clean_db_inserts(get_classifieds_category_name($cur_cart['sub_category']))),$cur_cart['new_ad'],$cur_cart['sub_category'],$_SESSION['store_customers_id'],$new_order_id);
#$sql = "INSERT INTO listings (start_date,exp_date,section_id,title,content,added,modified,sub_id,store_customers_id,orders_id) VALUES (?,?,1,?,?,NOW(),NOW(),?,?,?);";
#$sth = $db->prepare($sql);
#$res = $db->execute($sth,$values);

#$new_listing_id = mysql_insert_id();
#} else {
#$new_listing_id = '';
#}

$values = array($new_order_id,$cur_cart['item_id'],$cur_cart['item_name'],$cur_cart['item_cost'],$cur_cart['weeks'],$cur_cart['sub_category'],$cur_cart['new_ad'],$cur_cart['word_count'],$cur_cart['new_ad_title'],$new_listing_id);
$sql = "INSERT INTO order_items (orders_id,item_id,name,price,weeks,sub_cat,new_ad,word_count,new_ad_title,listing_id) VALUES (?,?,?,?,?,?,?,?,?,?);";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);
}

$email_content = EMAIL_PURCHASE . print_order_invoice($order_comments,$gift_address);

// send customer email including invoice
$subject = "Your Gloucester Gazette order has been received.";
send_email($customer_result['email'],CONTACT_EMAIL,$subject,'',$email_content);

// send email to stores set contact email address
$subject = "Gloucester Gazette order from - " . $customer_result['email'];
send_email(CONTACT_EMAIL,CONTACT_EMAIL,$subject,'',$email_content);

// remove cart session
unset($_SESSION['cart']);

header('Location: '.SITE_ADDRESS.'store/order_complete.php');

}

// prints month drop down
function print_month_select($selected_month) {

$month_array = array(
'1' => 'January',
'2' => 'February',
'3' => 'March',
'4' => 'April',
'5' => 'May',
'6' => 'June',
'7' => 'July',
'8' => 'August',
'9' => 'September',
'10' => 'October',
'11' => 'November',
'12' => 'December',
);

$select_box = "<select name=\"month\"> 
<option value=\"\" selected=\"selected\">Select a Month</option> \r\n";
foreach ($month_array as $ini => $name) {
$select_box .= "<option value=\"".$ini."\" ".($selected_month == $ini ? "selected" : ""). ">".$name."</option> \r\n";
}
$select_box .= "</select>";

return $select_box;
}

// prints expiration year drop down
function print_exp_year_select($selected_year) {

$select_box = "<select name=\"year\"> 
<option value=\"\" selected=\"selected\">Select a year</option> \r\n";
for ($i = (int)date("Y"); $i <= (int)(date("Y") + 5); $i++) {
$select_box .= "<option value=\"".$i."\" ".($selected_year == $i ? "selected" : ""). ">".$i."</option> \r\n";
}
$select_box .= "</select>";

return $select_box;
}

// prints state select drop down
function print_state_select($selected_state) {

$state_array = array(
'AL' => 'Alabama',
'AK' => 'Alaska',
'AZ' => 'Arizona',
'AR' => 'Arkansas',
'CA' => 'California',
'CO' => 'Colorado',
'CT' => 'Connecticut',
'DE' => 'Delaware',
'DC' => 'District Of Columbia',
'FL' => 'Florida',
'GA' => 'Georgia',
'HI' => 'Hawaii',
'ID' => 'Idaho',
'IL' => 'Illinois',
'IN' => 'Indiana',
'IA' => 'Iowa',
'KS' => 'Kansas',
'KY' => 'Kentucky',
'LA' => 'Louisiana',
'ME' => 'Maine',
'MD' => 'Maryland',
'MA' => 'Massachusetts',
'MI' => 'Michigan',
'MN' => 'Minnesota',
'MS' => 'Mississippi',
'MO' => 'Missouri',
'MT' => 'Montana',
'NE' => 'Nebraska',
'NV' => 'Nevada',
'NH' => 'New Hampshire',
'NJ' => 'New Jersey',
'NM' => 'New Mexico',
'NY' => 'New York',
'NC' => 'North Carolina',
'ND' => 'North Dakota',
'OH' => 'Ohio',
'OK' => 'Oklahoma',
'OR' => 'Oregon',
'PA' => 'Pennsylvania',
'RI' => 'Rhode Island',
'SC' => 'South Carolina',
'SD' => 'South Dakota',
'TN' => 'Tennessee',
'TX' => 'Texas',
'UT' => 'Utah',
'VT' => 'Vermont',
'VA' => 'Virginia',
'WA' => 'Washington',
'WV' => 'West Virginia',
'WI' => 'Wisconsin',
'WY' => 'Wyoming'
);

$select_box = "<select name=\"state\" limit=\"7\"> 
<option value=\"\" selected=\"selected\">Select a State</option> \r\n";
foreach ($state_array as $ini => $name) {
$select_box .= "<option value=\"".$ini."\" ".($selected_state == $ini ? "selected" : ""). ">".$name."</option> \r\n";
}
$select_box .= "</select>";

return $select_box;
}

// pring billing information
function get_billing_information($address_id) {

$address_query = mysql_query("SELECT address1, address2, city, state, zip FROM store_customers_address WHERE store_customers_address_id = '".$address_id."';");
$address_result = mysql_fetch_array($address_query);

$address_output = '<table class="address_output">
					<tr>
						<td>' .
						$address_result['address1']
						. '</td>
					</tr>
					<tr>
						<td>' .
						$address_result['address2']
						. '</td>
					</tr>
					<tr>
						<td>' .
						$address_result['city'] . ', ' . $address_result['state'] . ' ' . $address_result['zip']
						. '</td>
					</tr>
					</table>';
					
return $address_output;
}

function createPassword($length) {
	$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$i = 0;
	$password = "";
	while ($i <= $length) {
		$password .= $chars{mt_rand(0,strlen($chars))};
		$i++;
	}
	return $password;
}

function print_order_invoice($order_comments = '',$gift_address = '') {
// print shipping and billing information
$customer_query = mysql_query("SELECT fname, mi, lname, shipping_address, billing_address, email, phone FROM store_customers WHERE store_customers_id = '".$_SESSION['store_customers_id']."';");
$customer_result = mysql_fetch_array($customer_query);

$page_content .= '<table width="100%"><tr><td>';

$page_content .= '<table width="100%"><tr><td class="cart_header">';
$page_content .= '<strong>Billing Address:</strong>';
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $customer_result['fname'] . ' ' . $customer_result['mi'] . ' ' . $customer_result['lname'];
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $customer_result['phone'];
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $customer_result['email'];
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= get_billing_information($customer_result['billing_address']);
$page_content .= '</td></tr></table>';

$page_content .= '</td><td>';

$page_content .= '<table width="100%"><tr><td class="cart_header">';
$page_content .= '<strong>Shipping Address:</strong>';
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $customer_result['fname'] . ' ' . $customer_result['mi'] . ' ' . $customer_result['lname'];
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $customer_result['phone'];
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $customer_result['email'];
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= get_billing_information($customer_result['shipping_address']);
$page_content .= '</td></tr></table>';

$page_content .= '</td></tr></table>';

$page_content .= '<table width="100%"><tr><td align="left"><strong>Items in your cart:</strong></td></tr><tr><td>';

// if cart array is set print contents
$sub_total = 0;
if (isset($_SESSION['cart'])) {
$page_content .= '<table width="100%">
<tr class="cart_header"><td width="60" class="cart_header">Price</td><td class="cart_header">Name</td><td class="cart_header">Info</td></tr>';

foreach ($_SESSION['cart'] as $id => $cur_cart) {
$sub_total += $cur_cart['item_cost'];

$page_content .= '<tr class="cart_list"><td width="60">$' . sprintf ('%0.2f', round($cur_cart['item_cost'],2)) . '</td><td>' . $cur_cart['item_name'] . '</td><td>' . $cur_cart['new_ad'] . '</td></tr>';
}
$page_content .= '</table>';
$page_content .= '<table align="right" width="200">';
$page_content .= '<tr><td class="cart_header">Sub-Total</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . sprintf ('%0.2f', round($sub_total,2)) . '</td></tr>';
if (get_customers_state($_SESSION['store_customers_id']) == 'VA' && ENABLE_ORDER_TAX == 1) {
$page_content .= '<tr><td class="cart_header">Tax</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . sprintf ('%0.2f', (round($sub_total,2)*0.05)) .'</td></tr>';
$page_content .= '<tr><td class="cart_header">Total</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . sprintf ('%0.2f', (round($sub_total,2)*0.05)+$sub_total) .'</td></tr>';
} else {
$page_content .= '<tr><td class="cart_header">Total</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . sprintf ('%0.2f', round($sub_total,2)) .'</td></tr>';
}
$page_content .= '</table>';
}

$page_content .= '</td></tr></table>';

if (!empty($gift_address)) $page_content .= '<br><strong>Gift Address</strong><br>'.str_replace("\n","<br>",$gift_address);

$page_content .= '<br><strong>Order Comments</strong><br>'.$order_comments;


return $page_content;
}

function get_classifieds_category_name($classifieds_id) {
$classifieds_cat_qry = mysql_query("SELECT name FROM classifieds_categories WHERE classifieds_cat_id = '".$classifieds_id."';");
$classifieds_cat_result = mysql_fetch_array($classifieds_cat_qry);

return $classifieds_cat_result['name'];
}
?>