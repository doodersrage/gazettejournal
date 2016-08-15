<?PHP
class article_content {

// get article main image
function main_article_image($image_id) {
$articles_query = mysql_query("SELECT image, caption FROM articles_images WHERE articles_images_id = '".$image_id."' ORDER BY sort_order ASC LIMIT 0,1;");
$articles_result = mysql_fetch_array($articles_query);

$article_head_image = '<div class="articles_head_image"><img src="images/'.$articles_result['image'].'" width="'.ARTICLE_IMAGE_WIDTH.'" height="'.ARTICLE_IMAGE_HEIGHT.'" align="left" class="article_head_image">'.(!empty($articles_result['caption']) ? '<br>' . $articles_result['caption'] : '').'</div>';

return $article_head_image;
}

// get article header image
function head_image($articles_id) {
$articles_query = mysql_query("SELECT image, caption FROM articles_images WHERE articles_id = '".$articles_id."' ORDER BY sort_order ASC LIMIT 0,1;");
$articles_result = mysql_fetch_array($articles_query);

if (mysql_num_rows($articles_query) > 0) {
$article_head_image = '<div class="articles_head_image"><img src="images/'.$articles_result['image'].'" width="'.ARTICLE_IMAGE_WIDTH.'" height="'.ARTICLE_IMAGE_HEIGHT.'" align="left" class="article_head_image">'.(!empty($articles_result['caption']) ? '<br>' . $articles_result['caption'] : '').'</div>';
}

return $article_head_image;
}

// get other article images
function other_article_images($articles_id,$main_image_id = '') {
if (!empty($main_image_id)) {
$not_in = "and articles_images_id <> '" . $main_image_id ."' ";
$limit_str = "";
} else {
$limit_str = " LIMIT 1,100";
$not_in = "";
}

$articles_query = mysql_query("SELECT image, caption FROM articles_images WHERE articles_id = '".$articles_id."' ".$not_in."ORDER BY sort_order ASC".$limit_str.";");

if (mysql_num_rows($articles_query) > 0) {
$articles_images = '<div class="articles_extra_images">';
while ($articles_result = mysql_fetch_array($articles_query)) {

$articles_images .= '<img src="images/'.$articles_result['image'].'" width="'.ARTICLE_IMAGE_WIDTH.'" height="'.ARTICLE_IMAGE_HEIGHT.'"  class="article_head_image">'.(!empty($articles_result['caption']) ? '<br>' . $articles_result['caption'] : '');
}

$articles_images .= '</div>';
}

return $articles_images;
}

// populate article content area
function article_content_area($articles_id) {
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
$articles_author = "<div class=\"style16\">BY " . $articles_result['author'] . "</div>";
$content_area = $articles_title . "\n\r" . $articles_author . "\n\r" . $head_image . $body_images . $articles_result['content'] . "\n\r" ;

return $content_area;
}

// print category listings for found article
function print_article_category_listing($articles_id) {
global $category_listings;

$categories_list = '<strong>Categories</strong><br>';

$categories_query = mysql_query("SELECT categories_id FROM articles_to_categories WHERE articles_id = '".$articles_id."';");
while ($categories_result = mysql_fetch_array($categories_query)) {

$categories_list .= $category_listings->categories_listings($categories_result['categories_id'],ARTICLE_COUNT_ARTICLE_PAGE_LIMIT);
$categories_list .= '<br>';
}

return $categories_list;
}

}
?>