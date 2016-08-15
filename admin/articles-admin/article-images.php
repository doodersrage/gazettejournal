<?PHP
require '../includes/application_top.php';

// vars
$delete_image = $_GET['deleteimage'];
$art_id = $_GET['artid'];
$sesid = $_GET['sesid'];
$file_name = basename( $_FILES['file']['name']);
$target_path = IMAGES_DIRECTORY . $file_name; 
$sort_order = $_POST['sort_order'];

if (!empty($delete_image)) {
mysql_query("DELETE FROM articles_images WHERE articles_images_id = '".$delete_image."';");
}


if ($_POST['submitted'] == 1 && (!empty($art_id) || !empty($sesid))) {

// check to see if image exists
$image_check = mysql_query('SELECT image FROM articles_images WHERE articles_id = "'.$art_id.'" and image = "'.$file_name.'" and new_article_session = "'.$sesid.'";');
$images_found = mysql_num_rows($image_check);

if ($images_found == 0) {
if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
    echo "The file ".  basename( $_FILES['file']['name']). 
    " has been uploaded";
mysql_query('INSERT INTO articles_images (articles_id,image,new_article_session,sort_order) VALUES ("'.$art_id.'","'.$file_name.'","'.$sesid.'","'.$sort_order.'");');
} else{
    echo "There was an error uploading the file, please try again!";
}
} else {
echo "That image appears to already be assigned to this article. If you want to replace the image please delete the original first then upload the new image.";
}

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Articles: Article Images</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
</head>

<body style="background-color:#FFFFFF;">
<?PHP
if (!empty($art_id) || !empty($sesid)) {
if (!empty($art_id)) $where_clause = "WHERE articles_id = '".$art_id."'";
if (!empty($sesid)) $where_clause = "WHERE new_article_session = '".$sesid."'";
// bof build image list
echo '<table align="center"><tr><td class="table_header">Preview</td><td class="table_header">Name</td><td class="table_header">Sort Order</td><td class="table_header">Options</td></tr>';
$articles_images_qry = mysql_query("SELECT articles_images_id, articles_id, image, sort_order FROM articles_images ".$where_clause.";");
$list = 0;
while ($articles_images_result = mysql_fetch_array($articles_images_qry)) {
$list++;
$list == 2 ? $list = 0 : ''; 
$list == 1 ? $cssclass = "class=\"listing_even\" " : $cssclass = ""; 
echo '
<tr '.$cssclass.'>' . "\n". 
'<td valign="middle" style="padding:3px;"><a href="javascript: void(0)" onMouseDown="javascript: new_centered_popup('.ARTICLE_IMAGE_WIDTH.','.ARTICLE_IMAGE_HEIGHT.',\''.IMAGES_ADDRESS.$articles_images_result['image'].'\')"><img src="'.IMAGES_ADDRESS.$articles_images_result['image'].'" width="55" height="55"></a></td>' . "\n"
.'<td valign="middle"><a href="javascript: void(0)" onMouseDown="javascript: new_centered_popup('.ARTICLE_IMAGE_WIDTH.','.ARTICLE_IMAGE_HEIGHT.',\''.IMAGES_ADDRESS.$articles_images_result['image'].'\')" >'.$articles_images_result['image'].'</a></td>' . "\n"
.'<td valign="middle" align="center">'.$articles_images_result['sort_order'].'</td>' . "\n"
.'<td valign="top"><a href="?editimage='.$articles_images_result['articles_images_id'].(!empty($art_id) ? '&artid='.$art_id : (!empty($sesid) ? '&sesid='.$sesid : '') ).'" class="button_new">Edit</a> <a href="?deleteimage='.$articles_images_result['articles_images_id'].(!empty($art_id) ? '&artid='.$art_id : (!empty($sesid) ? '&sesid='.$sesid : '') ).'" onClick="return confirm(\'Are you sure you want to delete this item?\')" class="button_new">Delete</a></td>' . "\n" . '</tr>';
}
echo "\n". '</table>';
// eof build image list
}
?>
<form action="<?PHP echo (!empty($art_id) ? '?artid='.$art_id : (!empty($sesid) ? '?sesid='.$sesid : '') ); ?>" method="post" enctype="multipart/form-data" name="form1">
  <table width="200" border="0" align="center" cellpadding="3">
    <tr>
      <td colspan="2">Browse to where the image is stored then click submit to upload. </td>
    </tr>
    <tr>
      <td>Sort Order:
        <input name="sort_order" type="text" size="4"></td>
    </tr>
    <tr>
      <td><input type="file" name="file"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="hidden" name="submitted" value="1">
      <input type="submit" name="Submit" value="Submit"></td>
    </tr>
  </table>
</form>
</body>
</html>
