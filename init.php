<?php

// Directory paths
define('ROOT', str_replace('\\', '/', dirname(__FILE__)));
define('DOCUMENT_ROOT', str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT));

require_once 'settings/config.php';
require_once 'auth/auth.php';
require_once 'db/database.php';

$cfg->auth = new Authenticator();
$cfg->db = new DatabaseHelper(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

?>