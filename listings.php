<?PHP
require 'includes/application_top.php';

// load category listing class
require INCLUDES_DIRECTORY.'/classes/category_listings.php';
$category_listings = new category_listings();

// load article class
require INCLUDES_DIRECTORY.'/classes/listings.php';
$listings_page = new listings_page();

// get page vars
$listing_id = $_GET['listings_id'];

$content = $template->fill_area($listings_page->get_listings_content($listing_id));

$search_box = print_classifieds_search_box();
// load template file
require INCLUDES_DIRECTORY.'/templates/inside_pages.php';
?>
