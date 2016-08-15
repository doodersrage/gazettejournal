<?PHP
require '../includes/application_top.php';

if ($_GET['mode'] == 'delete') {
mysql_query('DELETE FROM categories WHERE categories_id = "' . $_GET['catid'] . '";');
}

$update_sel = $_GET['update'];

switch ($update_sel) {
case 'category_updated':
$message = '<script language="javascript">alert(\'Category Updated!\');</script>';
break;
case 'category_added':
$message = '<script language="javascript">alert(\'New category added!\');</script>';
break;
}

// order by
	  switch ($_GET['orderby']) {
	  case 'id':
	  $orderby_str = 'categories_id ASC ';
	  break;
	  case 'iddesc':
	  $orderby_str = 'categories_id DESC ';
	  break;
	  case 'name':
	  $orderby_str = 'name ASC ';
	  break;
	  case 'namedesc':
	  $orderby_str = 'name DESC ';
	  break;
	  case 'added':
	  $orderby_str = 'created ASC ';
	  break;
	  case 'addeddesc':
	  $orderby_str = 'created DESC ';
	  break;
	  case 'modified':
	  $orderby_str = 'modified ASC ';
	  break;
	  case 'modifieddesc':
	  $orderby_str = 'modified DESC ';
	  break;
	  default:
	  $orderby_str = 'name ASC ';
	  }
	  
if (isset($_POST['search'])) {
$search_string = " WHERE name LIKE '%".$_POST['search']."%'";
}

$categories_query = mysql_query("SELECT categories_id, name, parent, modified, created, parent FROM categories".$search_string." ORDER BY ".$orderby_str.";");
$categories_count = mysql_num_rows($categories_query);
$categories_rows = "";

$list = 0;
while ($categories_result = mysql_fetch_array($categories_query)) {
$list++;
$list == 2 ? $list = 0 : ''; 
$list == 1 ? $cssclass = "class=\"listing_even\" " : $cssclass = ""; 

$categories_rows .= "<tr ".$cssclass.">\r\n";
$categories_rows .= "<td class=\"section_list_left\">".$categories_result['name']."</td>\r\n";
//$categories_rows .= "<td class=\"section_list\">";
// bof get categories parents name
//$categories_parent_query = mysql_query("SELECT name FROM categories WHERE categories_id = '".$categories_result['parent']."';");
//$categories_parent_result = mysql_fetch_array($categories_parent_query);
//$categories_rows .= $categories_parent_result['name'];
//eof get categories parents name
$categories_rows .= "</td>\r\n";
$categories_rows .= "<td class=\"section_list\" align=\"center\">".date('m/d/Y',strtotime($categories_result['created']))."</td>\r\n";
$categories_rows .= "<td class=\"section_list\" align=\"center\">".date('m/d/Y',strtotime($categories_result['modified']))."</td>\r\n";
$categories_rows .= "<td align=\"center\" class=\"section_list\"><NOBR><a href=\"categories-edit.php?mode=edit&catid=".$categories_result['categories_id']."\" class=\"button\">Edit</a> <a href=\"categories.php?mode=delete&catid=".$categories_result['categories_id']."\" onclick=\"return confirm('Are you sure you want to delete this item?')\" class=\"button\">Delete</a> </NOBR</td>\r\n";
$categories_rows .= "</tr>\r\n";
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Articles: Categories</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
</head>
<body>
<div class="container">
  <div class="header_area"></div>
  <div class="content">
    <table width="800" border="0" align="center" cellpadding="3" class="main_table">
      <tr>
        <td width="127" valign="top" class="left_nav"><?PHP require('../includes/mainmenu.php'); ?></td>
        <td valign="top"><div class="bc_nav"><?PHP echo brc(array('Categories' => 'articles-admin/categories.php')); ?></div>
          <table border="0" align="center" cellpadding="0" cellspacing="0" class="item_list">
            <tr align="right">
              <td colspan="6" class="listing_search"><form name="form1" method="post" action="">
                  <input name="search" type="text" id="search">
                  <input type="submit" name="Submit" value="Search">
                </form></td>
            </tr>
            <tr>
              <td colspan="2"><table width="100%" border="0" cellpadding="2" cellspacing="0">
                  <tr align="center">
                    <td class="table_header"><a href="<?PHP echo ($_GET['orderby'] == 'name' ?  '?orderby=namedesc' :  '?orderby=name'); ?>">Name</a></td>
<!--                     <td class="table_header">Child Of </td>
 -->                    <td class="table_header"><a href="<?PHP echo ($_GET['orderby'] == 'added' ? '?orderby=addeddesc' : '?orderby=added' ); ?>">Added</a> </td>
                    <td class="table_header"><a href="<?PHP echo ($_GET['orderby'] == 'modified' ? '?orderby=modifieddesc' : '?orderby=modified' ); ?>">Modified</a></td>
                    <td width="90" class="table_header_right">Options</td>
                  </tr>
                  <?PHP echo $categories_rows; ?>
                </table></td>
            </tr>
            <tr align="right">
              <td align="left" class="table_footer"><a href="categories-edit.php" class="button">Add New</a> </td>
              <td class="table_footer"><?PHP echo $categories_count; ?> Categories Found</td>
            </tr>
          </table></td>
      </tr>
    </table>
    <?PHP require '../includes/footer.php'; ?>
  </div>
</div>
<?PHP
echo $message;
?>
</body>
</html>
