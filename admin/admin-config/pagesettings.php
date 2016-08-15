<?PHP
require '../includes/application_top.php';
include_once(ADMIN_INCLUDES_DIRECTORY."/fckeditor/fckeditor.php");

// vars
$form_check = $_POST['form_check'];
$edit = $_GET['edit'];

// pull list of available settings
$settings_qry = mysql_query("SELECT * FROM store_settings WHERE " . (!empty($edit) ? "store_settings_id = '".$edit."' AND " : "contenttype = 'page' OR constant = 'CONTACT_US_TEXT' AND") . " (hidden = '' OR hidden is null) ORDER BY name ASC;");
$item_id = 0;
while ($settings_rslt = mysql_fetch_array($settings_qry)) {

// set row styling
$item_id == 1 ? $item_id = 0 : $item_id++;
$item_id == 1 ? $row_style = ' style="background:#EBEBEB; border-bottom:1px solid #666; padding-bottom:4px;" ' : $row_style = ' style="background:#FFF; border-bottom:1px solid #000; padding-bottom:4px; color:#666" ';

$field_updated = 'settings'.$settings_rslt['store_settings_id'];
$field_value = (isset($_POST[$field_updated]) ? str_replace(array("\'",'\"'),array("'",'"'),$_POST[$field_updated]) : $settings_rslt['value']);

// read and write proper field type
switch ($settings_rslt['type']) {
case 'textfield':
$field = $htmlfunctions->text_field('settings'.$settings_rslt['store_settings_id'],40,'',$field_value);
break;
case 'textarea':
//		  $oFCKeditor = new FCKeditor('settings'.$settings_rslt['store_settings_id']) ;
//		  $oFCKeditor->BasePath = '../includes/fckeditor/' ;
//		  $oFCKeditor->Value = $field_value;
		  
//$field = $oFCKeditor->Create();

$field = $htmlfunctions->textarea('settings'.$settings_rslt['store_settings_id'],40,26,$field_value);
break;
}

$settings_list .= '<tr><td valign="top"'.$row_style.($edit == $settings_rslt['store_settings_id'] ? ' colspan="2"' : '').'><strong>'.$settings_rslt['name'].'</strong><br>'.$settings_rslt['description'].($edit == $settings_rslt['store_settings_id'] ? '<br>'.$field : '</td><td'.$row_style.'>'.'<a href="?edit='.$settings_rslt['store_settings_id'].'">Click to edit.</a>' ).'</td></tr>';
}


// submit changes
if ($form_check == 1) {
$sql = "UPDATE store_settings SET value = ? WHERE store_settings_id = ?;";
$values = array(str_replace(array("\'",'\"'),array("'",'"'),$_POST['settings'.$edit]),$edit);
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);
update_last_updated();
header ('Location: pagesettings.php');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Admin Config: Page Settings</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="../includes/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
	// Notice: The simple theme does not use all options some of them are limited to the advanced theme
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor",
	theme_advanced_buttons3 : "search,replace,|,cut,copy,paste,pastetext,pasteword,|,sub,sup,|,charmap,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",

		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
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
    <td valign="top">
	<div class="bc_nav"><?PHP echo brc(array('Pages Content' => 'admin-config/pagesettings.php')); ?></div>
    <form name="form1" method="post" action="">
      <table border="0" align="center" cellpadding="3" width="600">
        <tr>
          <td class="table_header"><strong>Name</strong></td>
          <td class="table_header"><strong>Value</strong></td>
        </tr>
       <?PHP echo $settings_list; ?>
        <tr align="center">
          <td colspan="2"><?PHP 
		  echo $htmlfunctions->text_field('form_check','','',1,'hidden'); 
		  echo $htmlfunctions->submit_button('Submit');?></td>
          </tr>
      </table>
    </form>	</td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
