<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

//vars
$cart = $_GET['cart'];
$update = $_POST['update'];
$delete_items = $_POST['delete_items'];
$checkout = $_POST['checkout'];

if ($checkout == 1) header('Location: '.SECURE_SITE_ADDRESS.'store/order.php');

if ($cart == 'clear') unset($_SESSION['cart']);


if ($update == 1 && !empty($delete_items)) {
foreach($delete_items as $del_items_id) {
unset($_SESSION['cart'][$del_items_id]);
}
}

$cur_row = 0;
$page_content = "<div class=\"store_header\">Checkout</div>";
$page_content .= print_store_header();
if (!isset($_SESSION['store_sessionid'])) {
$page_content .= 'You must first login before adding items to your cart.';
} else {

// print shipping and billing information
$customer_query = mysql_query("SELECT fname, mi, lname, shipping_address, billing_address FROM store_customers WHERE store_customers_id = '".$_SESSION['store_customers_id']."';");
$customer_result = mysql_fetch_array($customer_query);

if (!empty($customer_result['billing_address'])) {
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
}

$page_content .= '<table width="100%"><tr><td align="left"><strong>Items in your cart:</strong></td></tr><tr><td>';

// if cart array is set print contents
$sub_total = 0;
if (isset($_SESSION['cart'])) {
$page_content .= '<form name="form1" id="form1" method="post" action="">
<table width="100%">
<tr class="cart_header"><td width="60" class="cart_header">Price</td><td class="cart_header">Name</td><td class="cart_header">Info</td><td width="60" class="cart_header">Remove</td></tr>';

foreach ($_SESSION['cart'] as $id => $cur_cart) {
$sub_total += $cur_cart['item_cost'];

$page_content .= '<tr class="cart_list"><td width="60">$' . sprintf ('%0.2f', round($cur_cart['item_cost'],2)) . '</td><td>' . $cur_cart['item_name'] . '</td><td>' . $cur_cart['new_ad'] . '</td><td width="60" align="center">' . $htmlfunctions->checkbox('delete_items[]',$id,'delete_items[]','') . '</td></tr>';
}
$page_content .= '<tr class="cart_header"><td colspan="2"><a href="'.SECURE_SITE_ADDRESS.'store/checkout.php?cart=clear">Remove All Items From Cart</a></td><td colspan="2" align="right"><input type="submit" name="Submit" value="Update Cart" />'.$htmlfunctions->text_field('update','','update',1,'hidden').'</td></tr>';
$page_content .= '</table></form>';
$page_content .= '<form name="form1" id="form1" method="post" action="">';
$page_content .= '<table align="right" width="200">';
$page_content .= '<tr><td class="cart_header">Sub-Total</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . sprintf ('%0.2f', round($sub_total,2)) .'</td></tr>';
if (get_customers_state($_SESSION['store_customers_id']) == 'VA' && ENABLE_ORDER_TAX == 1) {
$page_content .= '<tr><td class="cart_header">Tax</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . sprintf ('%0.2f', (round($sub_total,2)*0.05)) .'</td></tr>';
$page_content .= '<tr><td class="cart_header">Total</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . sprintf ('%0.2f', (round($sub_total,2)*0.05)+$sub_total) .'</td></tr>';
} else {
$page_content .= '<tr><td class="cart_header">Total</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . sprintf ('%0.2f', round($sub_total,2)) .'</td></tr>';
}
$page_content .= '<tr><td class="cart_header"><input type="submit" name="Submit" value="Checkout" />'.$htmlfunctions->text_field('checkout','','checkout',1,'hidden').'</td></tr>';
$page_content .= '</table></form>';
}

$page_content .= '</td></tr></table>';
}
$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>