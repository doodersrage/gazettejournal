<?PHP
require 'includes/application_top.php';

// set vars
$searchstring = $_POST['searchstring'];
$searchtype = $_POST['searchtype'];

if (!empty($searchstring)) {
	  
	  switch($searchtype) {
	  case 'weddings':
	  $extra_where = " section_id = 2 AND (sub_id = 1 OR sub_id = 2) AND";
	  break;
	  case 'obituaries':
	  $extra_where = " section_id = 3 AND";
	  break;
	  case 'births':
	  $extra_where = " section_id = 2 AND sub_id = 3 AND";
	  break;
	  case 'both':
	  $extra_where = "";
	  break;
	  }
	  
$search_array = explode(" ",$searchstring);

$extra_where .= " (";
$search_cnt = count($search_array);
$search_cur = 0;
foreach($search_array as $search_str) {
$search_cur++;
$extra_where .= "upper(content) LIKE '%" . strtoupper($search_str) . "%' ".($search_cur < $search_cnt ? "AND " : "");
}
$extra_where .= ")";
	  
$articles_query = mysql_query("SELECT customers_articles_id, title, image, summary, content FROM listings WHERE".$extra_where." ORDER BY exp_date DESC;");

$article_num = 0;

$previews_string = '<table width="100%" border="0">';
$search_result = mysql_num_rows($articles_query);
if ($search_result > 0) {
while ($articles_result = mysql_fetch_array($articles_query)) {
$article_num == 2 ? $article_num = 0 : '';

$previews_string .= '<tr><td>';
$article_num == 0 ? $previews_string .= '<table width="100%" border="0" class="article_preview">
<tr>
<td>
<div class="style14">'.$articles_result['title'].'</div>'
.(!empty($articles_result['summary']) ? $articles_result['summary'] : $articles_result['content']) . '<br>'
 . print_listing_link($articles_result['title'],$articles_result['customers_articles_id'],'Click here to read more...') . '</td>
 <td width="130px">
 '.(!empty($articles_result['image']) ? '<img src="images/'.$articles_result['image'].'" width="95" height="95">' : '&nbsp;').'
 </td>
 </tr>
 </table>' : $previews_string .= '<table width="100%" border="0" class="article_preview">
 <tr><td width="100px">
 '.(!empty($articles_result['image']) ? '<img src="images/'.$articles_result['image'].'" width="95" height="95">' : '&nbsp;').'
 </td>
 <td>
 <div class="style14">'.$articles_result['title'].'</div>'
 .(!empty($articles_result['summary']) ? $articles_result['summary'] : $articles_result['content']) . '<br>'
  . print_listing_link($articles_result['title'],$articles_result['customers_articles_id'],'Click here to read more...') . '</td>
  </tr>
  </table>';

$previews_string .= '</td></tr>';

$article_num++;
}
} else {
$notfound_str = '<center>We are sorry but your search returned zero results. Please adjust your search and try again.</center>';
}
$previews_string .= '</table>';

$content = $template->fill_area(!empty($notfound_str) ? $notfound_str : $previews_string);
}

// load template file
require INCLUDES_DIRECTORY.'/templates/inside_pages.php';

?>