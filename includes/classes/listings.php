<?PHP

class listings_page {

// get listing image
function prepare_listing_image($image_name = '') {
global $embed;

if (!empty($image_name)) {
$listing_image = $embed->determine_media_type($image_name,ARTICLE_IMAGE_WIDTH,ARTICLE_IMAGE_HEIGHT,'left','article_head_image');
} else $listing_image = '';

return $listing_image;
}

// get listing content
function get_listings_content($listings_id) {
global $htmlfunctions;

$listings_query = mysql_query("SELECT title, image, content, section_id, event_date, source, sub_id FROM listings WHERE customers_articles_id = '".$listings_id."';");
$listings_result = mysql_fetch_array($listings_query);

$listings_title = "<div class=\"style14\">" . $listings_result['title'] . "</div>";

if ($listings_result['section_id'] == 2 || $listings_result['section_id'] == 3) {
$purchase_box = "<div class=\"email_box\"><form name=\"form1\" id=\"form1\" method=\"post\" action=\"".SECURE_SITE_ADDRESS."store/add_to_cart.php\">" . "\r\n" .
			  $htmlfunctions->text_field('total_cost_val','','total_cost_val',($listings_result['sub_id'] == 3 ? 3 : 5),'hidden') . "\r\n" .
			  $htmlfunctions->text_field('items_id','','items_id',($listings_result['sub_id'] == 3 ? 7 : 6),'hidden') . "\r\n" .
			  $htmlfunctions->text_field('items_name','','items_name',($listings_result['section_id'] == 2 ? $listings_result['sub_id'] == 3 ? 'Birth' : 'Wedding' : 'Obituary') . ' Transcript - ' . $listings_result['title'] . ' Date: ' . out_put_date_string($listings_result['event_date']) . ' Source: ' . $listings_result['source'],'hidden') . "\r\n" .
"<input type=\"submit\" name=\"Submit\" value=\"Purchase Transcript\" /></form></div>";
}

$content_area = $listings_title . "\n\r" . $this->prepare_listing_image($listings_result['image']) . "\n\r" . $listings_result['content'] . "\n\r" . ($listings_result['section_id'] == 2 || $listings_result['section_id'] == 3 ? '<br>Date: ' . out_put_date_string($listings_result['event_date']) . '<br>Source: ' . $listings_result['source'] :  '') . "\r\n" . $purchase_box . "\n\r";

return $content_area;
}

// get list of listings
function get_list_listings($listing_id) {
global $category_listings;

$category_query = mysql_query("SELECT section_id, sub_id FROM listings WHERE customers_articles_id = '".$listing_id."';");
$category_result = mysql_fetch_array($category_query);

$listing_string = $category_listings->list_listings($category_result['section_id'],$category_result['sub_id']);

return $listing_string;
}

}

?>