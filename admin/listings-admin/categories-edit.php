<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Listings: Categories Edit</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="../includes/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
	// Notice: The simple theme does not use all options some of them are limited to the advanced theme
	tinyMCE.init({
		mode : "textareas",
		theme : "simple"
	});
</script>
</head>

<body>
<div class="container">
<div class="header_area"></div>
<div class="content">
<table width="800" border="0" align="center" cellpadding="3" class="main_table">
  <tr>
    <td width="127" valign="top" class="left_nav">
	<div class="section_title">Listings</div>
	<a href="articles.php?type=classifieds">Classifieds</a>
	<a href="articles.php?type=weddings">Weddings</a>
	<a href="articles.php?type=obituaries">Obituaries</a></td>
    <td valign="top"><div class="bc_nav">Listings -&gt; Categories -&gt; Categories Edit </div>
	  <form action="" method="post" enctype="multipart/form-data" name="form1">
<table border="0" align="center" cellpadding="3" cellspacing="0">
        <tr>
          <td><strong>Article ID:</strong> 1 </td>
          <td><strong>Created:</strong> 01/08/2008 </td>
          <td><strong>Modified:</strong> 01/08/2008</td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Status:</strong></td>
          <td colspan="2">Enabled
            <input name="radiobutton" type="radio" value="1" checked>
            Disabled
            <input name="radiobutton" type="radio" value="0"></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Parent Category: </strong></td>
          <td colspan="2"><select name="parent_category" id="parent_category">
            <option value="1" selected> </option>
            <option value="2">Test Category</option>
            <option value="3">Test Category1</option>
          </select></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Title:</strong></td>
          <td colspan="2"><input name="title" type="text" id="title" value="Test Category" size="60"></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Summary:</strong></td>
          <td colspan="2"><textarea name="summary" cols="60" rows="5" id="summary">Test Summary</textarea></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Content:</strong></td>
          <td colspan="2"><textarea name="content" cols="60" rows="10" id="content">Test Content</textarea></td>
        </tr>
        <tr align="center">
          <td colspan="3" valign="top"><input type="submit" name="Submit" value="Submit"></td>
        </tr>
      </table>
	  </form>
	  
    </td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
