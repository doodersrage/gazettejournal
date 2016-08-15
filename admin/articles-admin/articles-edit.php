<?PHP
require '../includes/application_top.php';
include_once(ADMIN_INCLUDES_DIRECTORY."/fckeditor/fckeditor.php");


// assign vars
$mode = $_GET['mode'];
$article_id = (!empty($_GET['artid']) ? $_GET['artid'] : $_POST['article_id']);
if (!isset($_SESSION['article_session']) || $_SESSION['article_list'] == 1) {
$_SESSION['article_list'] = 0;
$_SESSION['article_session'] = md5(date(r));
// reset article session vars
unset($_SESSION['title']);
unset($_SESSION['author']);
unset($_SESSION['summary']);
unset($_SESSION['content']);
unset($_SESSION['categories']);
unset($_SESSION['read_more_link']);
unset($_SESSION['article_sort_order']);

$article_title = '';
$article_author = '';
$article_summary = '';
$article_content = '';
$selected_categories = '';
$read_more_link = '';
$article_sort_order = '';

} else {
$article_title = htmlentities($_SESSION['title']);
$article_author = htmlentities($_SESSION['author']);
$article_summary = $_SESSION['summary'];
$article_content = $_SESSION['content'];
$selected_categories = $_SESSION['categories'];
$read_more_link = $_SESSION['read_more_link'];
$article_sort_order = $_SESSION['article_sort_order'];
}


$homepage_image = $_POST['homepage_image'];
$article_title_tag = str_replace(array("\'",'\"'),array("'",'"'),$_POST['title_tag']);
$article_meta_description = $_POST['meta_description'];
$article_meta_keywords = $_POST['meta_keywords'];
$front_section = $_POST['front_section'];
$template = $_POST['template'];
$deleted_images = $_POST['deleted_images'];
$article_sort_order = $_POST['article_sort_order'];


// pull existing article info in edit mode
if ($mode == 'edit' && !isset($_POST['article_id'])) {
if (isset($_SESSION['article_session'])) unset($_SESSION['article_session']);

$article_qry = mysql_query("SELECT * FROM articles WHERE articles_id = '".$article_id."';");
$article_result = mysql_fetch_array($article_qry);

$created = $article_result['created'];
$modified = $article_result['modified'];
$article_title = $article_result['title'];
$article_author = $article_result['author'];
$article_summary = $article_result['summary'];
$article_content = $article_result['content'];
$selected_option_name = $article_result['status'];
$front_section = $article_result['front_section'];
$template = $article_result['template'];
$homepage_image = $article_result['homepage_image'];
$article_sort_order = $article_result['sort_order'];
$read_more_link = $article_result['read_more_link'];
// bof assign set categories
$categories_qry = mysql_query("SELECT categories_id FROM articles_to_categories WHERE articles_id = '".$article_id."';");
$categories_ids = array();
while ($categories_result = mysql_fetch_array($categories_qry)) {
array_push($categories_ids,$categories_result['categories_id']);
}
// eof assign set categories
$selected_categories = $categories_ids;
$article_title_tag = $article_result['title_tag'];
$article_meta_description = $article_result['meta_description'];
$article_meta_keywords = $article_result['meta_keywords'];


} else {
clear_article_session_data();
}

// bof build image list
if (!empty($_SESSION['article_session'])) {
$articles_images_qry = mysql_query("SELECT articles_images_id, articles_id, image, sort_order, caption FROM articles_images WHERE new_article_session = '".$_SESSION['article_session']."';");
} elseif (!empty($article_id)) {
$articles_images_qry = mysql_query("SELECT articles_images_id, articles_id, image, sort_order, caption FROM articles_images WHERE articles_id = '".$article_id."';");
}
while ($articles_images_result = mysql_fetch_array($articles_images_qry)) {
$image_list .= '<tr id="image'.$articles_images_result['articles_images_id'].'"><td valign="middle" style="padding:3px;"><a href="javascript: void(0)" onMouseDown="javascript: new_centered_popup('.ARTICLE_IMAGE_WIDTH.','.ARTICLE_IMAGE_HEIGHT.',\''.IMAGES_ADDRESS.$articles_images_result['image'].'\')">'.$embed->determine_media_type($articles_images_result['image'],55,55,'','','','',SITE_ADDRESS.'images/').'</a></td>
<td valign="middle"><a href="javascript: void(0)" onMouseDown="javascript: new_centered_popup('.ARTICLE_IMAGE_WIDTH.','.ARTICLE_IMAGE_HEIGHT.',\''.IMAGES_ADDRESS.$articles_images_result['image'].'\')">'.$articles_images_result['image'].'</a></td>
<td valign="middle" align="center">'.$htmlfunctions->text_field('image_sort'.$articles_images_result['articles_images_id'],4,'',$articles_images_result['sort_order']).'</td>
<td valign="middle" align="center">'.$htmlfunctions->radio_button('homepage_image',($articles_images_result['articles_images_id'] == $homepage_image ? 1 : ''),'',$articles_images_result['articles_images_id']).'</td>
<td valign="middle" align="center">'.$htmlfunctions->textarea('image_caption'.$articles_images_result['articles_images_id'],15,3,$articles_images_result['caption'],'image_caption'.$articles_images_result['articles_images_id']).'</td>
<td valign="top"><a href="javascript: remove_image('.$articles_images_result['articles_images_id'].');" onClick="return confirm(\'Are you sure you want to delete this item?\')" class="button_new">Delete</a></td></tr>';
}
// eof build image list

// build status list
$status_list_qry = mysql_query("SELECT articles_status_id, status FROM articles_status ORDER BY articles_status_id ASC;");
$status_ids = array();
$status_names = array();
while ($status_list_result = mysql_fetch_array($status_list_qry)) {
array_push($status_ids,$status_list_result['articles_status_id']);
array_push($status_names,$status_list_result['status']);
}

// build categories list
$categories_list_qry = mysql_query("SELECT categories_id, name FROM categories ORDER BY categories_id ASC;");
$categories_list = '';
while ($categories_list_result = mysql_fetch_array($categories_list_qry)) {
$categories_list .= '<td align="right">'.$categories_list_result['name'].'</td>
              <td>' . $htmlfunctions->checkbox('categories[]',$categories_list_result['categories_id'],'',(!empty($selected_categories) ? in_array($categories_list_result['categories_id'],$selected_categories) ? 1 : '' : '')) . '</td>
            </tr>';
}

if (!empty($article_id)) $formname = 'edit_article'; else $formname = 'new_article';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Articles: Articles Edit</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/jquery-1.2.6.min.js" type="text/javascript"></script>
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
	});
</script>
</head>
<body>
<div class="container">
  <div class="header_area"></div>
  <div class="content">
    <table width="800" border="0" align="center" cellpadding="3" class="main_table">
      <tr>
      <td width="127" valign="top" class="left_nav">      <?PHP require('../includes/mainmenu.php'); ?>
      </td>
      <td valign="top">
      <div class="bc_nav"><?PHP echo brc(array('Articles' => 'articles-admin/articles.php','Articles Edit' => 'articles-admin/articles-edit.php?mode='.$mode.'&artid='.$article_id)); ?></div>
      <?PHP echo $htmlfunctions->draw_form($formname,'articles_preview.php?mode='.$mode,'','multipart/form-data'); ?>
      <table border="0" align="center" cellpadding="0" cellspacing="3">
        <tr>
          <td></td>
          <td><?PHP echo (!empty($created) ? '<strong>Created:</strong> '.date('m/d/Y',strtotime($created)).'</td>' : ''); ?></td>
          <td><?PHP echo (!empty($modified) ? '<strong>Modified:</strong> '.date('m/d/Y',strtotime($modified)).'</td>' : ''); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Status:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->select_box('status',$status_ids,$status_names,$selected_option_name); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Title:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('title',70,'',$article_title); ?> </td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Sort Order:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('article_sort_order',10,'',$article_sort_order); ?> </td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Hide Read More Link:</strong></td>
          <td colspan="2" class="field_bg"><input name="read_more_link" type="checkbox" value="1" <?PHP if ($read_more_link == 1) 'checked'; ?>></td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Images:</strong></td>
          <td colspan="2" valign="top" class="field_bg">
		  <table width="100%">
		  <tr><td class="table_header">Preview</td><td class="table_header">Name</td><td class="table_header">Sort Order</td><td class="table_header">Main Image</td><td class="table_header">Caption</td><td class="table_header">Options</td></tr>
              <?PHP echo $image_list; ?>
          </table>
		  <?PHP echo $htmlfunctions->text_field('deleted_images','','deleted_images','','hidden'); ?>
		  
            <table width="100" border="0" align="center" cellpadding="3" id="images_table"><tr><td><table>
              <tr>
                <td colspan="2">Browse to where the image is stored then click submit to upload. </td>
              </tr>
              <tr>
                <td>Main Image:
                  <?PHP echo $htmlfunctions->radio_button('homepage_image','','','new_image_1'); ?></td>
                <td>Sort Order:
                  <input name="sort_order[1]" type="text" size="4"></td>
              </tr>
              <tr>
                <td colspan="2"><input type="file" name="image[1]"></td>
              </tr>
              <tr>
                <td colspan="2">Caption:<br>
                  <?PHP echo $htmlfunctions->textarea('image_caption[1]',30,3,'','image_caption[1]') ?></td>
              </tr>
              <tr>
                <td colspan="2"><a href="javascript: new_image_fld();">Add Image</a></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  </td>
              </tr></table></td></tr>
            </table>
			<div id="extra_images">
			</div>
			</td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Author:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('author',60,'',$article_author); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Upload File:</strong></td>
          <td colspan="2" class="field_bg"><input type="file" name="file">
		  <?PHP
		  $file_query = mysql_query("SELECT file_name FROM articles_files WHERE articles_id = '".$article_id."';");
		  $file_result = mysql_fetch_array($file_query);
		  if (!empty($file_result['file_name'])) echo '<span id="file_info"> Current File: ' . $file_result['file_name'] . ' <a href="javascript: remove_file(1);" onClick="return confirm(\'Are you sure you want to delete this item?\')">Delete</a></span>'.$htmlfunctions->text_field('delete_file','','delete_file','','hidden');
		  ?>
		  </td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Summary:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->textarea('summary',60,8,$article_summary,'summary') ?>
            <!-- <script language="javascript">tinyMCE.execCommand('mceAddControl', false, 'summary');</script> --></td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Content:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->textarea('content',60,13,$article_content,'content') ?>
            <!-- <script language="javascript">tinyMCE.execCommand('mceAddControl', false, 'content');</script> -->
          </td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Categories:</strong></td>
          <td colspan="2" class="field_bg"><table border="0" cellpadding="2">
              <?PHP echo $categories_list; ?>
            </table>
            <br>
          </td>
        </tr>        
 <!--         <tr>
          <td align="right" valign="top" class="field_title"><strong>Template:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->select_box('template',array('default'),array('default'),$template); ?> </td>
      </tr>
        <tr align="center" class="art_status">
          <td colspan="3" valign="top"><strong>Header Content </strong></td>
        </tr>
        <tr align="center">
          <td align="right" valign="top" class="field_title"><strong>Title Tag: </strong></td>
          <td colspan="2" align="left" valign="top" class="field_bg"><?PHP echo $htmlfunctions->text_field('title_tag',60,'',$article_title_tag); ?></td>
        </tr>
        <tr align="center">
          <td align="right" valign="top" class="field_title"><strong>Meta Description:</strong></td>
          <td colspan="2" align="left" valign="top" class="field_bg"><?PHP echo $htmlfunctions->textarea('meta_description',60,5,$article_meta_description,'meta_description') ?></td>
        </tr>
        <tr align="center">
          <td align="right" valign="top" class="field_title"><strong>Meta Keywords: </strong></td>
          <td colspan="2" align="left" valign="top" class="field_bg"><?PHP echo $htmlfunctions->textarea('meta_keywords',60,4,$article_meta_keywords,'meta_keywords') ?></td>
      </tr> --> 

        <tr align="center">
          <td colspan="3" valign="top"><?PHP 
		  echo (!empty($article_id) ? $htmlfunctions->text_field('article_id','','',$article_id,'hidden') : $htmlfunctions->text_field('new','','',1,'hidden'));?><input name="Preview" type="submit" value="Preview"><input name="Submit" type="submit" value="Submit"></td>
        </tr>
      </table>
      </form>
      
      </td>
      
      </tr>
      
    </table>
    <?PHP require '../includes/footer.php'; ?>
  </div>
</div>
<?PHP
echo $message;
?>
<script language="javascript">start_tinyMCE();</script>
</body>
</html>
