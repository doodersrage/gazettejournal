<?PHP
require '../includes/application_top.php';

// vars
$mode = $_GET['mode'];
$items_id = $_GET['pid'];

// delete selected product
if ($mode == 'delete') {
mysql_query("DELETE FROM items WHERE items_id = '".$items_id."';");
}

// pull list of available products
$products_query = mysql_query("SELECT items_id, created, edited, name, price, status FROM items ;");
if (mysql_num_rows($products_query) > 0) {
	  $list = 0;
while ($products_result = mysql_fetch_array($products_query)) {
	  $list++;
	  $list == 2 ? $list = 0 : ''; 
	  $list == 1 ? $cssclass = "class=\"listing_even\" " : $cssclass = ""; 

$product_listing .= "<tr ".$cssclass.">
        <td>".$products_result['name']." </td>
        <td>".$products_result['created']."</td>
        <td>".$products_result['edited']."</td>
        <td align=\"center\"><a href=\"products-edit.php?pid=".$products_result['items_id']."&mode=edit\"  class=\"button\">Edit</a> <a href=\"?mode=delete&pid=".$products_result['items_id']."\" onclick=\"return confirm('Are you sure you want to delete this item?')\"  class=\"button\">Delete</a> </td>
      </tr> \r\n";
}
} else {
$product_listing = "<tr> 
        <td colspan=\"4\" align=\"center\">No items were found.</td> 
      </tr> \r\n";
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Store: Products</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
</head>

<body>
<div class="container">
<div class="header_area"></div>
<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" rowspan="2" valign="top" class="left_nav">
	<?PHP require('../includes/mainmenu.php'); ?></td>
  </tr>
  <tr>
    <td valign="top">
	<div class="bc_nav"><?PHP echo brc(array('Items' => 'store-admin/products.php')); ?></div>
	<table border="0" align="center" cellpadding="3" cellspacing="0" class="item_list">
      <tr align="right">
        <td colspan="5" class="listing_search">&nbsp;</td>
      </tr>
      <tr align="center">
        <td class="table_header"><a href="#">Name</a> </td>
        <td class="table_header"><a href="#"> Added </a></td>
        <td class="table_header"><a href="#">Modified</a></td>
        <td class="table_header_right">Options</td>
      </tr>
<?PHP echo $product_listing; ?>
      <tr align="right">
        <td colspan="2" align="left" class="table_footer"><a href="products-edit.php?mode=new" class="button">Add New</a> </td>
        <td colspan="2" class="table_footer">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
