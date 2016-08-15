<?PHP
// disabled in favor of updateable constants document
// writes store settings to constant values
//$constants_query = mysql_query('SELECT constant, value FROM store_settings ;');

//while ($constants_result = mysql_fetch_array($constants_query)) {
//define($constants_result['constant'],$constants_result['value']);
//}

require ADMIN_INCLUDES_DIRECTORY . '/constants.php';

// update constants static file
function update_constants_file() {
$myFile = ADMIN_INCLUDES_DIRECTORY . "/constants.php";

$fh = fopen($myFile, 'w') or die("can't open file");

$constants_query = mysql_query('SELECT constant, value FROM store_settings ;');

// write set constants as static variables
$stringData =  "<?PHP" . "\r\n";
while ($constants_result = mysql_fetch_array($constants_query)) {
$stringData .= "define('".$constants_result['constant']."','".str_replace(array("\'","'"),"\'",$constants_result['value'])."');" . "\r\n";
}
$stringData .=  "?>";

fwrite($fh, $stringData);
fclose($fh);
}
?>