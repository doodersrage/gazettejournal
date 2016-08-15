<?PHP

// check login pass against stored password
function compare_login_pass($saved_pass,$submitted_pass,$created) {
$login_check = 0;

if ($saved_pass == md5($submitted_pass.$created)) $login_check = 1;

return $login_check;
}

// login user
function login_user($username,$password) {

// look up user in database
$user_query = mysql_query("SELECT created, user_name, password, admin FROM admin_users WHERE user_name = '".$username."' AND status = 1;");
$user_query_result = mysql_fetch_array($user_query);
if (mysql_num_rows($user_query) != 0) {
if (compare_login_pass($user_query_result['password'],$password,$user_query_result['created']) == 1) {
// generate and store session_id
$_SESSION['sessionid'] = md5(date(r).$user_query_result['password']);
$_SESSION['user_name'] = $user_query_result['user_name'];
$_SESSION['admin'] = $user_query_result['admin'];

mysql_query("UPDATE admin_users SET session_id = '".$_SESSION['sessionid']."' WHERE user_name = '".$user_query_result['user_name']."';");

header("Location:".ADMIN_ADDRESS."index.php");
} else {
header("Location:".ADMIN_ADDRESS."login.php?error=incorrectlogin");
}
} else {
header("Location:".ADMIN_ADDRESS."login.php?error=incorrectlogin");
}
}

// remove user session cache
function logout_user() {
session_unset();
session_destroy();
header("Location:".ADMIN_ADDRESS."login.php");
}

// user session check
function session_check($session_id,$username) {
$session_check = 0;
$session_query = mysql_query("SELECT session_id FROM admin_users WHERE session_id = '".$session_id."' AND user_name = '".$username."' AND status = 1 AND session_id is not null and session_id <> '';");
$session_query_result = mysql_fetch_array($session_query);
if ($session_query_result['session_id'] == $session_id && mysql_num_rows($session_query) != 0) $session_check = 1;

return $session_check;
}

?>