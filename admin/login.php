<?PHP
require 'includes/application_top.php';
session_unset();
srand();

if ($_POST['login_form']) {
login_user($_POST['login_user_name'], $_POST['login_password']);
}

if ($_GET['error'] == 'incorrectlogin') $message = '<script language="javascript">alert(\'The login information you provided does not appear to be correct. Please try again.\');</script>';

// login debug
//mysql_query("ALTER TABLE admin_users ADD COLUMN session_id VARCHAR(100)");
//mysql_query("UPDATE admin_users SET session_id = '89953ae632710e1eaae314e606a3bec0';");
//$user_query = mysql_query("SELECT session_id FROM admin_users WHERE user_name = 'robmcd' AND status = 1;");
//$user_query_result = mysql_fetch_array($user_query);
echo $user_query_result['session_id'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Gazette-Journal Online Editor  </title>
<link type="text/css" href="includes/global.css" rel="stylesheet">
</head>

<body>
<div class="container">
<div class="header_area"></div>
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="707">
		  <form class="login" name="login" id="login" method="post" action="">
		    <table width="200" border="0" align="center" cellpadding="3" class="login_box">
              <tr>
                <td align="right">Username:</td>
                <td><?PHP echo $htmlfunctions->text_field('login_user_name',15); ?></td>
              </tr>
              <tr>
                <td align="right">Password:</td>
                <td><?PHP echo $htmlfunctions->password_field('login_password',15); ?></td>
              </tr>
              <tr align="center">
                <td colspan="2"><?PHP echo $htmlfunctions->text_field('login_form','','',1,'hidden') . $htmlfunctions->submit_button('Login'); ?></td>
              </tr>
            </table>
	      </form>

	</td>
  </tr>
</table>
<?PHP require 'includes/footer.php'; ?>
</div>
<?PHP
echo $message;
?>
</body>
</html>
