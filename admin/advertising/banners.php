<?PHP
require '../includes/application_top.php';

// vars
$mode = $_GET['mode'];
$banner_id = $_GET['bid'];

// delete selected product
if ($mode == 'delete') {
mysql_query("DELETE FROM banners WHERE banner_id = '".$banner_id."';");
}

// pull list of available products
$banners_query = mysql_query("SELECT banner_id, created, edited, name, status FROM banners ;");
if (mysql_num_rows($banners_query) > 0) {
while ($banners_result = mysql_fetch_array($banners_query)) {

$banners_listing .= "<tr>
        <td>".$banners_result['name']." </td>
        <td>".$banners_result['created']."</td>
        <td>".$banners_result['edited']."</td>
        <td align=\"center\"><a href=\"banners-edit.php?bid=".$banners_result['banner_id']."&mode=edit\"  class=\"button\">Edit</a> <a href=\"?mode=delete&bid=".$banners_result['banner_id']."\" onclick=\"return confirm('Are you sure you want to delete this item?')\"  class=\"button\">Delete</a> </td>
      </tr> \r\n";
}
} else {
$banners_listing = "<tr> 
        <td colspan=\"4\" align=\"center\">No banners were found.</td> 
      </tr> \r\n";
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Banners</title>
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
	<div class="bc_nav"><?PHP echo brc(array('Banners' => 'advertising/banners.php')); ?></div>
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
<?PHP echo $banners_listing; ?>
      <tr align="right">
        <td colspan="2" align="left" class="table_footer"><a href="banners-edit.php?mode=new" class="button">Add New</a> </td>
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
