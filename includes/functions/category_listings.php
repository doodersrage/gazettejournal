<?PHP
class category_listings {

// get top news listings
function top_news_listings() {
$category_listing = '';

$articles_query = mysql_query("SELECT articles_id, title FROM articles WHERE status not in (2,4) ORDER BY status DESC, modified ASC LIMIT 0,5;");

while ($articles_result = mysql_fetch_array($articles_query)) {
$category_listing = $articles_result['title'].$articles_result['articles_id'];
}

}

// get listing of top news previews
function top_news_listings_previews() {
$articles_query = mysql_query("SELECT a.articles_id, a.title, a.author, a.summary, a.homepage_image FROM articles a LEFT JOIN articles_to_categories atc ON atc.articles_id = a.articles_id WHERE a.status not in (2,4) ORDER BY a.modified, a.status DESC LIMIT 0,3;");

$article_num = 0;

$previews_string = '<table width="100%" border="0">';
while ($articles_result = mysql_fetch_array($articles_query)) {
$article_num == 2 ? $article_num = 0 : '';

$previews_string .= '<tr><td>';
$article_num == 0 ? $previews_string .= '<table width="100%" border="0" class="article_preview"><tr><td><div class="style14">'.$articles_result['title'].'</div><div class="style16">BY '.$articles_result['author'].'</div>'.$articles_result['summary'].'</td><td width="130px">'.$this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id']). '</td></tr></table>' : $previews_string .= '<table width="100%" border="0" class="article_preview"><tr><td width="100px">' . $this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id']).'</td><td>'.'<div class="style14">'.$articles_result['title'].'</div><div class="style16">BY '.$articles_result['author'].'</div>'.$articles_result['summary']. '</td></tr></table>';

$previews_string .= '</td></tr>';

$article_num++;
}
$previews_string .= '</table>';

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

$category_listing .= '<strong>' . $this->get_category_name($categories_id) . '</strong><br>';

$articles_query = mysql_query("SELECT a.articles_id, a.title FROM articles a LEFT JOIN articles_to_categories atc ON atc.articles_id = a.articles_id WHERE atc.categories_id = '".$categories_id."' AND a.status not in (2,4) ORDER BY a.modified DESC, a.status DESC ".$limit_string.";");
while ($articles_result = mysql_fetch_array($articles_query)) {
$category_listing .= print_article_link($articles_result['title'],$articles_result['articles_id']) . '<br>';
}

return $category_listing;
}

// get articles preview image
function article_preview_image($image_id = '',$articles_id = '') {
$articles_images_query = mysql_query("SELECT image FROM articles_images WHERE articles_images_id = '".$image_id."';");
if (mysql_num_rows($articles_images_query) > 0) {
$articles_images_result = mysql_fetch_array($articles_images_query);
$article_image = '<img src="images/'.$articles_images_result['image'].'" width="95" height="95">';
} else {
$articles_images_query = mysql_query("SELECT image FROM articles_images WHERE articles_id = '".$articles_id."' ORDER BY sort_order ASC LIMIT 0,1;");
if (mysql_num_rows($articles_images_query) > 0) {
$articles_images_result = mysql_fetch_array($articles_images_query);
$article_image = '<img src="images/'.$articles_images_result['image'].'" width="95" height="95">';
} else {
$article_image = '';
}
}

return $article_image;
}

// get categories previews
function categories_previews($categories_id) {
$articles_query = mysql_query("SELECT a.articles_id, a.title, a.author, a.summary, a.homepage_image FROM articles a LEFT JOIN articles_to_categories atc ON atc.articles_id = a.articles_id WHERE atc.categories_id = '".$categories_id."' AND a.status not in (2,4) ORDER BY a.modified, a.status DESC LIMIT 0,3;");

$article_num = 0;

$previews_string = '<table width="100%" border="0">';
while ($articles_result = mysql_fetch_array($articles_query)) {
$article_num == 2 ? $article_num = 0 : '';

$previews_string .= '<tr><td>';
$article_num == 0 ? $previews_string .= '<table width="100%" border="0" class="article_preview"><tr><td><div class="style14">'.$articles_result['title'].'</div><div class="style16">BY '.$articles_result['author'].'</div>'.$articles_result['summary'] . '<br>' . print_article_link($articles_result['title'],$articles_result['articles_id'],'Click here to read more...').'</td><td width="130px">'.$this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id']). '</td></tr></table>' : $previews_string .= '<table width="100%" border="0" class="article_preview"><tr><td width="100px">' . $this->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id']).'</td><td>'.'<div class="style14">'.$articles_result['title'].'</div><div class="style16">BY '.$articles_result['author'].'</div>'.$articles_result['summary'] . '<br>' . print_article_link($articles_result['title'],$articles_result['articles_id'],'Click here to read more...') . '</td></tr></table>';

$previews_string .= '</td></tr>';

$article_num++;
}
$previews_string .= '</table>';

return $previews_string;
}

function list_listings($listings_id) {
$listings_output = '';

switch ($listings_id) {
case 1: 
$listings_output = '<strong>Classifieds</strong><br>';
break;
case 2: 
$listings_output = '<strong>Weddings</strong><br>';
break;
case 3: 
$listings_output = '<strong>Obituaries</strong><br>';
break;
}

$listings_query = mysql_query("SELECT customers_articles_id, title FROM listings WHERE section_id = '".$listings_id."' AND start_date <= CURDATE() and (exp_date >= CURDATE() or exp_date is null or exp_date = '');");

if (mysql_num_rows($listings_query) > 0) {
while ($listings_result = mysql_fetch_array($listings_query)) {
$listings_output .= print_listing_link($listings_result['title'],$listings_result['customers_articles_id']) . '<br>';
}
} else {
$listings_output .= "No listings found.";
}
return $listings_output;
}

function listings_preview($listings_id) {
$articles_query = mysql_query("SELECT customers_articles_id, title, image, summary, content FROM listings WHERE section_id = '".$listings_id."' AND start_date <= CURDATE() and (exp_date >= CURDATE() or exp_date is null or exp_date = '') ORDER BY exp_date DESC LIMIT 0,3;");

$article_num = 0;

$previews_string = '<table width="100%" border="0">';
while ($articles_result = mysql_fetch_array($articles_query)) {
$article_num == 2 ? $article_num = 0 : '';

$previews_string .= '<tr><td>';
$article_num == 0 ? $previews_string .= '<table width="100%" border="0" class="article_preview"><tr><td><div class="style14">'.$articles_result['title'].'</div>'.(!empty($articles_result['summary']) ? $articles_result['summary'] : $articles_result['content']) . '<br>' . print_listing_link($articles_result['title'],$articles_result['customers_articles_id'],'Click here to read more...') . '</td><td width="130px"><img src="images/'.$articles_result['image'].'" width="95" height="95"></td></tr></table>' : $previews_string .= '<table width="100%" border="0" class="article_preview"><tr><td width="100px"><img src="images/'.$articles_result['image'].'" width="95" height="95"></td><td>'.'<div class="style14">'.$articles_result['title'].'</div>'.(!empty($articles_result['summary']) ? $articles_result['summary'] : $articles_result['content']) . '<br>' . print_listing_link($articles_result['title'],$articles_result['customers_articles_id'],'Click here to read more...') . '</td></tr></table>';

$previews_string .= '</td></tr>';

$article_num++;
}
$previews_string .= '</table>';

return $previews_string;
}

}

?>