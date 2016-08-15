<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Help - Admin Settings</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
</head>

<body>
<div class="container">
<div class="header_area"></div>
<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" valign="top" class="left_nav">
	<?PHP require('../includes/mainmenu.php'); ?></td>
    <td valign="top"><div align="center">
      <table width="100%"  border="0" cellpadding="3" bgcolor="#FFFFFF">
        <tr>
          <td width="79%">
		  <p><a href="#add_user">Adding new users</a><br>
              <a href="#edit_user">Editing existing users</a><br>
                <a href="#global_settings">Global Settings</a> </p>
            <p><strong><a name="add_user"></a>Adding new users:</strong></p>
            <p>1. Click on the add new bottom to the bottom left of your current user listings.</p>
            <p>2. Set the users status to enabled or disabled.</p>
            <p>3. Check admin user if you want the user to also be able to edit/add or remove users.</p>
            <p>4. Enter a username for the new user to use. (This must be different that any other assigned username.)</p>
            <p>5. Create a new password for the user.</p>
            <p>6. Enter the users full name. (This field is not required.)</p>
            <p>7. Input the users email address. </p>
            <p>8. Check &quot;Send Update Alert Emails&quot; if you want the user to be updated when an article has been changed or added. </p>
            <p>&nbsp;</p>
            <p><strong><a name="edit_user"></a>Editing an existing user:</strong></p>
            <p>To edit an existing user click on admin users then click the edit button to the far right of the user you want to edit.</p>
            <p>&nbsp;</p>
            <p><strong><a name="global_settings"></a>Global Settings:</strong></p>
            <p>This section contains settings used throughout the front end such as image sizes, column widths, and other settings.</p></td>
        </tr>
      </table>
    </div></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
