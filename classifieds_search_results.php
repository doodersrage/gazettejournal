<?PHP
require 'includes/application_top.php';

// set vars
$searchstring = $_POST['search_string'];
$searchtype = $_POST['search_option'];

	  if (!empty($searchstring)) {
	  
	  $extra_where .= "(title like '%" . $searchstring . "%' or summary like '%" . $searchstring . "%' or content like '%" . $searchstring . "%')" . (!empty($searchtype) ? " AND sub_id = '".$searchtype . "'" : "");
	  
$articles_query = mysql_query("SELECT customers_articles_id, title, image, summary, content FROM listings WHERE section_id = 1 AND ".$extra_where." AND (exp_date > NOW() or exp_date = '' or exp_date is null) ORDER BY exp_date DESC;");

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

$content = $template->fill_area($previews_string);
}

$search_box = print_classifieds_search_box();

// load template file
require INCLUDES_DIRECTORY.'/templates/inside_pages.php';

?>