<?PHP

// login user
function login_user($username,$password) {

// look up user in database
$user_query = mysql_query("SELECT store_customers_id, username, password FROM store_customers WHERE username = '".$username."' AND account_status = 1;");
$user_query_result = mysql_fetch_array($user_query);
if (mysql_num_rows($user_query) != 0) {
// generate and store session_id
$_SESSION['store_sessionid'] = md5(date(r).$user_query_result['password']);
$_SESSION['store_username'] = $user_query_result['username'];
$_SESSION['store_customers_id'] = $user_query_result['store_customers_id'];

mysql_query("UPDATE store_customers SET session_id = '".$_SESSION['store_sessionid']."' WHERE username = '".$user_query_result['username']."';");

if (isset($_SESSION['cart'])) unset($_SESSION['cart']);

$_SESSION['loggedin'] = 1;
header("Location: ./");
} else {
$GLOBALS['message']='<script language="javascript">alert(\'Your login information does not appear to be correct. \r\n Please check your username or password and try again.\');</script>';
}
}

// remove user session cache
function logout_user() {
session_unset();
session_destroy();
header("Location: store_login.php");
}

// user session check
function session_check($session_id,$username) {
$session_check = 0;
$session_query = mysql_query("SELECT session_id FROM store_customers WHERE session_id = '".$session_id."' AND username = '".$username."' AND account_status = 1 AND session_id is not null and session_id <> '';");
$session_query_result = mysql_fetch_array($session_query);
if ($session_query_result['session_id'] == $session_id && mysql_num_rows($session_query) != 0) $session_check = 1;

return $session_check;
}

?>