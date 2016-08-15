<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';


//vars
$submit = $_POST['submit'];
$email = str_replace(array("\'",'\"'),array("'",'"'),$_POST['email']);
$firstname = str_replace(array("\'",'\"'),array("'",'"'),$_POST['firstname']);
$lastname = str_replace(array("\'",'\"'),array("'",'"'),$_POST['lastname']);
$middlei = $_POST['middlei'];
$address1 = str_replace(array("\'",'\"'),array("'",'"'),$_POST['address1']);
$address2 = str_replace(array("\'",'\"'),array("'",'"'),$_POST['address2']);
$city = str_replace(array("\'",'\"'),array("'",'"'),$_POST['city']);
$state = str_replace(array("\'",'\"'),array("'",'"'),$_POST['state']);
$zip = $_POST['zip'];
$password = (!empty($_POST['password']) ? md5($_POST['password']) : '');
$phone = $_POST['phone'];


// input error check function
function check_errors() {
global $username,$password,$email,$firstname,$lastname,$address1,$city,$zip,$phone;

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
if (empty($phone)) {
$error_count++;
}

return $error_count;

}


// update user
if (!empty($submit)) {
// error check

if (check_errors() == 0) {
// post new user
mysql_query("UPDATE store_customers SET modified=NOW(),email='".clean_db_inserts($email)."',fname='".clean_db_inserts($firstname)."',mi='".clean_db_inserts($middlei)."',lname='".clean_db_inserts($lastname)."'".(!empty($password) ? ",password='".$password."'" : "").",phone='".$phone."' WHERE store_customers_id = '".$_SESSION['store_customers_id']."';");

// insert address info
mysql_query("UPDATE store_customers_address SET address1='".clean_db_inserts($address1)."',address2='".clean_db_inserts($address2)."',city='".clean_db_inserts($city)."',state='".$state."',zip='".$zip."' WHERE store_customers_id = '".$_SESSION['store_customers_id']."';");

header ("Location: /");
} else {
$message = '<script language="javascript">alert(\'Some errors were found while submitting your information. Please review your input and try again.\');</script>';
}
}


$cur_row = 0;
$page_content = "<div class=\"store_header\">Create Account</div>";
$page_content .= print_store_header();

$userin_qry = mysql_query("SELECT store_customers_id, created, modified, username, password, email, fname, mi, lname, account_status, phone FROM store_customers WHERE username = '".$_SESSION['store_username']."';");
$userin_result = mysql_fetch_array($userin_qry);

$customer_id = $userin_result['store_customers_id'];
$username = $userin_result['username'];
$email = $userin_result['email'];
$firstname = $userin_result['fname'];
$lastname = $userin_result['lname'];
$middlei = $userin_result['mi'];
$phone = $userin_result['phone'];

$userin_addr_qry = mysql_query("SELECT address1, address2, city, state, zip FROM store_customers_address WHERE store_customers_id = '".$customer_id."';");
$userin_addr_result = mysql_fetch_array($userin_addr_qry);

$address1 = $userin_addr_result['address1'];
$address2 = $userin_addr_result['address2'];
$city = $userin_addr_result['city'];
$state = $userin_addr_result['state'];
$zip = $userin_addr_result['zip'];


$page_content .= "<div class=\"store_login_box\">
<form action=\"\" method=\"post\">
<table width=\"400\"  border=\"0\" align=\"center\">
  <tr>
    <td align=\"right\"><strong>Password:</strong></td>
    <td>".$htmlfunctions->text_field('password',20,'password','','text')."</td>
    <td>(Will remain the same if left blank)</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Email Address*:</strong></td>
    <td colspan=\"2\">".$htmlfunctions->text_field('email',20,'email',$email,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>First Name*:</strong></td>
    <td colspan=\"2\">".$htmlfunctions->text_field('firstname',20,'firstname',$firstname,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Last Name*:</strong></td>
    <td colspan=\"2\">".$htmlfunctions->text_field('lastname',20,'lastname',$lastname,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Middle Initial:</strong></td>
    <td colspan=\"2\">".$htmlfunctions->text_field('middlei',2,'middlei',$middlei,'text','',2)."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Phone*:</strong></td>
    <td colspan=\"2\">".$htmlfunctions->text_field('phone',20,'phone',$phone,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Address1*:</strong></td>
    <td colspan=\"2\">".$htmlfunctions->text_field('address1',20,'address1',$address1,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Address2:</strong></td>
    <td colspan=\"2\">".$htmlfunctions->text_field('address2',20,'address2',$address2,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>City*:</strong></td>
    <td colspan=\"2\">".$htmlfunctions->text_field('city',20,'city',$city,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>State*:</strong></td>
    <td colspan=\"2\">
" . print_state_select($state) . "
</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Zip*:</strong></td>
    <td colspan=\"2\">".$htmlfunctions->text_field('zip',20,'zip',$zip,'text')."</td>
  </tr>
  <tr align=\"center\">
    <td colspan=\"3\">".$htmlfunctions->text_field('submit','','',1,'hidden')."<input type=\"submit\" name=\"Submit\" value=\"Submit\"></td>
    </tr>
</table>
</form>
</div>";

$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>