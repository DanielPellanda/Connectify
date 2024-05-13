<?php 

require_once ROOT.'/init.php';
require_once $cfg->GetAbsolutePath('posthelp');
require_once $cfg->GetAbsolutePath('user');

function InitSessionVars() {
	$_SESSION['context'] = 'Home';

	if (!empty($_SESSION['userid'])) 
	{
		$user = new User($_SESSION['userid']);
		$_SESSION['fullname'] = $user->GetFirstname().' '.$user->GetSurname();
		$_SESSION['pfp'] = $user->GetProfilePicture();
	}
}

function GetMiscPosts() {
	global $cfg;

	// If the user doesn't have an active session, we don't gather any post for him
	if (empty($_SESSION['userid'])) {
		return false;
	}
	
	// Let's get the feed from the db
	$posts = $cfg->db->getMixedPostFeed($_SESSION['userid']);
	if (!isset($posts) || count($posts) <= 0) {
		// We don't have any posts to show
		return false;
	}
	return $posts;
}

function GetPrivatePosts() {
	global $cfg;

	// If the user doesn't have an active session, we don't gather any post for him
	if (empty($_SESSION['userid'])) {
		return false;
	}
	
	// Let's get the feed from the db
	$posts = $cfg->db->getFollowedPostFeed($_SESSION['userid']);
	if (!isset($posts) || count($posts) <= 0) {
		// We don't have any posts to show
		return false;
	}
	return $posts;
}

// # Params:
// $posts : query result of the posts to render
function RenderPosts($posts) {	
	global $cfg;

	if ($posts) {
		// Send the query result to the post factory method.
		$list = Post::CreateListFromQuery($cfg, $posts);

		// Generate all the posts 
		foreach ($list as $p) {
			$p->Generate($cfg);
		}
	}
}

?>