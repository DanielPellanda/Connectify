<?php 

require_once '../../init.php';

if (!isset($_REQUEST))
    exit;

if (empty($_REQUEST['post']))
    exit;
$postid = $_REQUEST['post'];

$result = $cfg->db->getPost($postid);
if (empty($result) || count($result) == 0) {
    exit;
}

if (is_file(ROOT.$result[0]['postimg'])) {
    unlink(ROOT.$result[0]['postimg']);
}

$cfg->db->deletePost($postid);
?>