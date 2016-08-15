<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Help - Articles</title>
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
          <td width="70%">
		  <a href="#adding_article">Adding a new article</a><br>
            <a href="#modify_article">Modifying an existing article</a> <br>            <br>
            <a href="#article_status">Article Status options</a> <br>
            <br>
            <a href="#add_category">Adding a new category</a><br>
            <a href="#modify_category">modifying an existing category</a>            <br>
			
		  <a name="adding_article"></a><p><strong>Adding a new article:</strong></p>
            <p>1. Click the &quot;Add New&quot; button to the button left of your existing article listings.</p>
            <p>2. Once on the &quot;Articles Edit &quot; page fill in the form data for:</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;a. Status - Sets the articles status. (Current options include: Active, Hidden, Featured, Archived)</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;b. Sort Order - Determines the articles position within the output page or front end of the site.</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;c. Title - Assign a title to be displayed for your article.</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;d. Author - Enter the authors name of this article. </p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;e. Summary - A summary of the content of this article. </p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;f. Content - The contents to be displayed within the articles page. </p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;g. Categories - The categories that will display this article within its listings.</p>
            <p>3. After filling in the form data then clicking the preview button you will be taken to a page that will allow you to preview your new article. On the preview page your will see all of your new content and a listing of your assigned images for the article.</p>
            <p>4. Before clicking submit to post your new article make sure that it has been assigned to the correct categories. If it has not you can change them one more time.</p>
            <p>5. Click submit and your new article will be displayed according to your status selection.</p>
            <p>&nbsp;</p>
            <a name="modify_article"></a><p><strong>Modifying an existing article:</strong></p>
            <p>To modify an existing article simply browse through the article listings until your find the article you want to edit then click the edit button. </p>
            <p>&nbsp;</p>
            <p><strong><a name="article_status"></a>Article status options:</strong></p>
            <p>1. Active - These articles will be displayed within its assigned categories of the site. </p>
            <p>2. Hidden - Articles that are hidden are not searchable nor are they displayed within any of the assigned categories.</p>
            <p>3. Featured - Featured articles are the articles that are displayed first within a category listing.</p>
            <p>4. Archived - Archived articles are not displayed within their assigned category listings but are searchable.  </p>
            <p>&nbsp;</p>
            <p><strong><a name="add_category"></a>Adding a new category: </strong></p>
            <p>1. Click on the &quot;Add new&quot; button at the bottom left of your category listing.</p>
            <p>2. Once on the &quot;Categories Edit&quot; page fill in form data for:</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;a. Hidden - If you would like to have this category and its contents hidden click here.</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;b. Name - Enter the name you would like this category to be listed under. </p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;c. Image - If you would like to have an image associated with this category click browse then browse to where your image is stored on your computer.</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;d. Parent Category - If this category is a child category of a parent select its parent from this drop down menu. </p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;e. Article Per Page - If you would like to have the category display more or less than the assigned default articles per page enter a value here. </p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;f. Categories Link Name - Assign a friendly name to be used when linking to this directory.</p>
            <p>&nbsp;</p>
            <p><strong><a name="modify_category"></a>Modifying an existing category:</strong></p>
            <p>To modify an existing category browse your category listings then click the edit button to the right of it.  </p></td>
        </tr>
        <tr>
          <td class="help_left">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
    </div></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
