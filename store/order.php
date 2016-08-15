<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

if (!isset($_SESSION['store_sessionid'])) header('Location: ./');

//vars
$submit_order = $_POST['submit_order'];
$ccname = $_POST['ccname'];
$ccnum = $_POST['ccnum'];
$month = $_POST['month'];
$year = $_POST['year'];
$cccvv = $_POST['cccvv'];
$total = $_POST['total'];
$tax = $_POST['tax'];
$sub_total = $_POST['sub_total'];
$order_comments = $_POST['order_comments'];
$gift_address = $_POST['gift_address'];

if ($submit_order == 1) {
if (payment_error_check($ccname,$ccnum,$month,$year,$cccvv) == 0) {
process_order($ccname,$ccnum,$month,$year,$cccvv,$total,$tax,$sub_total,$order_comments,$gift_address);
} else {
$message = '<script language="javascript">alert(\'Your payment information does not appear to be correct.\n Please review it and try submitting your order again.\');</script>';
}
}

$cur_row = 0;
$page_content = "<div class=\"store_header\">Order</div>";
$page_content .= print_store_header();
if (!isset($_SESSION['store_sessionid'])) {
$page_content .= 'You must first login before adding items to your cart.';
} else {
// print shipping and billing information
$customer_query = mysql_query("SELECT fname, mi, lname, shipping_address, billing_address FROM store_customers WHERE store_customers_id = '".$_SESSION['store_customers_id']."';");
$customer_result = mysql_fetch_array($customer_query);

$page_content .= '<table width="100%"><tr><td>';

$page_content .= '<table width="100%"><tr><td class="cart_header">';
$page_content .= 'Billing Address:';
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $customer_result['fname'] . ' ' . $customer_result['mi'] . ' ' . $customer_result['lname'];
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= get_billing_information($customer_result['billing_address']);
$page_content .= '</td></tr></table>';

$page_content .= '</td><td>';

$page_content .= '<table width="100%"><tr><td class="cart_header">';
$page_content .= 'Shipping Address:';
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $customer_result['fname'] . ' ' . $customer_result['mi'] . ' ' . $customer_result['lname'];
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
$page_content .= '<form name="form1" id="form1" method="post" action="">';
$page_content .= '<table width="100%"><tr><td valign="top">';

$page_content .= '<table><tr><td>';

$page_content .= '<table align="left">';
$page_content .= '<tr><td class="cart_header" colspan="2">Order Comments:</td></tr>';
$page_content .= '<td class="cart_list_payment">'.$htmlfunctions->textarea('order_comments',60,3,$order_comments,'order_comments').'</td></tr>';
$page_content .= '</table>';

$page_content .= '</td></tr><tr><td>';

$page_content .= '<table align="left">';
$page_content .= '<tr><td class="cart_header" colspan="2">Gift This Order(Enter Address Below):</td></tr>';
$page_content .= '<td class="cart_list_payment">'.$htmlfunctions->textarea('gift_address',40,3,$gift_address,'gift_address').'</td></tr>';
$page_content .= '</table>';

$page_content .= '</td></tr><tr><td>';

$page_content .= '<table align="left">';
$page_content .= '<tr><td class="cart_header" colspan="2">Payment Information:</td></tr>';
$page_content .= '<tr><td class="cart_header_payment">Name on Credit Card:</td>';
$page_content .= '<td class="cart_list_payment">'.$htmlfunctions->text_field('ccname',20,'ccname',$ccname,'text').'</td></tr>';
$page_content .= '<tr><td class="cart_header_payment">Credit Card Number:</td>';
$page_content .= '<td class="cart_list_payment">'.$htmlfunctions->text_field('ccnum',20,'ccnum',$ccnum,'text').'</td></tr>';
$page_content .= '<tr><td class="cart_header_payment">Experation Date:</td>';
$page_content .= '<td class="cart_list_payment">'.print_month_select($month) . ' / ' . print_exp_year_select($year) .'</td></tr>';
//$page_content .= '<tr><td class="cart_header_payment">CVV:</td>';
//$page_content .= '<td class="cart_list_payment">'.$htmlfunctions->text_field('cccvv',4,'cccvv',$cccvv,'text','',4).'</td></tr>';
$page_content .= '<tr><td class="cart_header" colspan="2"><input type="submit" name="Submit" value="Submit Order" />'.
$htmlfunctions->text_field('submit_order','','submit_order',1,'hidden') . 
$htmlfunctions->text_field('sub_total','','sub_total',sprintf('%0.2f', round($sub_total,2)),'hidden') . 
$htmlfunctions->text_field('total','','total',(get_customers_state($_SESSION['store_customers_id']) == 'VA' && ENABLE_ORDER_TAX == 1 ? sprintf ('%0.2f', (round($sub_total,2)*0.05)+$sub_total) : sprintf ('%0.2f', round($sub_total,2)) ),'hidden').
(get_customers_state($_SESSION['store_customers_id']) == 'VA' && ENABLE_ORDER_TAX == 1 ? $htmlfunctions->text_field('tax','','tax',sprintf('%0.2f', (round($sub_total,2)*0.05)) ,'hidden') : '' ) .
'</td></tr>';
$page_content .= '</table>';

$page_content .= '</td></tr></table>';

$page_content .= '</td><td valign="top">';
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
$page_content .= '</td></tr></table></form>';
}

$page_content .= '</td></tr></table>';
}
$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>