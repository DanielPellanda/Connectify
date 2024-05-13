<?php 

require_once '../../init.php';

if (!isset($_REQUEST))
    exit;

if (!isset($_REQUEST['user']) || !isset($_REQUEST['post']))
    exit;

$userid = $_REQUEST['user'];
$postid = $_REQUEST['post'];

// Check if user exists
$result = $cfg->db->getUsernameFromUserId($userid);
if (empty($result) || count($result) == 0) {
    exit;
}
// Check if post exists
$result = $cfg->db->getPost($postid);
if (empty($result) || count($result) == 0) {
    exit;
}
$receiverid = $result[0]['user'];

if (isset($_REQUEST['add']) && $_REQUEST['add'] == 1) {
    // Add like
    $cfg->db->like($userid, $postid);

    // Send notification to the post owner
    if ($userid != $receiverid) {
        $cfg->db->notifyLike($userid, $receiverid, $postid);
    }
    exit;
}
// Remove like
$cfg->db->removeLike($userid, $postid);

?>