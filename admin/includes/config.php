<?PHP

error_reporting(E_ERROR);

// file system constants
// directory which stores the site
define('HOST_DIRECTORY',$_SERVER['DOCUMENT_ROOT']);
// front end location
define('INCLUDES_DIRECTORY',HOST_DIRECTORY.'/includes');
// admin directory
define('ADMIN_DIRECTORY',HOST_DIRECTORY.'/admin');
define('ADMIN_INCLUDES_DIRECTORY',ADMIN_DIRECTORY.'/includes');
// images store
define('IMAGES_DIRECTORY',HOST_DIRECTORY.'/images/');
// files store
define('FILES_DIRECTORY',HOST_DIRECTORY.'/files/');


//Address constants
// Address used to writing links dynamically within the site
define('SITE_ADDRESS','http://www.beta.gazettejournal.net/');
// Address used to writing links dynamically within the site
define('SECURE_SITE_ADDRESS','https://www.gazettejournal.net/');
// Address for writing dynamic links within the admin
define('ADMIN_ADDRESS',SITE_ADDRESS.'admin/');
// images address
define('IMAGES_ADDRESS',SITE_ADDRESS.'images/');
// file address
define('FILES_ADDRESS',SITE_ADDRESS.'files/');

// database config constants
define('DB_TYPE','mysql');
define('DB_HOST','pc48.seva.net');
define('DB_NAME','ggazett');
define('DB_USERNAME','ggazett');
define('DB_PASSWORD','94jdu4k');

// pear constants
define('DEBUG_ENV', true);
define('PEAR_DB_DONTDIE', false);

// other constants
define('LB',"\r\n");

// enable or disable order tax 1 = enabled 0 = disabled
define('ENABLE_ORDER_TAX',0);
?>