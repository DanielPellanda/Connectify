<?php

require_once 'settings.php';

class ConfigHelper {
	
	private $site_title='FakeBook';
	private $formatted_title='<b>Fake</b>Book';
	
	private $paths = array(
		// Settings
		'settings' 	  		=> '/settings/settings.php',
		'init'		  		=> '/settings/init.php',
		'stylesheets' 		=> '/settings/styles/styles.php',
		'filemgr'			=> '/settings/filemanager.php',
				
		// Auth		
		'auth'        		=> '/auth/auth.php',
		'login'       		=> '/auth/login/login.php',
		'loginhelp'       	=> '/auth/login/login_helper.php',
		'register'    		=> '/auth/register/register.php',
		'registerhelp'    	=> '/auth/register/register_helper.php',
		'user'				=> '/auth/user.php',
				
		// Database		
		'db'		  		=> '/db/database.php',
				
		// Sidebar		
		'sidebar'	  		=> '/accs/sidebar/sidebar.php',
		'sidebarhelp' 		=> '/accs/sidebar/sidebar_helper.php',
		
		// Navbar
		'navbar'	  		=> '/accs/navbar/navbar.php',
		
		// Dashboard
		'dashboard'   		=> '/pages/dashboard/dashboard.php',
		'dashboardhelp'   	=> '/pages/dashboard/dashboard_helper.php',
		'dashboardscript'   => '/pages/dashboard/dashboard.js',

		// Posts
		'post' 		 	  	=> '/pages/post/post.php',
		'posthelp' 		  	=> '/pages/post/post_helper.php',
		'postlike'			=> '/pages/post/post_likes.php',
		'postdelete'		=> '/pages/post/post_delete.php',
		'postscript'		=> '/pages/post/post.js',

		'postcomment'		=> '/pages/post/comment/post_comment.php',
		'postcommentnew'	=> '/pages/post/comment/post_comment_new.php',
		'postcommentdelete'	=> '/pages/post/comment/post_comment_delete.php',
		'postcommentempty'	=> '/pages/post/comment/post_comment_empty.php',
		'postcommentscript'	=> '/pages/post/comment/post_comment.js',

		'posteditor'		=> '/pages/post/editor/post_editor.php',
		'posteditorscript'	=> '/pages/post/editor/post_editor.js',
		'posteditorhelp'	=> '/pages/post/editor/post_editor_helper.php',

		// Default pfp
		'defaultpfp' 		=> '/img/profile/default.jpg',

		// Profile 
		'profile' 		=> '/pages/profile/profile.php',
		'profilehelp' 		=> '/pages/profile/profile_helper.php',

		// Notification 
		'notification' 		=> '/pages/notification/notification.php',
		'notificationhelp' 		=> '/pages/notification/notification_helper.php',

		// Modify
		'modifyprofile' 		=> '/pages/edit/modify_profile.php',
		'modifyscript' 		=> '/pages/edit/modify_profile.js',
		'modifyhelp' 		=> '/pages/edit/modify_helper.php',
		
	);
	public $auth;
	public $db;
		
	function __construct() {
	}
	
	public function GetTitle() {
		return $this->site_title;
	}
	
	public function GetFormattedTitle() {
		return $this->formatted_title;
	}
	
	public function PrintHeadTitle($context) {
		echo '
		<title>'.$this->site_title.' | '.$context.'</title>
		';
	}

	public function GetURL($key) {
		$key = strtolower(trim($key));
		
		return SERVER_URL.$this->paths[$key];
	}
	
	public function GetAbsolutePath($key) {
		$key = strtolower(trim($key));
		
		return ROOT.$this->paths[$key];
	}

	public function ShowAlert($message) {
		echo '<script type="text/javascript">alert("' . $message . '")</script>';
	}

	public function ValidateSession() {
		if(!$this->auth->ValidateSession($this->db))
		{
			// If session is not valid, user will be redirected to login page
			$this->Redirect($this->GetURL('login'));
		}
	}

	public function Redirect($path) {		
		header("location: ".$path);
		exit;
	}

	public function IncludeStylesheets() {
		include $this->GetAbsolutePath('stylesheets');
	}
}
$cfg = new ConfigHelper();

?>