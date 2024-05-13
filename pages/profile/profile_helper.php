<?php 

require_once ROOT.'/init.php';
require_once $cfg->GetAbsolutePath('posthelp');
require_once $cfg->GetAbsolutePath('user');

function InitSession() {
    $_SESSION['context'] = 'Profilo';

	if (!empty($_SESSION['userid'])) 
	{
		$user = new User($_SESSION['userid']);
		$_SESSION['fullname'] = $user->GetFirstname().' '.$user->GetSurname();
		$_SESSION['pfp'] = $user->GetProfilePicture();
	}
}

function RenderUserPosts($userid) {
	global $cfg;

	// If the user doesn't have an active session, we don't gather any post for him
	if (empty($_SESSION['userid'])) {
		return false;
	}

	// get posts from db
	$posts = $cfg->db->getPostsFromUser($_SESSION['userid'], $userid);
	if (!isset($posts) || count($posts) <= 0) {
		// We don't have any posts to show
		return false;
	}
    //Generate user posts 
    $list = Post::CreateListFromQuery($cfg, $posts);
    foreach ($list as $p) {
        $p->Generate($cfg);
    }

    return true;
}

function FollowersCount($userid){
	global $cfg;

	$followers = $cfg->db->getFollowersCount($userid)[0]['num_followers'];
	if (!isset($followers)) {
		return 0;
	}
	return $followers;
}

function FollowedCount($userid){
	global $cfg;

	$followed =  $cfg->db->getFollowedCount($userid)[0]['num_followed'];
	if (!isset($followed)) {
		return 0;
	}
	return $followed;
}

function GetRegistry($userid){
	global $cfg;

	$registry =  $cfg->db->getProfileRegistry($userid)[0];
	if (!isset($registry)) {
		return ;
	}
	return $registry;
}

function GetUsername($userid){
	global $cfg;

	$username =  $cfg->db->getUsernameFromUserId($userid)[0]['username'];
	if (!isset($username)) {
		return ;
	}
	return $username;
}

function IsFollower($userid){
	global $cfg;

	return $cfg->db->follows($_SESSION['userid'], $userid)[0]['is_following'] ? true : false;
}
?>