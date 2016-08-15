<?PHP
require 'application_top.php';

if ($_GET['articles_id']) {
// bof build image list
echo '<table>';
$articles_images_qry = mysql_query("SELECT articles_images_id, articles_id, image FROM articles_images WHERE articles_id = '".$_GET['articles_id']."';");
while ($articles_images_result = mysql_fetch_array($articles_images_qry)) {
echo '<tr><td valign="middle"><a href="'.IMAGES_ADDRESS.$articles_images_result['image'].'" target="_new">'.$articles_images_result['image'].'</a></td><td valign="middle"><a href="?deleteimage='.$articles_images_result['articles_images_id'].'" onClick="return confirm(\'Are you sure you want to delete this item?\')"><img src="../images/delete.gif" width="63" height="25" border="0"></a></td></tr>';
}
echo '</table>';
// eof build image list
}
?>