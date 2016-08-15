<?PHP
echo $htmlfunctions->write_link('index.php?logout_user=1','Logout');
echo '<br>';

echo '<div class="section_title">Articles</div>';
echo $htmlfunctions->write_link('articles-admin/articles.php','Articles');
//echo $htmlfunctions->write_link('articles-admin/articles-status.php','Articles Status');
echo $htmlfunctions->write_link('articles-admin/categories.php','Categories');
echo $htmlfunctions->write_link('admin-config/church-news.php','Church News');
echo $htmlfunctions->write_link('articles-admin/templates.php','Templates');
echo $htmlfunctions->write_link('articles-admin/mass-upload.php','Mass Upload');

echo '<br>';
echo '<div class="section_title">Listings</div>';
//echo $htmlfunctions->write_link('listings-admin/articles.php?type=classifieds','Classifieds');
echo $htmlfunctions->write_link('listings-admin/articles.php?type=weddings','Weddings');
echo $htmlfunctions->write_link('listings-admin/articles.php?type=births','Births');
echo $htmlfunctions->write_link('listings-admin/articles.php?type=obituaries','Obituaries');
echo $htmlfunctions->write_link('listings-admin/mass-upload.php','Mass Upload');

echo '<br>';
echo '<div class="section_title">Other</div>';
echo $htmlfunctions->write_link('advertising/banners.php','Front Bottom');
echo $htmlfunctions->write_link('admin-config/notepad.php','Notepad');
echo $htmlfunctions->write_link('admin-config/pagesettings.php','Pages Content');
echo $htmlfunctions->write_link('admin-config/emailsettings.php','Email Messages');
echo '<a href="'.SITE_ADDRESS.'webcalendar/login.php" target="_blank">Local Events</a>';
echo '<a href="'.SITE_ADDRESS.'cgi-bin/webadverts/ads_admin.cgi" target="_blank">Advertisements</a>';

echo '<br>';
echo '<div class="section_title">Store</div>';
echo $htmlfunctions->write_link('store-admin/orders.php','Orders');
echo $htmlfunctions->write_link('store-admin/customers.php','Customers');
//echo $htmlfunctions->write_link('store-admin/customer-contact.php','Email Customers');
echo $htmlfunctions->write_link('store-admin/products.php','Items');
echo $htmlfunctions->write_link('store-admin/zip-codes.php','Zip Codes');
//echo $htmlfunctions->write_link('store-admin/categories.php','Categories');

echo '<br>';	
echo '<div class="section_title">Admin Settings</div>';
echo $htmlfunctions->write_link('admin-config/admin-users.php','Admin Users');
echo $htmlfunctions->write_link('admin-config/globalsettings.php','Global Settings');
echo $htmlfunctions->write_link('admin-config/file_manager.php','File Manager');
//echo $htmlfunctions->write_link('admin-config/homesettings.php','Homepage Conf');

echo '<br>';	
echo '<div class="section_title">Help</div>';
echo $htmlfunctions->write_link('help/media.php','Media Types');
echo $htmlfunctions->write_link('help/articles.php','Articles');
echo $htmlfunctions->write_link('help/listings.php','Listings');
echo $htmlfunctions->write_link('help/other.php','Other');
echo $htmlfunctions->write_link('help/store.php','Store');
echo $htmlfunctions->write_link('help/adminconfig.php','Admin Settings');

	  
?>