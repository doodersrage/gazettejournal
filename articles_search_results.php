<?PHP
require 'includes/application_top.php';
require INCLUDES_DIRECTORY.'/classes/category_listings.php';
$category_listings = new category_listings();

// set vars
$searchstring = $_POST['search_string'];
$searchtype = $_POST['search_option'];

if (!empty($searchstring)) {

$extra_where .= " (a.title like '%" . $searchstring . "%' or a.summary like '%" . $searchstring . "%' or a.content like '%" . $searchstring . "%')";
if (!empty($searchtype)) $extra_where .= " and atc.categories_id = '".$searchtype."'";
	  
$articles_query = mysql_query("SELECT a.articles_id, a.title, a.author, a.summary, a.content, a.homepage_image FROM articles a LEFT JOIN articles_to_categories atc on a.articles_id = atc.articles_id WHERE".$extra_where." ORDER BY modified DESC;");

$article_num = 0;

$previews_string = '<table width="100%" border="0">';
while ($articles_result = mysql_fetch_array($articles_query)) {
$article_num == 2 ? $article_num = 0 : '';

$previews_string .= '<tr><td>';
$article_num == 0 ? $previews_string .= '<table width="100%" border="0" class="article_preview"><tr><td><div class="style14">'.$articles_result['title'].'</div>'.(!empty($articles_result['summary']) ? $articles_result['summary'] : substr($articles_result['content'],0,700)) . '<br>' . print_article_link($articles_result['title'],$articles_result['articles_id'],'Click here to read more...') . '</td><td width="130px">' . $category_listings->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id']) . '</td></tr></table>' : $previews_string .= '<table width="100%" border="0" class="article_preview"><tr><td width="100px">' . $category_listings->article_preview_image($articles_result['homepage_image'],$articles_result['articles_id']) . '</td><td>'.'<div class="style14">'.substr($articles_result['title'],0,700).'</div>'.(!empty($articles_result['summary']) ? $articles_result['summary'] : $articles_result['content']) . '<br>' . print_article_link($articles_result['title'],$articles_result['articles_id'],'Click here to read more...') . '</td></tr></table>';

$previews_string .= '</td></tr>';

$article_num++;
}
$previews_string .= '</table>';

$search_box = print_search_box();

$content = $template->fill_area($previews_string);
}

// load template file
require INCLUDES_DIRECTORY.'/templates/inside_pages.php';

?>