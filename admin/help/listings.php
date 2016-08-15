<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Help - Listings</title>
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
		  <td width="68%">
		  <a href="#add_listing">Adding a new listing</a><br>
            <a href="#modify_listing">Modifying an existing listing</a><br>
			
		  <p><strong><a name="add_listing"></a>Adding a new listing:</strong></p>
            <p>1. Click which section you would like to add the new listing to from the navigation menu to the left.</p>
            <p>2. Once on the page the lists the listings assigned to that section then click the &quot;Add New&quot; button to the bottom left.</p>
            <p>3. Select a start date and a expiration date for the new article. (To post an article that does not expire leave the expiration date blank.)</p>
            <p>4. Assign a title to your new listing.</p>
            <p>5. Add an image if needed.</p>
            <p>6. Enter a listing summary if needed.</p>
            <p>7. Fill in the listings content then click submit.</p>
            <p>&nbsp;</p>
            <p><strong><a name="modify_listing"></a>Modifying an existing listing:</strong></p>
            <p>Select the listing section that contains the listing that you want to modify then click the edit button to the far left of the listing to be modified. </p></td>
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
