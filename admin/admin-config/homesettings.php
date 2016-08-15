<?PHP
require '../includes/application_top.php';

// vars
$form_check = $_POST['form_check'];

// pull list of available settings
$settings_qry = mysql_query("SELECT * FROM store_settings WHERE contenttype = 'home' AND (hidden = '' OR hidden is null) ORDER BY name ASC;");
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
$field = $htmlfunctions->textarea('settings'.$settings_rslt['store_settings_id'],40,9,$field_value);
break;
}

$settings_list .= '<tr><td valign="top"'.$row_style.'><strong>'.$settings_rslt['name'].'</strong><br>'.$settings_rslt['description'].'</td><td'.$row_style.'>'.$field.'</td></tr>';
}


// submit changes
if ($form_check == 1) {
$settings_qry = mysql_query("SELECT store_settings_id FROM store_settings WHERE contenttype = 'home' AND hidden is null ORDER BY name ASC;");
while ($settings_rslt = mysql_fetch_array($settings_qry)) {
$field = 'settings'.$settings_rslt['store_settings_id'];
mysql_query("UPDATE store_settings SET value = '".clean_db_inserts($_POST[$field])."' WHERE store_settings_id = '".$settings_rslt['store_settings_id']."';");
}
update_last_updated();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Admin Config: Global Settings</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="../includes/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
	// Notice: The simple theme does not use all options some of them are limited to the advanced theme
tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
	
	plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",

	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor",
	theme_advanced_buttons3 : "search,replace,|,cut,copy,paste,pastetext,pasteword,|,sub,sup,|,charmap,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		
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
	<div class="bc_nav"><?PHP echo brc(array('Homepage Conf' => 'admin-config/homesettings.php')); ?></div>
    <form name="form1" method="post" action="">
      <table border="0" align="center" cellpadding="3">
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
