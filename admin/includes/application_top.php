<?PHP
// start session
session_start();
// load config constants
require 'config.php';
// load database connection functions
require ADMIN_INCLUDES_DIRECTORY . '/functions/db.php';
// connect to database
dbconnect();
// load store constants
require ADMIN_INCLUDES_DIRECTORY . '/store_settings.php';
// load html functions
require ADMIN_INCLUDES_DIRECTORY . '/classes/htmlfunctions.php';
// initialize functions
$htmlfunctions = new htmlfunctions();
// load general functions
require ADMIN_INCLUDES_DIRECTORY . '/functions/general.php';
// load login functions
require ADMIN_INCLUDES_DIRECTORY . '/functions/login.php';
// load csv import class
require ADMIN_INCLUDES_DIRECTORY . '/classes/parsecsv.lib.php';
// load email function
require ADMIN_INCLUDES_DIRECTORY . '/functions/email.php';
// load media class
require INCLUDES_DIRECTORY . '/classes/embed.php';
$embed = new embed;


// check user status
if (!session_check($_SESSION['sessionid'],$_SESSION['user_name']) && $_SERVER['PHP_SELF'] != '/admin/login.php') {
logout_user();
header("Location:".ADMIN_ADDRESS."login.php");
}

// logout user
if ($_GET['logout_user']==1) {
logout_user();
}

?>