<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

if (!isset($_SESSION['store_sessionid'])) header('Location: ./');

// print store items info
$store_query = mysql_query("SELECT items_id, name, price, image FROM items WHERE status = 1 ;");

$cur_row = 0;
$page_content = "<div class=\"store_header\">Order Completed.</div>";
$page_content .= print_store_header();
$page_content .= "<div>Your order has been processed. If you have any questions about your order please contact us.</div>";
$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>