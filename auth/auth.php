<?php 

// Authentication and Session Manager
class Authenticator {
	
	private $hash_key='caboto01';
	private $hash_key2='cingolo3';
	
	private $cookie_id_key = 'UID';
	private $cookie_userid_key = 'User';
	
	// Session length in seconds
	private $session_time=60*30;
	
	// Remember me cookie time
	private $remember_me_time=60*60*24*120;
	
	function __construct() {
	}
	
	private function IsValidSessionActive($db) {		
		if (!isset($_COOKIE[$this->cookie_userid_key]) || !isset($_COOKIE[$this->cookie_id_key])) {
			return false;
		}
		$username = $db->getUsernameFromUserId($_COOKIE[$this->cookie_userid_key]);
		if (!isset($username) || count($username) <= 0) {
			return false;
		}
		$username = $username[0]['username'];
		if (!isset($_COOKIE[$this->GenerateHash($username)])) {
			return false;
		}
		
		$hashed_password = $_COOKIE[$this->GenerateHash($username)];
		$cookie = $_COOKIE[$this->cookie_id_key];
		return !strcmp($cookie, $this->GenerateAuthToken($hashed_password));
	}
	
	public function ValidateSession($db) {
		if (!$this->IsValidSessionActive($db)) {
			$this->ResetCookies();
			return false;
		}
		$_SESSION['userid'] = $_COOKIE[$this->cookie_userid_key];
		$_SESSION['username'] = $db->getUsernameFromUserId($_COOKIE[$this->cookie_userid_key])[0]['username'];
		return true;
	}
	
	public function GenerateHash($string) {
		return hash('sha512', $string);
	}
	
	public function GenerateAuthToken($hashed_password) {
		return $this->GenerateHash($hashed_password.$this->hash_key2);
	}
	
	public function GenerateHashedPass($password) {
		return $this->GenerateHash($password.$this->hash_key);
	}
	
	public function ResetCookies() {
		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			foreach($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);
				setcookie($name, '', time()-1000);
				setcookie($name, '', time()-1000, '/');
			}
		}
	}
	
	public function CreateCookies($userid, $username, $hashed_password, $remember_me) {
		$token = $this->GenerateAuthToken($hashed_password);
		session_name($username);
		session_start();
		$time = time()+$this->session_time;
		if ($remember_me) {
			$time = time()+$this->remember_me_time;
		}
		setcookie($this->cookie_id_key, $token, $time ,'/');
		setcookie($this->cookie_userid_key, $userid, $time,'/');
		setcookie($this->GenerateHash($username), $hashed_password, $time,'/');
	}
}

?>