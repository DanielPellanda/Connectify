<?php

// Initialize required components
require_once '../../init.php';
require_once $cfg->GetAbsolutePath('loginhelp');

// String localizer
$strings = array(
	'login_box_msg'=>'Inserisci le tue credenziali per accedere',
	'remember_me'=>'Rimani Collegato',
	'sign_in'=>'Accedi',
	'forgot_password'=>'Password dimenticata?',
	'sign_up_invite'=>'Non sei ancora registrato?',
	'sign_up_href'=>'Registrati',
	'placeholder_username'=>'Username',
	'placeholder_password'=>'Password',
	'missing_username'=>'Inserire un indirizzo mail o username.',
	'missing_password'=>'Inserire una password',
	'error_username'=>"Non esiste alcun account con quell'username.",
	'error_password'=>'La password inserita non è corretta, riprova.',
	'error_activation'=> 'Non è possibile accedere ad un account già connesso.',
	'error_generic'=>'Oops! Qualcosa è andato storto. Per favore riprova più tardi.',
);

// Link paths
$hrefs = array(
	'login_logo'=>'',
	'forgot_password'=>'',
	'sign_up'=>$cfg->GetURL('register'),
);

// Form POST keys
$username_key = 'username';
$password_key = 'password';
$remember_me_key = 'remember_me';

$cfg->auth->ResetCookies();

unset($error);
// Processing form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Validate request
	if (($status = ValidateAuthRequest($username_key, $password_key)) == ExitCode::Success) {
		
		// Credentials validated.
		// Start a new session and bind the username to the session.
		$cfg->auth->CreateCookies($userid, $username, $hashed_password, isset($_POST[$remember_me_key]));
		
		$cfg->Redirect($cfg->GetURL('dashboard'));
		die('Authentication completed. User should be redirected...');
	}
	
	// Assigns the error message to show under the form.
	$error = match ($status) {
		ExitCode::Success => null,

		// Don't output an error message when submitting an empty username,
		// otherwise it will show up everytime the page is reloaded.
		//ExitCode::ErrEmptyUsername => $strings['missing_username'],
		ExitCode::ErrEmptyUsername => null,

		ExitCode::ErrEmptyPassword => $strings['missing_password'],
		ExitCode::ErrWrongUsername => $strings['error_username'],
		ExitCode::ErrWrongPassword => $strings['error_password'],

		default => $strings['error_generic'],
	};
	
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php $cfg->PrintHeadTitle('Login') ?>
  <?php $cfg->IncludeStylesheets() ?>
</head>
<body class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="<?php echo $hrefs['login_logo'] ?>"><b><?php echo $cfg->GetFormattedTitle() ?></b></a>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg"><?php echo $strings['login_box_msg'] ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
          <div class="input-group mb-3">
            <input type="text" name="<?php echo $username_key ?>" class="form-control" placeholder="<?php echo $strings['placeholder_username'] ?>">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="<?php echo $password_key ?>" class="form-control" placeholder="<?php echo $strings['placeholder_password'] ?>">
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
		      ?>
          <div class="row mb-3">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" name="<?php echo $remember_me_key ?>" id="remember">
                <label for="remember">
                  <?php echo $strings['remember_me'] ?>
                </label>
              </div>
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block"><?php echo $strings['sign_in'] ?></button>
            </div>
          </div>
        </form>
        <p class="mb-0">
          <?php echo $strings['sign_up_invite'] ?> <a href="<?php echo $hrefs['sign_up'] ?>" class="text-center"><?php echo $strings['sign_up_href'] ?></a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
