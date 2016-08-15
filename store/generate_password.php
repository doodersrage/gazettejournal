<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

// vals
$submit = $_POST['submit'];
$username = $_POST['username'];
$email = $_POST['email'];

// on submit generate new password
if (!empty($submit)) {
$email_query = mysql_query("SELECT username, email FROM store_customers WHERE username = '".$username."' and email = '".$email."';");

if (mysql_num_rows($email_query) > 0) {

$email_result = mysql_fetch_array($email_query);

$password = createPassword(8);

mysql_query("UPDATE store_customers SET password = '".md5($password)."' WHERE username = '".$username."' and email = '".$email."';");

// send customer email notification
$email_subject = "Your new Gloucester Gazette store password.";
$plaintext_message = "Your newly assigned password is: " . $password;
$html_message = "Your newly assigned password is: " . $password;
send_email($email_result['email'],CONTACT_EMAIL,$email_subject,$plaintext_message,$html_message);

$generate_success = 1;
//header('Location: store_login.php');
} else {
$message = '<script language="javascript">alert(\'We are sorry but we were unable to find that username within our database.\');</script>';
}
}

$cur_row = 0;
$page_content = "<div class=\"store_header\">Generate New Password</div>";
$page_content .= print_store_header();


if ($generate_success == 1) {
$page_content .= "Your new password has been generated and emailed to you. Please check your email to retreive your new password. <a href=\"".SECURE_SITE_ADDRESS."store/store_login.php\">Click here to return to the login page.</a>";
} else {
$page_content .= "<div class=\"store_login_box\">
<form action=\"\" method=\"post\">
<div align=\"center\">To generate a new password please fill in the username and email address that you used when creating your account.</div> 
<table width=\"250\"  border=\"0\" align=\"center\">
  <tr>
    <td align=\"right\"><strong>Username:</strong></td>
    <td>".$htmlfunctions->text_field('username',20,'username',$username,'text')."</td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Email Address:</strong></td>
    <td>".$htmlfunctions->text_field('email',20,'email',$email,'text')."</td>
  </tr>
  <tr align=\"center\">
    <td colspan=\"2\">".$htmlfunctions->text_field('submit','','submit',1,'hidden')."<input type=\"submit\" name=\"Submit\" value=\"Submit\"></td>
    </tr>
</table>
</form>
</div>";
}

$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>