<?php

// Initialize required components
require_once '../../init.php';
require_once $cfg->GetAbsolutePath('registerhelp');

// Form POST keys
$username_key = 'username';
$firstname_key = 'name';
$surname_key = 'surname';
$password_key = 'password';
$confirm_password_key = 'confirm';
$email_key = 'email';

// String localizer
$strings = array(
	'login_box_msg'=>'Crea un nuovo account',
	'remember_me'=>'Rimani Collegato',
	
	'sign_up'=>'Registrati',
	'sign_in_href'=>'Sei già registrato?',

	'placeholder_username'=>'Username',
	'placeholder_firstname'=>'Nome',
	'placeholder_surname'=>'Cognome',
	'placeholder_password'=>'Password',
	'placeholder_confirm_password'=>'Conferma Password',
	'placeholder_email'=>'Email',

	'missing_username'=>'Inserire un username.',
	'missing_firstname'=>'Inserire un nome.',
	'missing_surname'=>'Inserire un cognome.',
	'missing_password'=>'Inserire una password.',
	'missing_email'=>'Inserire una email.',

	'error_username'=>"È già presente un account con quell'username.",
	'error_username_long'=>"L'username inserito non può essere lungo più di ".$username_max_length." caratteri.",
	'error_firstname_long'=>'IL nome inserito non può essere lungo più di '.$username_max_length.' caratteri.',
	'error_surname_long'=>'Il cognome inserito non può essere lungo più di '.$username_max_length.' caratteri.',
	'error_password_long'=>'La password inserita non può essere lunga più di '.$password_max_length.' caratteri.',
	'error_password_short'=>'La password inserita non può essere lunga meno di '.$password_min_length.' caratteri.',
	'error_password_case'=>'La password inserita deve contenere almeno un carattere maiuscolo (A) e minuscolo (a).',
	'error_password_number'=>'La password inserita deve contenere almeno un numero.',
	'error_password_schar'=>'La password inserita deve contenere almeno un carattere speciale ( [\'^£$%&*()}{@#~?><>,|=_+¬-] ).',
	'error_confirm_password'=>'La due password inserite non coincidono.',
	'error_email_long'=>'La email inserita non può essere lunga più di '.$email_max_length.' caratteri.',
	'error_email_invalid'=>'La email inserita non è valida',
	'error_email'=>'La email inserita è già in uso',
	'error_generic'=>'Oops! Qualcosa è andato storto. Per favore riprova più tardi.',
	'success'=>'Account creato con successo!',
);

// Link paths
$hrefs = array(
	'login_logo'=>'',
	'sign_in'=>$cfg->GetURL('login'),
);

$cfg->auth->ResetCookies();

unset($error);
// Processing form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	// Validate request
	$status = RegisterNewAccount($username_key, $password_key, $confirm_password_key, $firstname_key, $surname_key, $email_key);
	
	// Assigns the error message to show under the form.
	$error = match ($status) {
		ExitCode::Success => null,
		
		// Don't output an error message when submitting an empty username,
		// otherwise it will show up everytime the page is reloaded.
		//ExitCode::ErrEmptyUsername => $strings['missing_username'],
		ExitCode::ErrEmptyUsername => null,
		
		ExitCode::ErrEmptyPassword => $strings['missing_password'],
		ExitCode::ErrEmptyFirstname => $strings['missing_firstname'],
		ExitCode::ErrEmptySurname => $strings['missing_surname'],
		ExitCode::ErrExistsUsername => $strings['error_username'],
		ExitCode::ErrLongUsername => $strings['error_username_long'],
		ExitCode::ErrLongFirstname => $strings['error_firstname_long'],
		ExitCode::ErrLongSurname => $strings['error_surname_long'],
		ExitCode::ErrLongPassword => $strings['error_password_long'],
		ExitCode::ErrShortPassword => $strings['error_password_short'],
		ExitCode::ErrCasePassword => $strings['error_password_case'],
		ExitCode::ErrNumberPassword => $strings['error_password_number'],
		ExitCode::ErrScharPassword => $strings['error_password_schar'],
		ExitCode::ErrConfirmPassword => $strings['error_confirm_password'],
		ExitCode::ErrEmptyEmail => $strings['missing_email'],
		ExitCode::ErrUsedEmail => $strings['error_email'],
		ExitCode::ErrLongEmail => $strings['error_email_long'],
		ExitCode::ErrInvalidEmail => $strings['error_email_invalid'],
		
		default => $strings['error_generic'],
	};

	if ($status == ExitCode::Success){
		$cfg->Redirect($cfg->GetURL('login'));
	}
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php $cfg->PrintHeadTitle('Register') ?>
  <?php $cfg->IncludeStylesheets() ?>
</head>
<body class="register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="<?php echo $hrefs['login_logo'] ?>"><strong><?php echo $cfg->GetFormattedTitle() ?></strong></a>
  </div>

  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg"><?php echo $strings['login_box_msg'] ?></p>

      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div class="input-group mb-3">
		  <label for="<?php echo $username_key ?>" hidden><?php echo $strings['placeholder_username'] ?></label>
          <input type="text" id="<?php echo $username_key ?>" name="<?php echo $username_key ?>" title="<?php echo $username_key ?>" class="form-control" placeholder="<?php echo $strings['placeholder_username'] ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
		<div class="input-group mb-3">
		  <label for="<?php echo $email_key ?>" hidden><?php echo $strings['placeholder_email'] ?></label>
          <input type="text" id="<?php echo $email_key ?>" name="<?php echo $email_key ?>" title="<?php echo $email_key ?>" class="form-control" placeholder="<?php echo $strings['placeholder_email'] ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
		<div class="input-group mb-3">
		  <label for="<?php echo $firstname_key ?>" hidden><?php echo $strings['placeholder_firstname'] ?></label>
          <input type="text" id="<?php echo $firstname_key ?>" name="<?php echo $firstname_key ?>" title="<?php echo $firstname_key ?>" class="form-control" placeholder="<?php echo $strings['placeholder_firstname'] ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
		<div class="input-group mb-3">
		  <label for="<?php echo $surname_key ?>" hidden><?php echo $strings['placeholder_surname'] ?></label>
          <input type="text" id="<?php echo $surname_key ?>" name="<?php echo $surname_key ?>" title="<?php echo $surname_key ?>" class="form-control" placeholder="<?php echo $strings['placeholder_surname'] ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
		  <label for="<?php echo $password_key ?>" hidden><?php echo $strings['placeholder_password'] ?></label>
          <input type="password" id="<?php echo $password_key ?>" name="<?php echo $password_key ?>" title="<?php echo $password_key ?>" class="form-control" placeholder="<?php echo $strings['placeholder_password'] ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
		  <label for="<?php echo $confirm_password_key ?>" hidden><?php echo $strings['placeholder_confirm_password'] ?></label>
          <input type="password" id="<?php echo $confirm_password_key ?>" name="<?php echo $confirm_password_key ?>" title="<?php echo $confirm_password_key ?>" class="form-control" placeholder="<?php echo $strings['placeholder_confirm_password'] ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
		<?php 
			if (!empty($error)) {
				echo '<p class="text-danger">'.$error.'</p>';
			}
			else if (isset($status) && $status == ExitCode::Success) {
				echo '<p class="text-success">'.$strings['success'].'</p>';
			}
		?>
        <div class="row">
          <div class="col-8">
			<a href="<?php echo $hrefs['sign_in'] ?>" class="text-center"><?php echo $strings['sign_in_href'] ?></a>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block"><?php echo $strings['sign_up'] ?></button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
