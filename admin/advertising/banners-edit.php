<?PHP
require '../includes/application_top.php';

// set page vars
$mode = $_GET['mode'];
$banners_id = $_GET['bid'];
$update_id = $_POST['update_id'];
$new = $_POST['new'];

// banners vars
$status = $_POST['status'];
$name = str_replace(array("\'",'\"'),array("'",'"'),$_POST['name']);
$image_alt_text = str_replace(array("\'",'\"'),array("'",'"'),$_POST['image_alt_text']);
$link = $_POST['link'];
$new_window = $_POST['new_window'];
//$image = $_POST['image'];
$weight = $_POST['weight'];
$current_image = str_replace(array("\'",'\"'),array("'",'"'),$_POST['current_image']);


if ($mode == 'edit' && !empty($banners_id) && empty($update_id)) {
$banners_query = mysql_query("SELECT banner_id, name, image, image_alt_text, link, new_window, weight, status, created, edited FROM banners WHERE banner_id = '".$banners_id."';");
$banners_result = mysql_fetch_array($banners_query);

$created = $banners_result['created'];
$modified = $banners_result['edited'];
$status = $banners_result['status'];
$name = $banners_result['name'];
$image_alt_text = $banners_result['image_alt_text'];
$link = $banners_result['link'];
$new_window = $banners_result['new_window'];
$image = $banners_result['image'];
$weight = $banners_result['weight'];
}

// insert new product
if ($new == 1) {
if (!empty($name)) {
$target_path = IMAGES_DIRECTORY . basename( $_FILES['image']['name']); 
move_uploaded_file($_FILES['image']['tmp_name'], $target_path);

mysql_query("INSERT INTO banners (created, name, image, image_alt_text, link, new_window, weight, status) VALUES (NOW(),'".clean_db_inserts($name)."','".$_FILES['image']['name']."','".clean_db_inserts($image_alt_text)."','".$link."','".$new_window."','".$weight."','".$status."');");
header('Location: banners.php');
} else {
$message = '<script language="javascript">alert(\'You must atleast assign a product name.\');</script>';
}
}

// update existing product
if ($mode == 'edit' && !empty($update_id)) {
if (!empty($name)) {

// find and set image
if (!empty($current_image) && empty($_FILES['image']['name'])) {
$image_name = $current_image;
} else {
$target_path = IMAGES_DIRECTORY . basename( $_FILES['image']['name']); 
move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
$image_name = $_FILES['image']['name'];
}


mysql_query("UPDATE banners set name='".clean_db_inserts($name)."', image='".$image_name."', image_alt_text='".clean_db_inserts($image_alt_text)."', link='".$link."', new_window='".$new_window."', weight='".$weight."', status='".$status."' WHERE banner_id = '".$update_id."';");
header('Location: banners.php');
} else {
$message = '<script language="javascript">alert(\'You must atleast assign a product name.\');</script>';
}
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Banners Edit</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
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
	<div class="bc_nav"><?PHP echo brc(array('Banners' => 'advertising/banners.php','Banners Edit' => 'advertising/banners-edit.php?mode='.$mode.'&bid='.$items_id)); ?></div>
	<form action="" method="post" enctype="multipart/form-data" name="customer_edit">
      <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="horizontal_pad">&nbsp;</td>
          <td class="horizontal_pad"><?PHP if (!empty($created)) echo '<strong> Created:</strong>'.date('m/d/Y',strtotime($created)); ?></td>
          <td class="horizontal_pad"><?PHP if (!empty($modified)) echo '<strong>Modified:</strong>'.date('m/d/Y',strtotime($modified)); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Status: </strong></td>
          <td valign="top" colspan="2">Enabled
              <input name="status" type="radio" value="1" <?PHP if ($status == 1) echo 'checked'; ?>>
        Disabled
        <input name="status" type="radio" value="0" <?PHP if ($status == 0) echo 'checked'; ?>></td>
        </tr>
        <tr>
          <td align="right"><strong>Name:</strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->text_field('name',40,'',$name); ?></td>
          </tr>
        <tr>
          <td align="right" valign="top"><strong>Image:</strong></td>
          <td colspan="2"><input name="image" type="file" id="image"><?PHP echo (!empty($image) ? 'Current Image: ' . $image . $htmlfunctions->text_field('current_image','','',$image,'hidden') : ''); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Image Alternate Text:</strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->text_field('image_alt_text',50,'',$image_alt_text); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Link:</strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->text_field('link',50,'',$link); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Open New Window On Click:</strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->checkbox ('new_window',1,'new_window',$new_window); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Listing Weight:</strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->text_field('weight',10,'',$weight); ?></td>
        </tr>
        <tr align="center">
          <td colspan="3"><?PHP echo (!empty($banners_id) ? $htmlfunctions->text_field('update_id','','',$banners_id,'hidden').$htmlfunctions->text_field('update','','',1,'hidden') : $htmlfunctions->text_field('new','','',1,'hidden')); ?><input type="submit" name="Submit" value="Submit"></td>
        </tr>
      </table>
    </form></td>
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
