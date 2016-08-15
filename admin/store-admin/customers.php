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
$message = '<script language="javascript">alert(\'New user added!\');</script>';
break;
}

// set page filter
if (!isset($_GET['pageid'])) $_GET['pageid'] = 1;
$_SESSION['ppfilterid'] = (isset($_GET['filterpp']) ? $_GET['filterpp'] : (isset($_SESSION['ppfilterid']) ? $_SESSION['ppfilterid'] : 10));
$start_val = ((isset($_GET['pageid']) ? $_GET['pageid'] - 1 : 0 )) * $_SESSION['ppfilterid'];
$end_val = ($_GET['pageid'] * $_SESSION['ppfilterid']);
$_SESSION['ppfilter'] = ' LIMIT '.$_SESSION['ppfilterid'].' OFFSET ' . $start_val;

if (!empty($search)) {
$search_string = " WHERE username LIKE '%".$search."%' OR fname LIKE '%".$search."%' OR lname LIKE '%".$search."%'";
}

if ($mode == 'delete') {
mysql_query('DELETE FROM store_customers WHERE store_customers_id = "' . $userid . '";');
mysql_query('DELETE FROM store_customers_address WHERE store_customers_id = "' . $userid . '";');
}

	  // order users
	  switch ($orderby) {
	  case 'username':
	  $orderby_str = 'username ASC ';
	  break;
	  case 'usernamedesc':
	  $orderby_str = 'username DESC ';
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
	  $orderby_str = 'username ASC ';
	  }
	  
	  // read admin users from database
	$userin_qry = mysql_query("SELECT store_customers_id, created, modified, username, password, email, fname, mi, lname, account_status FROM store_customers" . (!empty($search_string) ? $search_string : "") . " ORDER BY ".$orderby_str.' '.$_SESSION['ppfilter']);

	$userin_count_qry = mysql_query("SELECT store_customers_id, created, modified, username, password, email, fname, mi, lname, account_status FROM store_customers" . (!empty($search_string) ? $search_string : "") . " ORDER BY ".$orderby_str);

	  // pring findings
	  $users_count = mysql_num_rows($userin_count_qry);
	  
	  // write page links
	  $users_count_div = ceil($users_count/$_SESSION['ppfilterid']);
	  $max_ceil_users = $_SESSION['ppfilterid'] * $users_count_div;
	  $page_num = 0;
	  for ($num = $_SESSION['ppfilterid']; $num <= $max_ceil_users; $num = $num + $_SESSION['ppfilterid']) {
	  $page_num++;
	  $pagelinks .= '<a href="?pageid='.$page_num.'">'.$page_num.'</a> ';
	  }

	  $list = 0;
	  while($userin_result = mysql_fetch_array($userin_qry)) {
	  $list++;
	  $list == 2 ? $list = 0 : ''; 
	  $list == 1 ? $cssclass = "class=\"listing_even\" " : $cssclass = ""; 
	  $user_listings .= "<tr ".$cssclass.">\n";
	  $user_listings .= "<td class=\"section_list_left\">".$userin_result['username']."</td>\n";
	  $user_listings .= "<td class=\"section_list_left\">".$userin_result['fname']. ' ' . $userin_result['mi'] . ' ' . $userin_result['lname'] ."</td>\n";
      $user_listings .= "<td class=\"section_list\">".date('m/d/Y',strtotime($userin_result['created']))."</td>\n";
      $user_listings .= "<td class=\"section_list\">".date('m/d/Y',strtotime($userin_result['modified']))."</td>\n";
      $user_listings .= "<td align=\"center\" class=\"section_list\"><NOBR><a href=\"customers-edit.php?mode=edit&userid=".$userin_result['store_customers_id']."\" class=\"button\">Edit</a> <a href=\"?mode=delete&userid=".$userin_result['store_customers_id']."\" onclick=\"return confirm('Are you sure you want to delete this item?');\" class=\"button\">Delete</a> </NOBR></td>";
      $user_listings .= "</tr>\n";
	  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Store: Customers</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>
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
	<div class="bc_nav"><?PHP echo brc(array('Customers' => 'store-admin/customers.php')); ?></div>
	<table border="0" align="center" cellpadding="3" cellspacing="0" class="item_list">
       <tr align="right">
        <td colspan="2" class="listing_search"><form name="articlelimitform1" method="post" action="">
    # Per Page:
        <select name="ppmenu1" onChange="MM_jumpMenu('parent',this,0)">
          <option value="?filterpp=10" <?PHP echo ($_SESSION['ppfilterid'] == 10 ? 'selected' : ''); ?>>10</option>
          <option value="?filterpp=25" <?PHP echo ($_SESSION['ppfilterid'] == 25 ? 'selected' : '');?>>25</option>
          <option value="?filterpp=50" <?PHP echo ($_SESSION['ppfilterid'] == 50 ? 'selected' : '');?>>50</option>
        </select>
                </form></td>
        <td colspan="3" class="listing_search"><form name="form1" method="post" action="">
            <input name="search" type="text" id="search">
            <input type="submit" name="Submit" value="Search">
                </form></td>
        </tr>
      <tr align="center">
        <td class="table_header"><a href="<?PHP echo ($orderby == 'username' ?  '?orderby=usernamedesc' :  '?orderby=username'); ?>">Username</a></td>
        <td class="table_header">Name</td>
        <td class="table_header"> <a href="<?PHP echo ($orderby == 'added' ? '?orderby=addeddesc' : '?orderby=added' ); ?>">Added</a> </td>
        <td class="table_header"><a href="<?PHP echo ($orderby == 'modified' ? '?orderby=modifieddesc' : '?orderby=modified' ); ?>">Modified</a></td>
        <td class="table_header_right">Options</td>
      </tr>
	  <?PHP 
	  echo $user_listings;
	  ?>
      <tr align="right">
        <td colspan="2" class="table_footer"><?PHP echo ' &nbsp;Showing ' . ($start_val+1) . ' - ' . ($users_count < $end_val ? $users_count : $end_val) . ' of ' . $users_count; ?> Users</td>
        <td colspan="3" class="table_footer"><div align="right">Pages <?PHP echo $pagelinks ?></div></td>
        </tr>
    </table></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
