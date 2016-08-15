<?PHP
require '../includes/application_top.php';

// set page vars
$mode = $_GET['mode'];
$items_id = $_GET['pid'];
$update_id = $_POST['update_id'];
$new = $_POST['new'];

//item vars
$status = $_POST['status'];
$name = str_replace(array("\'",'\"'),array("'",'"'),$_POST['name']);
$price = $_POST['price'];
$extprice = $_POST['extprice'];
$description = str_replace(array("\'",'\"'),array("'",'"'),$_POST['description']);
$content = str_replace(array("\'",'\"'),array("'",'"'),$_POST['content']);
$current_image = $_POST['current_image'];
$item_type = $_POST['item_type'];
$allow_cart_button = $_POST['allow_cart_button'];

if ($mode == 'edit' && !empty($items_id) && empty($update_id)) {
$products_query = mysql_query("SELECT created, edited, name, description, content, price, extprice, status, image, type, allow_cart_button FROM items WHERE items_id = '".$items_id."';");
$products_result = mysql_fetch_array($products_query);

$created = $products_result['created'];
$modified = $products_result['edited'];
$status = $products_result['status'];
$name = $products_result['name'];
$price = $products_result['price'];
$extprice = $products_result['extprice'];
$description = $products_result['description'];
$content = $products_result['content'];
$image = $products_result['image'];
$item_type = $products_result['type'];
$allow_cart_button = $products_result['allow_cart_button'];
}

// insert new product
if ($new == 1) {
if (!empty($name)) {
$target_path = IMAGES_DIRECTORY . basename( $_FILES['image']['name']); 
move_uploaded_file($_FILES['image']['tmp_name'], $target_path);

mysql_query("INSERT INTO items (created,edited,name,description,content,price,status,image,type,extprice,allow_cart_button) VALUES (NOW(),NOW(),'".clean_db_inserts($name)."','".clean_db_inserts($description)."','".clean_db_inserts($content)."','".$price."','".$status."','".$_FILES['file']['name']."','".$item_type."','".$extprice."','".$allow_cart_button."');");
update_last_updated();
header('Location: products.php');
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

mysql_query("UPDATE items SET edited = NOW(), image = '".$image_name."',name = '".clean_db_inserts($name)."',description = '".clean_db_inserts($description)."',content = '".clean_db_inserts($content)."',price = '".$price."',extprice = '".$extprice."',status = '".$status."',type = '".$item_type."',allow_cart_button = '".$allow_cart_button."' WHERE items_id = '".$update_id."';");
update_last_updated();
header('Location: products.php');
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
<title>Store: Products Edit</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
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
	});</script>
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
	<div class="bc_nav"><?PHP echo brc(array('Items' => 'store-admin/products.php','Items Edit' => 'store-admin/products-edit.php?mode='.$mode.'&pid='.$items_id)); ?></div>
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
          <td align="right" valign="top"><strong>Item Type: </strong></td>
          <td valign="top" colspan="2"><select name="item_type" id="item_type">
            <option value="1" <?PHP if ($item_type == 1) echo 'selected'; ?>>Subscription</option>
            <option value="2" <?PHP if ($item_type == 2) echo 'selected'; ?>>Item</option>
            <option value="3" <?PHP if ($item_type == 3) echo 'selected'; ?>>Classified</option>
          </select></td>
        </tr>
        <tr>
          <td align="right"><strong>Name:</strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->text_field('name',40,'',$name); ?></td>
          </tr>
        <tr>
          <td align="right" valign="top"><strong>Price:</strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->text_field('price',10,'',$price); ?></td>
          </tr>
        <tr>
          <td align="right" valign="top"><strong>Extended Price: </strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->text_field('extprice',10,'',$extprice); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Display Add to Cart Button: </strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->checkbox('allow_cart_button',1,'allow_cart_button',$allow_cart_button); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Image:</strong></td>
          <td colspan="2"><input name="image" type="file" id="image"><?PHP echo (!empty($image) ? 'Current Image: ' . $image . $htmlfunctions->text_field('current_image','','',$image,'hidden') : ''); ?></td>
          </tr>
        <tr>
          <td align="right" valign="top"><strong>Description:</strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->textarea('description',60,5,$description,'description') ?></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Content:</strong></td>
          <td colspan="2"><?PHP echo $htmlfunctions->textarea('content',60,10,$content,'content') ?></td>
        </tr>
        <tr align="center">
          <td colspan="3"><?PHP echo (!empty($items_id) ? $htmlfunctions->text_field('update_id','','',$items_id,'hidden').$htmlfunctions->text_field('update','','',1,'hidden') : $htmlfunctions->text_field('new','','',1,'hidden')); ?><input type="submit" name="Submit" value="Submit"></td>
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
