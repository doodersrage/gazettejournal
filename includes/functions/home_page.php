<?PHP
class home_page {

function build_homepage_query($article_id) {
$content_query = mysql_query("SELECT title, author, summary, content FROM articles WHERE articles_id = '".$article_id."';");
return $content_query;
}

// load left area content
function left_content() {
$content_query = $this->build_homepage_query(HOMEPAGE_LEFT_ARTICLE);
$content_result = mysql_fetch_array($content_query);

$content_title = "<div class=\"style14\">".$content_result['title']."</div>";
$content_author = "<div class=\"style16\">BY ".$content_result['author']."</div>";
if (!empty($content_result['summary'])) $content_text = $content_result['summary']; else $content_text = $content_result['content'];
$content_text = substr($content_text,0,LEFT_HOMEPAGE_CHARACTER_LIMIT);

$content_area = $content_title . "\n\r" . $content_author . "\n\r" . $content_text . "\n\r <br>" . print_article_link($content_result['title'],HOMEPAGE_LEFT_ARTICLE,'Click here to read more...');
return $content_area;
}

// load center area content
function center_content() {
$content_query = $this->build_homepage_query(HOMEPAGE_CENTER_ARTICLE);
$content_result = mysql_fetch_array($content_query);

$content_title = "<div class=\"style14\">".$content_result['title']."</div>";
$content_author = "<div class=\"style16\">BY ".$content_result['author']."</div>";
if (!empty($content_result['summary'])) $content_text = $content_result['summary']; else $content_text = $content_result['content'];
$content_text = substr($content_text,0,CENTER_HOMEPAGE_CHARACTER_LIMIT);

$content_area = $content_title . "\n\r" . $content_author . "\n\r" . $content_text . "\n\r <br>" . print_article_link($content_result['title'],HOMEPAGE_CENTER_ARTICLE,'Click here to read more...');
return $content_area;
}

// load right area content
function right_content() {
$content_query = $this->build_homepage_query(HOMEPAGE_RIGHT_ARTICLE);
$content_result = mysql_fetch_array($content_query);

$content_title = "<div class=\"style14\">".$content_result['title']."</div>";
$content_author = "<div class=\"style16\">BY ".$content_result['author']."</div>";
if (!empty($content_result['summary'])) $content_text = $content_result['summary']; else $content_text = $content_result['content'];
$content_text = substr($content_text,0,RIGHT_HOMEPAGE_CHARACTER_LIMIT);

$content_area = $content_title . "\n\r" . $content_author . "\n\r" . $content_text . "\n\r <br>" . print_article_link($content_result['title'],HOMEPAGE_RIGHT_ARTICLE,'Click here to read more...');
return $content_area;
}

// load right area content
function center_image() {
$content_query = mysql_query("SELECT homepage_image FROM articles WHERE articles_id = '".HOMEPAGE_IMAGE."';");
$content_result = mysql_fetch_array($content_query);
$content_image_query = mysql_query("SELECT image FROM articles_images WHERE articles_images_id = '".$content_result['homepage_image']."';");
$content_image_result = mysql_fetch_array($content_image_query);

$content_image = '<img src="images/'.$content_image_result['image'].'" height="'.HOMEPAGE_IMAGE_HEIGHT.'" width="'.HOMEPAGE_IMAGE_WIDTH.'">';
return $content_image;
}


}
?>