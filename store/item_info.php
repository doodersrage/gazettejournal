<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

$pid = (int)$_GET['pid'];

// pull products information
$store_query = mysql_query("SELECT items_id, name, price, extprice, image, description, content, type, allow_cart_button FROM items WHERE items_id = '".$pid."' ;");
$store_result = mysql_fetch_array($store_query);


// set vars
$item_price = set_item_price($store_result['price'],$store_result['extprice']);
$items_id = $store_result['items_id'];
$item_name = $store_result['name'];
$item_price = $item_price;
$item_image = $store_result['image'];
$item_description = $store_result['description'];
$item_content = $store_result['content'];
$item_type = $store_result['type'];
$allow_cart_button = $store_result['allow_cart_button'];

$page_content .= "<div class=\"store_header\">Item Information</div>";
$page_content .= print_store_header();

// print global item description information
$page_content .= '<div align="center">
      <form name="form1" id="form1" method="post" action="'.SECURE_SITE_ADDRESS.'store/add_to_cart.php">
      ' .
		  (!empty($item_image) ? '<img src="images/'.$item_image.'" border="0" width="120" height="120"><br>' : '') .
		  '<strong>' . $item_name . '</strong><br>' .
		  ($item_type == 1 || $item_type == 3 ? 'Starting At: ' : 'Price: ') . '$' . sprintf ('%0.2f', $item_price) .
		  '<table  border="0" cellpadding="3">' . "\r\n" .
            '<tr>' . "\r\n" .
              '<td>' . "\r\n" .
			  $item_description . "\r\n" .
			  $item_content . "\r\n" .
			  $htmlfunctions->text_field('total_cost_val','','total_cost_val',$item_price,'hidden') . "\r\n" .
			  $htmlfunctions->text_field('items_id','','items_id',$store_result['items_id'],'hidden') . "\r\n" .
			  $htmlfunctions->text_field('items_name','','items_name',$store_result['name'],'hidden') . "\r\n" .
			  '</td>' . "\r\n" .
           '</tr>' . "\r\n";
			
// if item = classified ad
if ($item_type == 3) {
$page_content .= '<tr>' . "\r\n" .
'<td>' . "\r\n" .
'<table align="center">' . "\r\n" .
'<tr>' . "\r\n" .
'<td align="right">' . "\r\n" .
'<strong>Weeks:</strong>' . "\r\n" .
'<select name="weeks" onchange="javascript: calc_total_price();"> ' . "\r\n" .
'<option value="1" selected="selected">1</option> ' . "\r\n" .
'<option value="2">2</option> ' . "\r\n" .
'<option value="3">3</option> ' . "\r\n" .
'</select>' . "\r\n" .
'</td>' . "\r\n" .
'<td align="right"><strong>Section:</strong><select name="sub_category">' . "\r\n";

		$classified_sub_query = mysql_query("SELECT classifieds_cat_id, name FROM classifieds_categories ;");
		while ($classified_result = mysql_fetch_array($classified_sub_query)) {
		$page_content .= '<option value="'.$classified_result['classifieds_cat_id'].'" '.($classified_result['classifieds_cat_id'] == $sub_category ? 'selected' : '').'>'.$classified_result['name'].'</option>' . "\r\n";
		}
$page_content .= '</select>' . "\r\n" .
'</td>' . "\r\n" .
'</tr>' . "\r\n" .
//'<tr>' . "\r\n" .
//	'<td align="right"><strong>Ad Title:</strong></td>' . "\r\n" .
//	'<td><input onkeydown="javascript: countit()" name="new_ad_title" size="50" type="text"></td>' . "\r\n" .
//'</tr>' . "\r\n" .
'<tr>' . "\r\n" .
  '<td align="right" valign="top"> <strong>Type your ad here:</strong>&nbsp;&nbsp;</td>' . "\r\n" .
  '<td><textarea onkeydown="javascript: countit()" name="new_ad" rows="10" cols="44"></textarea></td>' . "\r\n" .
'</tr>' . "\r\n" .
'<tr>' . "\r\n" .
  '<td align="right"><strong>Word Count:</strong><span id="word_count">0</span>'.$htmlfunctions->text_field('tot_word_count','','tot_word_count','','hidden').'</td>' . "\r\n" .
  '<td align="right"><strong>Total Cost:</strong>$<span id="total_cost"></span></td>' . "\r\n" .
'</tr>' . "\r\n" .
'</table>' . "\r\n" .
'<script language="Javascript">calc_total_price()</script>' . "\r\n" .
'</td>' . "\r\n" .
'</tr>' . "\r\n";
}		
// close description table and finish form
$page_content .= '</table>'.(!empty($allow_cart_button) ? '<input type="submit" name="Submit" value="Add to Cart" />' : '') . "\r\n" .
      '</form> ' . "\r\n" . 
	  '</div>' . "\r\n";

$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/item_info.php';
?>
