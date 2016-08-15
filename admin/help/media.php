<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Help - Media</title>
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
          <td width="70%"><a href="#mediatypes">Supported Media Types </a><br>
              <br>
              <a name="mediatypes"></a>
              <p><strong>Media types supported within the system:</strong></p>
              <p><strong>Works with Quicktime or a Quicktime equivalent: </strong><br>
        .mov<br>
        <strong>Can be played back in most media players:</strong><br>
        .avi<br>
        .mpg<br>
        .wmv<br>
        <strong>Requires Adobe Flash: </strong><br>
        .flv<br>
        .swf<br>
        <strong>Requires Real Player: </strong><br>
        .ra <br>
        .rm <br>
            </p></td>
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
