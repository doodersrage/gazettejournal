<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';


if (isset($_SESSION['store_sessionid'])) {

//vars
$total_cost_val = $_POST['total_cost_val'];
$items_id = $_POST['items_id'];
$items_name = str_replace(array("\'",'\"'),array("'",'"'),$_POST['items_name']);
$weeks = $_POST['weeks'];
$sub_category = $_POST['sub_category'];
$new_ad = str_replace(array("\'",'\"'),array("'",'"'),$_POST['new_ad']);
$tot_word_count = $_POST['tot_word_count'];
$new_ad_title = str_replace(array("\'",'\"'),array("'",'"'),$_POST['new_ad_title']);

// check for existing cart session
if (!isset($_SESSION['cart'])) {
$_SESSION['cart'] = array();
}

// add item to shopping cart
if ($items_id) {
array_push($_SESSION['cart'], array('item_id' => $items_id, 'item_name' => $items_name, 'item_cost' => $total_cost_val, 'weeks' => $weeks, 'sub_category' => $sub_category, 'new_ad' => $new_ad, 'word_count' => $tot_word_count, 'new_ad_title' => $new_ad_title));
}
}

header('Location: checkout.php');
?>