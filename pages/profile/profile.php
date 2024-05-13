<?php

// Connect config helper
require_once '../../init.php';
require_once $cfg->GetAbsolutePath('profilehelp');
// Initialize the editor
require_once $cfg->GetAbsolutePath('posteditorhelp');

$cfg->ValidateSession();
InitSession();

$ids = array (
  'main' => 'divMainContainer',
);

$strings = array(
	'alt_pfp' => 'Immagine di Profilo',
	'header' => ' Profilo ',
  'unfollow' => ' Smetti di seguire ',
  'follow' => ' Segui ',
	'new_post_create' => 'Crea nuovo post',
  'modify_profile' => 'Modifica il profilo',
  'remove_posts' => 'Elimina post',
  'followed' => 'seguiti',
  'followers' => 'followers',
);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
	<?php $cfg->PrintHeadTitle($_SESSION['context']) ?> 
	<?php $cfg->IncludeStylesheets() ?>
  <?php
    $userid = $_GET['user'];
    $username = GetUsername($userid);
    $registry = GetRegistry($userid);
    $name = $registry['firstname'];
    $surname = $registry['surname'];
    $biography = $registry['biography'];
    $pic = $registry['profilePicture'];
    if (!isset($pic) || !is_file(ROOT.$pic)) {
      //Default user pic
      $pic = $cfg->GetURL('defaultpfp');
    } else {
      $pic = SERVER_URL.$pic;
    }

  ?>
  </head>
  <body class="hold-transition layout-top-nav">
    <div class="wrapper">
      <?php include $cfg->GetAbsolutePath('navbar') ?>
      <div class="content-wrapper">
        <div class="content-header">
          <div class="container">
            <div class="row mb-2">
              <div class="col-7 col-md-9">
                <h1 class="m-0"><?php echo $strings['header'] ?></h1>
              </div>
            </div>
          </div>
        </div>
        <!-- Main content -->
        <div class="content">
          <div class="container">
          <div class="row">
              <div class="col-lg-12" id="<?php echo $ids['main'] ?>">
				      <!-- Add new post card -->
				        <div class="card card-widget" id="profile">
				        <!-- Current user name and image -->
                  <div class="card-body">
                    <div class="row mb-3">
                      <div class="col-sm-3">
                        <h4> <?php echo $username ?> </h4>
                        <img class="rounded img-thumbnail " style="width:200px; height:200px;" src="<?php echo $pic ?>" alt="<?php echo $strs_post['alt_pfp'] ?>">
                      </div>
                      <div class="col-sm-1 align-self-center"></div>
                      <div class="col-sm-4 align-self-center">
                        <h4><a href="list.php?user=<?php echo $userid?>#followers"><i class="fa-solid fa-users"></i> <?php echo $strings['followers'] ?></a> : <?php echo FollowersCount($userid) ?></h4>
                      </div>
                      <div class="col-sm-4  align-self-center">
                        <h4><a href="list.php?user=<?php echo $userid?>#followed"><i class="fa-solid fa-users"></i> <?php echo $strings['followed'] ?></a> : <?php echo FollowedCount($userid) ?></h4>
                      </div>
					          </div>
                    <?php
                      if($_SESSION['userid'] == $userid || IsFollower($userid)){
                        echo '
                        <div class="row mb-3">
                          <h6>'.$name." ".$surname.'</h6>
                        </div>
                        <div class="row">
                          <p>'.$biography.'</p>
                        </div>';
                      }
                    ?>
						        <!-- buttons -->
						        <div class="row">
                      <?php
                        if ($_SESSION['userid'] == $userid) {
                          echo '
                          <div class="col-4 col-lg-4">
                            <a href="'.$cfg->GetURL('modifyprofile').'" class="btn btn-success btn-block">
                            <i class="fa-solid fa-pencil"></i> '.$strings['modify_profile'].' </a>
                          </div>
                          <div class="col-4 col-lg-4">
                          </div>
                          <div class="col-4 col-lg-4">
                            <a href="'.$cfg->GetURL('dashboard').'#newPost" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-square-plus"></i> '.$strings['new_post_create'].' </a>
                          </div>';
                        } elseif(IsFollower($userid)) {
                          echo '
                          <div class="col-4 col-lg-4">
                            <form id="unfollowForm" action="unfollow.php" method="post">
                              <input type="hidden" name="myid" value="'.$_SESSION['userid'].'">
                              <input type="hidden" name="userid" value="'.$userid.'">
                              <button type="submit" class="btn btn-secondary btn-block form-check-label" id="unfollowBtn">
                                <i class="fa-solid fa-user"></i> '.$strings['unfollow'].'
                              </button>
                            </form>
                          </div>';
                        } else {
                          echo '
                          <div class="col-4 col-lg-4">
                            <form id="unfollowForm" action="follow.php" method="post">
                              <input type="hidden" name="myid" value="'.$_SESSION['userid'].'">
                              <input type="hidden" name="userid" value="'.$userid.'">
                              <button type="submit" class="btn btn-primary btn-block form-check-label" id="followBtn">
                                <i class="fa-solid fa-user"></i> '.$strings['follow'].'
                              </button>
                            </form>
                          </div>';
                        }
                      ?>
						        </div>
                  </div>
              </div>
              </div>
            </div>
            <?php RenderUserPosts($userid) ?>
            <script src="<?php echo $cfg->GetURL('postscript') ?>" type="text/javascript"></script>
            <script src="<?php echo $cfg->GetURL('postcommentscript') ?>" type="text/javascript"></script>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>