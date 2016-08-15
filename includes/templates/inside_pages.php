<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Gloucester-Mathews Gazette-Journal<?PHP echo (!empty($title_tag) ? ': ' . $title_tag: '' ); ?></title>
<link type="text/css" href="/includes/styles/categories.css" rel="stylesheet">
<base href="<?PHP echo SITE_ADDRESS; ?>">
</head>

<body>
<div class="container">
<div class="content">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center" valign="top" class="template_blue"><table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
              <td align="center"><?PHP require 'includes/top_nav.php'; ?></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td bgcolor="#F2F1D1"><img src="images/banner.jpg" alt="Gloucester-Mathews Gazette-Journal" width="771" height="114"></td>
      </tr>
      <tr>
        <td class="template_line1"><table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
              <td><div class="header_left"><?PHP echo DATE_VOLUME_STRING; ?></div> <div class="last_updated">Last Updated: <?PHP echo date("F j, Y, g:i a",strtotime(SITE_LAST_UPDATED)); ?></div></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td><img src="images/line2.jpg" alt="Holding The line" width="858" height="6"></td>
      </tr>
    </table>
<?PHP echo $content; ?>
<div align="center"><P class="copywrite">Copyright &copy; <?PHP echo date("Y"); ?>, Tidewater Newspapers, Inc.</P></div>
<?PHP echo $search_box; ?>
</div>
<div class="notepad">
<?PHP echo NOTEPAD_TEXT; ?>
</div>
</div>
<?PHP
echo $message;
?>
</body>
</html>
