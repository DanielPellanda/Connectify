<?php

require_once $cfg->GetAbsolutePath('user');

enum PostExitCode {
	case Success;
	case ErrGeneric;
	case ErrEmptyTitle;
	case ErrEmptyText;
}

class Post {

	// # Params
	public $id = -1;
	public $user = null;				// User owner of the post
	public $title = ''; 				// Title of the post
	public $content = ''; 				// Content of the post
	public $date = ''; 					// Date of the post
	public $img = ''; 					// Path for the image attached 
	public $public = true; 				// Privacy setting for the post
	public $liked = false;				// Whather the post is already liked by the current user
	public $numlikes = 0; 				// Number of likes
	public $comments = array(); 		// Array of comments for this post
	public $numcomments = 0; 			// Number of comments

	public static $img_storage = '/img/posts/';
	public static $upload_fail_msg = 'Non è stato possibile caricare l\'immagine, il post non avrà alcuna immagine allegata';

	function __construct($postid) {
		$this->id = $postid;
	} 

	// Main factory method. Get a list of post instances from a query result.
	// # Params
	// $cfg : Global helper instance
	// $result : Query result to use
	public static function CreateListFromQuery($cfg, $result) {			
		require_once $cfg->GetAbsolutePath('user');

		$list_post = array();
		foreach ($result as $p) {
			if (!isset($p['postid'])) {
				continue;
			}
	
			// Create a new params object that we'll pass to the post generator
			$post = new Post($p['postid']);
			$post->pfp = $cfg->GetURL('defaultpfp');
			
			// Get the registry data of the user who made the post
			if (isset($p['user'])) {
				$post->user = new User($p['user']);
			}
			
			// Parse the generic data
			if (isset($p['title'])) { 
				$post->title = $p['title'];
			}
			if (isset($p['text'])) { 
				$post->content = $p['text'];
			}
			if (isset($p['date'])) {
				$post->date = $p['date'];
			}
			if (isset($p['postimg'])) {
				if (is_file(ROOT.$p['postimg'])) {
					$post->img = $p['postimg'];
				}
			}
			if (isset($p['is_public'])) {
				$post->public = $p['is_public'] > 0;
			}
			
			// Get the number of likes
			$likes = $cfg->db->getLikesCount($p['postid']);
			if (!empty($likes) && isset($likes[0]) && isset($likes[0]['COUNT(*)']) && is_numeric($likes[0]['COUNT(*)'])) {
				$post->numlikes = $likes[0]['COUNT(*)'];
			}

			// Get whether the user already liked the post
			$is_liked = false;
			if (isset($_SESSION['userid'])) {
				$likelist = $cfg->db->getLikesList($p['postid']);
				if (isset($likelist) && count($likelist) > 0) {
					foreach($likelist as $like) {
						if (!empty($like['userid'])) {
							if ($_SESSION['userid'] == $like['userid']) {
								$is_liked = true;
								break;
							}
						}
					}
				}
			}
			$post->liked = $is_liked;
	
			// Get the comments
			$comments = $cfg->db->getCommentbyPost($p['postid']);
			if (isset($comments) && count($comments) > 0) {
				$post->comments = PostComment::CreateListFromQuery($cfg, $comments);
				$post->numcomments = count($post->comments);
			}
			
			// Append to the main array
			array_push($list_post, $post);
		}
		return $list_post;
	}
	
	public function GenerateComments($cfg) {
		if (empty($this->comments))
		{
			PostComment::GenerateEmptyComment($cfg);
			return;
		}
		
		foreach ($this->comments as $comment) {
			$comment->Generate($cfg);
		}
	}

	public function Generate($cfg) {
		$p = $this;
		
		include $cfg->GetAbsolutePath('post');
	}
}

class PostComment {

	// # Params
	public $id = -1;
	public $postid = -1;
	public $user = null;				// User who made the comment
	public $date = ''; 					// Date of the comment
	public $content = ''; 				// Text of the comment

	function __construct($commentid) {
		$this->id = $commentid;
	}

	public static function CreateListFromQuery($cfg, $result) {
		require_once $cfg->GetAbsolutePath('user');

		$list_comments = array();
		foreach ($result as $c) {
			if (!isset($c['commentid'])) {
				continue;
			}

			// Create a new params object
			$comment = new PostComment($c['commentid']);

			// Parse data
			if (isset($c['postid'])) {
				$comment->postid = $c['postid'];
			}
			if (isset($c['userid'])) {
				$comment->user = new User($c['userid']);
			}
			if (isset($c['date'])) {
				$comment->date = $c['date'];
			}
			if (isset($c['text'])) {
				$comment->content = $c['text'];
			}
			
			// Append the params object to the array
			array_push($list_comments, $comment);
		}
		return $list_comments;
	}

	public static function GenerateEmptyComment($cfg) {
		include $cfg->GetAbsolutePath('postcommentempty');
	}
	
	public function Generate($cfg) {
		$c = $this;
		
		include $cfg->GetAbsolutePath('postcomment');
	}
}

?>