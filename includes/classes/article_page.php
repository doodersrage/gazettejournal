<?PHP
class article_content {

// get article main image
function main_article_image($image_id) {
global $embed;

$articles_query = mysql_query("SELECT image, caption FROM articles_images WHERE articles_images_id = '".$image_id."' ORDER BY sort_order ASC LIMIT 0,1;");
$articles_result = mysql_fetch_array($articles_query);

$article_head_image = '<div class="articles_head_image"><table width="'.get_image_area_width($articles_result['image'],ARTICLE_IMAGE_WIDTH,ARTICLE_IMAGE_HEIGHT).'"><tr><td>' . LB .
$embed->determine_media_type($articles_result['image'],ARTICLE_IMAGE_WIDTH,ARTICLE_IMAGE_HEIGHT,'','article_head_image').'</td></tr>'.(!empty($articles_result['caption']) ? '<tr><td>' . LB . $articles_result['caption'] . '</td></tr>' : '').'</table></div>' . LB;

return $article_head_image;
}

// get article header image
function head_image($articles_id) {
global $embed;

$articles_query = mysql_query("SELECT image, caption FROM articles_images WHERE articles_id = '".$articles_id."' ORDER BY sort_order ASC LIMIT 0,1;");
$articles_result = mysql_fetch_array($articles_query);

if (mysql_num_rows($articles_query) > 0) {
$article_head_image = '<div class="articles_head_image"><table width="'.get_image_area_width($articles_result['image'],ARTICLE_IMAGE_WIDTH,ARTICLE_IMAGE_HEIGHT).'"><tr><td>' . LB .
$embed->determine_media_type($articles_result['image'],ARTICLE_IMAGE_WIDTH,ARTICLE_IMAGE_HEIGHT,'','article_head_image').'</td></tr>'.(!empty($articles_result['caption']) ? '<tr><td>' . LB . $articles_result['caption'] . '</td></tr>' : '').'</table></div>' . LB;
}

return $article_head_image;
}

// get other article images
function other_article_images($articles_id,$main_image_id = '') {
global $embed;

if (!empty($main_image_id)) {
$not_in = "and articles_images_id <> '" . $main_image_id ."' ";
$limit_str = "";
} else {
$limit_str = " LIMIT 1,100";
$not_in = "";
}

$articles_query = mysql_query("SELECT image, caption FROM articles_images WHERE articles_id = '".$articles_id."' ".$not_in."ORDER BY sort_order ASC".$limit_str.";");

if (mysql_num_rows($articles_query) > 0) {
$articles_images = '<div class="articles_extra_images">' . LB;
while ($articles_result = mysql_fetch_array($articles_query)) {

$articles_images .= '<table width="'.get_image_area_width($articles_result['image'],ARTICLE_IMAGE_WIDTH,ARTICLE_IMAGE_HEIGHT).'"><tr><td>' . LB .
$embed->determine_media_type($articles_result['image'],ARTICLE_IMAGE_WIDTH,ARTICLE_IMAGE_HEIGHT,'','article_head_image').'</td></tr>'.(!empty($articles_result['caption']) ? '<tr><td>' . LB . $articles_result['caption'] . '</td></tr>' : '').'</table>' . LB;
}

$articles_images .= '</div>' . LB;
}

return $articles_images;
}

// populate article content area
function article_content_area($articles_id,$printer_friendly = '') {
$articles_query = mysql_query("SELECT a.articles_id, a.title, a.author, a.summary, a.content, a.homepage_image FROM articles a WHERE a.articles_id = '".$articles_id."';");
$articles_result = mysql_fetch_array($articles_query);

if (!empty($articles_result['homepage_image'])) {
$head_image = $this->main_article_image($articles_result['homepage_image']);
$body_images = $this->other_article_images($articles_id,$articles_result['homepage_image']);
} else {
$head_image = $this->head_image($articles_id);
$body_images = $this->other_article_images($articles_id);
}

$articles_title = "<div class=\"style14\">" . $articles_result['title'] . "</div>";

$articles_author = (!empty($articles_result['author']) ? "<div class=\"style16\">by " . $articles_result['author'] . "</div>" : "");

$article_email_lnk = (empty($printer_friendly) ? "<div class=\"email_box\">" . LB . 
"<a href=\"" . SITE_ADDRESS . 'email-to-friend-id-' . $articles_result['articles_id'] . "/\"><img src=\"images/email.jpg\" border=\"0\" alt=\"Email a friend\">Email To A Friend</a><br>" . LB . 
"<a href=\"" . SITE_ADDRESS . 'printer_friendly.php?artid=' . $articles_result['articles_id'] . "\" target=\"_blank\"><img src=\"images/printer.jpg\" border=\"0\" alt=\"View printer friendly page\">Printer Friendly View</a>" . LB . 
"</div>" . LB : "" );

$content_area = $articles_title . "\n\r" . $articles_author . "\n\r" . $head_image . "\n\r" . $article_email_lnk . "\n\r" . $body_images . $articles_result['content'] . "\n\r" ;

return $content_area;
}

// print category listings for found article
function print_article_category_listing($articles_id) {
global $category_listings;

//$categories_list = '<strong>Categories</strong><br>';

$categories_query = mysql_query("SELECT categories_id FROM articles_to_categories WHERE articles_id = '".$articles_id."';");
while ($categories_result = mysql_fetch_array($categories_query)) {

$categories_list .= $category_listings->categories_listings($categories_result['categories_id'],''/*ARTICLE_COUNT_ARTICLE_PAGE_LIMIT*/);
$categories_list .= '<br>' . LB;
}

return $categories_list;
}

}
?>