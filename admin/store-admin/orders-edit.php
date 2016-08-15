<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

// vars
$orderid = $_GET['orderid'];
$ordernote = str_replace(array("\'",'\"'),array("'",'"'),$_POST['ordernote']);
$postnote = $_POST['postnote'];
$status_update = $_POST['status_update'];
$product_status = $_POST['product_status'];
$payment_info_update = $_POST['payment_info_update'];
$ccname = str_replace(array("\'",'\"'),array("'",'"'),$_POST['ccname']);
$ccnum = $_POST['ccnum'];
$cvv = $_POST['cvv'];
$month = $_POST['month'];
$year = $_POST['year'];

// update payment information
if (!empty($payment_info_update)) {
mysql_query("UPDATE orders SET ccname='".$ccname."',ccnum='".$ccnum."',cc_exp_date='".$month.'/'.$year."',cvv='".$cvv."' WHERE orders_id = '".$orderid."';");
}

// update product status
if (!empty($status_update)) {
mysql_query("UPDATE orders SET order_status = '".$product_status."' WHERE orders_id = '".$orderid."';");
}

// post new note
if (!empty($postnote)) {
mysql_query("INSERT INTO order_notes (added,orders_id,note) VALUES (NOW(),'".$orderid."','".$ordernote."');");
}

$order_qry = mysql_query("SELECT o.orders_id, o.order_date, o.customer_name, o.shipping_address, o.billing_address, o.shipping, o.tax, o.subtotal, o.total, o.payment_type, o.ccname, o.ccnum, o.cc_exp_date, o.cvv, o.order_status, o.total, o.order_comments, sc.email, sc.phone, o.gift_address FROM orders o LEFT JOIN store_customers sc ON sc.store_customers_id = o.store_customers_id WHERE orders_id = '".$orderid."';");
$order_result = mysql_fetch_array($order_qry);

$customer_name = $order_result['customer_name'];
$shipping_address = $order_result['shipping_address'];
$billing_address = $order_result['billing_address'];
$subtotal = $order_result['subtotal'];
$tax = $order_result['tax'];
$total = $order_result['total'];
$order_status = $order_result['order_status'];
$cc_exp_date = explode('/',$order_result['cc_exp_date']);
$month = $cc_exp_date[0];
$year = $cc_exp_date[1];
$order_comments = $order_result['order_comments'];
$email = $order_result['email'];
$phone = $order_result['phone'];
$gift_address = $order_result['gift_address'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Store: Orders Edit</title>
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
	<div class="bc_nav"><?PHP echo brc(array('Orders' => 'store-admin/orders.php','Orders Edit' => 'store-admin/orders-edit.php')); ?></div>
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td><strong>Shipping Address: </strong><br>
      <?PHP echo $customer_name; ?><br>
      <?PHP echo $email; ?><br>
      <?PHP echo $phone; ?><br>
      <?PHP echo get_billing_information($shipping_address); ?></td>
        <td><strong>Billing Address: </strong><br>
      <?PHP echo $customer_name; ?><br>
      <?PHP echo $email; ?><br>
      <?PHP echo $phone; ?><br>
      <?PHP echo get_billing_information($billing_address); ?></td>
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
        <td>
		<table align="left" cellpadding="3">
		<tr><td align="right"><strong>Sub-Total:</strong></td><td> $<?PHP echo $subtotal; ?></td></tr>
        <?PHP if (!empty($tax)) { ?><tr><td align="right"><strong>Tax:</strong></td><td> $<?PHP echo $tax; ?> </td></tr><?PHP } ?>
        <tr><td align="right"><strong>Total:</strong></td><td> $<?PHP echo $total; ?> </td></tr>
		</table>
		</td>
        <td valign="top" class="horizontal_pad" colspan="><strong>Payment Method Update:</strong>
	    <form name="form2" method="post" action="">
		<table cellpadding="1">
      <?PHP
	  echo ($order_result['payment_type'] == 'CC' ? '<tr><td colspan="2" align="center"><strong>Credit Card</strong></tr></td>' : '') . '<br>';
	  echo '<tr><td align="right"><strong>Name:</strong> </td><td>' . $htmlfunctions->text_field('ccname',20,'ccname',$order_result['ccname'],'text') . '</td></tr>';
	  echo '<tr><td align="right"><strong>Number:</strong> </td><td>' . $htmlfunctions->text_field('ccnum',20,'ccnum',$order_result['ccnum'],'text') . '</td></tr>';
	  echo '<tr><td align="right"><strong>Expiration Date:</strong> </td><td>'.print_month_select($month) . ' / ' . print_exp_year_select($year)  . '</td></tr>';
//	  echo '<tr><td align="right"><strong>CVV number:</strong> </td><td>' . $htmlfunctions->text_field('cvv',4,'cvv',$order_result['cvv'],'text','',4) . '</td></tr>';
	  ?>
	  </table>
	  <?PHP echo $htmlfunctions->text_field('payment_info_update','','payment_info_update',1,'hidden'); ?>
	      <input type="submit" name="Submit" value="Update Payment Information">
	      </form></td>
      </tr>
	  <?PHP if (!empty($gift_address)) { ?>
      <tr>
        <td colspan="3" valign="top"><p><strong>Gift Address: </strong></p>		  
		<p><?PHP echo str_replace("\n","<br>",$gift_address); ?></p>
		</td>
        </tr>
      <tr>
	  <?PHP } ?>
      <tr>
        <td colspan="2" valign="top"><p><strong>Order Comments: </strong></p>		  
		<p><?PHP echo $order_comments; ?></p></td>
        <td rowspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><p><strong>Order Notes:</strong></p>
          <form method="post" action="">
            <?PHP 
		$order_notes_qry = mysql_query("SELECT added, note FROM order_notes WHERE orders_id = '".$orderid."' ORDER BY added ASC;");
		while($order_notes_result = mysql_fetch_array($order_notes_qry)) {
		echo '<p><strong>Added:</strong> '.date("F j, Y, g:i a",strtotime($order_notes_result['added'])).'<br>';
		echo $order_notes_result['note'].'</p>';
		}
		?>
            <?PHP echo $htmlfunctions->textarea('ordernote',20,6,'','ordernote'); 
		echo $htmlfunctions->text_field('postnote','','postnote',1,'hidden');
		?> <br>
            <input name="Submit" type="submit" value="Submit Note">
          </form></td>
        <td valign="top"><p><strong>Order Status Update: </strong></p>
          <form name="form1" method="post" action="">
            <select name="product_status">
              <option value="1" <?PHP if ($order_status == 1) echo "selected";?>>Processing</option>
              <option value="2" <?PHP if ($order_status == 2) echo "selected";?>>Successful</option>
              <option value="3" <?PHP if ($order_status == 3) echo "selected";?>>Failed</option>
            </select>
            <input type="submit" name="Submit" value="Update Status">
            <?PHP
		echo $htmlfunctions->text_field('status_update','','status_update',1,'hidden');
		?>
          </form>
          <p><strong></strong></p></td>
      </tr>
    </table></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
