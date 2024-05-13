<?php 

require_once '../../../init.php';

if (!isset($_REQUEST))
    exit;

if (empty($_REQUEST['comment']))
    exit;
$commentid = $_REQUEST['comment'];
$cfg->db->deletePostComment($commentid);

?>