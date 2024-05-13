<?php 

require_once '../../init.php';

if (!isset($_REQUEST))
    exit;

if (empty($_REQUEST['post']))
    exit;
$postid = $_REQUEST['post'];
$cfg->db->deletePost($postid);

?>