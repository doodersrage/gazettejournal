<?PHP
require '../includes/application_top.php';

// vars
$update = $_GET['update'];
$search = $_POST['search'];
$mode = $_GET['mode'];
$userid = $_GET['userid'];
$orderby = $_GET['orderby'];

switch ($update) {
case 'user_updated':
$message = '<script language="javascript">alert(\'User Updated!\');</script>';
break;
case 'user_added':
$message = '<script language="javascript">alert(\'New admin user added!\');</script>';
break;
}

if (!empty($search)) {
$search_string = " WHERE user_name LIKE '%".$search."%' or full_name LIKE '%".$search."%'";
}

if ($mode == 'delete') {
mysql_query('DELETE FROM admin_users WHERE admin_users_id = "' . $userid . '";');
}

	  // order users
	  switch ($orderby) {
	  case 'username':
	  $orderby_str = 'user_name ASC ';
	  break;
	  case 'usernamedesc':
	  $orderby_str = 'user_name DESC ';
	  break;
	  case 'admin':
	  $orderby_str = 'admin ASC ';
	  break;
	  case 'admindesc':
	  $orderby_str = 'admin DESC ';
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
	  $orderby_str = 'user_name ASC ';
	  }
	  
	  // read admin users from database
	  $admin_users_qry = mysql_query("SELECT admin_users_id, admin, user_name, created, modified FROM admin_users" . (!empty($search_string) ? $search_string : "") . " ORDER BY ".$orderby_str);
	  
	  $list = 0;
	  while($admin_users_result = mysql_fetch_array($admin_users_qry)) {
	  $list++;
	  $list == 2 ? $list = 0 : ''; 
	  $list == 1 ? $cssclass = "class=\"listing_even\" " : $cssclass = ""; 
	  $user_listings .= "<tr ".$cssclass.">\n";
	  $user_listings .= "<td class=\"section_list_left\">".$admin_users_result['user_name']."</td>\n";
      $user_listings .= "<td align=\"center\" class=\"section_list\">".( $admin_users_result['admin'] == 1 ? 'Y' : 'N' )."</td>\n";
      $user_listings .= "<td class=\"section_list\">".date('m/d/Y',strtotime($admin_users_result['created']))."</td>\n";
      $user_listings .= "<td class=\"section_list\">".date('m/d/Y',strtotime($admin_users_result['modified']))."</td>\n";
      $user_listings .= "<td align=\"center\" class=\"section_list\"><NOBR><a href=\"admin-users-edit.php?mode=edit&userid=".$admin_users_result['admin_users_id']."\" class=\"button\">Edit</a> <a href=\"?mode=delete&userid=".$admin_users_result['admin_users_id']."\" onclick=\"return confirm('Are you sure you want to delete this item?');\" class=\"button\">Delete</a> </NOBR></td>";
      $user_listings .= "</tr>\n";
	  }
	  
	  // pring findings
	  
	  $admin_users_count = mysql_num_rows($admin_users_qry);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Admin Config: Admin Users</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
</head>

<body>
<div class="container">
<div class="header_area"></div>
<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" valign="top" class="left_nav">
	<?PHP require('../includes/mainmenu.php'); ?>
 </td>
    <td valign="top"><div class="bc_nav"><?PHP echo brc(array('Admin Users' => 'admin-config/admin-users.php')); ?></div>
	<?PHP if ($_SESSION['admin'] == 1) { ?> 
	  <table border="0" align="center" cellpadding="3" cellspacing="0" class="item_list">
      <tr align="right">
        <td colspan="7" class="listing_search"><form name="search" method="post" action="">
          <input name="search" type="text" id="search">
          <input type="submit" name="Submit" value="Search">
        </form></td>
        </tr>
      <tr align="center">
        <td class="table_header"><a href="<?PHP echo ($orderby == 'username' ?  '?orderby=usernamedesc' :  '?orderby=username'); ?>">Username</a></td>
        <td class="table_header"><a href="<?PHP echo ($orderby == 'admin' ? '?orderby=admindesc' : '?orderby=admin' ); ?>">Admin User</a></td>
        <td class="table_header"> <a href="<?PHP echo ($orderby == 'added' ? '?orderby=addeddesc' : '?orderby=added' ); ?>">Added</a> </td>
        <td class="table_header"><a href="<?PHP echo ($orderby == 'modified' ? '?orderby=modifieddesc' : '?orderby=modified' ); ?>">Modified</a></td>
        <td class="table_header_right">Options</td>
      </tr>
	  <?PHP
	  echo $user_listings;
	  ?>
      <tr align="right">
        <td colspan="3" align="left" class="table_footer"><a href="admin-users-edit.php?mode=new" class="button">Add New</a> </td>
        <td colspan="2" class="table_footer"><?PHP echo $admin_users_count; ?> Users Found</td>
      </tr>
    </table>
	<?PHP 
	} else {
	echo 'You are not allowed to view this page unless you have been granted admin priviledges.';
	}
	?>
	</td>
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
