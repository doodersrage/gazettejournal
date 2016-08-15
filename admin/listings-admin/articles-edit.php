<?PHP
require '../includes/application_top.php';

// vars
$section_id = $_POST['section_id'];
$artid = $_GET['artid'];
$listing_id = $_POST['listing_id'];
$mode = $_GET['mode'];
$sub_category = (!empty($_POST['sub_category']) ? $_POST['sub_category'] : $_GET['sub_id']);
$new_image = basename( $_FILES['image']['name']);
$target_path = IMAGES_DIRECTORY . $new_image; 
$date_start = clean_date_string($_POST['date_start']);
$date_end = clean_date_string($_POST['date_end']);
$title = str_replace(array("\'",'\"'),array("'",'"'),$_POST['title']);
$summary = str_replace(array("\'",'\"'),array("'",'"'),$_POST['summary']);
$content = str_replace(array("\'",'\"'),array("'",'"'),$_POST['content']);
$current_image = $_POST['current_image'];
$section_id = (isset($_GET['secid']) ? $_GET['secid'] : $_POST['section_id']);
$updated = $_POST['updated'];
$new = $_POST['new'];
$source = $_POST['source'];
$event_date = clean_date_string($_POST['event_date']);

switch ($section_id) {
case 1:
$section_name = 'classifieds';
break;
case 2:
if ($sub_category == 3) {
$section_name = 'births';
} else {
$section_name = 'weddings';
}
break;
case 3:
$section_name = 'obituaries';
break;
}


// pull existing article info in edit mode
if ($mode == 'edit' && empty($listing_id)) {

$listings_qry = mysql_query("SELECT * FROM listings WHERE customers_articles_id = '".$artid."';");
$listings_result = mysql_fetch_array($listings_qry);

$listing_id = $listings_result['customers_articles_id'];
$date_start = out_put_date_string($listings_result['start_date']);
$date_end = out_put_date_string($listings_result['exp_date']);
$title = $listings_result['title'];
$summary = $listings_result['summary'];
$content = $listings_result['content'];
$current_image = $listings_result['image'];
$section_id = $listings_result['section_id'];
$sub_category = $listings_result['sub_id'];
$store_customers_id = $listings_result['store_customers_id'];
$orders_id = $listings_result['orders_id'];
$source = $listings_result['source'];
$event_date = out_put_date_string($listings_result['event_date']);

switch ($section_id) {
case 1:
$section_name = 'classifieds';
break;
case 2:
if ($sub_category == 3) {
$section_name = 'births';
} else {
$section_name = 'weddings';
}
break;
case 3:
$section_name = 'obituaries';
break;
}

}

// update existing article
if (!empty($listing_id) && $updated == 1) {
move_uploaded_file($_FILES['image']['tmp_name'], $target_path);

!empty($current_image) && empty($image_name) ? $image_name = $current_image : $image_name = $new_image;

$values = array($date_start,$date_end,$title,$summary,$content,$image_name,$sub_category,$source,$event_date,$listing_id);
$sql = "UPDATE listings SET modified = NOW(), start_date = ? ,exp_date = ? ,title = ? ,summary = ? ,content = ? ,image = ?,sub_id = ?,source=?,event_date=? WHERE customers_articles_id = ?;";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

update_last_updated();
header('Location: articles.php?update=article_updated&type='.$section_name);
}

// post new article
if ($new == 1) {
$target_path = IMAGES_DIRECTORY . $new_image; 
move_uploaded_file($_FILES['image']['tmp_name'], $target_path);

$values = array($date_start,$date_end,$title,$summary,$content,$new_image,$section_id,$sub_category,$source,$event_date);
$sql = "INSERT INTO listings (added,modified,start_date,exp_date,title,summary,content,image,section_id,sub_id,source,event_date) VALUES (NOW(),NOW(),?,?,?,?,?,?,?,?,?,?);";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

update_last_updated();
header('Location: articles.php?update=article_added&type='.$section_name);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Listings: Articles Edit</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../includes/epoch/epoch_styles.css" /> <!--Epoch's styles-->
<script type="text/javascript" src="../includes/epoch/epoch_classes.js"></script> <!--Epoch's Code-->
<script type="text/javascript">
/*<![CDATA[*/
/*You can also place this code in a separate file and link to it like epoch_classes.js*/
	var bas_cal,dp_cal,ms_cal;      
window.onload = function () {
	dp_cal  = new Epoch('epoch_popup','popup',document.getElementById('date_start'));
	dp_cal1  = new Epoch('epoch_popup1','popup',document.getElementById('date_end'));
	dp_cal1  = new Epoch('epoch_popup1','popup',document.getElementById('event_date'));
};
/*]]>*/
</script>
<script language="javascript" type="text/javascript" src="../includes/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
	// Notice: The simple theme does not use all options some of them are limited to the advanced theme
	tinyMCE.init({
		// General options
		mode : "none",
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
<div class="container">
<div class="header_area"></div>
<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" valign="top" class="left_nav">
	<?PHP require('../includes/mainmenu.php'); ?></td>
    <td valign="top"><div class="bc_nav">
	<?PHP 
	$breadc = array(ucfirst($section_name) => 'listings-admin/articles.php?type='.$section_name, ucfirst($section_name) . ' Edit' => 'listings-admin/articles-edit.php?mode='.$mode.'&artid='.$artid);
	echo brc($breadc);
	?>
	</div>
	  <form action="" method="post" enctype="multipart/form-data" name="form1">
<table border="0" align="center" cellpadding="0" cellspacing="3">
        <tr>
          <td></td>
          <td><strong>Start Date:</strong> <?PHP echo $htmlfunctions->text_field('date_start',10,'date_start',$date_start); ?>		  </td>
          <td><strong>Expiration Date:</strong> 
            <?PHP echo $htmlfunctions->text_field('date_end',10,'date_end',$date_end); ?>
            </td>
        </tr>
		<?PHP if (!empty($store_customers_id)) { ?>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Classified Posted By:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo get_customers_name($store_customers_id); ?></td>
        </tr>
		<?PHP } ?>
		<?PHP if (!empty($orders_id)) { ?>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>In Order:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo '<a href="'.ADMIN_ADDRESS.'store-admin/orders-view.php?orderid='.$orders_id.'">'.$orders_id.'</a>'; ?></td>
        </tr>
		<?PHP } ?>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Title:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('title',60,'',$title); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Image:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->filebox('image','image'); 
		  echo (!empty($current_image) ? '<br><strong>Current Image:</strong>'.'&nbsp;&nbsp;'.$current_image.$htmlfunctions->text_field('current_image','','',$current_image,'hidden') : '');
		  ?></td>
        </tr>
		<?PHP if ($section_id == 1 || $section_id == 2) { ?>
		<tr>
		<td align="right" valign="top" class="field_title"><strong>
		<?PHP
		if ($section_id == 1) {
		echo 'Classifieds Sub-Category:';
		} else {
		echo 'Type:';
		}
		?>
		</strong></td>
		<td colspan="2">
		<select name="sub_category">
		<?PHP 
		if ($section_id == 1) {
		$classified_sub_query = mysql_query("SELECT classifieds_cat_id, name FROM classifieds_categories ;");
		while ($classified_result = mysql_fetch_array($classified_sub_query)) {
		echo '<option value="'.$classified_result['classifieds_cat_id'].'" '.($classified_result['classifieds_cat_id'] == $sub_category ? 'selected' : '').'>'.$classified_result['name'].'</option>';
		}
		} else {
		$classified_sub_query = mysql_query("SELECT events_categories_id, name FROM events_categories ;");
		while ($classified_result = mysql_fetch_array($classified_sub_query)) {
		echo '<option value="'.$classified_result['events_categories_id'].'" '.($classified_result['events_categories_id'] == $sub_category ? 'selected' : '').'>'.$classified_result['name'].'</option>';
		}
		}
		?>
		</select>
		</td>
		</tr>
		<?PHP if ($section_id == 3 || $section_id == 2) { ?>
		<tr>
		  <td align="right" valign="top" class="field_title">Source:</td>
		  <td colspan="2"><select name="source">
		  <option>None</option>
		  <?PHP
		  // source array
		  $sources = array('GJ','GG','MJ','NR','FP');
		  
		  foreach ($sources as $sourceopt) {
		  
		  echo '<option value="'.$sourceopt.'" '.($source == $sourceopt ? 'selected' : '').'>'.$sourceopt.'</option>';
		  
		  }		  
		  ?>
		    </select></td>
		  </tr>
		<tr>
		  <td align="right" valign="top" class="field_title">Event Date: </td>
		  <td colspan="2"><?PHP echo $htmlfunctions->text_field('event_date',10,'event_date',$event_date); ?></td>
		  </tr>
		  <?PHP } ?>
		<?PHP } ?>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Summary:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->textarea('summary',60,5,$summary,'summary') ?>
		  <script language="javascript">tinyMCE.execCommand('mceAddControl', false, 'summary');</script>
		  </td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Content:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->textarea('content',60,10,$content,'content') ?>
		  <script language="javascript">tinyMCE.execCommand('mceAddControl', false, 'content');</script></td>
        </tr>
        <tr align="center">
          <td colspan="3" valign="top"><?PHP 
		  echo (!empty($listing_id) ? $htmlfunctions->text_field('listing_id','','',$listing_id,'hidden').$htmlfunctions->text_field('section_id','','',$section_id,'hidden').$htmlfunctions->text_field('updated','','',1,'hidden') : $htmlfunctions->text_field('new','','',1,'hidden')).$htmlfunctions->text_field('section_id','','',$section_id,'hidden'); 
		  echo $htmlfunctions->submit_button('Submit');?></td>
          </tr>
      </table>
      </form>
	  
    </td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
