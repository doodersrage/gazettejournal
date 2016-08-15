<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Gloucester-Mathews Gazette-Journal<?PHP echo (!empty($title_tag) ? ': ' . $title_tag: '' ); ?></title>
<link type="text/css" href="/includes/styles/categories.css" rel="stylesheet">
<base href="<?PHP echo SITE_ADDRESS; ?>supercali/" />
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td width="85" height="66"><img src="images/space.gif" alt="sp" width="85" height="48" /></td>
    <td width="777" height="48">&nbsp;</td>
    <td width="76" height="48"><img src="images/space.gif" alt="sp" width="76" height="48" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td valign="top"><table width="771" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center" valign="top" background="images/blue.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
              <td align="center"><?PHP require 'supercali/index.php'; ?></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td bgcolor="#F2F1D1"><img src="images/banner.jpg" alt="Gloucester-Mathews Gazette-Journal" width="771" height="114" /></td>
      </tr>
      <tr>
        <td background="images/line1.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
              <td><div class="header_left"><?PHP echo DATE_VOLUME_STRING; ?></div> <div class="last_updated">Last Updated: <?PHP echo date("F j, Y, g:i a",strtotime(SITE_LAST_UPDATED)); ?></div></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td><img src="images/line2.jpg" alt="Holding The line" width="771" height="6" /></td>
      </tr>
    </table>
<?PHP echo $content; ?>
	</td>
    <td>&nbsp;</td>
    <td align="left" valign="bottom">
	  <table width="228" height="280" border="0" cellpadding="15" cellspacing="0" class="notepad_area">
        <tr>
          <td valign="top"><?PHP echo NOTEPAD_TEXT; ?>
              <p><img src="images/space.gif" alt="sp" width="200" height="5" /></p></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
