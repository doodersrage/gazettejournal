<?PHP
require '../includes/application_top.php';


// vars
$update = $_GET['update'];
$search = $_POST['search'];
$mode = $_GET['mode'];
$orderid = $_GET['orderid'];
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
$search_string = " WHERE customer_name LIKE '%".$search."%' OR order_date LIKE '%".$search."%'";
}

if ($mode == 'delete') {
mysql_query('DELETE FROM orders WHERE orders_id = "' . $orderid . '";');
mysql_query('DELETE FROM order_items WHERE orders_id = "' . $orderid . '";');
mysql_query('DELETE FROM order_notes WHERE orders_id = "' . $orderid . '";');
mysql_query('DELETE FROM listings WHERE orders_id = "' . $orderid . '";');
}

	  // order users
	  switch ($orderby) {
	  case 'customer_name':
	  $orderby_str = 'customer_name ASC ';
	  break;
	  case 'customer_namedesc':
	  $orderby_str = 'customer_name DESC ';
	  break;
	  case 'order_date':
	  $orderby_str = 'order_date ASC ';
	  break;
	  case 'order_datedesc':
	  $orderby_str = 'order_date DESC ';
	  break;
	  case 'order_status':
	  $orderby_str = 'order_status ASC ';
	  break;
	  case 'order_statusdesc':
	  $orderby_str = 'order_status DESC ';
	  break;
	  case 'total':
	  $orderby_str = 'total ASC ';
	  break;
	  case 'totaldesc':
	  $orderby_str = 'total DESC ';
	  break;
	  default:
	  $orderby_str = 'order_date DESC ';
	  }
	  
	  // read orders from database
	  $order_qry = mysql_query("SELECT orders_id, order_date, customer_name, order_status, total FROM orders" . (!empty($search_string) ? $search_string : "") . " ORDER BY ".$orderby_str.' '.$_SESSION['ppfilter']);
	  
	  $order_count_qry = mysql_query("SELECT orders_id, order_date, customer_name, order_status, total FROM orders" . (!empty($search_string) ? $search_string : "") . " ORDER BY ".$orderby_str);

	  // pring findings
	  
	  $order_count = mysql_num_rows($order_count_qry);
	  
	  // write page links
	  $order_count_div = ceil($order_count/$_SESSION['ppfilterid']);
	  $max_ceil_order = $_SESSION['ppfilterid'] * $order_count_div;
	  $page_num = 0;
	  for ($num = $_SESSION['ppfilterid']; $num <= $max_ceil_order; $num = $num + $_SESSION['ppfilterid']) {
	  $page_num++;
	  $pagelinks .= '<a href="?pageid='.$page_num.'">'.$page_num.'</a> ';
	  }

	  
	  $list = 0;
	  while($order_result = mysql_fetch_array($order_qry)) {
	  
	  $list++;
	  $list == 2 ? $list = 0 : ''; 
	  $list == 1 ? $cssclass = "class=\"listing_even\" " : $cssclass = ""; 
	  $order_listings .= "<tr ".$cssclass.">\n";
	  $order_listings .= "<td class=\"section_list_left\">" . date('m/d/Y g:i A',strtotime($order_result['order_date'])) . "</td>\n";
	  $order_listings .= "<td class=\"section_list_left\">" . $order_result['customer_name'] . "</td>\n";
      $order_listings .= "<td class=\"section_list\">".set_order_status($order_result['order_status'])."</td>\n";
      $order_listings .= "<td class=\"section_list\">$".$order_result['total']."</td>\n";
      $order_listings .= "<td align=\"center\" class=\"section_list\"><a href=\"orders-view.php?orderid=".$order_result['orders_id']."\" class=\"button\">View</a> <a href=\"orders-edit.php?mode=edit&orderid=".$order_result['orders_id']."\" class=\"button\">Edit</a> <a href=\"?mode=delete&orderid=".$order_result['orders_id']."\" onclick=\"return confirm('Are you sure you want to delete this item?');\" class=\"button\">Delete</a></td>";
      $order_listings .= "</tr>\n";
	  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Store: Orders</title>
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
	<div class="bc_nav"><?PHP echo brc(array('Orders' => 'store-admin/orders.php')); ?></div>
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
        <td class="table_header"><a href="<?PHP echo ($orderby == 'order_date' ?  '?orderby=order_datedesc' :  '?orderby=order_date'); ?>">Order Date</a></td>
        <td class="table_header"><a href="<?PHP echo ($orderby == 'customer_name' ?  '?orderby=customer_namedesc' :  '?orderby=customer_name'); ?>">Customer Name</a></td>
        <td class="table_header"> <a href="<?PHP echo ($orderby == 'order_status' ? '?orderby=order_statusdesc' : '?orderby=order_status' ); ?>">Order Status</a> </td>
        <td class="table_header"><a href="<?PHP echo ($orderby == 'total' ? '?orderby=totaldesc' : '?orderby=total' ); ?>">Order Total</a></td>
        <td class="table_header_right">Options</td>
      </tr>
	  <?PHP 
	  echo $order_listings;
	  ?>
      <tr align="right">
        <td colspan="2" class="table_footer"><?PHP echo ' &nbsp;Showing ' . ($start_val+1) . ' - ' . ($order_count < $end_val ? $order_count : $end_val) . ' of ' . $order_count; ?> Orders</td>
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
