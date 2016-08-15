<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

//vars
$userid = (isset($_GET['userid']) ? $_GET['userid'] : $_POST['userid']);
$submit = $_POST['submit'];
$email = clean_db_inserts($_POST['email']);
$firstname = clean_db_inserts(str_replace(array("\'",'\"'),array("'",'"'),$_POST['firstname']));
$lastname = clean_db_inserts(str_replace(array("\'",'\"'),array("'",'"'),$_POST['lastname']));
$middlei = clean_db_inserts($_POST['middlei']);
$address1 = clean_db_inserts(str_replace(array("\'",'\"'),array("'",'"'),$_POST['address1']));
$address2 = clean_db_inserts(str_replace(array("\'",'\"'),array("'",'"'),$_POST['address2']));
$city = clean_db_inserts(str_replace(array("\'",'\"'),array("'",'"'),$_POST['city']));
$state = $_POST['state'];
$zip = $_POST['zip'];
$status = $_POST['status'];
$phone = $_POST['phone'];

// input error check function
function check_errors() {
global $username,$password,$email,$firstname,$lastname,$address1,$city,$zip;

$error_count = 0;

if (empty($email)) {
$error_count++;
}
if (empty($firstname)) {
$error_count++;
}
if (empty($lastname)) {
$error_count++;
}
if (empty($address1)) {
$error_count++;
}
if (empty($city)) {
$error_count++;
}
if (empty($zip)) {
$error_count++;
}

return $error_count;

}

// update user
if (!empty($submit)) {
// error check

if (check_errors() == 0) {
// post new user
mysql_query("UPDATE store_customers SET modified=NOW(),email='".$email."',fname='".$firstname."',mi='".$middlei."',lname='".$lastname."',account_status='".$status."', phone = '".$phone."' WHERE store_customers_id = '".$userid."';");

// insert address info
mysql_query("UPDATE store_customers_address SET address1='".$address1."',address2='".$address2."',city='".$city."',state='".$state."',zip='".$zip."' WHERE store_customers_id = '".$userid."';");

header ("Location: customers.php");
} else {
$message = '<script language="javascript">alert(\'Some errors were found while submitting your information. Please review your input and try again.\');</script>';
}
}


// pull existing customer info
$userin_qry = mysql_query("SELECT store_customers_id, created, modified, username, password, email, fname, mi, lname, account_status, phone FROM store_customers WHERE store_customers_id = '".$userid."';");
$userin_result = mysql_fetch_array($userin_qry);

$customer_id = $userin_result['store_customers_id'];
$username = $userin_result['username'];
$email = $userin_result['email'];
$firstname = $userin_result['fname'];
$lastname = $userin_result['lname'];
$middlei = $userin_result['mi'];
$created = $userin_result['created'];
$modified = $userin_result['modified'];
$status = $userin_result['account_status'];
$phone = $userin_result['phone'];

$userin_addr_qry = mysql_query("SELECT address1, address2, city, state, zip FROM store_customers_address WHERE store_customers_id = '".$customer_id."';");
$userin_addr_result = mysql_fetch_array($userin_addr_qry);

$address1 = $userin_addr_result['address1'];
$address2 = $userin_addr_result['address2'];
$city = $userin_addr_result['city'];
$state = $userin_addr_result['state'];
$zip = $userin_addr_result['zip'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Store: Customers</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
</head>

<body>
<div id="center_form"></div>
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
	<div class="bc_nav"><?PHP echo brc(array('Customers Edit' => 'store-admin/customers-edit.php')); ?></div>
	<form name="customer_edit" method="post" action="">
      <table border="0" align="center" cellpadding="3" cellspacing="0">
        <tr>
          <td class="horizontal_pad"><strong>Created:</strong> <?PHP echo date('m/d/Y',strtotime($created)); ?> </td>
          <td class="horizontal_pad"><strong>Modified:</strong> <?PHP echo date('m/d/Y',strtotime($modified)); ?></td>
        </tr>
        <tr>
          <td align="right"><strong>Account Status:</strong></td>
          <td colspan="2">Enabled
            <?PHP echo $htmlfunctions->radio_button('status',($status == true ? true : ''),'',1); ?>
            Disabled
			<?PHP echo $htmlfunctions->radio_button('status',($status == false ? true : ''),'',0); ?>
        </tr>
<?PHP
echo  "<tr>
    <td align=\"right\"><strong>Email Address*:</strong></td>
    <td>".$htmlfunctions->text_field('email',20,'email',$email,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>First Name*:</strong></td>
    <td>".$htmlfunctions->text_field('firstname',20,'firstname',$firstname,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Last Name*:</strong></td>
    <td>".$htmlfunctions->text_field('lastname',20,'lastname',$lastname,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Middle Initial:</strong></td>
    <td>".$htmlfunctions->text_field('middlei',2,'middlei',$middlei,'text','',2)."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Phone*:</strong></td>
    <td>".$htmlfunctions->text_field('phone',20,'phone',$phone,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Address1*:</strong></td>
    <td>".$htmlfunctions->text_field('address1',20,'address1',$address1,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Address2:</strong></td>
    <td>".$htmlfunctions->text_field('address2',20,'address2',$address2,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>City*:</strong></td>
    <td>".$htmlfunctions->text_field('city',20,'city',$city,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>State*:</strong></td>
    <td>
" . print_state_select($state) . "
</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Zip*:</strong></td>
    <td>".$htmlfunctions->text_field('zip',20,'zip',$zip,'text')."</td>
  </tr>
  <tr align=\"center\">
    <td colspan=\"2\">".$htmlfunctions->text_field('userid','','',$userid,'hidden').$htmlfunctions->text_field('submit','','',1,'hidden')."<input type=\"submit\" name=\"Submit\" value=\"Submit\"></td>
    </tr>";
?>
      </table>
    </form></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
<?
echo $message;
?>
</body>
</html>
