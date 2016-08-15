<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Listings: Customers</title>
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
	<div class="section_title">Listings</div>
	<a href="articles.php?type=classifieds">Classifieds</a>
	<a href="articles.php?type=weddings">Weddings</a>
	<a href="articles.php?type=obituaries">Obituaries</a></td>
  </tr>
  <tr>
    <td valign="top">
	<div class="bc_nav">Listings -&gt; Customers -&gt; Customers Edit</div>
	<form name="customer_edit" method="post" action="">
      <table border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="horizontal_pad"><strong>Customer ID:</strong> 1 </td>
          <td class="horizontal_pad"><strong> Created:</strong> 01/08/2008 <strong> </strong></td>
          <td class="horizontal_pad"><strong>Modified</strong>: 01/08/2008</td>
        </tr>
        <tr>
          <td align="right"><strong>Account Status: </strong></td>
          <td colspan="2">Enabled
              <input name="status" type="radio" value="radiobutton" checked>
        Disabled
        <input name="status" type="radio" value="radiobutton"></td>
        </tr>
        <tr>
          <td align="right"><strong>Username:</strong></td>
          <td colspan="2"><input name="username" type="text" id="username" value="tester"></td>
        </tr>
        <tr>
          <td align="right"><strong>Frist Name: </strong></td>
          <td colspan="2"><input name="first_name" type="text" id="first_name" value="Test"></td>
        </tr>
        <tr>
          <td align="right"><strong>Middle Initial: </strong></td>
          <td colspan="2"><input name="mi" type="text" id="mi"></td>
        </tr>
        <tr>
          <td align="right"><strong>Last Name: </strong></td>
          <td colspan="2"><input name="last_name" type="text" id="last_name" value="Name"></td>
        </tr>
        <tr>
          <td align="right"><strong>Company:</strong></td>
          <td colspan="2"><input name="company" type="text" id="company" value="Test Company"></td>
        </tr>
        <tr>
          <td align="right"><strong>Email:</strong></td>
          <td colspan="2"><input name="email" type="text" id="email" value="testuser@test.com"></td>
        </tr>
        <tr>
          <td align="right"><strong>Default Shipping Address:</strong></td>
          <td colspan="2"><select name="def_ship" id="def_ship">
              <option selected>123 Test Lane, Test City, Test State, 12345</option>
            </select>
              <a href="javascript: void(0)" onMouseDown="javascript: new_centered_popup(450,250,'../store-admin/address-book.html')">Edit Address Book</a> </td>
        </tr>
        <tr>
          <td align="right"><strong>Default Payment Address: </strong></td>
          <td colspan="2"><select name="def_payment" id="def_payment">
              <option selected>123 Test Lane, Test City, Test State, 12345</option>
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
