<?PHP
class home_page {

function build_homepage_query($article_id) {
$content_query = mysql_query("SELECT title, author, summary, content, read_more_link FROM articles WHERE articles_id = '".$article_id."';");
return $content_query;
}

function build_homepage_image_query($articles_images_id) {
$content_query = mysql_query("SELECT image FROM articles_images WHERE articles_images_id = '".$articles_images_id."';");

return $content_query;
}

// load left area content
function left_content($preview = '') {
global $embed;
// query for area content
$content_query = $this->build_homepage_query(HOMEPAGE_LEFT_ARTICLE);
$content_result = mysql_fetch_array($content_query);

//collect area content
$content_title = "<div class=\"article_title\">".$content_result['title']."</div>";
$content_author = (!empty($content_result['author']) ? "<div class=\"article_author\">by ".$content_result['author']."</div>" : "");
if (!empty($content_result['summary'])) $content_text = $content_result['summary']; else $content_text = $content_result['content'];
$content_text = (empty($preview) ? $content_text : substr($content_text,0,20));

// check for images
if (HOMEPAGE_LEFT_ARTICLE_IMAGE != 'none' || HOMEPAGE_LEFT_ARTICLE_IMAGE != null) {
$image_query = $this->build_homepage_image_query(HOMEPAGE_LEFT_ARTICLE_IMAGE);
$image_result = mysql_fetch_array($image_query);
$image_name = $image_result['image'];
$found_image = $embed->determine_media_type($image_result['image'],HOMEPAGE_IMAGE_WIDTH,HOMEPAGE_IMAGE_HEIGHT,HOMEPAGE_LEFT_ARTICLE_IMAGE_POSITION,'home_image');
}

$content_area = (!empty($image_name) ? print_article_link($content_result['title'],HOMEPAGE_IMAGE,$found_image) : '') . $content_title . "\n\r" . $content_author . "\n\r" . $content_text . "\n\r <br>" . (empty($preview) && empty($content_result['read_more_link']) ? print_article_link($content_result['title'],HOMEPAGE_LEFT_ARTICLE,'Click here to read more...') : "");

return $content_area;
}

// load center area content
function center_content($preview = '') {
global $embed;

$content_query = $this->build_homepage_query(HOMEPAGE_CENTER_ARTICLE);
$content_result = mysql_fetch_array($content_query);

$content_title = "<div class=\"article_title\">".$content_result['title']."</div>";
$content_author = (!empty($content_result['author']) ? "<div class=\"article_author\">by ".$content_result['author']."</div>" : "");
if (!empty($content_result['summary'])) $content_text = $content_result['summary']; else $content_text = $content_result['content'];
$content_text = (empty($preview) ? $content_text : substr($content_text,0,20));

// check for images
if (HOMEPAGE_CENTER_ARTICLE_IMAGE != 'none' || HOMEPAGE_CENTER_ARTICLE_IMAGE != null) {
$image_query = $this->build_homepage_image_query(HOMEPAGE_CENTER_ARTICLE_IMAGE);
$image_result = mysql_fetch_array($image_query);
$image_name = $image_result['image'];
$found_image = $embed->determine_media_type($image_result['image'],HOMEPAGE_IMAGE_WIDTH,HOMEPAGE_IMAGE_HEIGHT,HOMEPAGE_CENTER_ARTICLE_IMAGE_POSITION,'home_image');
}

$content_area = (!empty($image_name) ? print_article_link($content_result['title'],HOMEPAGE_IMAGE,$found_image) : '') . $content_title . "\n\r" . $content_author . "\n\r" . $content_text . "\n\r <br>" . (empty($preview) && empty($content_result['read_more_link']) ? print_article_link($content_result['title'],HOMEPAGE_CENTER_ARTICLE,'Click here to read more...') : "");
return $content_area;
}

// load right area content
function right_content($preview = '') {
global $embed;

$content_query = $this->build_homepage_query(HOMEPAGE_RIGHT_ARTICLE);
$content_result = mysql_fetch_array($content_query);

$content_title = "<div class=\"article_title\">".$content_result['title']."</div>";
$content_author = (!empty($content_result['author']) ? "<div class=\"article_author\">by ".$content_result['author']."</div>" : "");
if (!empty($content_result['summary'])) $content_text = $content_result['summary']; else $content_text = $content_result['content'];
$content_text = (empty($preview) ? $content_text : substr($content_text,0,20));

// check for images
if (HOMEPAGE_RIGHT_ARTICLE_IMAGE != 'none' || HOMEPAGE_RIGHT_ARTICLE_IMAGE != null) {
$image_query = $this->build_homepage_image_query(HOMEPAGE_RIGHT_ARTICLE_IMAGE);
$image_result = mysql_fetch_array($image_query);
$image_name = $image_result['image'];
$found_image = $embed->determine_media_type($image_result['image'],HOMEPAGE_IMAGE_WIDTH,HOMEPAGE_IMAGE_HEIGHT,HOMEPAGE_RIGHT_ARTICLE_IMAGE_POSITION,'home_image');
}

$content_area = (!empty($image_name) ? print_article_link($content_result['title'],HOMEPAGE_IMAGE,$found_image) : '') . $content_title . "\n\r" . $content_author . "\n\r" . $content_text . "\n\r <br>" . (empty($preview) && empty($content_result['read_more_link']) ? print_article_link($content_result['title'],HOMEPAGE_RIGHT_ARTICLE,'Click here to read more...') : "");

return $content_area;
}

// load right area content
function center_image() {
global $embed;

$content_query = $this->build_homepage_query(HOMEPAGE_IMAGE);
$content_result = mysql_fetch_array($content_query);

$content_title = "<div class=\"article_title\">".$content_result['title']."</div>";
$content_author = (!empty($content_result['author']) ? "<div class=\"article_author\">by ".$content_result['author']."</div>" : "");
if (!empty($content_result['summary'])) $content_text = $content_result['summary']; else $content_text = $content_result['content'];
$content_text = (empty($preview) ? $content_text : substr($content_text,0,20));

// check for images
if (HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE != 'none' || HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE != null) {
$image_query = $this->build_homepage_image_query(HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE);
$image_result = mysql_fetch_array($image_query);
$image_name = $image_result['image'];
$found_image = $embed->determine_media_type($image_result['image'],HOMEPAGE_IMAGE_WIDTH,HOMEPAGE_IMAGE_HEIGHT,HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE_POSITION,'home_image');
}

$content_area = (!empty($image_name) ? print_article_link($content_result['title'],HOMEPAGE_IMAGE,$found_image) : '') . $content_title . "\n\r" . $content_author . "\n\r" . $content_text . "\n\r <br>" . (empty($preview) && empty($content_result['read_more_link']) ? print_article_link($content_result['title'],HOMEPAGE_IMAGE,'Click here to read more...') : "");


// 4/14/2008 disabled upon request
#$content_query = mysql_query("SELECT homepage_image FROM articles WHERE articles_id = '".HOMEPAGE_IMAGE."';");
#$content_result = mysql_fetch_array($content_query);
#$content_image_query = mysql_query("SELECT image FROM articles_images WHERE articles_images_id = '".$content_result['homepage_image']."';");
#$content_image_result = mysql_fetch_array($content_image_query);

#$content_image = $embed->determine_media_type($content_image_result['image'],HOMEPAGE_IMAGE_WIDTH,HOMEPAGE_IMAGE_HEIGHT);

return $content_area;
}


}
?>