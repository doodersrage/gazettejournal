<?PHP
require '../includes/application_top.php';
require INCLUDES_DIRECTORY.'/classes/home_page.php';
$home_page = new home_page();

$templateval = ($_POST['templateval'] ? $_POST['templateval'] : HOMEPAGE_TEMPLATE_VAL);

if ($_POST['form_post'] == 1) {
mysql_query("UPDATE store_settings set value = '".$_POST['templateval']."' WHERE constant = 'HOMEPAGE_TEMPLATE_VAL'");
mysql_query("UPDATE store_settings set value = '".$_POST['left_article']."' WHERE constant = 'HOMEPAGE_LEFT_ARTICLE'");
mysql_query("UPDATE store_settings set value = '".$_POST['center_article']."' WHERE constant = 'HOMEPAGE_CENTER_ARTICLE'");
mysql_query("UPDATE store_settings set value = '".$_POST['right_article']."' WHERE constant = 'HOMEPAGE_RIGHT_ARTICLE'");
mysql_query("UPDATE store_settings set value = '".$_POST['center_article_image']."' WHERE constant = 'HOMEPAGE_IMAGE'");

// added 4/14/08 to handle article images
mysql_query("UPDATE store_settings set value = '".$_POST['left_article_image']."' WHERE constant = 'HOMEPAGE_LEFT_ARTICLE_IMAGE'");
mysql_query("UPDATE store_settings set value = '".$_POST['center_bottom_article_image']."' WHERE constant = 'HOMEPAGE_CENTER_ARTICLE_IMAGE'");
mysql_query("UPDATE store_settings set value = '".$_POST['right_article_image']."' WHERE constant = 'HOMEPAGE_RIGHT_ARTICLE_IMAGE'");
mysql_query("UPDATE store_settings set value = '".$_POST['center_top_article_image']."' WHERE constant = 'HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE'");

mysql_query("UPDATE store_settings set value = '".$_POST['left_article_image_position']."' WHERE constant = 'HOMEPAGE_LEFT_ARTICLE_IMAGE_POSITION'");
mysql_query("UPDATE store_settings set value = '".$_POST['center_bottom_article_image_position']."' WHERE constant = 'HOMEPAGE_CENTER_ARTICLE_IMAGE_POSITION'");
mysql_query("UPDATE store_settings set value = '".$_POST['right_article_image_position']."' WHERE constant = 'HOMEPAGE_RIGHT_ARTICLE_IMAGE_POSITION'");
mysql_query("UPDATE store_settings set value = '".$_POST['center_top_article_image_position']."' WHERE constant = 'HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE_POSITION'");

update_last_updated();
header('Location: templates.php');
}

// functions to print template data
function print_left_template() {
global $home_page;
$left_template = '<div class="articles_select_text">
		  Left Article:
		  </div>
		  <select class="article_select" name="left_article" onchange="form.submit();">
  		  <option>none</option>';

		  $articles_qry = mysql_query("SELECT articles_id, title FROM articles ORDER BY modified ASC;");
		  while ($articles_rslt = mysql_fetch_array($articles_qry)) {
$left_template .= '<option value="'.$articles_rslt['articles_id'].'" '.($articles_rslt['articles_id'] == HOMEPAGE_LEFT_ARTICLE ? 'SELECTED' : '').'>' . $articles_rslt['title'] . '</option>' . "\r\n";
		  }

$left_template .= '</select>';

$left_template .= '<br>Image:<br><select class="article_select" name="left_article_image" onchange="form.submit();">
  		  <option>none</option>';

		  $articles_qry = mysql_query("SELECT articles_images_id, image FROM articles_images WHERE articles_id = '".HOMEPAGE_LEFT_ARTICLE."' ORDER BY image ASC;");
		  while ($articles_rslt = mysql_fetch_array($articles_qry)) {
$left_template .= '<option value="'.$articles_rslt['articles_images_id'].'" '.($articles_rslt['articles_images_id'] == HOMEPAGE_LEFT_ARTICLE_IMAGE ? 'SELECTED' : '').'>' . $articles_rslt['image'] . '</option>' . "\r\n";
		  }

$left_template .= '</select>';

$left_template .= '<br>Image Position:<br><select class="article_select" name="left_article_image_position" onchange="form.submit();">
  		  <option '.(HOMEPAGE_LEFT_ARTICLE_IMAGE_POSITION == 'top' ? 'selected' : '').'>top</option>
  		  <option '.(HOMEPAGE_LEFT_ARTICLE_IMAGE_POSITION == 'left' ? 'selected' : '').'>left</option>
  		  <option '.(HOMEPAGE_LEFT_ARTICLE_IMAGE_POSITION == 'right' ? 'selected' : '').'>right</option>';
$left_template .= '</select>';

//$left_template .= $home_page->left_content(1);

return $left_template;
}

function print_center_template() {
global $home_page;
$center_template = '<div class="articles_select_text">
		  Center Bottom Article:
		  </div>
		  <select class="article_select" name="center_article" onchange="form.submit();">
  		  <option>none</option>';

		  $articles_qry = mysql_query("SELECT articles_id, title FROM articles ORDER BY modified ASC;");
		  while ($articles_rslt = mysql_fetch_array($articles_qry)) {
$center_template .= '<option value="'.$articles_rslt['articles_id'].'" '.($articles_rslt['articles_id'] == HOMEPAGE_CENTER_ARTICLE ? 'SELECTED' : '').'>' . $articles_rslt['title'] . '</option>' . "\r\n";
		  }
$center_template .= '</select>';

$center_template .= '<br>Image:<br><select class="article_select" name="center_bottom_article_image" onchange="form.submit();">
  		  <option>none</option>';

		  $articles_qry = mysql_query("SELECT articles_images_id, image FROM articles_images WHERE articles_id = '".HOMEPAGE_CENTER_ARTICLE."' ORDER BY image ASC;");
		  while ($articles_rslt = mysql_fetch_array($articles_qry)) {
$center_template .= '<option value="'.$articles_rslt['articles_images_id'].'" '.($articles_rslt['articles_images_id'] == HOMEPAGE_CENTER_ARTICLE_IMAGE ? 'SELECTED' : '').'>' . $articles_rslt['image'] . '</option>' . "\r\n";
		  }

$center_template .= '</select>';

$center_template .= '<br>Image Position:<br><select class="article_select" name="center_bottom_article_image_position" onchange="form.submit();">
  		  <option '.(HOMEPAGE_CENTER_ARTICLE_IMAGE_POSITION == 'top' ? 'selected' : '').'>top</option>
  		  <option '.(HOMEPAGE_CENTER_ARTICLE_IMAGE_POSITION == 'left' ? 'selected' : '').'>left</option>
  		  <option '.(HOMEPAGE_CENTER_ARTICLE_IMAGE_POSITION == 'right' ? 'selected' : '').'>right</option>';
$center_template .= '</select>';

//$center_template .= $home_page->center_content(1);
		  
return $center_template;
}

function print_center_image_template() {
global $home_page, $embed;

$center_image_template = '<div class="articles_select_text">
		  Center Top Article:
		  </div>
		  <select class="article_select" name="center_article_image" onchange="form.submit();">
  		  <option>none</option>';

		  $articles_qry = mysql_query("SELECT articles_id, title FROM articles ORDER BY modified ASC;");
		  while ($articles_rslt = mysql_fetch_array($articles_qry)) {
$center_image_template .= '<option value="'.$articles_rslt['articles_id'].'" '.($articles_rslt['articles_id'] == HOMEPAGE_IMAGE ? 'SELECTED' : '').'>' . $articles_rslt['title'] . '</option>' . "\r\n";
		  }
$center_image_template .= '</select>';

$center_image_template .= '<br>Image:<br><select class="article_select" name="center_top_article_image" onchange="form.submit();">
  		  <option>none</option>';

		  $articles_qry = mysql_query("SELECT articles_images_id, image FROM articles_images WHERE articles_id = '".HOMEPAGE_IMAGE."' ORDER BY image ASC;");
		  while ($articles_rslt = mysql_fetch_array($articles_qry)) {
$center_image_template .= '<option value="'.$articles_rslt['articles_images_id'].'" '.($articles_rslt['articles_images_id'] == HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE ? 'SELECTED' : '').'>' . $articles_rslt['image'] . '</option>' . "\r\n";
		  }

$center_image_template .= '</select>';

$center_image_template .= '<br>Image Position:<br><select class="article_select" name="center_top_article_image_position" onchange="form.submit();">
  		  <option '.(HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE_POSITION == 'top' ? 'selected' : '').'>top</option>
  		  <option '.(HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE_POSITION == 'left' ? 'selected' : '').'>left</option>
  		  <option '.(HOMEPAGE_CENTER_TOP_ARTICLE_IMAGE_POSITION == 'right' ? 'selected' : '').'>right</option>';
$center_image_template .= '</select>';

// disabled upon request 4/14/2008
#$center_image_template = '<div class="articles_select_text">
#		  Center Top Article:
#		  </div>
#		  <select class="article_select" name="center_article_image" onchange="form.submit();">
#            <option>none</option>';
#		  $articles_qry = mysql_query("SELECT articles_id, title, homepage_image FROM articles WHERE homepage_image is not null and homepage_image > 0 ORDER BY modified ASC;");
#		  while ($articles_rslt = mysql_fetch_array($articles_qry)) {
#$center_image_template .= '<option value="'.$articles_rslt['articles_id'].'" ';
#		  if ($articles_rslt['articles_id'] == HOMEPAGE_IMAGE) {
#$center_image_template .= 'SELECTED';
#		  $homepage_image = $articles_rslt['homepage_image'];
#		  }
#$center_image_template .= '>'.$articles_rslt['title'].'</option>' . "\r\n";
#		  }
#$center_image_template .= '</select>';	  

#$articles_query = mysql_query("SELECT image FROM articles_images WHERE articles_images_id = '".$homepage_image."' ORDER BY sort_order ASC LIMIT 0,1;");
#$articles_result = mysql_fetch_array($articles_query);

#$center_image_template .= $embed->determine_media_type($articles_result['image'],60,60,'','','','',SITE_ADDRESS.'images/');
		  
return $center_image_template;
}

function print_right_template() {
global $home_page;

$right_template = '<div class="articles_select_text">
		  Right Article:
		  </div>
		  <select class="article_select" name="right_article" onchange="form.submit();">
  		  <option>none</option>';

		  $articles_qry = mysql_query("SELECT articles_id, title FROM articles ORDER BY modified ASC;");
		  while ($articles_rslt = mysql_fetch_array($articles_qry)) {
$right_template .= '<option value="'.$articles_rslt['articles_id'].'" '.($articles_rslt['articles_id'] == HOMEPAGE_RIGHT_ARTICLE ? 'SELECTED' : '').'>' . $articles_rslt['title'] . '</option>' . "\r\n";
		  }

$right_template .= '</select>';

$right_template .= '<br>Image: <br><select class="article_select" name="right_article_image" onchange="form.submit();">
  		  <option>none</option>';

		  $articles_qry = mysql_query("SELECT articles_images_id, image FROM articles_images WHERE articles_id = '".HOMEPAGE_RIGHT_ARTICLE."' ORDER BY image ASC;");
		  while ($articles_rslt = mysql_fetch_array($articles_qry)) {
$right_template .= '<option value="'.$articles_rslt['articles_images_id'].'" '.($articles_rslt['articles_images_id'] == HOMEPAGE_RIGHT_ARTICLE_IMAGE ? 'SELECTED' : '').'>' . $articles_rslt['image'] . '</option>' . "\r\n";
		  }

$right_template .= '</select>';

$right_template .= '<br>Image Position:<br><select class="article_select" name="right_article_image_position" onchange="form.submit();">
  		  <option '.(HOMEPAGE_RIGHT_ARTICLE_IMAGE_POSITION == 'top' ? 'selected' : '').'>top</option>
  		  <option '.(HOMEPAGE_RIGHT_ARTICLE_IMAGE_POSITION == 'left' ? 'selected' : '').'>left</option>
  		  <option '.(HOMEPAGE_RIGHT_ARTICLE_IMAGE_POSITION == 'right' ? 'selected' : '').'>right</option>';
$right_template .= '</select>';

//$right_template .= $home_page->right_content(1);

return $right_template;
}


// This section handles homepage options
// vars
$form_check = $_POST['form_check'];
$edit = $_GET['edit'];

// pull list of available settings
$settings_qry = mysql_query("SELECT * FROM store_settings WHERE contenttype = 'home' AND (hidden = '' OR hidden is null) ORDER BY name ASC;");

$item_id = 0;

while ($settings_rslt = mysql_fetch_array($settings_qry)) {

// set row styling
$item_id == 1 ? $item_id = 0 : $item_id++;
$item_id == 1 ? $row_style = ' style="background:#EBEBEB; border-bottom:1px solid #666; padding-bottom:4px;" ' : $row_style = ' style="background:#FFF; border-bottom:1px solid #000; padding-bottom:4px; color:#666" ';

$field_updated = 'settings'.$settings_rslt['store_settings_id'];
$field_value = (isset($_POST[$field_updated]) ? $_POST[$field_updated] : $settings_rslt['value']);

// read and write proper field type
switch ($settings_rslt['type']) {
case 'textfield':
$field = $htmlfunctions->text_field('settings'.$settings_rslt['store_settings_id'],40,'',$field_value);
break;
case 'textarea':
$field = $htmlfunctions->textarea('settings'.$settings_rslt['store_settings_id'],40,9,$field_value);
break;
}

$settings_list .= '<tr><td valign="top"'.$row_style.'><strong>'.$settings_rslt['name'].'</strong><br>'.$settings_rslt['description'].'</td><td'.$row_style.'>'.($edit == $settings_rslt['store_settings_id'] ? $field : '<a href="?edit='.$settings_rslt['store_settings_id'].'">Click to edit.</a>' ).'</td></tr>';
}


// submit changes
if ($form_check == 1) {
mysql_query("UPDATE store_settings SET value = '".clean_db_inserts(str_replace(array("\'",'\"'),array("'",'"'),$_POST['settings'.$edit]))."' WHERE store_settings_id = '".$edit."';");
update_constants_file();
header("Location: templates.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Templates</title>
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
	<?PHP require('../includes/mainmenu.php'); ?></td>
    <td>
      <table width="100%">
        <tr>
          <td align="center"><div class="template_image">
		  <form name="form1" method="post" action="">
<?PHP if ($templateval == 1) {
echo 	  '<div class="articles_left" style="position:absolute;bottom:30px;left:52px;height:341px;width:137px;">';
echo 	  print_left_template();
echo 	  '</div>';
echo 	  '<div class="articles_center" style="position:absolute;bottom:30px;left:192px;height:171px;width:198px;">';
echo 	  print_center_template();
echo 	  '</div>';
echo 	  '<div class="articles_center_image" style="position:absolute;bottom:200px;left:192px;height:170px;width:198px;">';
echo 	  print_center_image_template();
echo 	  '</div>';
echo 	  '<div class="articles_right" style="position:absolute;bottom:30px;left:390px;height:341px;width:210px;">';
echo 	  print_right_template() . '</div>'; 
 } elseif ($templateval == 2) { 
echo 	  '<div class="articles_left" style="position:absolute;bottom:30px;left:52px;height:341px;width:137px;">';
echo 	  print_left_template();
echo 	  '</div>';
echo 	  '<div class="articles_center" style="position:absolute;bottom:135px;left:192px;height:120px;width:408px;">';
echo 	  print_center_template();
echo 	  '</div>';
echo 	  '<div class="articles_center_image" style="position:absolute;bottom:250px;left:192px;height:120px;width:408px;">';
echo 	  print_center_image_template();
echo 	  '</div>';
echo 	  '<div class="articles_right" style="position:absolute;bottom:30px;left:192px;height:107px;width:408px;">';
echo 	  print_right_template();
echo 	  '</div>';
} elseif ($templateval == 3) {
echo 	  '<div class="articles_left" style="position:absolute;bottom:250px;left:52px;height:120px;width:408px;">';
echo 	  print_left_template();
echo 	  '</div>';
echo 	  '<div class="articles_center" style="position:absolute;bottom:30px;left:52px;height:107px;width:408px;">';
echo 	  print_center_template();
echo 	  '</div>';
echo 	  '<div class="articles_center_image" style="position:absolute;bottom:135px;left:52px;height:120px;width:408px;">';
echo 	  print_center_image_template();
echo 	  '</div>';
echo 	  '<div class="articles_right" style="position:absolute;bottom:30px;left:460px;height:341px;width:137px;">';
echo 	  print_right_template();
echo 	  '</div>';
 } ?>
		  <p>
		    Select template to make active: <select name="templateval" onchange="form.submit();">
			<?PHP for ($template = 1; $template <= 3; $template++) {
			echo '<option value="'.$template.'" '.($templateval == $template ? 'selected' : '').'>Template '.$template.'</option>';
			}
			?>
		      </select>
		    <img src="../images/template.png" width="560" height="450">
		      
</div>
</p>
<a href="<?PHP echo SITE_ADDRESS; ?>" target="_blank">Click	here to review changes.
              </a>
		      <p>
		  <?PHP echo $htmlfunctions->text_field('form_post','','',1,'hidden'); ?>
		    <input type="submit" name="Submit" value="Submit">
</p>
		  </form>
</td>
        </tr>
        <tr>
          <td align="center"><form name="settings" method="post" action="">
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
    </form></td>
        </tr>
      </table></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
