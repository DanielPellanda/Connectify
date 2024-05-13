<?php 

require_once ROOT.'/init.php';
require_once $cfg->GetAbsolutePath('user');

function InitSession() {
    $_SESSION['context'] = 'Notifiche';

	if (!empty($_SESSION['userid'])) 
	{
		$user = new User($_SESSION['userid']);
		$_SESSION['fullname'] = $user->GetFirstname().' '.$user->GetSurname();
		$_SESSION['pfp'] = $user->GetProfilePicture();
	}
}

function GenerateList($notifications){
	global $cfg;
	global $strings;

	echo '
		<ul class="list-group list-group-flush">';

	foreach($notifications as $notification){
		echo '
			<li class="list-group-item">
				<div class="row">
					<div class="col-lg-10">
						<p>';

		switch ($notification['type']) {
			case 'Follow' :
				echo '
					<a href="'.$cfg->GetURL('profile').'?user='.$notification['senderid'].'">'.GetUsername($notification['senderid']).'</a>
					'.$strings['follow'].'';
				  break;
			case 'Like' :
				echo '
					<a href="'.$cfg->GetURL('profile').'?user='.$notification['senderid'].'">'.GetUsername($notification['senderid']).'</a>
					'.$strings['like'].'"'.$cfg->db->getPostTitle($notification['postid'])[0]['title'].'"';
				  break;
			case 'Comment' :
				echo '
				  	<a href="'.$cfg->GetURL('profile').'?user='.$notification['senderid'].'">'.GetUsername($notification['senderid']).'</a>
				  	'.$strings['comment1'].''.$cfg->db->getCommentTextbyId($notification['commentid'])[0]['text'].'
					'.$strings['comment2'].''.$cfg->db->getPostTitle($notification['postid'])[0]['title'].'"';
				  break;
			case 'Post' :
				echo '
					<a href="'.$cfg->GetURL('profile').'?user='.$notification['senderid'].'">'.GetUsername($notification['senderid']).'</a>
					'.$strings['post'].''.$cfg->db->getPostTitle($notification['postid'])[0]['title'].'"';
				break;
		}

		echo'
						</p>
					</div>
					<div class="col-lg-2 align-self-center">
						<p>'.$notification['date'].'</p>
	  				</div>
				</div>
		  	</li>';
		
		$cfg->db->visualizeNotification($notification['notificationid']);
	}
	echo '</ul>';
}

function NotificationCount($userid){
	global $cfg;

	$count = $cfg->db->getNotificationCount($userid)[0]['num_notifications'];
	if (!isset($count)) {
		return 0;
	}
	return $count;
}

function ReadNotificationCount($userid){
	global $cfg;

	$count = $cfg->db->getReadNotificationCount($userid)[0]['num_notifications'];
	if (!isset($count)) {
		return 0;
	}
	return $count;
}

function GetUsername($userid){
	global $cfg;

	$username =  $cfg->db->getUsernameFromUserId($userid)[0]['username'];
	if (!isset($username)) {
		return ;
	}
	return $username;
}
?>