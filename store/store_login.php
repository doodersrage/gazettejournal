<?PHP
require '../includes/application_top.php';
// load store functions
require INCLUDES_DIRECTORY.'/functions/store.php';

// vals
$submit = $_POST['submit'];
$username = $_POST['username'];
$password = $_POST['password'];

if (!empty($submit)) {
login_user($username,$password);
}

$cur_row = 0;
$page_content = "<div class=\"store_header\">Store Login</div>";
$page_content .= print_store_header();

$page_content .= "<div class=\"store_login_box\">
<form action=\"\" method=\"post\">
<div align=\"center\">To login to the store please enter your username and password then click submit.</div> 
<table width=\"300\"  border=\"0\" align=\"center\">
  <tr>
    <td align=\"right\"><strong>Username:</strong></td>
    <td><input name=\"username\" type=\"text\" size=\"20\"></td>
  </tr>
  <tr>
    <td align=\"right\"><strong>Password:</strong></td>
    <td><input name=\"password\" type=\"password\" size=\"20\"></td>
  </tr>
  <tr align=\"center\">
    <td colspan=\"2\"><input type=\"submit\" name=\"Submit\" value=\"Submit\"></td>
    </tr>
  <tr align=\"center\">
    <td colspan=\"2\"><input name=\"submit\" type=\"hidden\" value=\"1\"><a href=\"".SECURE_SITE_ADDRESS."store/create_account.php\">Create Account</a> / <a href=\"".SECURE_SITE_ADDRESS."store/generate_password.php\">Forget Password?</a></td>
    </tr>
</table>
</form>
</div>";


$search_box = print_search_box();

$content = $template->fill_area($page_content);

// load template file
require INCLUDES_DIRECTORY.'/templates/store.php';
?>