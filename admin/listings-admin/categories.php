<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Listings: Categories</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
</head>

<body>
<div class="container">
<div class="header_area"></div>
<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" valign="top" class="left_nav">
	<div class="section_title">Listings</div>
	<a href="articles.php?type=classifieds">Classifieds</a>
	<a href="articles.php?type=weddings">Weddings</a>
	<a href="articles.php?type=obituaries">Obituaries</a></td>
    <td valign="top"><div class="bc_nav">Listings -&gt; Categories </div>
	  <table border="0" align="center" cellpadding="3" cellspacing="0" class="item_list">
      <tr align="right">
        <td colspan="6" class="listing_search"><form name="form1" method="post" action="">
          <input name="search" type="text" id="search">
          <input type="submit" name="Submit" value="Search">
        </form></td>
        </tr>
      <tr align="center">
        <td class="table_header"><a href="#">ID</a></td>
        <td class="table_header"><a href="#">Name</a></td>
        <td class="table_header"><a href="#">Parent Of </a></td>
        <td class="table_header"><a href="#"> Added </a></td>
        <td class="table_header"><a href="#">Modified</a></td>
        <td class="table_header_right">Options</td>
      </tr>
      <tr>
        <td>1</td>
        <td>Test Category</td>
        <td>&nbsp;</td>
        <td>01/08/2008</td>
        <td>1/08/2008</td>
        <td align="center"><a href="categories-edit.php">Edit</a> / <a href="javascript: void();" onMouseDown="return confirm('Are you sure you want to delete this item?')">Delete</a> </td>
      </tr>
      <tr>
        <td class="listing_even">2</td>
        <td class="listing_even">Test Category1 </td>
        <td class="listing_even">Test Category </td>
        <td class="listing_even">01/09/2008</td>
        <td class="listing_even">1/09/2008</td>
        <td align="center" class="listing_even"><a href="categories-edit.php">Edit</a> / <a href="javascript: void();" onMouseDown="return confirm('Are you sure you want to delete this item?')">Delete</a> </td>
      </tr>
      <tr align="right">
        <td colspan="4" align="left" class="table_footer"><a href="categories-edit.php">Add New</a> </td>
        <td colspan="2" class="table_footer">2 of 2 Categories </td>
      </tr>
    </table></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
