<?PHP
require 'includes/application_top.php';

// load category listing class
require INCLUDES_DIRECTORY.'/classes/category_listings.php';
$category_listings = new category_listings();

// load article class
require INCLUDES_DIRECTORY.'/classes/article_page.php';
$article_page = new article_content();

// get page vars
$articles_id = $_GET['artid'];

$content = $template->two_column($article_page->print_article_category_listing($articles_id),$article_page->article_content_area($articles_id));

$search_box = print_search_box();

// load template file
require INCLUDES_DIRECTORY.'/templates/inside_pages.php';
?>
