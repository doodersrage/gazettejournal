<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Help - Store</title>
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
          <td width="83%"><p><a href="#customers">Customers</a><br>
              <a href="#orders">Orders</a><br>
              <a href="#items">Items</a><br>
              <a href="#zipcodes">Zip Codes</a>          </p>
            <p><strong>Customers:</strong><a name="customers"></a><br>
              Within customers you can browse a list of existing customers and edit their assigned information or delete their accounts entirely.</p>
            <p>Customer settings include:<br>
              Enable/Disable Account <br>
              Email Address<br>
              First Name<br>
              Last Name<br>
              Middle Initial<br>
              Address1<br>
              Address2<br>
              City<br>
              State<br>
              Zip
</p>
            <p>If a customer forgets their password they have the ability to assign themselves a new one from the store by entering in their username and email address.</p>
            <p><strong>Items:</strong><a name="items"></a><br>
              Within this section you can add new items or adjust the settings for existing items.</p>
            <p>Item settings include:<br>
              Status - Allows you to enable or disable the display of an item<br>
              Item Type - Allows you to assign the type of item. Options include: Classified, Item, Subscription<br>
              Name - This is the name that will be assigned to the item<br>
              Price - Regular price of the item<br>
              Extended Price - If assigned this price will be given to customer who fall outside of the local area<br>
              Display Add To Cart Button - This option will hide the add to cart button for the specified item. It is currently only used for the wedding and obituaries transcript since they are ordered from the listing rather than the store.<br>
              Image - If you would like to assign an image to any item just click browse and select the image from anywhere on your local machine.<br>
              Description - Works much like articles description except the description will be displayed above content on the item information page if assigned. <br>
              Content - Works much like articles Content but appears just under description.<br> 
               </p>
            <p><strong>Orders:</strong><a name="orders"></a><br>
              Within this section you can view and edit all of the orders that have been placed. </p>
            <p>Within orders editor you can adjust payment details and enter order notes.</p>
            <p><strong>Zip Codes:</strong><a name="zipcodes"></a><br>
              Within this section you can input or change a listing of local zip codes that are used to decipher if the customer is local or non-local. If the customer is found not to be a local then the items which have an extended price assigned to them will then have this value override the regular price value. </p></td>
        </tr>
      </table>
    </div></td>
  </tr>
</table>
<?PHP require '../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
