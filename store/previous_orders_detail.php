<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

if (!isset($_SESSION['store_sessionid'])) header('Location: ./');

//vars
$orderid = $_GET['orderid'];

$order_query = mysql_query("SELECT tax, subtotal, total, order_comments FROM orders WHERE orders_id = '".$orderid."';");
$order_result = mysql_fetch_array($order_query);

$tax = $order_result['tax'];
$subtotal = $order_result['subtotal'];
$total = $order_result['total'];
$order_comments = $order_result['order_comments'];

$cur_row = 0;
$page_content = "<div class=\"store_header\">Previous Order Detail</div>";
$page_content .= print_store_header();

// print shipping and billing information
$orders_address_query = mysql_query("SELECT customer_name, shipping_address, billing_address FROM orders WHERE orders_id = '".$orderid."';");
$orders_address_result = mysql_fetch_array($orders_address_query);

$page_content .= '<table width="100%"><tr><td>';

$page_content .= '<table width="100%"><tr><td class="cart_header">';
$page_content .= 'Billing Address:';
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $orders_address_result['customer_name'];
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= get_billing_information($orders_address_result['billing_address']);
$page_content .= '</td></tr></table>';

$page_content .= '</td><td>';

$page_content .= '<table width="100%"><tr><td class="cart_header">';
$page_content .= 'Shipping Address:';
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= $orders_address_result['customer_name'];
$page_content .= '</td></tr><tr><td class="cart_list">';
$page_content .= get_billing_information($orders_address_result['shipping_address']);
$page_content .= '</td></tr></table>';

$page_content .= '</td></tr></table>';


// if cart array is set print contents
$sub_total = 0;
$page_content .= '<table width="100%">
<tr class="cart_header"><td width="60" class="cart_header">Price</td><td class="cart_header">Name</td><td class="cart_header">Info</td></tr>';

$order_item_query = mysql_query("SELECT name, price, new_ad FROM order_items WHERE orders_id = '".$orderid."';");
while ($order_item_result = mysql_fetch_array($order_item_query)) {
$page_content .= '<tr class="cart_list"><td width="60">$' . sprintf('%0.2f', round($order_item_result['price'],2)) . '</td><td>' . $order_item_result['name'] . '</td><td>' . $order_item_result['new_ad'] . '</td></tr>';
}
$page_content .= '</table>';
$page_content .= '<form name="form1" id="form1" method="post" action="">';
$page_content .= '<table align="right" width="200">';
$page_content .= '<tr><td class="cart_header">Sub-Total</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . $subtotal . '</td></tr>';
if (ENABLE_ORDER_TAX == 1) {
$page_content .= '<tr><td class="cart_header">Tax</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . $tax .'</td></tr>';
}
$page_content .= '<tr><td class="cart_header">Total</td></tr>';
$page_content .= '<tr><td class="cart_list" align="center">$' . $total .'</td></tr>';

$page_content .= '</table>';
$page_content .= '</form>';

$page_content .= '</td></tr></table>';

$page_content .= '<br><strong>Order Comments</strong><br>'.$order_comments;

$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>