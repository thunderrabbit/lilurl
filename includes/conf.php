<?php /* conf.php ( config file ) */

// page title
define('PAGE_TITLE', 'lil&#180; URL Generator');

// MySQL connection info
define('MYSQL_USER', 'username');
define('MYSQL_PASS', 'password');
define('MYSQL_DB', 'dbname');
define('MYSQL_HOST', 'localhost');

// MySQL tables
define('URL_TABLE', 'lil_urls');

// use mod_rewrite?
define('REWRITE', true);

// allow urls that begin with these strings
// $allowed_protocols = array('http:', 'https:', 'mailto:');
$allowed_protocols = array('http:');

$allowed_strings = array('mydomain.com/');   // all URLs must contain this string

// uncomment the line below to skip the protocol check
// $allowed_procotols = array();

// uncomment the line below to skip the URL check
// $allowed_strings = array();

?>
