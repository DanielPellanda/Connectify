<?php 
// Connect to database
require_once 'init.php';

$cfg->ValidateSession();

$cfg->Redirect($cfg->GetAbsolutePath('dashboard'));

?>
Redirecting...