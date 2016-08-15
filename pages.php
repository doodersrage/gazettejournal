<?PHP
require 'includes/application_top.php';

// set vars
$page_id = $_GET['pid'];

switch ($page_id) {
case '1':
$title_tag = 'Contact Us';
$page_content = CONTACT_US_CONTENT;
break;
case '2':
$title_tag = 'How To Place A Classified Ad';
$page_content = HOW_TO_PLACE_CLASSIFIED_AD;
break;
case '3':
$title_tag = 'How To Place Notice Content';
$page_content = HOW_TO_PLACE_NOTICE_CONTENT;
break;
case '4':
$title_tag = 'Information Resources';
$page_content = INFORMATION_RESOURCES_TEXT;
break;
case '5':
$title_tag = 'Listing Search';
$page_content = LISTING_SEARCH_TEXT;
$page_content .= '<form name="form1" id="form1" method="post" action="listings_search_results.php">
<table  border="0" cellpadding="3">
  <tr>
    <td>Search For:
      <input name="searchstring" type="text" size="50" maxlength="50" /></td>
    <td>Weddings:
      <input name="searchtype" type="radio" value="weddings" /> 
      Obituaries: 
      <input name="searchtype" type="radio" value="obituaries" />
	  Births:
      <input name="searchtype" type="radio" value="births" />
All:
<input name="searchtype" type="radio" value="both" /></td>
  </tr>
  <tr>
    <td colspan="2"><input type="submit" name="Submit" value="Submit" /></td>
    </tr>
</table>
</form>';
break;
case '6':
$title_tag = 'Local Links';
$page_content = LOCAL_LINKS_TEXT;
break;
case '7':
$title_tag = 'Subscription Information';
$page_content = SUBSCRIPTION_INFO_CONTENT;
break;
case '8':
$title_tag = 'Email Article To Friend';
$page_content = EMAIL_TO_FRIEND_CONTENT . '<form name="form1" method="post" action="'.SITE_ADDRESS.'email-to-friend-submit-id-'.$_GET['artid'].'/">
  <br>
  <table border="0" align="center">
    <tr>
      <td align="right" valign="top"><strong>Friends Email Address:</strong> </td>
      <td><input type="text" name="friend_email"></td>
    </tr>
    <tr>
      <td align="right" valign="top"><strong>Your Email Address:</strong> </td>
      <td><input type="text" name="your_email"></td>
    </tr>
    <tr>
      <td align="right" valign="top"><strong>Note to Friend:</strong> </td>
      <td><textarea name="friend_note" cols="30" rows="5"></textarea></td>
    </tr>
    <tr align="center">
      <td colspan="2"><input type="submit" name="Submit" value="Submit"></td>
    </tr>
  </table>
</form>';
break;
case '9':
if (!empty($_POST['friend_email'])) {
$subject = "Gloucester Gazette-Journal Article Suggestion.";
$articles_query = mysql_query("SELECT a.articles_id, a.title, a.author, a.summary, a.content, a.homepage_image FROM articles a WHERE a.articles_id = '".$_GET['artid']."';");
$articles_result = mysql_fetch_array($articles_query);

$article_link = print_article_link($articles_result['title'],$articles_result['articles_id'],$articles_result['title']);
$message = EMAIL_TO_FRIEND_EMAIL_CONTENT . "Your friend thinks that you will like this article.<br>".(empty($_POST['friend_note']) ? "" : "<strong>Friends Note:</strong><br>".$_POST['friend_note']."<br>")."Article: ".$article_link;

send_email($_POST['friend_email'],$_POST['your_email'],$subject,'',$message);
$title_tag = 'Email Article To Friend';
$page_content = EMAIL_TO_FRIEND_CONTENT_SUBMIT . '<br>Return to: ' . $article_link;
} else {
$page_content =  '<p>You did not enter your friends email address. To email this article to your friend please click the back button and fill in your friends email address.</p><div align="center"><input type="button" onClick="history.back()" value="Back"></div>';
}
break;
case '10':
$title_tag = 'Advanced Search';
$page_content = '<div class="store_header">Advanced Search</div>' . print_search_box(1) /*. print_classifieds_search_box(1)*/ . '<form name="form1" id="form1" method="post" action="listings_search_results.php">
<table  border="0" cellpadding="3" align="center">
  <tr>
    <td colspan="2" align="center"><div align="center"><strong>Weddings and Obituaries Search:</strong></div></td>
    </tr>
  <tr>
    <td colspan="2" align="center">Weddings:
      <input name="searchtype" type="radio" value="weddings" /> 
      Obituaries: 
      <input name="searchtype" type="radio" value="obituaries" />
	  Births:
      <input name="searchtype" type="radio" value="births" />
All:
<input name="searchtype" type="radio" value="both" checked selected /></td>
    </tr>
  <tr>
    <td colspan="2">Search For:
      <input name="searchstring" type="text" size="20" maxlength="100" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Search" class="search_button" /></td>
    </tr>
</table>
</form>';
break;
case '11':
$title_tag = 'Church News';
$page_content = CHURCH_NEWS_CONTENT;
break;
default:
$title_tag = 'Page Not Found';
$page_content = 'We are sorry but that page was not found.<br> Please check your address entry and try again.';
break;
}

$content = $template->fill_area($page_content);

$search_box = print_search_box();

// load template file
if (!empty($template_page)) {
require $template_page;
} else {
require INCLUDES_DIRECTORY.'/templates/inside_pages.php';
}
?>