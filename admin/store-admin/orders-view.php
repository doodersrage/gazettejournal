<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

$orderid = $_GET['orderid'];

$order_qry = mysql_query("SELECT o.orders_id, o.order_date, o.customer_name, o.shipping_address, o.billing_address, o.shipping, o.tax, o.subtotal, o.total, o.payment_type, o.ccname, o.ccnum, o.cc_exp_date, o.cvv, o.order_status, o.total, o.order_comments, sc.email, sc.phone, o.gift_address FROM orders o LEFT JOIN store_customers sc ON sc.store_customers_id = o.store_customers_id WHERE orders_id = '".$orderid."';");
$order_result = mysql_fetch_array($order_qry);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Store: Orders View</title>
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
	<div class="bc_nav"><?PHP echo brc(array('Orders' => 'store-admin/orders.php','Orders View' => 'store-admin/orders-view.php')); ?></div>
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td valign="top"><?PHP echo "<a href=\"orders-edit.php?mode=edit&orderid=".$order_result['orders_id']."\" class=\"button\">Edit Order</a>"; ?></td>
        <td><strong>Shipping Address: </strong><br>
      <?PHP echo $order_result['customer_name']; ?><br>
      <?PHP echo $order_result['email']; ?><br>
      <?PHP echo $order_result['phone']; ?><br>
      <?PHP echo get_billing_information($order_result['shipping_address']); ?></td>
        <td><strong>Billing Address: </strong><br>
      <?PHP echo $order_result['customer_name']; ?><br>
      <?PHP echo $order_result['email']; ?><br>
      <?PHP echo $order_result['phone']; ?><br>
      <?PHP echo get_billing_information($order_result['billing_address']); ?></td>
      </tr>
      <tr>
        <td colspan="3"><strong>Items Ordered:</strong><br>
            <table width="100%" border="1" cellpadding="3">
                <tr bgcolor="#CCCCCC">
                  <td><strong>Item Name </strong></td>
                  <td><strong>Price</strong></td>
                  <td><strong>Weeks</strong></td>
                  <td><strong>Word Count</strong></td>
                  <td><strong>Classifieds Category</strong></td>
                  <td><strong>Posted Ad</strong></td>
                  </tr>
				  <?PHP
				$order_items_qry = mysql_query("SELECT item_id, name, price, weeks, sub_cat, new_ad, word_count FROM order_items WHERE orders_id = '".$orderid."';");
				while($order_items_result = mysql_fetch_array($order_items_qry)) {
              	
				
				echo '<tr>';
                echo '<td>'.$order_items_result['name'].'</td>';
                echo '<td>$'.$order_items_result['price'].'</td>';
                echo '<td>'.(!empty($order_items_result['weeks']) ? $order_items_result['weeks'] : '').'</td>';
                echo '<td>'.(!empty($order_items_result['word_count']) ? $order_items_result['word_count'] : '').'</td>';
                echo '<td>'.get_classifieds_category_name($order_items_result['sub_cat']).'</td>';
                echo '<td>'.$order_items_result['new_ad'].'</td>';
                echo '</tr>';
				
				}
				?>
            </table></td>
        </tr>
      <tr>
        <td colspan="2">
		<table align="left" cellpadding="3">
		<tr><td align="right"><strong>Sub-Total:</strong></td><td> $<?PHP echo $order_result['subtotal']; ?></td></tr>
        <?PHP if (!empty($order_result['tax'])) { ?><tr><td align="right"><strong>Tax:</strong></td><td> $<?PHP echo $order_result['tax']; ?> </td></tr><?PHP } ?>
        <tr><td align="right"><strong>Total:</strong></td><td> $<?PHP echo $order_result['total']; ?> </td></tr>
		</table>
		</td>
        <td valign="top" class="horizontal_pad"><strong>Payment Method:</strong>
		<table cellpadding="2">
      <?PHP
	  echo ($order_result['payment_type'] == 'CC' ? '<tr><td colspan="2" align="center"><strong>Credit Card</strong></tr></td>' : '') . '<br>';
	  echo '<tr><td align="right"><strong>Name:</strong> </td><td>' . $order_result['ccname'] . '</td></tr>';
	  echo '<tr><td align="right"><strong>Number:</strong> </td><td>' . $order_result['ccnum'] . '</td></tr>';
	  echo '<tr><td align="right"><strong>Expiration Date:</strong> </td><td>' . $order_result['cc_exp_date'] . '</td></tr>';
	  echo '<tr><td align="right"><strong>CVV number:</strong> </td><td>' . $order_result['cvv'] . '</td></tr>';
	  ?>
	  </table>
	  </td>
      </tr>
	  <?PHP if (!empty($order_result['gift_address'])) { ?>
      <tr>
        <td colspan="3" valign="top"><p><strong>Gift Address: </strong></p>		  
		<p><?PHP echo str_replace("\n","<br>",$order_result['gift_address']); ?></p>
		</td>
        </tr>
      <tr>
	  <?PHP } ?>
        <td colspan="3" valign="top"><p><strong>Order Comments: </strong></p>		  
		<p><?PHP echo $order_result['order_comments']; ?></p>
		</td>
        </tr>
      <tr>
        <td colspan="2" valign="top"><p><strong>Order Notes:</strong></p>
          <?PHP 
		$order_notes_qry = mysql_query("SELECT added, note FROM order_notes WHERE orders_id = '".$orderid."' ORDER BY added ASC;");
		while($order_notes_result = mysql_fetch_array($order_notes_qry)) {
		echo '<p><strong>Added:</strong> '.date("F j, Y, g:i a",strtotime($order_notes_result['added'])).'<br>';
		echo $order_notes_result['note'].'</p>';
		}
		?></td>
        <td valign="top"><p><strong>Order Status: </strong></p>
          <p><?PHP echo set_order_status($order_result['order_status']); ?></p></td>
      </tr>
    </table></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
