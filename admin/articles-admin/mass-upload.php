<?PHP
require '../includes/application_top.php';

// vars
$form_submit = $_POST['form_submit'];
$uploaded_csv = $_FILES['file']['tmp_name'];
$art_count = 0;

if ($form_submit == 1) {
// set vars
$column_names = array();
$column_content = array();
$category_name = '';

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
switch ($col) {
case 'Category Name':
$category_name = $value;
break;
case 'Article Title';
// push column title to title array
array_push($column_names,'title');
// push column data to content array
array_push($column_content,$value);
break;
case 'Article Status';
// find status id assignment
if (!empty($value)) {
$status_query = mysql_query("SELECT articles_status_id FROM articles_status WHERE status = '".trim($value)."';");
if (mysql_num_rows($status_query) > 0) {
$status_result = mysql_fetch_array($status_query);
// assign found status
$found_status_id = $status_result['articles_status_id'];
}
}

// push column title to title array
array_push($column_names,'status');
// push column data to content array
array_push($column_content,$found_status_id);
break;
case 'Article Summary';
// push column title to title array
array_push($column_names,'summary');
// push column data to content array
array_push($column_content,$value);
break;
case 'Article Content';
// push column title to title array
array_push($column_names,'content');
// push column data to content array
array_push($column_content,$value);
break;
case 'Article Images';
$images = explode(';',$value);
break;
case 'Article File';
$article_file = $value;
break;
}


// end each column roll
}

// push column data to tables
// insert new article data
mysql_query("INSERT INTO articles (created,".parse_array_data($column_names,'1').") VALUES (NOW(),".parse_array_data($column_content).")");
// get new article id
$new_article_id = mysql_insert_id();

// find category id assignment
if (!empty($category_name)) {
$category_query = mysql_query("SELECT categories_id FROM categories WHERE name = '".trim($category_name)."';");
if (mysql_num_rows($category_query) > 0) {
$category_result = mysql_fetch_array($category_query);
// assign found category to article
mysql_query("INSERT INTO articles_to_categories (articles_id,categories_id) VALUES ('".$new_article_id."','".$category_result['categories_id']."');");
}
}

// check for and insert images
if (!empty($images)) {
if (is_array($images)) {
foreach ($images as $image_name) {
mysql_query("INSERT INTO articles_images (articles_id,image) VALUES ('".$new_article_id."','".clean_db_inserts($image_name)."');");
}
} else {
mysql_query("INSERT INTO articles_images (articles_id,image) VALUES ('".$new_article_id."','".clean_db_inserts($images)."');");
}
}

if (!empty($article_file)) {
mysql_query("INSERT INTO articles_files (articles_id,file_name) VALUES ('".$new_article_id."','".$article_file."')");
}

// clear set vars
$column_names = array();
$column_content = array();
$images = '';
$article_file = '';
$category_name = '';

// end each row roll
}

$message = '<script language="javascript">alert(\''.$art_count.' Articles Uploaded!\');</script>';

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
    <td><div class="bc_nav"><?PHP echo brc(array('Mass Upload' => 'articles-admin/mass-upload.php')); ?></div>
	<div align="center">
	  <table width="100%"  border="0" cellpadding="3">
        <tr>
          <td align="center"><strong>Correct Header Row Format For Input File: </strong></td>
        </tr>
        <tr>
          <td><table width="100%"  border="1" bordercolor="#000000" bgcolor="#FFFFFF">
            <tr align="center">
              <td>Category Name </td>
              <td>Article Title </td>
              <td>Article Status </td>
              <td>Article Author</td>
              <td>Article Summary </td>
              <td>Article Content </td>
              <td>Article Images</td>
              <td>Article File </td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td align="center"><strong>Example Article Row Layout:</strong></td>
        </tr>
        <tr>
          <td><table width="100%"  border="1" bordercolor="#000000" bgcolor="#CCCCCC">
            <tr>
              <td>Test Category</td>
              <td>Test Article</td>
              <td>Active</td>
              <td>Test Author</td>
              <td>test article summary . . . . . . . . . .</td>
              <td>Test article content . . . . . . . . . . . .</td>
              <td>image1.jpg;image2.jpg;image3.jpg</td>
              <td>new_file.pdf</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td align="center"><a href="<?PHP echo FILES_ADDRESS; ?>articles_import.csv">Click here to download preformatted file.</a></td>
        </tr>
        <tr>
          <td align="center"><p><strong>How to upload a file:</strong></p>
            <p align="left">To easily upload a file if you have not downloaded the template document first do so or recreate one using the above format (white table) data. As within the preformatted file you must keep the column titles within the first row If this is not done it will result in the untitled columns being skipped during the upload process which could easily prevent any new information from being imported into the system.</p>
            <p align="left"><strong>Creating a new CSV document:</strong></p>
            <p align="left">Within your favorite spreadsheet application create a new spreadsheet copy the above header information into the first row. Save the document as a CSV coma delimited double quote text qualifier document.</p>
            <p align="left"><strong>Opening existing file:</strong></p>
            <p align="left">MS Excel should be able to read this file and present it to you as a spreadsheet. The newer 2007 version may try to force the user to convert the document into its native XLSX format but be sure that you maintain the same CSV delimited format while working with the spreadsheet. Some other programs such as Open Office.Org have the tendency when opening non-native formats to ask which format the documents data is in. The data within the import document should always be coma delimited with a double quote text qualifier. </p>
            <p align="left"><strong>Entering new information:</strong></p>
            <p align="left"><strong>Category Name<span class="style1">(required)</span>:</strong> Fill in the correct category name you would like to have the new article inserted into. <br>
              <strong>Article Title<span class="style1">(required)</span>:</strong> This field stores the title to be assigned to the new article.<br>
              <strong>Article Status<span class="style1">(required)</span>:</strong> Enter in the status value you would like to have assigned to the new article. Currently available status options include: <?PHP 			$status_query = mysql_query("SELECT * FROM articles_status ORDER BY articles_status_id ASC;");
			while ($status_result = mysql_fetch_array($status_query)) {
             $status_options .= (!empty($status_options) ? ', ' : '').$status_result['status'];
			}
			echo $status_options;
?>.<br>
              <strong>Article Author:</strong> In this field you will enter in the articles author.<br>
              <strong>Article Summary: </strong>In this field you will enter in the articles summary information.<br>
              <strong>Article Content:</strong> In this field you will enter in the articles content.<br>
              <strong>Article Images:</strong> Within this field you can assign any number of images to your articles. This is done by entering in an image name followed by a semicolon. EX: image1.jpg;image2.jpg;image43.jpg<br>
              <strong>Article File:</strong> Only populate this field if you would like to have a document linked for this article instead stored summary and content information. <br>
            </p></td>
        </tr>
        <tr>
          <td align="center"><form action="" method="post" enctype="multipart/form-data" name="form1">
            <p>Upload new articles here. </p>
            <p>
              <input type="file" name="file">
              <input type="hidden" name="form_submit" value="1">			 
            </p>
            <p>
              <input type="submit" name="Submit" value="Submit">        
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
