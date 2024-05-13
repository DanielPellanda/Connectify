<?php 

require_once '../../init.php';
require_once $cfg->GetAbsolutePath('dashboardhelp');
require_once $cfg->GetAbsolutePath('posthelp');

if (empty($_REQUEST['user']))
{
    $cfg->Redirect('dashboard');
    exit;
}

$_SESSION['userid'] = $_REQUEST['user'];
InitSessionVars();

if (isset($_REQUEST['s']) && $_REQUEST['s']) {
    RenderPosts(GetPrivatePosts());
}
else {
    RenderPosts(GetMiscPosts());
}

?>