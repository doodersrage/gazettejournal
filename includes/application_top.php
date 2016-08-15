<?PHP
// start session
session_start();
// load config constants
require '/Users/newgazette/public_html/admin/includes/config.php';
// load database functions
require ADMIN_INCLUDES_DIRECTORY.'/functions/db.php';

// connect to database
dbconnect();

// load store settings
require ADMIN_INCLUDES_DIRECTORY.'/store_settings.php';
// load html functions
require ADMIN_INCLUDES_DIRECTORY.'/classes/htmlfunctions.php';
// initialize html functions
$htmlfunctions = new htmlfunctions();
// load general functions
require ADMIN_INCLUDES_DIRECTORY.'/functions/general.php';
// load login functions
require 'functions/login.php';
// load general front end functions
require 'functions/general.php';
// load template class
require 'classes/templates.php';
$template = new template;
// load banner class
require 'classes/banners.php';
$banners = new banners;
// load media class
require 'classes/embed.php';
$embed = new embed;
// load email function
require ADMIN_INCLUDES_DIRECTORY.'/functions/email.php';
// load nav constant
require ADMIN_INCLUDES_DIRECTORY.'/top_nav.php';

// check user status
//if (!session_check($_SESSION['sessionid'],$_SESSION['username'])) {
//logout_user();
//}

// logout user
if ($_GET['logout_user']==1) {
logout_user();
}

?>