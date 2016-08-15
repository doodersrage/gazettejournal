<?PHP
require 'includes/application_top.php';

// load category listing class
require INCLUDES_DIRECTORY.'/classes/category_listings.php';
$category_listings = new category_listings();

// load article class
require INCLUDES_DIRECTORY.'/classes/article_page.php';
$article_page = new article_content();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Printer Friendly View</title>
<link type="text/css" href="/includes/styles/printer_friendly.css" rel="stylesheet">
</head>

<body>
<div class="container">
<?PHP

// get page vars
$articles_id = $_GET['artid'];

$content = $article_page->article_content_area($articles_id,1);

echo $content;

?>
<div align="center"><P class="copywrite">Copyright &copy; <?PHP echo date("Y"); ?>, Tidewater Newspapers, Inc.</P></div>
</div>
</body>
</html>
