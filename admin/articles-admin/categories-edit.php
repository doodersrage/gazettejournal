<?PHP
require '../includes/application_top.php';

// vars
$catid = $_GET['catid'];
$mode = $_GET['mode'];
$hidden = $_POST['hidden'];
$name = str_replace(array("\'",'\"'),array("'",'"'),$_POST['name']);
$parent_cat = $_POST['parent_cat'];
$articles_pp = $_POST['articles_pp'];
$cat_link_name = $_POST['cat_link_name'];
$title_tag = $_POST['title_tag'];
$meta_description = $_POST['meta_description'];
$meta_keywords = $_POST['meta_keywords'];
$current_image = $_POST['current_image'];
$set_catid = $_POST['category_id'];
$sort_order = $_POST['sort_order'];


if ($mode == 'edit' && empty($set_catid)) {
$category_qry = mysql_query("SELECT * FROM categories WHERE categories_id = '".$catid."';");
$category_result = mysql_fetch_array($category_qry);

$category_id = $category_result['categories_id'];
$created = $category_result['created'];
$modified = $category_result['modified'];
$hidden = $category_result['hide_category'];
$name = $category_result['name'];
$parent_cat = $category_result['parent'];
$current_image = $category_result['image'];
$articles_pp = $category_result['per_page'];
$cat_link_name = $category_result['link_name'];
$title_tag = $category_result['title_tag'];
$meta_description = $category_result['meta_description'];
$meta_keywords = $category_result['meta_keywords'];
$sort_order = $category_result['sort_order'];
}

// build parent cat list
$parent_cat_list_qry = mysql_query("SELECT categories_id, name FROM categories ORDER BY categories_id ASC;");
$parent_cat_ids = array();
$parent_cat_names = array();
array_push($parent_cat_ids,'');
array_push($parent_cat_names,'');

while ($parent_cat_list_result = mysql_fetch_array($parent_cat_list_qry)) {
array_push($parent_cat_ids,$parent_cat_list_result['categories_id']);
array_push($parent_cat_names,$parent_cat_list_result['name']);
}

if ($_POST['new'] == 1) {
if (!empty($name)) {

$target_path = IMAGES_DIRECTORY . basename( $_FILES['image']['name']); 
move_uploaded_file($_FILES['image']['tmp_name'], $target_path);

mysql_query("INSERT INTO categories (created, modified, hide_category, name, parent,  per_page, link_name, image, title_tag, meta_description, meta_keywords,sort_order) VALUES (NOW(),NOW(),'".$hidden."','".clean_db_inserts($name)."','".$parent_cat."','".$articles_pp."','".$cat_link_name."','".$_FILES['file']['name']."','".clean_db_inserts($title_tag)."','".clean_db_inserts($meta_description)."','".clean_db_inserts($meta_keywords)."','".$sort_order."');");
update_last_updated();

// update top nav menu
update_categories_file(build_categories_menu());

header('Location: categories.php?update=category_added');
} else {
$message = '<script language="javascript">alert(\'You must atleast assign a category name.\');</script>';
}
}


// update article
if (isset($_POST['category_id'])) {
if (!empty($name)) {

// find and set image
if (!empty($current_image) && empty($_FILES['image']['name'])) {
$image_name = $current_image;
} else {
$target_path = IMAGES_DIRECTORY . basename( $_FILES['image']['name']); 
move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
$image_name = $_FILES['image']['name'];
}

mysql_query("UPDATE categories SET modified = NOW(), hide_category = '".$hidden."', name = '".clean_db_inserts($name)."', parent = '".$parent_cat."', per_page = '".$articles_pp."', link_name = '".$cat_link_name."', image = '".$image_name."', title_tag = '".clean_db_inserts($title_tag)."', meta_description = '".clean_db_inserts($meta_description)."', meta_keywords = '".clean_db_inserts($meta_keywords)."', sort_order = '".$sort_order."' WHERE categories_id = '".$_POST['category_id']."';");
update_last_updated();
// update top nav menu
update_categories_file(build_categories_menu());

header('Location: categories.php?update=category_updated');
} else {
$message = '<script language="javascript">alert(\'You must atleast assign a category.\');</script>';
}
}

if ($mode == 'new') $formname = 'new_category'; else $formname = 'edit_category';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Categories Admin: Categories Edit</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
</head>

<body>
<div class="container">

<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" valign="top" class="left_nav">
	<?PHP require('../includes/mainmenu.php'); ?></td>
    <td valign="top"><div class="bc_nav"><?PHP echo brc(array('Categories' => 'articles-admin/categories.php','Categories Edit' => 'articles-admin/categories-edit.php?mode='.$mode.'&catid='.$catid)); ?></div>
	  <?PHP echo $htmlfunctions->draw_form($formname,'','','multipart/form-data'); ?>
<table border="0" align="center" cellpadding="0" cellspacing="3">
        <tr>
          <td></td>
          <td><?PHP echo (!empty($created) ? '<strong>Created:</strong> '.date('m/d/Y',strtotime($created)).'</td>' : ''); ?></td>
          <td><?PHP echo (!empty($modified) ? '<strong>Modified:</strong> '.date('m/d/Y',strtotime($modified)).'</td>' : ''); ?></td>
        </tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Hidden:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->checkbox('hidden',1,'',$hidden); ?>
            </td></tr>
        <tr>
          <td align="right" valign="top" class="field_title"><strong>Name:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('name',60,'',$name); ?>
            </td></tr>
<!--        <tr>
          <td align="right" valign="top" class="field_title"><strong>Image:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->filebox('image','image'); 
		  echo (!empty($current_image) ? '<br><strong>Current Image:</strong>'.'&nbsp;&nbsp;'.$current_image.$htmlfunctions->text_field('current_image','','',$current_image,'hidden') : '');
		  ?>
            </td>
		</tr>
         <tr>
          <td align="right" valign="top" class="field_title"><strong>Parent Category: </strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->select_box('parent_cat',$parent_cat_ids,$parent_cat_names,$parent_cat); ?>		  </td>
        </tr> -->
		<tr>
          <td align="right" valign="top" class="field_title"><strong>Sort Order:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('sort_order',5,'sort_order',$sort_order); ?></td>
        </tr>
<!--		<tr>
          <td align="right" valign="top" class="field_title"><strong>Articles Per Page:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('articles_pp',5,'articles_pp',$articles_pp); ?></td>
        </tr>
         <tr>
          <td align="right" valign="top" class="field_title"><strong>Categories Link Name:</strong></td>
          <td colspan="2" class="field_bg"><?PHP echo $htmlfunctions->text_field('cat_link_name',50,'cat_link_name',$cat_link_name); ?>
		  </td>
        </tr>
       <tr align="center" class="art_status">
          <td colspan="3" valign="top"><strong>Header Content </strong></td>
          </tr>
        <tr align="center">
          <td align="right" valign="top" class="field_title"><strong>Title Tag: </strong></td>
          <td colspan="2" align="left" valign="top" class="field_bg"><?PHP echo $htmlfunctions->text_field('title_tag',60,'',$title_tag); ?></td>
          </tr>
        <tr align="center">
          <td align="right" valign="top" class="field_title"><strong>Meta Description:</strong></td>
          <td colspan="2" align="left" valign="top" class="field_bg"><?PHP echo $htmlfunctions->textarea('meta_description',60,5,$meta_description,'meta_description') ?></td>
          </tr>
        <tr align="center">
          <td align="right" valign="top" class="field_title"><strong>Meta Keywords: </strong></td>
          <td colspan="2" align="left" valign="top" class="field_bg"><?PHP echo $htmlfunctions->textarea('meta_keywords',60,4,$meta_keywords,'meta_keywords') ?></td>
        </tr> -->
        <tr align="center">
          <td colspan="3" valign="top"><?PHP 
		  echo (!empty($category_id) ? $htmlfunctions->text_field('category_id','','',$category_id,'hidden') : $htmlfunctions->text_field('new','','',1,'hidden')); 
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
