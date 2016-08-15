<?PHP
class category_listings {

// get top news listings
function top_news_listings() {
$category_listing = '';

$articles_query = mysql_query("SELECT articles_id, title FROM articles WHERE status not in (2,4) ORDER BY sort_order ASC, status DESC, modified ASC LIMIT 0,10;");

$cat_count = 0;
while ($articles_result = mysql_fetch_array($articles_query)) {
$cat_count++;
$category_listing = '<div class="category_link">' . $cat_count . '. ' . print_article_link($articles_result['title'],$articles_result['articles_id'],'',$articles_result['file_name']) . '</div>';
}

}

// get listing of top news previews
function top_news_listings_previews() {
$articles_query = mysql_query("SELECT a.articles_id, a.title, a.author, a.summary, a.homepage_image FROM articles a LEFT JOIN articles_to_categories atc ON atc.articles_id = a.articles_id WHERE a.status not in (2,4) ORDER BY a.sort_order ASC,a.modified, a.status DESC;");

$article_num = 0;

$previews_string = '<table width="100%" border="0">' . LB;
while ($articles_result = mysql_fetch_array($articles_query)) {
$article_num == 2 ? $article_num = 0 : '';

$previews_string .= '<tr><td>' . LB;
$article_num == 0 ? $previews_string .= '<table width="100%" border="0" class="article_preview">' . LB .
'<tr>' . LB .
'<td>' . LB .
'<div class="style14">'.$articles_result['title'].'</div>' . LB .
(!empty($articles_result['author']) ? '<div class="style16">by '.$articles_result['author'].'</div>' . LB : '') .
$articles_result['summary'] . LB .
'</td>' . LB .
($this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id'],$articles_result['title'],$articles_result['file_name']) ? '<td width="130px">' . LB .
$this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id'],$articles_result['title'],$articles_result['file_name']) . LB . 
'</td>' . LB : '') .
'</tr>' . LB .
'</table>' . LB
 : $previews_string .= '<table width="100%" border="0" class="article_preview">' . LB .
 '<tr>' . LB .
($this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id'],$articles_result['title'],$articles_result['file_name']) ? '<td width="130px">' . LB .
$this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id'],$articles_result['title'],$articles_result['file_name']) . LB . 
'</td>' . LB : '') .
  '<td>' . LB .
  '<div class="style14">' . LB .
  $articles_result['title'] . LB .
  '</div>' . LB .
(!empty($articles_result['author']) ? '<div class="style16">by '.$articles_result['author'].'</div>' . LB : '') .
  $articles_result['summary'] . LB .
  '</td>' . LB .
  '</tr>' . LB .
  '</table>' . LB;

$previews_string .= '</td>' . LB .
'</tr>' . LB;

$article_num++;
}
$previews_string .= '</table>' . LB;

return $previews_string;
}

// get assigned category name
function get_category_name($categories_id) {
$category_query = mysql_query("SELECT name FROM categories WHERE categories_id = '".$categories_id."';");
$category_result = mysql_fetch_array($category_query);

return $category_result['name'];
}

//get news listing for currently selected category
function categories_listings($categories_id,$limit_amount = '') {
$category_listing = '';

if (!empty($limit_amount)) $limit_string = " LIMIT 0,".$limit_amount;

$category_listing .= '<strong>' . $this->get_category_name($categories_id) . '</strong><br>' . LB;

$articles_query = mysql_query("SELECT a.articles_id, a.title, af.file_name FROM articles a LEFT JOIN articles_to_categories atc ON atc.articles_id = a.articles_id LEFT JOIN articles_files af ON af.articles_id = a.articles_id WHERE atc.categories_id = '".$categories_id."' AND a.status not in (2,4) ORDER BY a.sort_order ASC, a.modified DESC, a.status DESC ".$limit_string.";");

$cat_count = 0;
while ($articles_result = mysql_fetch_array($articles_query)) {
$cat_count++;
$category_listing .= '<div class="category_link">' . $cat_count . '. ' . print_article_link($articles_result['title'],$articles_result['articles_id'],'',$articles_result['file_name']) . '</div>';
}

return $category_listing;
}

// get articles preview image
function article_preview_image($image_id = '',$articles_id = '',$article_title = '',$article_file = '') {
global $embed;

$articles_images_query = mysql_query("SELECT image FROM articles_images WHERE articles_images_id = '".$image_id."';");
if (mysql_num_rows($articles_images_query) > 0) {
$articles_images_result = mysql_fetch_array($articles_images_query);
$article_image = $embed->determine_media_type($articles_images_result['image'],ARTICLE_LISTING_IMAGE_WIDTH,ARTICLE_LISTING_IMAGE_HEIGHT,'','',get_article_link($article_title,$articles_id,$article_file));
} else {
$articles_images_query = mysql_query("SELECT image FROM articles_images WHERE articles_id = '".$articles_id."' ORDER BY sort_order ASC LIMIT 0,1;");
if (mysql_num_rows($articles_images_query) > 0) {
$articles_images_result = mysql_fetch_array($articles_images_query);
$article_image = $embed->determine_media_type($articles_images_result['image'],ARTICLE_LISTING_IMAGE_WIDTH,ARTICLE_LISTING_IMAGE_HEIGHT,'','',get_article_link($article_title,$articles_id,$article_file));
} else {
$article_image = '';
}
}

return $article_image;
}

// get categories previews
function categories_previews($categories_id) {
$articles_query = mysql_query("SELECT a.articles_id, a.title, a.author, a.summary, a.homepage_image, af.file_name, a.read_more_link FROM articles a LEFT JOIN articles_to_categories atc ON atc.articles_id = a.articles_id LEFT JOIN articles_files af ON af.articles_id = a.articles_id WHERE atc.categories_id = '".$categories_id."' AND (af.file_name = '' OR af.file_name is null) and a.status not in (2,4) ORDER BY a.sort_order ASC, a.modified, a.status DESC;");

$article_num = 0;

$previews_string = '<table width="100%" border="0">';
while ($articles_result = mysql_fetch_array($articles_query)) {
$article_num == 2 ? $article_num = 0 : '';

$previews_string .= '<tr><td>';
$article_num == 0 ? $previews_string .= '<table width="100%" border="0" class="article_preview">' . LB .
'<tr>' . LB .
'<td>' . LB .
'<div class="style14">' . LB .
$articles_result['title'] . LB .
'</div>' . LB .
(!empty($articles_result['author']) ? '<div class="style16">by '.$articles_result['author'].'</div>' . LB : '') .
$articles_result['summary'] . '<br>' . LB . 
(empty($articles_result['read_more_link']) ? print_article_link($articles_result['title'],$articles_result['articles_id'],'Click here to read more...') : '').
'</td>' . LB .
($this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id'],$articles_result['title'],$articles_result['file_name']) ? '<td width="130px">' . LB .
$this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id'],$articles_result['title'],$articles_result['file_name']) . LB . 
'</td>' . LB : '') .
'</tr>' . LB .
'</table>' . LB : $previews_string .= '<table width="100%" border="0" class="article_preview">' . LB .
'<tr>' . LB .
($this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id'],$articles_result['title'],$articles_result['file_name']) ? '<td width="130px">' . LB .
$this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id'],$articles_result['title'],$articles_result['file_name']) . LB . 
'</td>' . LB : '') .
'<td>' . LB .
'<div class="style14">' . LB .
$articles_result['title'] . LB .
'</div>' . LB .
(!empty($articles_result['author']) ? '<div class="style16">by '.$articles_result['author'].'</div>' . LB : '') .
$articles_result['summary'] . '<br>' . LB . 
(empty($articles_result['read_more_link']) ? print_article_link($articles_result['title'],$articles_result['articles_id'],'Click here to read more...') : '') . 
'</td>' . LB .
'</tr>' . LB .
'</table>' . LB;

$previews_string .= '</td>' . LB .
'</tr>' . LB;

$article_num++;
}
$previews_string .= '</table>' . LB;

return $previews_string;
}

// pulls a list of available listings
function list_listings($listings_id,$listing_sub_id = '') {
$listings_output = '';

switch ($listings_id) {
case 1: 
$listings_output = '<strong>Classifieds</strong><br>' . LB;
$classified_sub_query = mysql_query("SELECT classifieds_cat_id, name FROM classifieds_categories ;");
while ($classified_result = mysql_fetch_array($classified_sub_query)) {
$listings_output .= '<div class="category_link">' . print_listing_cat_link($classified_result['name'],$classified_result['classifieds_cat_id'],$classified_result['name']).'</div>';
if ($_GET['listsubid'] == $classified_result['classifieds_cat_id'] || $listing_sub_id == $classified_result['classifieds_cat_id']) $listings_output .= $this->subcat_listings($listings_id,$classified_result['classifieds_cat_id']);
}
break;
case 2: 
$listings_output = '<strong>Events</strong><br>' . LB;
$events_sub_query = mysql_query("SELECT events_categories_id, name FROM events_categories ;");
while ($events_result = mysql_fetch_array($events_sub_query)) {
$listings_output .= '<div class="category_link">' . print_event_listing_cat_link($events_result['name'],$events_result['events_categories_id'],$events_result['name']).'</div>';
if ($_GET['listsubid'] == $events_result['events_categories_id'] || $listing_sub_id == $events_result['events_categories_id']) $listings_output .= $this->subcat_listings($listings_id,$events_result['events_categories_id']);
}
break;
case 3: 
$listings_output = '<strong>Obituaries</strong><br>' . LB;
break;
}

if ($listings_id > 2) {
$listings_query = mysql_query("SELECT customers_articles_id, title FROM listings WHERE section_id = '".$listings_id."' AND start_date <= CURDATE() and (exp_date >= CURDATE() or exp_date is null or exp_date = '');");

if (mysql_num_rows($listings_query) > 0) {
while ($listings_result = mysql_fetch_array($listings_query)) {
$listings_output .= '<div class="category_link">' . print_listing_link($listings_result['title'],$listings_result['customers_articles_id']) . '</div>';
}
} else {
$listings_output .= "<strong>No listings found.</strong>" . LB;
}
}
return $listings_output;
}

// print subcategory listings
function subcat_listings($section_id,$listub) {
$listings_query = mysql_query("SELECT customers_articles_id, title FROM listings WHERE section_id = '".$section_id."' AND sub_id = '".$listub."' AND start_date <= CURDATE() and (exp_date >= CURDATE() or exp_date is null or exp_date = '');");

if (mysql_num_rows($listings_query) > 0) {
$listings_output .= '<ul class="listing_list">' . LB;
while ($listings_result = mysql_fetch_array($listings_query)) {
$listings_output .= '<li>' . LB . '<div class="category_link">' . print_listing_link($listings_result['title'],$listings_result['customers_articles_id']) . '</div></li>' . LB;
}
$listings_output .= '</ul>' . LB;
} else {
$listings_output .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>No listings found.</strong><br>" . LB;
}

return $listings_output;
}

// pulls previews of available listings
function listings_preview($listings_id,$listing_sub = '') {
global $embed;

$articles_query = mysql_query("SELECT customers_articles_id, title, image, summary, content FROM listings WHERE section_id = '".$listings_id."' " . (!empty($listing_sub) ? " AND sub_id = '".$listing_sub."' " : "") . "AND start_date <= CURDATE() and (exp_date >= CURDATE() or exp_date is null or exp_date = '') ORDER BY exp_date DESC;");

$article_num = 0;

$previews_string = '<table width="100%" border="0">' . LB;
while ($articles_result = mysql_fetch_array($articles_query)) {
$article_num == 2 ? $article_num = 0 : '';

$previews_string .= '<tr>' . LB .
'<td>' . LB;
$article_num == 0 ? $previews_string .= '<table width="100%" border="0" class="article_preview">' . LB .
'<tr>' . LB .
'<td>' . LB .
'<div class="style14">' . LB .
$articles_result['title'] . LB .
'</div>' . LB .
(!empty($articles_result['summary']) ? $articles_result['summary'] : $articles_result['content']) . '<br>' . LB . (empty($articles_result['read_more_link']) ? print_listing_link($articles_result['title'],$articles_result['customers_articles_id'],'Click here to read more...') : '') . 
'</td>' . LB .
'<td width="130px">' . LB .
(!empty($articles_result['image']) ? $embed->determine_media_type($articles_result['image'],ARTICLE_LISTING_IMAGE_WIDTH,ARTICLE_LISTING_IMAGE_HEIGHT) . LB : '' ) . 
'</td>' . LB .
'</tr>' . LB .
'</table>' . LB : $previews_string .= '<table width="100%" border="0" class="article_preview">' . LB .
'<tr>' . LB .
'<td width="100px">' . LB .
(!empty($articles_result['image']) ? $embed->determine_media_type($articles_result['image'],ARTICLE_LISTING_IMAGE_WIDTH,ARTICLE_LISTING_IMAGE_HEIGHT) . LB : '' ) . 
'</td>' . LB .
'<td>' . LB .
'<div class="style14">' . LB .
$articles_result['title'] . LB .
'</div>' . LB .
(!empty($articles_result['summary']) ? $articles_result['summary'] : $articles_result['content']) . '<br>' . LB . 
(empty($articles_result['read_more_link']) ? print_listing_link($articles_result['title'],$articles_result['customers_articles_id'],'Click here to read more...') : '') . 
'</td>' . LB .
'</tr>' . LB .
'</table>' . LB;

$previews_string .= '</td>' . LB .
'</tr>' . LB;

$article_num++;
}
$previews_string .= '</table>' . LB;

return $previews_string;
}

}

?>