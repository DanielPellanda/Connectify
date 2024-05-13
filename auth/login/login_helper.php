<?php 

enum ExitCode {
	case Success;
	case ErrGeneric;
	case ErrEmptyUsername;
	case ErrEmptyPassword;
	case ErrWrongUsername;
	case ErrWrongPassword;
}

// Validates username and password submitted and checks if they match with a db entry. 
// Returns an exit code indicating the esit of the request (0 if successful).
function ValidateAuthRequest($username_key, $password_key) {
	global $cfg;
	global $username;
	global $userid;
	global $hashed_password;
	
	// Check if username field is empty
	if(empty(trim($_POST[$username_key]))) {
		return ExitCode::ErrEmptyUsername;
	}
	$username =  $_POST[$username_key];
	
	// Check if password field is empty
	if (empty(trim($_POST[$password_key]))) {
		return ExitCode::ErrEmptyPassword;
	}
	// Hash the password inserted, we'll compare this with the hash of the password registered for the account.
	$password = $cfg->auth->GenerateHashedPass($_POST[$password_key]);
		
	// Validate credentials
	// Check if username exists
	$userid = $cfg->db->getUserId($username);
	if (!isset($userid) || count($userid) <= 0) {
		return ExitCode::ErrWrongUsername;
	}
	
	// If we have more than a record with that username, throw generic error.
	if (count($userid) > 1) {
		return ExitCode::ErrGeneric;
	}
	
	$userid = $userid[0]['userid'];
	
	// Check if a password is associated to the account
	$hashed_password = $cfg->db->getLoginData($userid);

    // If the db cannot give us a password, throw generic error.
	if (!isset($hashed_password) || count($hashed_password) <= 0) {
		return ExitCode::ErrGeneric;
	}
	
	// If we have more than password binded with that username, throw generic error.
	if (count($hashed_password) > 1) {
		return ExitCode::ErrGeneric;
	}
	
	$hashed_password = $hashed_password[0]['password'];
	// Verify if the password inserted matches with the password received
	if (strcmp($password, $hashed_password)) {
		return ExitCode::ErrWrongPassword;
	}
	
	// Authentication successful
	return ExitCode::Success;
}

?>