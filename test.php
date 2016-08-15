<?PHP
// start session
session_start();
// load config constants
require '/Users/newgazette/public_html/admin/includes/config.php';
// load database functions
require ADMIN_INCLUDES_DIRECTORY.'/functions/db.php';

// connect to database
dbconnect();

// pull list of available settings
$settings_qry = mysql_query("SELECT * FROM store_settings WHERE (hidden = '' OR hidden is null) ORDER BY name ASC;");
$item_id = 0;
while ($settings_rslt = mysql_fetch_array($settings_qry)) {

// set row styling
echo '<strong>'.$settings_rslt['name'].'</strong><br>'.$settings_rslt['description'].'</td><td'.$row_style.'>'. $settings_rslt['value'] .'</td></tr>';
}

echo 'test';
?>