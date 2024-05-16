<?php 

require_once '../../../init.php';

if (!isset($_REQUEST))
    exit;

if (!isset($_REQUEST['post']))
    exit;
$postid = $_REQUEST['post'];

// Check if post exists
$result = $cfg->db->getPost($postid);
if (empty($result) || count($result) == 0) {
    exit;
}
$userid = $result[0]['user'];

// Check if the current user can access the image
if (!$cfg->auth->ValidateSession($cfg->db))
    exit;

if ($userid != $_SESSION['userid'])
    exit;

if (is_file(ROOT.$result[0]['postimg'])) {
    unlink(ROOT.$result[0]['postimg']);
}

?>