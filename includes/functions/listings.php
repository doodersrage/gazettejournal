<?PHP

class listings_page {

function prepare_listing_image($image_name = '') {

if (!empty($image_name)) {
$listing_image = '<img src="images/'.$image_name.'" width="'.ARTICLE_IMAGE_WIDTH.'" height="'.ARTICLE_IMAGE_HEIGHT.'" align="left" class="article_head_image">';
} else $listing_image = '';

return $listing_image;
}

function get_listings_content($listings_id) {
$listings_query = mysql_query("SELECT title, image, content FROM listings WHERE customers_articles_id = '".$listings_id."';");
$listings_result = mysql_fetch_array($listings_query);

$listings_title = "<div class=\"style14\">" . $listings_result['title'] . "</div>";
$content_area = $listings_title . "\n\r" . $this->prepare_listing_image($listings_result['image']) . "\n\r" . $listings_result['content'] . "\n\r" ;

return $content_area;
}

function get_list_listings($listing_id) {
global $category_listings;

$category_query = mysql_query("SELECT section_id FROM listings WHERE customers_articles_id = '".$listing_id."';");
$category_result = mysql_fetch_array($category_query);

$listing_string = $category_listings->list_listings($category_result['section_id']);

return $listing_string;
}

}

?>