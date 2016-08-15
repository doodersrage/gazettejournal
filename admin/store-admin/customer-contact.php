<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Store: Customers Contact</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
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
	<div class="bc_nav">Store -&gt; Customers -&gt; Email Customers</div>
	<form name="customer_edit" method="post" action="">
      <table width="600" border="0" align="center" cellspacing="5">
        <tr>
          <td width="76" align="right" valign="top">Customer:</td>
          <td width="508"><select name="customer" id="customer">
            <option selected>testuser@test.com</option>
          </select></td>
        </tr>
        <tr>
          <td align="right" valign="top">Title:</td>
          <td><input name="title" type="text" id="title"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Message:</td>
          <td><textarea name="message" cols="50" rows="15" id="message"></textarea></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><input type="submit" name="Submit" value="Submit"></td>
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
