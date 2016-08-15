<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

if (!isset($_SESSION['store_sessionid'])) header('Location: ./');

$cur_row = 0;
$page_content = "<div class=\"store_header\">Previous Orders</div>";
$page_content .= print_store_header();

$orders_query = mysql_query("SELECT orders_id, order_date, total FROM orders WHERE store_customers_id = '".$_SESSION['store_customers_id']."' ORDER BY order_date DESC;");
if (mysql_num_rows($orders_query) > 0) {
$page_content .= '<strong>Previous Orders Found:</strong><br>';
while ($orders_result = mysql_fetch_array($orders_query)) {
$page_content .= '<a href="'.SECURE_SITE_ADDRESS.'store/previous_orders_detail.php?orderid='.$orders_result['orders_id'].'">' . date("F j, Y, g:i a",strtotime($orders_result['order_date'])) . ' $' . $orders_result['total'] .'</a><br>';

}

}

$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>