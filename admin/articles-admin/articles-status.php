<?PHP
require '../includes/application_top.php';

// vars
$status = $_POST['status'];
$articles_status_id = $_POST['articles_status_id'];
$delete = $_GET['delete'];
$edit = $_GET['edit'];
$new = $_POST['new'];


if (!empty($delete)) {
mysql_query("DELETE FROM articles_status WHERE articles_status_id = '".$delete."';");
}

if (!empty($edit)) {
$status_query = mysql_query("SELECT * FROM articles_status WHERE articles_status_id = '".$edit."';");
$status_result = mysql_fetch_array($status_query);
$status = $status_result['status'];
$articles_status_id = $status_result['articles_status_id'];
}

if ($new == 1) {
mysql_query("INSERT INTO articles_status (status) values ('".$status."');");
header('Location: articles-status.php');
}

if (!empty($articles_status_id)) {
mysql_query("UPDATE articles_status SET status = '".$status."' WHERE articles_status_id = '".$articles_status_id."';");
header('Location: articles-status.php');
}

if (!empty($articles_status_id)) $formname = 'edit_article_status'; else $formname = 'new_article_status';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Articles</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
</head>

<body>
<div class="container">
<div class="header_area"></div>
<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" valign="top" class="left_nav">
	<?PHP require('../includes/mainmenu.php'); ?></td>
    <td valign="top"><div class="bc_nav"><?PHP echo brc(array('Articles Status' => 'articles-admin/articles-status.php')); ?></div>
	
	  <div align="center">
	    <table border="0" cellpadding="0" cellspacing="0" class="item_list">
          <tr align="center" class="art_status">
            <td colspan="2"><strong>Status Options:</strong></td>
            </tr>
			<tr>
			<td class="table_header">Name</td>
			<td class="table_header">Options</td>
			</tr>
			<?PHP 
			$status_query = mysql_query("SELECT * FROM articles_status ORDER BY articles_status_id ASC;");
			while ($status_result = mysql_fetch_array($status_query)) {
	        echo '<tr>';
             echo '<td align="center" class="section_list_left">'.$status_result['status'].'</td>';
             echo '<td class="section_list"><NOBR><a href="?edit='.$status_result['articles_status_id'].'" class="button">Edit</a> <a href="?delete='.$status_result['articles_status_id'].'" onclick="return confirm(\'Are you sure you want to delete this item?\')" class="button">Delete</a></NOBR></td>';
            echo '</tr>';
			}
			?>
          <tr>
            <td colspan="2">&nbsp;</td>
            </tr>
          <tr>
            <td colspan="2" align="center" class="art_status"><?PHP echo (!empty($articles_status_id) ? '<strong>Edit:</strong>' : '<strong>Add New:</strong>'); ?>
</td>
          </tr>
          <tr>
            <td colspan="2">
			<?PHP echo $htmlfunctions->draw_form($formname,''); ?>
			<div align="center">Status Name:
                <?PHP echo $htmlfunctions->text_field('status',15,'',$status); ?>
                <br>
                <?PHP 
		  echo (!empty($articles_status_id) ? $htmlfunctions->text_field('articles_status_id','','',$articles_status_id,'hidden') : $htmlfunctions->text_field('new','','',1,'hidden')); 
		  echo $htmlfunctions->submit_button('Submit');?>
		        </form>
			  </div></td>
          </tr>
        </table>	  
	  </div></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
