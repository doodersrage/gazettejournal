<?PHP

// print article link
function print_article_link($article_name,$article_id,$link_name = '',$link_file = '') {

$article_url = "<a href=\"" . (!empty($link_file) ? FILES_ADDRESS . $link_file : SITE_ADDRESS . preg_replace("/[^A-Za-z0-9]/", "-", strtolower($article_name)) . "-artid-" . $article_id . "/" ) . "\" " . (!empty($link_file) ? "target=\"_blank\"" : "") . " >" . (!empty($link_name) ? $link_name : $article_name) . "</a>" . LB;

return $article_url;
}

// print article link
function get_article_link($article_name,$article_id,$link_file = '') {

$article_url = (!empty($link_file) ? FILES_ADDRESS . $link_file : SITE_ADDRESS . preg_replace("/[^A-Za-z0-9]/", "-", strtolower($article_name)) . "-artid-" . $article_id . "/" );

return $article_url;
}


// print regular listing link
function print_listing_link($listing_name,$listing_id,$link_name = '') {

$listing_url = "<a href=\"" . SITE_ADDRESS . preg_replace("/[^A-Za-z0-9]/", "-", strtolower($listing_name)) . "-listid-" . $listing_id . "/\">" . (!empty($link_name) ? $link_name : $listing_name) . "</a>" . LB;

return $listing_url;
}

// create classifieds listing category link
function print_listing_cat_link($listing_name,$listing_id,$link_name = '') {

$listing_url = "<a href=\"" . SITE_ADDRESS . preg_replace("/[^A-Za-z0-9]/", "-", strtolower($listing_name)) . "-listsubid-" . $listing_id . "/\">" . (!empty($link_name) ? $link_name : $listing_name) . "</a>" . LB;

return $listing_url;
}

// create even listing category link
function print_event_listing_cat_link($listing_name,$listing_id,$link_name = '') {

$listing_url = "<a href=\"" . SITE_ADDRESS . preg_replace("/[^A-Za-z0-9]/", "-", strtolower($listing_name)) . "-eventsubid-" . $listing_id . "/\">" . (!empty($link_name) ? $link_name : $listing_name) . "</a>" . LB;

return $listing_url;
}

function print_search_box($pos_class = '') {
$search_box = '<div ' . (!empty($pos_class) ? 'align="center" style="width:223px; margin:0 auto;"' : 'class="search_box"') . '>' . LB .
'<div class="search_head">Articles Search</div>' . LB .
'<form action="articles_search_results.php" method="post" name="search">' . LB .
'<table width="100%"  border="0">' . LB .
  '<tr>' . LB .
    '<td>' . LB .
	'<select name="search_option" class="search_drop">' . LB .
      '<option value="">Search All</option>' . LB;

$category_query = mysql_query("SELECT categories_id, name FROM categories WHERE hide_category = 0 ORDER BY name ASC ;");
while ($category_result = mysql_fetch_array($category_query)) {
$search_box .= '<option value="'.$category_result['categories_id'].'">'.$category_result['name'].'</option>' . LB;
}

$search_box .= '</select></td></tr>' . LB .
  '<tr>' . LB .
    '<td><input name="search_string" type="text" class="search_string" ></td>' . LB .
    '</tr>' . LB . 
    '<tr><td>' . LB .
	'<input type="submit" name="Submit" value="Search" class="search_button"></td>' . LB .
  '</tr>' . LB .
	(!empty($pos_class) ? '' : '<tr>' . LB .
    '<td rowspan="2" align="center"><a href="'.SITE_ADDRESS.'advanced-search/">Advanced Search</a></td>' . LB .
    '</tr>' . LB) . 
'</table>' . LB .
'</form>' . LB .
'</div>' . LB;

return $search_box;
}

function print_classifieds_search_box($pos_class = '') {
$search_box = '<div ' . (!empty($pos_class) ? 'align="center" style="width:223px; margin:0 auto;"' : 'class="search_box"') . '>' . LB .
'<div class="search_head">Classifieds Search</div>' . LB .
'<form action="classifieds_search_results.php" method="post" name="search">' . LB .
'<table width="100%"  border="0">' . LB .
  '<tr>' . LB .
    '<td>' . LB .
	'<select name="search_option" class="search_drop">' . LB .
      '<option value="">Search All</option>' . LB;

$category_query = mysql_query("SELECT classifieds_cat_id, name FROM classifieds_categories ORDER BY name ASC ;");
while ($category_result = mysql_fetch_array($category_query)) {
$search_box .= '<option value="'.$category_result['classifieds_cat_id'].'">'.$category_result['name'].'</option>' . LB;
}

$search_box .= '</select></td>' . LB .
    '<td rowspan="2">' . LB .
	'<input type="submit" name="Submit" value="Search" class="search_button"></td>' . LB .
  '</tr>' . LB .
  '<tr>' . LB .
    '<td><input name="search_string" type="text" class="search_string"></td>' . LB .
    '</tr>' . LB .
	(!empty($pos_class) ? '' : '<tr>' . LB .
    '<td rowspan="2" align="center"><a href="'.SITE_ADDRESS.'advanced-search/">Advanced Search</a></td>' . LB .
    '</tr>' . LB) . 
'</table>' . LB .
'</form>' . LB .
'</div>' . LB;

return $search_box;
}

function encapsulate($data, $border = '') {
$encapsulation = '<table '.(!empty($border) ? 'style="'.$border.'"' : '').'><tr><td>'.$data.'</td></tr></table>';

return $encapsulation;
}

function get_image_area_width($media_name,$max_width = '',$max_height = '') {

if (strpos($media_name,'.jpg') || strpos($media_name,'.gif')) {
list($width,$height)=getimagesize('images/'.$media_name);

$x_ratio = $max_width / $width;
$y_ratio = $max_height / $height;

if( ($width <= $max_width) && ($height <= $max_height) ){
    $tn_width = $width;
    $tn_height = $height;
    }elseif (($x_ratio * $height) < $max_height){
        $tn_height = ceil($x_ratio * $height);
        $tn_width = $max_width;
    }else{
        $tn_width = ceil($y_ratio * $width);
        $tn_height = $max_height;
}

} else {
$tn_width = ARTICLE_IMAGE_WIDTH;
}

return $tn_width + 4;

}
?>