<?PHP 
// companion functions file for admin-config/admin-users-edit.php

// check for duplicate username
function username_check($username,$existing_user = '') {
if (!empty($existing_user)) {
$user_name_qry = mysql_query("SELECT user_name FROM admin_users WHERE user_name = '".clean_db_inserts($username)."' AND admin_users_id <> '".$existing_user."';");
$user_name_result = mysql_num_rows($user_name_qry);
} else {
$user_name_qry = mysql_query("SELECT user_name FROM admin_users WHERE user_name = '".clean_db_inserts($username)."';");
$user_name_result = mysql_num_rows($user_name_qry);
}

return $user_name_result;
}

// make sure required fields are populated
function required_fields_check ($check_type,$email,$user_name,$password) {
$errors = 0;
// count number of errors
empty($email) ? $errors++ : '';
$errors == 0 ? strpos($email,'@') == 0 ? $errors++ : "" : "";
empty($user_name) ? $errors++ : '';
if ($check_type == 'new') empty($password) ? $errors++ : '';

return $errors;
}

// if errors are found create error message
function error_check($check_type,$email,$user_name,$password) {
$error_string = '';
empty($email) ? $error_string = 'You did not enter an email address.\r\n' : '';
empty($error_string) ? strpos($email,'@') == 0 ? $error_string .= 'The entered email address does not appear to be valid.\r\n' : '' : "";
empty($user_name) ? $error_string .= 'You forgot to enter a username.\r\n' : '';
if ($check_type == 'new') empty($password) ? $error_string .= 'You did not enter a password.\r\n' : '';

return $error_string;
}

?>