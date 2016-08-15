<?PHP
require '../includes/application_top.php';

// vars
$zipcode = $_POST['zipcode'];
$zip_id = $_POST['zip_id'];
$delete = $_GET['delete'];
$edit = $_GET['edit'];
$new = $_POST['new'];
$update = $_POST['update'];

if (!empty($delete)) {
mysql_query("DELETE FROM zip_codes WHERE zip_id = '".$delete."';");
}

if (!empty($edit) && empty($update)) {
$zipcode_query = mysql_query("SELECT * FROM zip_codes WHERE zip_id = '".$edit."';");
$zipcode_result = mysql_fetch_array($zipcode_query);
$zipcode = $zipcode_result['zipcode'];
$zip_id = $zipcode_result['zip_id'];
}

if ($new == 1) {
mysql_query("INSERT INTO zip_codes (zipcode) values ('".$zipcode."');");
header('Location: zip-codes.php');
}

if (!empty($zip_id) && !empty($update)) {
mysql_query("UPDATE zip_codes SET zipcode = '".$zipcode."' WHERE zip_id = '".$zip_id."';");
header('Location: zip-codes.php');
}

if (!empty($zip_id)) $formname = 'edit_zip_codes'; else $formname = 'new_zip_codes';
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
    <td valign="top"><div class="bc_nav"><?PHP echo brc(array('Zip Codes' => 'store-admin/zip-codes.php')); ?></div>
	
	  <div align="center">
	    <table border="0" cellpadding="0" cellspacing="0" class="item_list">
          <tr align="center" class="art_status">
            <td colspan="2"><strong>Status Options:</strong></td>
            </tr>
			<tr>
			<td class="table_header">Zipcode</td>
			<td class="table_header">Options</td>
			</tr>
			<?PHP 
			$zipcode_query = mysql_query("SELECT * FROM zip_codes ORDER BY zipcode ASC;");
			while ($zipcode_result = mysql_fetch_array($zipcode_query)) {
	        echo '<tr>';
             echo '<td align="center" class="section_list_left">'.$zipcode_result['zipcode'].'</td>';
             echo '<td class="section_list"><NOBR><a href="?edit='.$zipcode_result['zip_id'].'" class="button">Edit</a> <a href="?delete='.$zipcode_result['zip_id'].'" onclick="return confirm(\'Are you sure you want to delete this item?\')" class="button">Delete</a></NOBR></td>';
            echo '</tr>';
			}
			?>
          <tr>
            <td colspan="2">&nbsp;</td>
            </tr>
          <tr>
            <td colspan="2" align="center" class="art_status"><?PHP echo (!empty($zip_id) ? '<strong>Edit:</strong>' : '<strong>Add New:</strong>'); ?>
</td>
          </tr>
          <tr>
            <td colspan="2">
			<?PHP echo $htmlfunctions->draw_form($formname,''); ?>
			<div align="center">Zipcode:
                <?PHP echo $htmlfunctions->text_field('zipcode',15,'',$zipcode); ?>
                <br>
                <?PHP 
		  echo (!empty($zip_id) ? $htmlfunctions->text_field('zip_id','','',$zip_id,'hidden') . $htmlfunctions->text_field('update','','',1,'hidden') : $htmlfunctions->text_field('new','','',1,'hidden')); 
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
