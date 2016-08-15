<?PHP
require '../includes/application_top.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Help - Other Section</title>
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
          <td width="83%"><p><a href="#frontbottom">Front Bottom </a><br>
              <a href="#notepad">Notepad</a>            <br>
              <a href="#pagescontent">Pages Content</a><br>
              <a href="#emailmessages">Email Messages
              </a><br>
              <a href="#localevents">Local Events
              </a><br>
              <a href="#advertisements">Advertisements </a>              </p>
            <p><strong>Front Bottom :</strong><a name="frontbottom" id="frontbottom"></a><br>
              Within this section you can assign images or videos to be displayed within the bottom left hand area on the front page. All items entered into this section are cycled randomly based on assigned weight.</p>
            <p>Available Settings:<br>
              Status - Enables you to enable or disable the display of an item<br>
              Name - The name assigned to an item<br>
              Image - While labeled image you can upload any type of media file specified within the media types documentation.<br>
              Image Alternate Text - Alternet text to be displayed if an image has been assigned.<br>
              Link - This link will be assigned to the image.<br>
              Open New Window On Click - If a link has been assigned this option will tell the link to open a new window once clicked. <br>
              Listing Weight - The larger the number the greater chance for the item to be displayed. </p>
            <p><strong>Notepad:</strong><a name="notepad" id="notepad"></a><br>
              Assign notepad area content in this section. The notepad is the notepad area to the right of all front end content pages. <br> 
               </p>
            <p><strong>Pages Content :</strong><a name="pagescontent" id="pagescontent"></a><br>
              Use the pages content section to assign content for pages such as &quot;how to place a classified ad&quot;, contact us page, listings search, and local links. </p>
            <p><strong>Email Messages:</strong><a name="emailmessages" id="emailmessages"></a><br>
              This section allows you to assign email content for order and signup emails. </p>
            <p><strong>Local Events :</strong><a name="localevents" id="localevents"></a><br>
              This link will take you to the event calendar administration interface. Within it you can add, edit, delete events from the calendar.</p>
            <p><strong>Advertisements:</strong><a name="advertisements" id="advertisements"></a><br>
              Click this link to load the advertisement management system. </p></td>
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
