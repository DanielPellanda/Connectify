<?php 

require_once '../../../init.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST')
    exit;

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

if (empty($_POST['txtComment'.$postid]))
    exit;
$text = $_POST['txtComment'.$postid];

$commentid = $cfg->db->newPostComment($userid, $postid, $text);
if ($userid != $receiverid) {
    $cfg->db->notifyComment($userid, $receiverid, $postid, $commentid);
}

$cfg->Redirect($cfg->GetURL('dashboard').'#post'.$postid);

?>