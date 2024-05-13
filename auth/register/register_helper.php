<?php 

// Initialize required components
require_once '../../init.php';

// Max lengths
$username_max_length=40;
$password_max_length=20;
$password_min_length=7;

enum ExitCode {
	case Success;
	case ErrGeneric;
	case ErrEmptyUsername;
	case ErrEmptyFirstname;
	case ErrEmptySurname;
	case ErrEmptyPassword;
	case ErrExistsUsername;
	case ErrLongUsername;
	case ErrLongFirstname;
	case ErrLongSurname;
	case ErrLongPassword;
	case ErrShortPassword;
	case ErrCasePassword;
	case ErrNumberPassword;
	case ErrScharPassword;
	case ErrConfirmPassword;
}

// Validates form data and registers new account in the db.
// Returns an exit code indicating the esit of the request (0 if successful).
function RegisterNewAccount($username_key, $password_key, $confirm_password_key, $firstname_key, $surname_key) {
    global $cfg;
	global $username_max_length;
	global $password_max_length;
	global $password_min_length;

	// Field empty checks
	// Check if username field is empty
	if(empty(trim($_POST[$username_key]))) {
		return ExitCode::ErrEmptyUsername;
	}
	$username =  $_POST[$username_key];
	
	// Check if firstname field is empty
	if(empty(trim($_POST[$firstname_key]))) {
		return ExitCode::ErrEmptyFirstname;
	}
	$firstname =  $_POST[$firstname_key];
	
	// Check if surname field is empty
	if(empty(trim($_POST[$surname_key]))) {
		return ExitCode::ErrEmptySurname;
	}
	$surname =  $_POST[$surname_key];
	
	// Check if password field is empty
	if (empty(trim($_POST[$password_key]))) {
		return ExitCode::ErrEmptyPassword;
	}
	$password = $_POST[$password_key];
	
	// Format checks
	// Check if username is too long
	if (strlen($username) > $username_max_length) {
		return ExitCode::ErrLongUsername;
	}
	// Check if firstname is too long
	if (strlen($firstname) > $username_max_length) {
		return ExitCode::ErrLongFirstname;
	}
	// Check if surname is too long
	if (strlen($surname) > $username_max_length) {
		return ExitCode::ErrLongSurname;
	}
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
	
	// Validate credentials
	// Check if username already exists
	$userid = $cfg->db->getUserId($username);
	if (isset($userid) && count($userid) > 0) {
		return ExitCode::ErrExistsUsername;
	}
	
	// Hash the password
	$password = $cfg->auth->GenerateHashedPass($password);
	
	// Register the new account entry in the db
	if (!$cfg->db->register($username, $password, $firstname, $surname)) {
		// If request was not successful, throw generic error.
		return ExitCode::ErrGeneric;
	}
	
	// Account registered successfully
	return ExitCode::Success;
}

?>