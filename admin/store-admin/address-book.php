<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2" align="center" bgcolor="#CCCCCC"><strong>Current Address List</strong></td>
    </tr>
    <tr>
      <td align="left">1. 123 Test Lane, Test City, Test State, 12345</td>
      <td align="center"><a href="#">Edit</a> / <a href="javascript: void();" onMouseDown="return confirm('Are you sure you want to delete this item?')">Delete</a></td>
    </tr>
    <tr>
      <td colspan="2" align="center" bgcolor="#CCCCCC"><strong>Add New/Edit</strong></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="right">Address:</td>
      <td><input type="text" name="address1" id="address1" /></td>
    </tr>
    <tr>
      <td align="right">Address2:</td>
      <td><input type="text" name="address2" id="address2" /></td>
    </tr>
    <tr>
      <td align="right">City:</td>
      <td><input type="text" name="city" id="city" /></td>
    </tr>
    <tr>
      <td align="right">State:</td>
      <td><input type="text" name="state" id="state" /></td>
    </tr>
    <tr>
      <td align="right">Zip:</td>
      <td><input type="text" name="zip" id="zip" /></td>
    </tr>
      </table></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" name="button" id="button" value="Submit" />
      <input type="button" name="close" id="close" value="Close" onmousedown="javascript: window.close()" /></td>
    </tr>
  </table>
</form>
</body>
</html>
