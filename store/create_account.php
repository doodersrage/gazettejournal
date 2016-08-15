<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

//vars
$submit = $_POST['submit'];
$username = str_replace(array("\'",'\"'),array("'",'"'),$_POST['username']);
$password = md5($_POST['password']);
$email = str_replace(array("\'",'\"'),array("'",'"'),$_POST['email']);
$firstname = str_replace(array("\'",'\"'),array("'",'"'),$_POST['firstname']);
$lastname = str_replace(array("\'",'\"'),array("'",'"'),$_POST['lastname']);
$middlei = $_POST['middlei'];
$address1 = str_replace(array("\'",'\"'),array("'",'"'),$_POST['address1']);
$address2 = str_replace(array("\'",'\"'),array("'",'"'),$_POST['address2']);
$city = str_replace(array("\'",'\"'),array("'",'"'),$_POST['city']);
$state = $_POST['state'];
$zip = $_POST['zip'];
$phone = $_POST['phone'];


// input error check function
function check_errors() {
global $username,$password,$email,$firstname,$lastname,$address1,$city,$zip,$phone;

$error_count = 0;

if (empty($username)) {
$error_count++;
}
if (empty($password)) {
$error_count++;
}
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

// check for existing username
function username_check($user) {
$username_result = 0;

$username_query = mysql_query("SELECT username FROM store_customers WHERE username = '".$user."';");
$username_result = mysql_num_rows($username_query);

return $username_result;
}


// create new user
if (!empty($submit)) {
// error check

if (check_errors() == 0) {
if (username_check($username) == 0) {
// post new user
mysql_query("INSERT INTO store_customers (created,modified,username,password,email,fname,mi,lname,account_status,phone) VALUES (NOW(),NOW(),'".clean_db_inserts($username)."','".$password."','".clean_db_inserts($email)."','".clean_db_inserts($firstname)."','".clean_db_inserts($middlei)."','".clean_db_inserts($lastname)."','1','".$phone."');");

$new_customer_id = mysql_insert_id();

// insert address info
mysql_query("INSERT INTO store_customers_address (store_customers_id,address1,address2,city,state,zip) VALUES ('".$new_customer_id."','".clean_db_inserts($address1)."','".clean_db_inserts($address2)."','".clean_db_inserts($city)."','".$state."','".$zip."');");

$new_address_id = mysql_insert_id();

//assign new address data to newly created customer
mysql_query("UPDATE store_customers SET shipping_address = '".$new_address_id."', billing_address = '".$new_address_id."' WHERE store_customers_id = '".$new_customer_id."';");

$email_subject = "Your new Gloucester Gazette store account.";
$plaintext_message = "Thank you for signing up for a Gloucester Gazette store account. You can now purchase services with your newly created store account. \r\n\r\n

The information you have provided for your new account is:\r\n
Username: " . $username . "\r\n" .
"Username: " . $_POST['password'] . "\r\n" .
"Email: " . $email . "\r\n" .
"Name: " . $firstname . ' ' . $middlei . ' ' . $lastname . "\r\n" .
"Phone: " . $phone . "<br>" .
"Address1: " . $address1 . "\r\n" .
"Address2: " . $address2 . "\r\n" .
"City: " . $city . "\r\n" .
"State: " . $state . "\r\n" .
"Zip: " . $zip;

$html_message = EMAIL_ACCOUNT_SIGNUP . "<p>Thank you for signing up for a Gloucester Gazette store account. You can now purchase services with your newly created store account.</p>

<p>The information you have provided for your new account is:<br>
Username: " . $username . "<br>" .
"Username: " . $_POST['password'] . "<br>" .
"Email: " . $email . "<br>" .
"Name: " . $firstname . ' ' . $middlei . ' ' . $lastname . "<br>" .
"Phone: " . $phone . "<br>" .
"Address1: " . $address1 . "<br>" .
"Address2: " . $address2 . "<br>" .
"City: " . $city . "<br>" .
"State: " . $state . "<br>" .
"Zip: " . $zip;

send_email($email,CONTACT_EMAIL,$email_subject,$plaintext_message,$html_message);

// login newly created user
login_user($username,$_POST['password']);
header("Location: ./");
} else {
$message = '<script language="javascript">alert(\'The username you have selected appears to already be in use. Please select another and try again.\');</script>';
}
} else {
$message = '<script language="javascript">alert(\'Some errors were found while submitting your information. Please review your input and try again.\');</script>';
}
}


$cur_row = 0;
$page_content = "<div class=\"store_header\">Create Account</div>";
$page_content .= print_store_header();

$page_content .= "<div class=\"store_login_box\">
<form action=\"\" method=\"post\">
<div align=\"center\">Please enter your information into the required fields to create an account. <br> * indicates required field</div> 
<table width=\"400\"  border=\"0\" align=\"center\">
  <tr>
    <td align=\"right\"><strong>Username*:</strong></td>
    <td>".$htmlfunctions->text_field('username',20,'username',$username,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Password*:</strong></td>
    <td><input name=\"password\" type=\"password\" size=\"20\" value=\"\"></td>
  </tr>
  <tr>
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
    <td colspan=\"2\">".$htmlfunctions->text_field('submit','','',1,'hidden')."<input type=\"submit\" name=\"Submit\" value=\"Submit\"></td>
    </tr>
</table>
</form>
</div>";


$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>