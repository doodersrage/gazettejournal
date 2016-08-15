<?PHP
require '../includes/application_top.php';
require ADMIN_INCLUDES_DIRECTORY.'/functions/admin_users_edit.php';

// vars
$admin_user = ($_POST['admin'] == 1 ? true : '');
$user_name = str_replace(array("\'",'\"'),array("'",'"'),$_POST['user_name']);
$full_name = str_replace(array("\'",'\"'),array("'",'"'),$_POST['full_name']);
$password = $_POST['password'];
$email = str_replace(array("\'",'\"'),array("'",'"'),$_POST['email']);
$email_alerts = ($_POST['email_alert'] == 1 ? true : '');
$status = ($_POST['status'] == 1 ? true : '');
$email_alerts = ($_POST['alerts'] == 1 ? true : '');
$userid = (int)$_GET['userid'];
$user_id = (int)$_POST['user_id'];
$mode = $_GET['mode'];
$new = $_POST['new'];
$update = $_POST['update'];
$created = $_POST['created'];

function encrypt_update_pass($password,$created,$user_id) {
global $db;

$encrypted_password = md5($password.$created);

$sql = "UPDATE admin_users SET password = ? WHERE admin_users_id = ?;";
$values = array($encrypted_password,$user_id);
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);
}

// pull existing user information
if ($mode == 'edit' && empty($update)) {
$form_name = 'edit_account';

$admin_users_qry = mysql_query("SELECT admin_users_id, created, modified, admin, user_name, full_name, email, email_alert, status FROM admin_users WHERE admin_users_id = ".$userid.";");
$admin_users_result = mysql_fetch_array($admin_users_qry);

$user_id = $admin_users_result['admin_users_id'];
$created = $admin_users_result['created'];
$modified = $admin_users_result['modified'];
$admin_user = ($admin_users_result['admin'] == 1 ? true : '');
$user_name = $admin_users_result['user_name'];
$full_name = $admin_users_result['full_name'];
$email = $admin_users_result['email'];
$email_alerts = ($admin_users_result['email_alert'] == 1 ? true : '');
$status = ($admin_users_result['status'] == 1 ? true : '');

} elseif ($mode == 'new') {
$form_name = 'new_account';
}

// update an existing user
if (!empty($user_id) && $update == 1) {
!empty($password) ? $error_type = 'new' : $error_type = '';
if (required_fields_check($error_type,$email,$user_name,$password) == 0) {
if (username_check($user_name,$user_id) == 0) {

$sql = "UPDATE admin_users SET modified = NOW(), admin = ?, user_name = ?, full_name = ?, email = ?, email_alert = ?, status = ? WHERE admin_users_id = ?;";
$values = array($admin_user,$user_name,$full_name,$email,$email_alerts,$status,$user_id);
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

If ($error_type == 'new') {
encrypt_update_pass($password,$created,$user_id);
}
header('Location: admin-users.php?update=user_updated');
} else {
$message = '<script language="javascript">alert(\'That username already exists. Please try another.\');</script>';
}
} else {
$message = "<script language=\"javascript\">alert('".error_check($error_type,$email,$user_name,$password)."');</script>";
}
}

// insert new user record
if (!empty($new)) {
if (required_fields_check('new',$email,$user_name,$password) == 0) {
if (username_check($user_name) == 0) {

$values = array($admin_user,$user_name,$full_name,$email,$email_alerts,$status);
$sql = "INSERT INTO admin_users (created, modified, admin, user_name, full_name, email, email_alert, status) values (NOW(),NOW(),?,?,?,?,?,?)";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

$new_user_id = mysql_insert_id();

//look up create date/time string
$user_created_qry = mysql_query("SELECT admin_users_id, created FROM admin_users WHERE admin_users_id = '".$new_user_id."';");
$user_created_result = mysql_fetch_array($user_created_qry);

// insert password
encrypt_update_pass($password,$user_created_result['created'],$new_user_id);

header('Location: admin-users.php?update=user_added');
}
$message = '<script language="javascript">alert(\'That username already exists. Please try another.\');</script>';
} else {
$message = "<script language=\"javascript\">alert('".error_check('new',$email,$user_name,$password)."');</script>";
}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Admin Config: Admin Users Edit</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
</head>

<body>
<div class="container">
<div class="header_area"></div>
<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" valign="top" class="left_nav">
	<?PHP require('../includes/mainmenu.php'); ?>
 </td>
    <td valign="top"><div class="bc_nav"><?PHP echo brc(array('Admin Users' => 'admin-config/admin-users.php','Admin Users Edit' => 'admin-config/admin-users-edit.php?mode='.$mode.'&userid='.$userid)); ?></div>
	<?PHP if ($_SESSION['admin'] == 1) { ?> 
	  <?PHP echo $htmlfunctions->draw_form($form_name,''); ?>
<table border="0" align="center" cellspacing="3">
        <tr>
          <td></td>
          <td><?PHP echo (!empty($created) ? '<strong>Created:</strong> '.date('m/d/Y',strtotime($created)).$htmlfunctions->text_field('created','','',$created,'hidden').'</td>' : ''); ?></td>
          <td><?PHP echo (!empty($modified) ? '<strong>Modified:</strong> '.date('m/d/Y',strtotime($modified)).'</td>' : ''); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Status:</strong></td>
          <td colspan="2" class="field_bg">Enabled
            <?PHP echo $htmlfunctions->radio_button('status',($status == true ? true : ''),'',1); ?>
            Disabled
			<?PHP echo $htmlfunctions->radio_button('status',($status == false ? true : ''),'',0); ?>
            </td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Admin User: </strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->checkbox('admin',1,'',$admin_user); ?>            </td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>User Name:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('user_name',60,'',$user_name); ?>            </td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Password:</strong></td>
          <td colspan="2" valign="top" class="field_bg"><?PHP echo ($mode == 'new' ? $htmlfunctions->text_field('password',60,'','') : '<div id="password_area">'.$htmlfunctions->text_field('password',60,'','','password').'</div><div id="password_link"><a href="javascript: void()" onMouseDown="display_password_fld()">Click here to enter a new password.</a></div>'); ?></td>
          </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Full Name: </strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('full_name',60,'',$full_name); ?>
            </td></tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Email:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('email',40,'',$email); ?>            </td>
        </tr>
<!--         <tr>
          <td align="right" valign="top" class="field_title"><strong>Send Update Email Alerts:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->checkbox('alerts',1,'',$email_alerts); ?>            </td>
        </tr>
 -->        <tr align="center">
          <td colspan="3" valign="top">
		  <?PHP 
		  echo (!empty($user_id) ? $htmlfunctions->text_field('user_id','','',$user_id,'hidden').$htmlfunctions->text_field('update','','',1,'hidden') : $htmlfunctions->text_field('new','','',1,'hidden')); 
		  echo $htmlfunctions->submit_button('Submit');?></td>
          </tr>
      </table>
      </form>
	  	<?PHP 
	} else {
	echo 'You are not allowed to view this page unless you have been granted admin priviledges.';
	}
	?>
    </td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
<?PHP
echo $message;
?>
</body>
</html>
