<?php 

// Initialize required components
require_once '../../init.php';
require_once $cfg->GetAbsolutePath('user');
require_once $cfg->GetAbsolutePath('filemgr');

// Max lengths
$username_max_length=40;
$password_max_length=20;
$password_min_length=7;
$email_max_length=50;
$bio_max_length=255;
$profile_img_storage = '/img/profile/';


enum ExitCode {
	case Success;
	case ErrGeneric;
	case ErrEmptyUsername;
	case ErrEmptyFirstname;
	case ErrEmptySurname;
	case ErrEmptyPassword;
	case ErrEmptyEmail;
	case ErrExistsUsername;
	case ErrLongUsername;
	case ErrLongFirstname;
	case ErrLongSurname;
	case ErrLongEmail;
	case ErrInvalidEmail;
	case ErrLongPassword;
	case ErrLongBio;
	case ErrShortPassword;
	case ErrCasePassword;
	case ErrNumberPassword;
	case ErrScharPassword;
	case ErrConfirmPassword;
	case ErrUsedEmail;
}

function InitSession() {
    $_SESSION['context'] = 'Modifica Profilo';

	if (!empty($_SESSION['userid'])) 
	{
		$user = new User($_SESSION['userid']);
		$_SESSION['fullname'] = $user->GetFirstname().' '.$user->GetSurname();
		$_SESSION['pfp'] = $user->GetProfilePicture();
	}
}

function GetUsername($userid){
	global $cfg;

	$username =  $cfg->db->getUsernameFromUserId($userid)[0]['username'];
	if (!isset($username)) {
		return ;
	}
	return $username;
}

function GetEmail($userid){
	global $cfg;

	$email =  $cfg->db->getEmailFromUserId($userid)[0]['email'];
	if (!isset($email)) {
		return ;
	}
	return $email;
}

function GetRegistry($userid){
	global $cfg;

	$registry =  $cfg->db->getProfileRegistry($userid)[0];
	if (!isset($registry)) {
		return ;
	}
	return $registry;
}

function ChangeUsername($username_key){
    global $cfg;
	global $username_max_length;

	// Check if username field is empty
	if(empty(trim($_POST[$username_key]))) {
		return ExitCode::ErrEmptyUsername;
	}
	$username =  $_POST[$username_key];

	// Check if username is too long
	if (strlen($username) > $username_max_length) {
		return ExitCode::ErrLongUsername;
	}

	// Check if username already exists
	$userid = $cfg->db->getUserId($username);
	if (isset($userid) && count($userid) > 0) {
		return ExitCode::ErrExistsUsername;
	}
		
	// Modify the username
	if (!$cfg->db->changeUsername($_SESSION['userid'], $username )) {
		// If request was not successful, throw generic error.
		return ExitCode::ErrGeneric;
	}
	
	// Account modified successfully
	return ExitCode::Success;

}

function ChangePassword($password_key, $confirm_password_key){
    global $cfg;
    global $password_max_length;
	global $password_min_length;

    // Check if password field is empty
	if (empty(trim($_POST[$password_key]))) {
		return ExitCode::ErrEmptyPassword;
	}
	$password = $_POST[$password_key];

    // Check if password is too short
	if (strlen($password) < $password_min_length) {
		return ExitCode::ErrShortPassword;
	}
	// Check if password is too long
	if (strlen($password) > $password_max_length) {
		return ExitCode::ErrLongPassword;
	}
	// Check if password has at least one lower and one upper char.
	if (!strcmp($password, strtolower($password)) || !strcmp($password, strtoupper($password))) {
		return ExitCode::ErrCasePassword;
	}
	// Check if the password has at least one number.
	if (!preg_match('~[0-9]+~', $password)) {
		return ExitCode::ErrNumberPassword;
	}
	// Check if the password has at least one special char.
	if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)) {
		return ExitCode::ErrScharPassword;
	}
	
	// Check if password and confirmed password match.
	if (strcmp($password, $_POST[$confirm_password_key])) {
		return ExitCode::ErrConfirmPassword;
	}

    // Hash the password
	$password = $cfg->auth->GenerateHashedPass($password);

    // Modify the password
	if (!$cfg->db->changePassword($_SESSION['userid'], $password)) {
		// If request was not successful, throw generic error.
		return ExitCode::ErrGeneric;
	}
	
	// Account modified successfully
	return ExitCode::Success;
}

function ChangeName($firstname_key){
    global $cfg;
    global $username_max_length;

    // Check if firstname field is empty
	if(empty(trim($_POST[$firstname_key]))) {
		return ExitCode::ErrEmptyFirstname;
	}
	$firstname =  $_POST[$firstname_key];

    // Check if firstname is too long
	if (strlen($firstname) > $username_max_length) {
		return ExitCode::ErrLongFirstname;
	}

    // Change firstname
	if (!$cfg->db->updateFirstName( $_SESSION['userid'], $firstname)) {
		// If request was not successful, throw generic error.
		return ExitCode::ErrGeneric;
	}
	
	// Account modified successfully
	return ExitCode::Success;
}

function ChangeSurname($surname_key){
    global $cfg;
    global $username_max_length;

    // Check if surname field is empty
	if(empty(trim($_POST[$surname_key]))) {
		return ExitCode::ErrEmptySurname;
	}
	$surname =  $_POST[$surname_key];

	// Check if surname is too long
	if (strlen($surname) > $username_max_length) {
		return ExitCode::ErrLongSurname;
	}

    // Change Surname
	if (!$cfg->db->updateSurname( $_SESSION['userid'], $surname )) {
		// If request was not successful, throw generic error.
		return ExitCode::ErrGeneric;
	}	
	// Account modified successfully
	return ExitCode::Success;
}

function ChangeBio($bio_key){
    global $cfg;
    global $bio_max_length;

    // Check if biography field is empty
	if(empty(trim($_POST[$bio_key]))) {
		$bio = "";
	}
	$bio =  $_POST[$bio_key];

	// Check if biography is too long
	if (strlen($bio) > $bio_max_length) {
		return ExitCode::ErrLongBio;
	}

    // Modifiy biography for the account
	if (!$cfg->db->updateBiography( $_SESSION['userid'], $bio )) {
		// If request was not successful, throw generic error.
		return ExitCode::ErrGeneric;
	}	
	// Biography changed successfully
	return ExitCode::Success;
}

function ChangePic($pfp_key){
    global $cfg;
	global $profile_img_storage;

    // Generate the image name
	$max_pad = 5;
    $img_name = str_pad(strval($_SESSION['userid']), $max_pad, '0', STR_PAD_LEFT);


	$file = $_FILES[$pfp_key];
	$img = false;
	// Check if image file is present and it's not a actual image or fake image
	if (!empty($file['name']) && !empty($file['size']) && getimagesize($file['tmp_name'])) {
		// Upload the image
    	$img = FileManager::Upload($_FILES[$pfp_key], $profile_img_storage, $img_name);
	}
	$img = !$img ? '' : $img;
	
    // Change profile picture
	if (!$cfg->db->updateProfilePicture($_SESSION['userid'], $img)) {
		// If request was not successful, throw generic error.
		return ExitCode::ErrGeneric;
	}	
	// picture changed successfuly
	return ExitCode::Success;
}

function ChangeEmail($email_key){
    global $cfg;
    global $email_max_length;

	// Check if email field is empty
	if (empty(trim($_POST[$email_key]))) {
		return ExitCode::ErrEmptyEmail;
	}
	$email = $_POST[$email_key];

	// Check if email is too long
	if (strlen($email) > $email_max_length) {
		return ExitCode::ErrLongEmail;
	}
	// Check if the email contains a @.
	if (!preg_match('/[@]/', $email)) {
		return ExitCode::ErrInvalidEmail;
	}
	// Check if email is already in use
	$userid = $cfg->db->getUseridFromEmail($email);
	if (isset($userid) && count($userid) > 0) {
		return ExitCode::ErrUsedEmail;
	}

    // Change Email
	if (!$cfg->db->updateEmail( $_SESSION['userid'], $email )) {
		// If request was not successful, throw generic error.
		return ExitCode::ErrGeneric;
	}	
	// Account modified successfully
	return ExitCode::Success;
}
?>