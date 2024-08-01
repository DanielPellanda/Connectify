<?php 

// Connect config helper
require_once '../../../init.php';
require_once $cfg->GetAbsolutePath('user');

$cfg->ValidateSession();

if ((empty($_POST['post']) || empty($_POST['redirect'])) && (empty($_REQUEST['post']) || empty($_REQUEST['redirect'])))
{
    $cfg->Redirect($cfg->GetURL('dashboard'));
}

$_SESSION['context'] = 'Modifica Post';
if (!empty($_SESSION['userid'])) 
{
	$user = new User($_SESSION['userid']);
	$_SESSION['fullname'] = $user->GetFirstname().' '.$user->GetSurname();
	$_SESSION['pfp'] = $user->GetProfilePicture();
}

if (!isset($_SESSION['pfp']) || !is_file($_SESSION['pfp'])) {
	//Default user pic
	$_SESSION['pfp'] = $cfg->GetURL('defaultpfp');
}

$alt_link = 'Indietro';

// Initialize the editor
require_once $cfg->GetAbsolutePath('posteditorhelp');

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <?php $cfg->PrintHeadTitle($_SESSION['context']) ?> 
	<?php $cfg->IncludeStylesheets() ?>
  </head>
  <body class="hold-transition layout-top-nav">
    <div class="wrapper">
      <?php include $cfg->GetAbsolutePath('navbar') ?>
      <div class="content-wrapper">
        <div class="content-header">
          <div class="container">
            <div class="row mb-2">
              <div class="col-1">
                <a href="<?php echo $cfg->GetURL('dashboard') ?>">
                  <button class="btn btn-dark btn-block"><em class="fa-solid fa-arrow-left"></em><p hidden><?php echo $alt_link ?></p></button>
                </a>
              </div>  
              <div class="col-11">
                <h1 class="m-0"><?php echo ' '.$_SESSION['context'].' ' ?></h1>
              </div>
            </div>
          </div>
        </div>
        <!-- Main content -->
        <div class="content">
          <div class="container">
            <div class="row">
              <div class="col-lg-12">
				<!-- Add new post card -->
                <?php require $cfg->GetAbsolutePath('posteditor') ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>