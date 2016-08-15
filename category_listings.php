<?PHP
require 'includes/application_top.php';
require INCLUDES_DIRECTORY.'/classes/category_listings.php';
$category_listings = new category_listings();

// set page vars
$categories_id = $_GET['catid'];
$listings_id = $_GET['listingid'];
$listing_sub = $_GET['listsubid'];


if (!empty($categories_id)) {
$left_column = $category_listings->categories_listings($categories_id);
$search_box = print_search_box();
} elseif (!empty($listings_id)) {
	$left_column = $category_listings->list_listings($listings_id);
$listings_id == 1 ? $search_box = print_classifieds_search_box() : $search_box = print_search_box();
} else {
	$left_column = $category_listings->top_news_listings();
$search_box = print_search_box();
}

if (!empty($categories_id)) {
	$right_column = $category_listings->categories_previews($categories_id);
} elseif (!empty($listings_id)) {
	$right_column = $category_listings->listings_preview($listings_id,$listing_sub);
} else {
	$right_column = $category_listings->top_news_listings_previews();
}

$content = $template->two_column($left_column,$right_column);

// load template file
require INCLUDES_DIRECTORY.'/templates/inside_pages.php';
?>
