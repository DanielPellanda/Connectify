<?php

// Connect config helper
require_once '../../../init.php';
require_once $cfg->GetAbsolutePath('modifyhelp');

$cfg->ValidateSession();

// Forms submit buttons
$username_key = 'username';
$firstname_key = 'name';
$surname_key = 'surname';
$password_key = 'password';
$biography_key = 'bio';
$profile_picture_key = 'pic';
$email_key = 'email';

//Forms field ids
$ids = array (
	'username' => 'chgUsername',
  'email' => 'chgEmail',
	'firstname' => 'chgFname',
	'surname' => 'chgSname',
	'password' => 'chgPw',
  'confirm_password' => 'confirmPw',
	'biography' => 'chgBio',
	'cpic' => 'chgImg',
	'upic' => 'upImg',
);

$strings = array(
	'alt_pfp' => 'Immagine di Profilo',
  'header' => ' Modifica ',
  'username' => 'Nome utente',
  'email' => 'E-mail',
  'password' => 'Cambia password',
  'confirm_password' => 'Conferma nuova password',
  'firstname' => 'Nome',
  'surname' => 'Cognome',
  'bio' => 'Biografia',
  'pic' => 'Foto profilo',
  'login' => 'Modifica dati di accesso',
  'extensioninfo'=> ' Accetta immagini in formato <br> .jpg, .jpeg, .png, .gif e .svg',
  'upload_img'=>'Carica Immagine',
  'attach_img'=>'Carica Immagine',
  'personal' => 'Modifica informazioni personali',

  'missing_username'=>'Inserire un username.',
  'missing_firstname'=>'Inserire un nome.',
  'missing_surname'=>'Inserire un cognome.',
  'missing_password'=>'Inserire una password.',
  'missing_email'=>'Inserire una email.',
  'error_username'=>"È già presente un account con quell'username.",
  'error_username_long'=>"L'username inserito non può essere lungo più di ".$username_max_length." caratteri.",
  'error_firstname_long'=>'IL nome non può essere lungo più di '.$username_max_length.' caratteri.',
  'error_surname_long'=>'Il cognome non può essere lungo più di '.$username_max_length.' caratteri.',
  'error_password_long'=>'La password non può essere lunga più di '.$password_max_length.' caratteri.',
  'error_password_short'=>'La password  non può essere lunga meno di '.$password_min_length.' caratteri.',
  'error_biography_long'=>'La biografia non può essere lunga più di '.$bio_max_length.' caratteri.',
  'error_password_case'=>'La password deve contenere almeno un carattere maiuscolo (A) e minuscolo (a).',
  'error_password_number'=>'La passworddeve contenere almeno un numero.',
  'error_password_schar'=>'La password deve contenere almeno un carattere speciale.',
  'error_confirm_password'=>'La due password inserite non coincidono.',
  'error_email_long'=>'La email inserita non può essere lunga più di '.$email_max_length.' caratteri.',
	'error_email_invalid'=>'La email inserita non è valida',
	'error_email'=>'La email inserita è già in uso',
  'error_generic'=>'Oops! Qualcosa è andato storto. Per favore riprova più tardi.',
  'success'=>'Modifica eseguita con successo',
);

unset($error);
// Processing form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	
  if (isset($_POST[$username_key])) {
    $status = ChangeUsername($ids['username']);
  } else if (isset($_POST[$password_key])) {
    $status = ChangePassword($ids['password'], $ids['confirm_password']);
  } else if (isset($_POST[$firstname_key])) {
    $status = ChangeName($ids['firstname']);
  } else if (isset($_POST[$surname_key])) {
    $status = ChangeSurname($ids['surname']);
  } else if (isset($_POST[$biography_key])) {
    $status = ChangeBio($ids['biography']);
  } else if (isset($_POST[$profile_picture_key])) {
    $status = ChangePic($ids['upic']);
  } else if (isset($_POST[$email_key])) {
    $status = ChangeEmail($ids['email']);
  }
	
	// Assigns the error message to show under the form.
	$error = match ($status) {
		ExitCode::Success => null,
		ExitCode::ErrEmptyUsername => null,
		
		ExitCode::ErrEmptyPassword => $strings['missing_password'],
		ExitCode::ErrEmptyFirstname => $strings['missing_firstname'],
		ExitCode::ErrEmptySurname => $strings['missing_surname'],
		ExitCode::ErrExistsUsername => $strings['error_username'],
		ExitCode::ErrLongUsername => $strings['error_username_long'],
		ExitCode::ErrLongFirstname => $strings['error_firstname_long'],
		ExitCode::ErrLongSurname => $strings['error_surname_long'],
		ExitCode::ErrLongPassword => $strings['error_password_long'],
    ExitCode::ErrLongBio => $strings['error_biography_long'],
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
}

InitSession();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
	<?php $cfg->PrintHeadTitle($_SESSION['context']) ?> 
	<?php $cfg->IncludeStylesheets() ?>
  <?php
    $userid = $_SESSION['userid'];
    $username = GetUsername($userid);
    $email = GetEmail($userid); 
    $registry = GetRegistry($userid);
    $name = $registry['firstname'];
    $surname = $registry['surname'];
    $biography = $registry['biography'];
    $pic = $_SESSION['pfp'];
  ?>
  </head>
  <body class="hold-transition layout-top-nav">
    <div class="wrapper">
      <?php include $cfg->GetAbsolutePath('navbar') ?>
      <div class="content-wrapper">
        <div class="content-header">
          <div class="container">
            <div class="row mb-2">
              <div class="col-6">
                <h1 class="m-0"><?php echo $strings['header'] ?></h1>
              </div>
              <div class="col-6">
                <?php
                  if (!empty($error)) {
                    echo '<div class="alert alert-danger">'.$error.'</div>';
                  } else if (isset($status) && $status == ExitCode::Success) {
                    echo '<div class="alert alert-success text-center">'.$strings['success'].'</div>';
                  }
                ?>
              </div>
            </div>
          </div>
        </div>
        <!-- Main content -->
        <div class="content">
          <div class="container">
            <div class="row">
              <div class="col-lg-12">
				        <div class="card card-widget">
				        <!-- Current user name and image -->
			            <div class="card-header">
					          <div class="user-block">
					            <img class="img-circle" src="<?php echo $_SESSION['pfp'] ?>" alt="<?php echo $strs_post['alt_pfp'] ?>">
					            <span class="username"><?php echo $_SESSION['fullname'] ?></span>
					          </div>
                  </div>
                  <div class="card">
                    <div class="card-header">
                      <div class="row">
                        <p class="card-title"><?php echo $strings['login'] ?></p>
                      </div>
                    </div>
                    <div class="card-body">
                    <div class="row">
					            <div class="col-md-5">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                          <label for="<?php echo $ids['username'] ?>"><?php echo $strings['username'] ?></label>
                          <div class="input-group mb-3">
                            <input type="text" class="form-control" id="<?php echo $ids['username'] ?>" name="<?php echo $ids['username'] ?>" value="<?php echo $username ?>">
                            <div class="input-group-append">
                              <button type="submit" class="btn btn-primary btn-block" name="<?php echo $username_key ?>"><em class="fa-solid fa-pencil"></em></button>
                            </div>
                          </div>
                        </form>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                          <label for="<?php echo $ids['email'] ?>"><?php echo $strings['email'] ?></label>
                          <div class="input-group mb-3">
                            <input type="text" class="form-control" id="<?php echo $ids['email'] ?>" name="<?php echo $ids['email'] ?>" value="<?php echo $email ?>">
                            <div class="input-group-append">
                              <button type="submit" class="btn btn-primary btn-block" name="<?php echo $email_key ?>"><em class="fa-solid fa-pencil"></em></button>
                            </div>
                          </div>
                        </form>
                      </div>
                      <div class="col-sm-2"></div>
					            <div class="col-md-5">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                          <label for="<?php echo $ids['password'] ?>"><?php echo $strings['password'] ?></label>
                          <input type="password" class="form-control" id="<?php echo $ids['password'] ?>" name="<?php echo $ids['password'] ?>">
                          <label for="<?php echo $ids['confirm_password'] ?>"><?php echo $strings['confirm_password'] ?></label>
                            <div class="input-group mb-3">
                              <input type="password" class="form-control" id="<?php echo $ids['confirm_password'] ?>" name="<?php echo $ids['confirm_password'] ?>">
                                <div class="input-group-append">
                                  <button type="submit" class="btn btn-primary btn-block" name="<?php echo $password_key ?>"><em class="fa-solid fa-pencil"></em></button>
                                </div>
                            </div>
                        </form>
                      </div>
                    </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="read">
                      <div class="row">
                        <p class="card-title"><?php echo $strings['personal'] ?></p>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-lg-3 p-3">
                          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                          <label><?php echo $strings['pic'] ?></label>
                            <div class="input-group">
                              <img class="rounded img-thumbnail " style="width:200px; height:200px;" src="<?php echo $pic ?>" alt="<?php echo $strs_post['alt_pfp'] ?>">
                              <input type="file" accept="image/*" name="<?php echo $ids['upic'] ?>" id="<?php echo $ids['upic'] ?>" hidden>
                              <button type="submit" name="<?php echo $profile_picture_key ?>" id="<?php echo $ids['cpic'] ?>" hidden></button>
                            </div>
                          </form>
                        </div>
                        <div class="col-lg-3 p-3">
                          <label><?php echo $strings['upload_img'] ?></label>
                          <p><?php echo $strings['extensioninfo'] ?></p>
                          <label for="<?php echo $ids['upic'] ?>" class="btn btn-primary" ><em class="fa-solid fa-upload"></em></label>
                          <label for="<?php echo $ids['cpic'] ?>" class="btn btn-primary" ><em class="fa-solid fa-pencil"></em></label>
                        </div>
                        <div class="col-lg p-3">
                          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <label for="<?php echo $ids['firstname'] ?>"><?php echo $strings['firstname'] ?></label>
                              <div class="input-group mb-3">
                                <input type="text" class="form-control" id="<?php echo $ids['firstname'] ?>" name="<?php echo $ids['firstname'] ?>" value="<?php echo $name ?>">
                                <div class="input-group-append">
                                  <button type="submit" class="btn btn-primary btn-block" name="<?php echo $firstname_key ?>"><em class="fa-solid fa-pencil"></em></button>
                                </div>
                              </div>
                          </form>
                          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <label for="<?php echo $ids['surname'] ?>"><?php echo $strings['surname'] ?></label>
                            <div class="input-group mb-3">
                              <input type="text" class="form-control" id="<?php echo $ids['surname'] ?>" name="<?php echo $ids['surname'] ?>" value="<?php echo $surname ?>">
                              <div class="input-group-append">
                                <button type="submit" class="btn btn-primary btn-block" name="<?php echo $surname_key ?>"><em class="fa-solid fa-pencil"></em></button>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                      <div class="row">                      
                        <div class="col-sm-12">
                          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <label for="<?php echo $ids['biography'] ?>"><?php echo $strings['bio'] ?></label>
                            <div class="input-group mb-3">
                            <textarea class="form-control" rows="7" name="<?php echo $ids['biography'] ?>" id="<?php echo $ids['biography'] ?>"><?php echo $biography?></textarea>
                              <div class="input-group-append align-self-end">
                                <button type="submit" class="btn btn-primary btn-block" name="<?php echo $biography_key ?>"><em class="fa-solid fa-pencil"></em></button>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>