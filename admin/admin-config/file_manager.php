<?PHP
require '../includes/application_top.php';

// vars
$submit = $_POST['submit'];
$del_files = $_POST['deletefile'];

// delete selected files
if (!empty($submit)) {

foreach ($del_files as $remove_filename) {
unlink(IMAGES_DIRECTORY.$remove_filename);
}

}


// list current files
// open this directory 
$myDirectory = opendir(IMAGES_DIRECTORY);

// get each entry
while($entryName = readdir($myDirectory)) {
	$dirArray[] = $entryName;
}

// close directory
closedir($myDirectory);

//	count elements in array
$indexCount	= count($dirArray);
//Print ("$indexCount files<br>\n");

// sort 'em
sort($dirArray);

$total_space_used = 0;

// print 'em
$listing_string = "<TABLE border=1 cellpadding=5 cellspacing=0 class=whitelinks align=\"center\">\n";
$listing_string .= "<TR class=\"filelist_header\"><TD>Filename</TD><td>Filesize k</td><td>Filesize MB</td><td>Delete</td></TR>\n";
// loop through the array of files and print them all
for($index=0; $index < $indexCount; $index++) {
		$total_space_used += filesize(IMAGES_DIRECTORY.$dirArray[$index]);
		
        if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
		$listing_string .= "<TR><TD><a href=\"" .IMAGES_ADDRESS.$dirArray[$index]."\" target=\"_blank\">$dirArray[$index]</a></td>";
		$listing_string .= "<td>";
		$listing_string .= number_format(round((filesize(IMAGES_DIRECTORY.$dirArray[$index])/1024),2), 2, '.', ',').' k';
		$listing_string .= "</td>";
		$listing_string .= "<td>";
		$listing_string .= number_format(round(((filesize(IMAGES_DIRECTORY.$dirArray[$index])/1024)/1024),2), 2, '.', ',').' MB';
		$listing_string .= "</td>";
		$listing_string .= "<td>";
		$listing_string .= "<input name=\"deletefile[]\" type=\"checkbox\" value=\"" . $dirArray[$index] . "\">";
		$listing_string .= "</td>";
		$listing_string .= "</TR>\n";
	}
}
		
$listing_string .= "</TABLE>\n";
$listing_string .= "<div align=\"center\"><br><strong>Total Files:</strong> ".$indexCount;
$listing_string .= "<br><strong>Total Space Used:</strong> ".number_format(round(($total_space_used/1024),2), 2, '.', ',')." k / ".number_format(round((($total_space_used/1024)/1024),2), 2, '.', ',')." MB</div><br>";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>File Manager</title>
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
    <td valign="top"><div align="center">
      <table width="100%"  border="0" cellpadding="3" bgcolor="#FFFFFF">
        <tr>
          <td width="79%">
<div class="bc_nav"><?PHP echo brc(array('File Manager' => 'admin-config/file_manager.php')); ?></div>
	<form action="" method="post">
	<?PHP
	echo $listing_string;
	?>
	<div align="center"><input name="Submit Changes" type="submit" value="Submit Changes"></div>
	<input name="submit" type="hidden" value="1">
	</form></td>
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
