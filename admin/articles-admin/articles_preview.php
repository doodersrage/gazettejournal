<?PHP
require '../includes/application_top.php';

// vars
$article_status = $_POST['status'];

if (!empty($_POST['title'])) $_SESSION['title'] = str_replace(array("\'",'\"'),array("'",'"'),$_POST['title']);
$article_title = htmlentities($_SESSION['title']);
if (!empty($_POST['author'])) $_SESSION['author'] = str_replace(array("\'",'\"'),array("'",'"'),$_POST['author']);
$article_author = htmlentities($_SESSION['author']);
if (!empty($_POST['summary'])) $_SESSION['summary'] = str_replace(array("\'",'\"'),array("'",'"'),$_POST['summary']);
$article_summary = $_SESSION['summary'];
if (!empty($_POST['content'])) $_SESSION['content'] = str_replace(array("\'",'\"'),array("'",'"'),$_POST['content']);
$article_content = $_SESSION['content'];
if (!empty($_POST['categories'])) $_SESSION['categories'] = $_POST['categories'];
$selected_categories = $_SESSION['categories'];
if (!empty($_POST['read_more_link'])) $_SESSION['read_more_link'] = $_POST['read_more_link'];
$read_more_link = $_SESSION['read_more_link'];
if (!empty($_POST['article_sort_order'])) $_SESSION['article_sort_order'] = $_POST['article_sort_order'];
$article_sort_order = $_SESSION['article_sort_order'];

$article_title_tag = str_replace(array("\'",'\"'),array("'",'"'),$_POST['title_tag']);
$article_meta_description = $_POST['meta_description'];
$article_meta_keywords = $_POST['meta_keywords'];
$article_id = $_POST['article_id'];
$front_section = $_POST['front_section'];
$template = $_POST['template'];
$deleted_images = $_POST['deleted_images'];
$homepage_image = $_POST['homepage_image'];


//insert new images and sort data if found
// vars
if (empty($_GET['submit_changes'])) {
$art_id = $_POST['article_id'];
$sesid = $_SESSION['article_session'];
$new_file_name = basename( $_FILES['file']['name']);
$files_target_path = FILES_DIRECTORY . $new_file_name; 
$new_image_ids_array = array();
$delete_file = $_POST['delete_file'];


// delete files from article if selected
if (!empty($delete_file)) {
mysql_query("DELETE FROM articles_files WHERE articles_id = '".$article_id."';");
}

// delete selected images
if (!empty($deleted_images)) delete_images($deleted_images);

// upload file if attached
if (move_uploaded_file($_FILES['file']['tmp_name'], $files_target_path)) {
$values = array($art_id,$new_file_name,$sesid);
$sql = "INSERT INTO articles_files (articles_id,file_name,new_article_session) VALUES (?,?,?);";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);
}

// upload any new images
// check to see if image exists
foreach ($_FILES["image"]["error"] as $key => $error) {
    if ($error == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["image"]["tmp_name"][$key];
        $file_name = $_FILES["image"]["name"][$key];
		$target_path = IMAGES_DIRECTORY . $file_name; 
		$image_sort_order = $_POST['sort_order'][$key];
		$image_caption = $_POST['image_caption'][$key];
		
$image_check = mysql_query('SELECT image FROM articles_images WHERE articles_id = "'.$art_id.'" and image = "'.$file_name.'" and new_article_session = "'.$sesid.'";');
$images_found = mysql_num_rows($image_check);

// if image is not found upload
if ($images_found == 0) {
if (move_uploaded_file($tmp_name, $target_path)) {

$values = array($art_id,$file_name,$sesid,$image_sort_order,$image_caption);
$sql = "INSERT INTO articles_images (articles_id,image,new_article_session,sort_order,caption) VALUES (?,?,?,?,?);";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

//push new image ids to array
$new_image_id = mysql_insert_id();
array_push($new_image_ids_array,$new_image_id);

// set homepage key if assigned
if ($homepage_image == 'new_image_'.$key) $homepage_image = $new_image_id;
}
} else {
echo '<script language="javascript">alert(\'That image appears to already be assigned to this article. If you want to replace the image please delete the original first then upload the new image.\');</script>';
}

    }
}

if (!empty($art_id) || !empty($sesid)) {
if (!empty($art_id)) $where_clause = "articles_id = '".$art_id."'";
if (!empty($sesid) && empty($art_id)) $where_clause = "new_article_session = '".$sesid."'";

//check for new images
if (count($new_image_ids_array) > 0) $new_image_list = implode(',',$new_image_ids_array); else $new_image_list = '';

// bof build image list
$articles_images_update_qry = mysql_query("SELECT articles_images_id FROM articles_images WHERE ".$where_clause." ".(!empty($new_image_list) ? "and articles_images_id not in (".$new_image_list.")" : "" ).";");
while ($articles_images_update_result = mysql_fetch_array($articles_images_update_qry)) {

$values = array($_POST['image_sort'.$articles_images_update_result['articles_images_id']],$_POST['image_caption'.$articles_images_update_result['articles_images_id']],$articles_images_update_result['articles_images_id']);
$sql = "UPDATE articles_images SET sort_order = ?, caption = ? WHERE articles_images_id = ?;";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

}
}
}

function delete_images($image_ids) {
$image_array = explode(',',$image_ids);
foreach($image_array as $images) {
mysql_query("DELETE FROM articles_images WHERE articles_images_id = '".$images."';");
}
}

if ($_GET['submit_changes'] == 1 || $_POST['Submit'] === 'Submit') {
// insert new article
if ($_POST['new'] == 1) {
if (!empty($article_title)) {

$values = array($article_title,$article_status,$article_author,$article_summary,$article_content,$article_title_tag,$article_meta_description,$article_meta_keywords,$front_section,$template,$homepage_image,$article_sort_order,$read_more_link);
$sql = "INSERT INTO articles (created,modified,title,status,author,summary,content,title_tag,meta_description,meta_keywords,front_section,template,homepage_image,sort_order,read_more_link) VALUES (NOW(),NOW(),?,?,?,?,?,?,?,?,?,?,?,?,?);";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

$new_rec_id = mysql_insert_id();
$values = array($new_rec_id,$_SESSION['article_session']);
$sql = "UPDATE articles_images SET articles_id = ?,new_article_session = NULL WHERE new_article_session = ?;";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

$values = array($new_rec_id,$_SESSION['article_session']);
$sql = "UPDATE articles_files SET articles_id = ?,new_article_session = NULL WHERE new_article_session = ?;";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

reset($selected_categories);
foreach ($selected_categories as $sel_cats) {
mysql_query("INSERT INTO articles_to_categories (articles_id,categories_id) VALUES ('".$new_rec_id."','".$sel_cats."');");
}
unset($_SESSION['article_session']);
update_last_updated();
clear_article_session_data();
header('Location: articles.php?update=article_added');
} else {
$message = '<script language="javascript">alert(\'You must atleast assign an article title.\');</script>';
}
}

// update article
if (!empty($article_id)) {
if (!empty($article_title)) {
$new_article_title = $article_title;
$new_article_author = $article_author;
$new_article_summary = $article_summary;
$new_article_content = $article_content;

$values = array($new_article_title,$article_status,$new_article_author,$new_article_summary,$new_article_content,$article_title_tag,$article_meta_description,$article_meta_keywords,$front_section,$template,$homepage_image,$article_sort_order,$read_more_link,$article_id);
$sql = "UPDATE articles SET modified = NOW(), title = ?,status = ?,author = ?,summary = ?,content = ?,title_tag = ?,meta_description = ?,meta_keywords = ?, front_section = ?, template = ?, homepage_image = ?, sort_order = ?, read_more_link = ? WHERE articles_id = ?;";
$sth = $db->prepare($sql);
$res = $db->execute($sth,$values);

// bof copy newly assigned categories
reset($selected_categories);
foreach ($selected_categories as $sel_cats) {
$articles_categories_qry = mysql_query("SELECT articles_id FROM articles_to_categories WHERE categories_id = '".$sel_cats."' and articles_id = '".$article_id."';");
$articles_categories_cnt = mysql_num_rows($articles_categories_qry);
if ($articles_categories_cnt == 0) mysql_query("INSERT INTO articles_to_categories (articles_id,categories_id) VALUES ('".$article_id."','".$sel_cats."');");
}
// eof copy newly assigned ctagories

// bof add assigned categories and clean listing of unassigned categories
reset($selected_categories);
// clear unused cats
$start_cat_cnt = count($selected_categories);
$cat_count = 0;
foreach ($selected_categories as $del_cats) {
$cat_count++;
$del_cats_string .= $del_cats . ($cat_count < $start_cat_cnt ? ',' : '');
}
mysql_query("DELETE FROM articles_to_categories WHERE articles_id = '".$article_id."' and categories_id not in (".$del_cats_string.");");
// eof add assigned categories and clean listing of unassigned categories

update_last_updated();
clear_article_session_data();
header('Location: articles.php?update=article_updated');
} else {
$message = '<script language="javascript">alert(\'You must atleast assign an article title.\');</script>';
}
}
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
<title><?PHP echo $article_title_tag; ?></title>
<meta name="description" content="<?PHP echo $article_meta_description; ?>">
<meta name="keywords" content="<?PHP echo $article_meta_keywords; ?>">
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript" src="../includes/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
	// Notice: The simple theme does not use all options some of them are limited to the advanced theme
tinyMCE.init({
    mode : "none",
    theme : "simple"
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
    <td valign="top"><div class="bc_nav"></div>
	  <?PHP echo $htmlfunctions->draw_form($formname,'?submit_changes=1'); ?>
<?PHP
// copy new data to hidden fields
echo $htmlfunctions->text_field('status','','',$article_status,'hidden') . "\n";
echo $htmlfunctions->text_field('front_section','','',$front_section,'hidden') . "\n";
echo $htmlfunctions->text_field('template','','',$template,'hidden') . "\n";
echo $htmlfunctions->text_field('title_tag','','',$article_title_tag,'hidden') . "\n";
echo $htmlfunctions->text_field('meta_description','','',$article_meta_description,'hidden') . "\n";
echo $htmlfunctions->text_field('meta_keywords','','',$article_meta_keywords,'hidden') . "\n";
echo $htmlfunctions->text_field('article_id','','',$article_id,'hidden') . "\n";
echo $htmlfunctions->text_field('deleted_images','','',$deleted_images,'hidden') . "\n";
echo $htmlfunctions->text_field('homepage_image','','',$homepage_image,'hidden') . "\n";
echo $htmlfunctions->text_field('article_sort_order','','',$article_sort_order,'hidden') . "\n";
?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="3">
        <tr>
          <td width="80" align="right" valign="top" class="field_title"><strong>Title:</strong></td>
          <td class="field_bg"><?PHP echo $article_title; ?>            </td>
        </tr>
        <tr>
          <td width="80" align="right" valign="top" class="field_title"><strong>Author:</strong></td>
          <td class="field_bg"><?PHP echo $article_author; ?></td>
        </tr>
        <tr>
          <td width="80" align="right" valign="top" class="field_title"><strong>Summary:</strong></td>
          <td class="field_bg"><?PHP echo $article_summary; ?>
		  </td>
        </tr>
        <tr>
          <td width="80" align="right" valign="top" class="field_title"><strong>Content:</strong></td>
          <td class="field_bg"><?PHP echo $article_content; ?>
              </td>
        </tr>
        <tr>
          <td width="80" align="right" valign="top" class="field_title"><strong>Categories:</strong></td>
          <td class="field_bg">
          <table border="0" cellpadding="2">
          <?PHP echo $categories_list; ?>
          </table>
            <br>            </td>
        </tr>
        <tr>
          <td width="80" align="right" valign="top" class="field_title"><strong>Images:</strong></td>
          <td valign="top" class="field_bg">
		  <?PHP
		  echo '<table><tr><td valign="middle">';
		  if (!empty($article_id)) {
		  $where_clase = "articles_id = '".$article_id."'";
		  } else {
		  $where_clase = "new_article_session = '".$_SESSION['article_session']."'";
		  }
		  
!empty($deleted_images) ? $extended_where_images = "and articles_images_id not in (".$deleted_images.")" : '';
$articles_images_qry = mysql_query("SELECT articles_images_id, articles_id, image FROM articles_images WHERE ".$where_clase." ".$extended_where_images.";");
while ($articles_images_result = mysql_fetch_array($articles_images_qry)) {
echo '<a href="'.IMAGES_ADDRESS.$articles_images_result['image'].'" target="_new">'.$embed->determine_media_type($articles_images_result['image'],50,50,'','','','',SITE_ADDRESS.'images/').'</a>';
}
echo '</td></tr></table>';
		  ?>
		  </td>
          </tr>
        <tr align="center">
          <td colspan="2" valign="top"><input name="Back" type="button" value="Back" onclick="javascript: window.history.back();"><?PHP 
		  echo (!empty($article_id) ? $htmlfunctions->text_field('article_id','','',$article_id,'hidden') : $htmlfunctions->text_field('new','','',1,'hidden')); 
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
<?PHP
echo $message;
?>
</body>
</html>
