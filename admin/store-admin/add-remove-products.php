<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
</head>

<body>
<form name="form1" method="post" action="">
  <table border="0" align="center" cellpadding="3">
    <tr>
      <td align="center" bgcolor="#CCCCCC"><strong>Items in Cart </strong></td>
    </tr>
    <tr>
      <td><table border="1" cellpadding="5">
          <tr bgcolor="#CCCCCC">
            <td>Name</td>
            <td>Quantity</td>
            <td>Price</td>
            <td>Options</td>
          </tr>
          <tr>
            <td>Test Item </td>
            <td>x3 </td>
            <td>$100.00</td>
            <td><a href="#">Edit</a> /<a href="javascript: void();" onMouseDown="return confirm('Are you sure you want to delete this item?')"> Delete</a> </td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center" bgcolor="#CCCCCC"><strong>Add New Item </strong></td>
    </tr>
    <tr>
      <td align="center"><select name="select">
          <option value="1">Test Item - $100.00</option>
      </select></td>
    </tr>
    <tr>
      <td align="center"><input type="submit" name="Submit" value="Submit"></td>
    </tr>
  </table>
</form>
</body>
</html>
