<?PHP
require '../includes/application_top.php';

// vars
$form_submit = $_POST['form_submit'];
$upload_type = $_POST['upload_type'];
$uploaded_csv = $_FILES['file']['tmp_name'];
$art_count = 0;

if ($form_submit == 1) {

// clear existing data depending on submit data type
switch($upload_type) {
case 'weddings':
mysql_query("DELETE FROM listings WHERE section_id = 2 AND (sub_id = 1 OR sub_id = 2);");
break;
case 'obituaries':
mysql_query("DELETE FROM listings WHERE section_id = 3;");
break;
case 'births':
mysql_query("DELETE FROM listings WHERE section_id = 2 AND sub_id = 3;");
break;
}

// set vars
$column_names = array();
$column_content = array();

# create new parseCSV object.
$csv = new parseCSV();

# Parse '_books.csv' using automatic delimiter detection.
$csv->auto($uploaded_csv);

//process row data
foreach ($csv->data as $key => $row) {
$art_count++;

// process column data
foreach ($row as $col => $value) {


// parse column data
if ($upload_type == 'wedding') {
switch ($col) {
case 'BRIDE':
$entry_content = 'Bride: ' . $value;
$entry_title = 'Bride: ' . $value;
break;
case 'ADDRESSBRIDE':
$entry_content .= '<br>Brides Address: ' . $value;
break;
case 'BRIDEGROOM':
$entry_content .= '<br>Groom: ' . $value;
$entry_title .= ' and Groom: ' . $value;
break;
case 'ADDRESSGROOM':
$entry_content .= '<br>Grooms Address: ' . $value;
// assemble content area
array_push($column_names,'content');
array_push($column_content,$entry_content);
// assemble title area
array_push($column_names,'title');
array_push($column_content,str_replace("\n","",$entry_title));
// assign wedding sub_id
array_push($column_names,'sub_id');
strstr($entry_title,'(engaged)') ? $sub_id = 2 : $sub_id = 1;
array_push($column_content,$sub_id);
// push column Section to title array
array_push($column_names,'section_id');
// push column data to content array
array_push($column_content,2);
break;
case 'SOURCE':
array_push($column_names,'source');
array_push($column_content,$value);
break;
case 'ISSUE':
empty($value) ? $value = 'NOW()' : '';
array_push($column_names,'start_date');
array_push($column_names,'exp_date');
array_push($column_names,'modified');
// push column added to title array
array_push($column_names,'added');
array_push($column_names,'event_date');
array_push($column_content,clean_date_string_weddings_import($value));
array_push($column_content,clean_date_string_weddings_import($value));
array_push($column_content,clean_date_string_weddings_import($value));
array_push($column_content,clean_date_string_weddings_import($value));
array_push($column_content,clean_date_string_weddings_import($value));
break;
}


} elseif($upload_type == 'obituaries') {
switch ($col) {
case 'NAME':
$entry_content = $value;
$entry_title = $value;
break;
case 'LOCATION':
$entry_content .= '<br>Address: ' . $value;
$entry_title .= ', Address: ' . $value;
break;
case 'AGE':
$entry_content .= '<br>Age: ' . $value;
$entry_title .= ', Age: ' . $value;
// assemble content area
array_push($column_names,'content');
array_push($column_content,$entry_content);
// assemble title area
array_push($column_names,'title');
array_push($column_content,str_replace("\n","",$entry_title));
// push column Section to title array
array_push($column_names,'section_id');
// push column data to content array
array_push($column_content,3);
break;
case 'SOURCE':
array_push($column_names,'source');
$source = $value;
array_push($column_content,$value);
break;
case 'DATE':
empty($value) ? $value = 'NOW()' : '';
array_push($column_names,'start_date');
array_push($column_names,'exp_date');
array_push($column_names,'modified');
// push column added to title array
array_push($column_names,'event_date');
array_push($column_names,'added');
array_push($column_content,clean_date_string_weddings_import($value,$source));
array_push($column_content,clean_date_string_weddings_import($value,$source));
array_push($column_content,clean_date_string_weddings_import($value,$source));
array_push($column_content,clean_date_string_weddings_import($value,$source));
array_push($column_content,clean_date_string_weddings_import($value,$source));
break;
}

} elseif ($upload_type == 'births') {
switch ($col) {
case 'NAME':
$entry_content = 'Name: ' . $value;
$entry_title = 'Name: ' . $value;
break;
case 'ADDRESS':
$entry_content .= '<br>Address: ' . $value;
break;
case 'PARENTS':
$entry_content .= '<br>Parents: ' . $value;
$entry_title .= ', Parents: ' . $value;
// assemble content area
array_push($column_names,'content');
array_push($column_content,$entry_content);
// assemble title area
array_push($column_names,'title');
array_push($column_content,str_replace("\n","",$entry_title));
// assign wedding sub_id
array_push($column_names,'sub_id');
$sub_id = 3;
array_push($column_content,$sub_id);
// push column Section to title array
array_push($column_names,'section_id');
// push column data to content array
array_push($column_content,2);
break;
case 'SOURCE':
array_push($column_names,'source');
array_push($column_content,$value);
break;
case 'ISSUE':
empty($value) ? $value = 'NOW()' : '';
array_push($column_names,'start_date');
array_push($column_names,'exp_date');
array_push($column_names,'modified');
// push column added to title array
array_push($column_names,'event_date');
array_push($column_names,'added');
array_push($column_content,clean_date_string_weddings_import($value));
array_push($column_content,clean_date_string_weddings_import($value));
array_push($column_content,clean_date_string_weddings_import($value));
array_push($column_content,clean_date_string_weddings_import($value));
array_push($column_content,clean_date_string_weddings_import($value));
break;
}


} else {
switch ($col) {
case 'Start Date':
// push column Start Date to title array
array_push($column_names,'start_date');
// push column data to content array
array_push($column_content,clean_date_string($value));
// push column added to title array
array_push($column_names,'added');
// push column added to content array
array_push($column_content,'NOW()');
break;
case 'End Date':
// push column End Date to title array
array_push($column_names,'exp_date');
// push column data to content array
array_push($column_content,clean_date_string($value));
break;
case 'Title':
// push column title to title array
array_push($column_names,'title');
// push column data to content array
array_push($column_content,$value);
break;
case 'Image';
// push column image to title array
array_push($column_names,'image');
// push column data to content array
array_push($column_content,$value);
break;
case 'Summary';
// push column Summary to title array
array_push($column_names,'summary');
// push column data to content array
array_push($column_content,$value);
break;
case 'Content';
// push column Content to title array
array_push($column_names,'content');
// push column data to content array
array_push($column_content,$value);
break;
case 'Event Date';
// push column Content to title array
array_push($column_names,'event_date');
// push column data to content array
array_push($column_content,$value);
break;
case 'Main Section';
$section_id = '';
switch(ucfirst($value)) {
case 'Classifieds':
$section_id = 1;
break;
case 'Weddings':
$section_id = 2;
break;
case 'Obituaries';
$section_id = 3;
break;
}
// push column Section to title array
array_push($column_names,'section_id');
// push column data to content array
array_push($column_content,$section_id);
break;
case 'Sub Section';
switch ($section_id) {
case 1:
		$classified_sub_query = mysql_query("SELECT classifieds_cat_id FROM classifieds_categories WHERE name ='".trim($value)."';");
		if (mysql_num_rows($classified_sub_query) > 0) {
		$classified_result = mysql_fetch_array($classified_sub_query);
		$sub_id = $classified_result['classifieds_cat_id'];
		} else {
		$sub_id = '';
		}
break;
case 2;
		$classified_sub_query = mysql_query("SELECT events_categories_id FROM events_categories WHERE name ='".trim($value)."';");
		if (mysql_num_rows($classified_sub_query) > 0) {
		$classified_result = mysql_fetch_array($classified_sub_query);
		$sub_id = $classified_result['events_categories_id'];
		} else {
		$sub_id = '';
		}
break;
default:
$sub_id = '';
break;
}
// push column Sub id to title array
array_push($column_names,'sub_id');
// push column data to content array
array_push($column_content,$sub_id);
break;
}
}


// end each column roll
}

// push column data to tables
// insert new article data
$listing_insert_query = "INSERT INTO listings (".parse_array_data($column_names,'1').") VALUES (".parse_array_data($column_content).");";
mysql_query($listing_insert_query);

// clear set vars
$column_names = array();
$column_content = array();

// end each row roll
}

$message = '<script language="javascript">alert(\''.$art_count.' Listings Uploaded!\');</script>';

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Articles</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
</head>

<body>
<div class="container">
<div class="header_area"></div>
<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" valign="top" class="left_nav">
	<?PHP require('../includes/mainmenu.php'); ?></td>
    <td><div class="bc_nav"><?PHP echo brc(array('Mass Upload' => 'listings-admin/mass-upload.php')); ?></div>
	<div align="center">
	  <table width="100%"  border="0" cellpadding="3">
        <tr>
          <td align="center"><p><strong>How to upload a file:</strong></p>
            <p align="left">To upload a file first select the type of file that you would like to upload then browse to the file stored within your local machine or a network share. Once your have these two items selected then click the submit button. Give your spreadsheet some time to process then review your uploaded information within the listings section.</p>
            <p align="left">&nbsp;</p></td>
        </tr>
        <tr>
          <td align="center"><form action="" method="post" enctype="multipart/form-data" name="form1">
            <p>Upload new listings here. </p>
            <p>Upload Type: </p>
            <p>Weddings:
              <input name="upload_type" type="radio" value="wedding">
              Obituaries:
              <input name="upload_type" type="radio" value="obituaries">
              Births:
              <input name="upload_type" type="radio" value="births">
            </p>
            <!-- <p>
              <input type="checkbox" name="obituaries" value="checkbox">
Obituaries import </p>
            <p>
              <input type="checkbox" name="wedding" value="checkbox">
weddings import </p> -->
            <p>
              <input type="file" name="file">
            </p>
            <p>
              <input type="submit" name="Submit" value="Submit">
			  <input type="hidden" name="form_submit" value="1">		        
                  </p>
          </form></td>
        </tr>
      </table>
	</div></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
<? echo $message; ?>
</body>
</html>
