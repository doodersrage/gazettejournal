<?PHP
// load config constants
require '/export/home/ggazett/ggazett/public_html/admin/includes/config.php';
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
// load general front end functions
require 'functions/general.php';
// load template class
require 'classes/templates.php';
$template = new template;


?>