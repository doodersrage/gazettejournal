<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

// print store items info
$store_query = mysql_query("SELECT items_id, name, price, extprice, image, type FROM items WHERE status = 1 ;");

$cur_row = 0;
$product_listings = "<div class=\"store_header\">Store</div><table width=\"100%\">";
$product_listings .= print_store_header();

while ($store_result = mysql_fetch_array($store_query)) {
if ($cur_row == 4) $cur_row = 0;
if ($cur_row == 0) $product_listings .= "<tr>";
$product_listings .= "<td class=\"prod_listing\" valign=\"bottom\" align=\"center\">
	<table width=\"100\">
		<tr>
			<td align=\"center\">";
if (!empty($store_result['image'])) $product_listings .= "<a href=\"".SECURE_SITE_ADDRESS."store/item_info.php?pid=".$store_result['items_id']."\"><img src=\"images/".$store_result['image']."\" border=\"0\" width=\"120\" height=\"120\"></a><br>";
$product_listings .= "<strong><a href=\"".SECURE_SITE_ADDRESS."store/item_info.php?pid=".$store_result['items_id']."\">" . $store_result['name'] . "</a></strong>
			</td>
		</tr>
		<tr>
			<td align=\"center\">
			" . ($store_result['type'] == 1 || $store_result['type'] == 3 ? "Starting At: " : "Price: ") . " $".sprintf ('%0.2f', set_item_price($store_result['price'],$store_result['extprice']))."
			</td>
		</tr>
	</table>
	</td>";

if ($cur_row == 4) $product_listings .= "</tr>";
$cur_row++;
}
if ($cur_row > 0) $product_listings .= "</tr>";
$product_listings .= "</table>";

$page_content = $product_listings;

$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>