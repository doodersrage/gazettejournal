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
<form name="form1" method="post" action="javascript: window.close()">
  <table border="0" align="center" cellpadding="3">
    <tr bgcolor="#CCCCCC">
      <td><strong>Option Name </strong></td>
      <td><strong>Price</strong></td>
      <td><strong>Prefix</strong></td>
    </tr>
    <tr>
      <td><input name="textfield" type="text" size="25"></td>
      <td><input name="textfield" type="text" size="10"></td>
      <td><select name="select">
        <option selected> </option>
        <option>+</option>
        <option>-</option>
      </select></td>
    </tr>
    <tr>
      <td colspan="3"><div align="center">
        <input type="submit" name="Submit" value="Submit">
      </div></td>
    </tr>
  </table>
</form>
</body>
</html>
