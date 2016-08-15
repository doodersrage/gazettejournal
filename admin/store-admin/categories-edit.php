<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Store: Categories Edit</title>
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
    <td width="127" rowspan="2" valign="top" class="left_nav">
	<?PHP require('../includes/mainmenu.php'); ?></td>
  </tr>
  <tr>
    <td valign="top">
	<div class="bc_nav">Store -&gt; Categories -&gt; Categories Edit</div>
	<form name="customer_edit" method="post" action="">
      <table border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="horizontal_pad"><strong>Category ID:</strong> 1 </td>
          <td class="horizontal_pad"><strong> Created:</strong> 01/08/2008 <strong> </strong></td>
          <td class="horizontal_pad"><strong>Modified:</strong> 01/08/2008</td>
        </tr>
        <tr>
          <td align="right"><strong>Status: </strong></td>
          <td colspan="2">Enabled
              <input name="radiobutton" type="radio" value="radiobutton" checked>
        Disabled
        <input name="radiobutton" type="radio" value="radiobutton">        </td>
          </tr>
        <tr>
          <td align="right"><strong>Name:</strong></td>
          <td colspan="2"><input name="name" type="text" id="name" value="Test Product "></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Description:</strong></td>
          <td colspan="2"><textarea name="description" cols="60" rows="10" id="description"></textarea></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Content:</strong></td>
          <td colspan="2"><textarea name="content" cols="60" rows="15" id="content"></textarea></td>
        </tr>
        <tr>
          <td align="right"><strong>Parent Category:</strong></td>
          <td colspan="2"><select name="parent_cat" id="parent_cat">
          </select></td>
        </tr>
        <tr align="center">
          <td colspan="3"><input type="submit" name="Submit" value="Submit Changes"></td>
        </tr>
      </table>
    </form></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
