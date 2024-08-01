<?php

// Connect config helper
require_once '../../init.php';
require_once $cfg->GetAbsolutePath('profilehelp');

$cfg->ValidateSession();
InitSession();

$ids = array (
  'main' => 'divMainContainer',
);

$strings = array(
	'alt_pfp' => 'Immagine di Profilo',
    'header' => ' Social ',
    'followed' => 'Seguiti',
    'followers' => 'Followers',
    'followerentry' => 'Segui già',
    'followedentry' => 'Non segui',
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
    $followers = $cfg->db->getFollowersList($userid);
    $followed = $cfg->db->getFollowedList($userid);
    $registry = GetRegistry($userid);
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
				      <div class="card card-widget" id="profile">
                <div class="card-header">
                  <div class="user-block">
                    <img class="img-circle" src="<?php echo $pic ?>" alt="<?php echo $strs_post['alt_pfp'] ?>">
                    <span class="username"> <?php echo $username ?> </span>
                  </div>
                </div>
                <div class="card-group">
                <div class="card">
                    <div class="card-header" id="followers">
                      <div class="row">
                      <div class="col-sm">
                        <p class="card-title"><strong class="fa-solid fa-users"></strong> <?php echo $strings['followers'] ?>  : <?php echo FollowersCount($userid) ?></p>
                      </div>
                      </div>
                    </div>
                    <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php
                          foreach($followers as $follower){
                            $follower_pic = $follower['follower_picture'];
                            if (!isset($follower_pic) || !is_file(ROOT.$follower_pic)) {
                              //Default user pic
                              $follower_pic = $cfg->GetURL('defaultpfp');
                            } else {
                              $follower_pic = SERVER_URL.$follower_pic;
                            }
                            echo '
                            <li class="list-group-item">
                            <div class="row">
                              <div class="user-block">
                                <img class="img-circle" src="'.$follower_pic.'" alt="'.$strings['alt_pfp'].'">
                                <span class="username">
                                  <a href="'.$cfg->GetURL('profile').'?user='.$follower['follower_id'].'">'.$follower['follower_username'].'</a>
                                </span>
                              </div>
                            </div>
                            </li>';
                          }
                        ?>
                      </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="followed">
                      <div class="row">
                      <div class="col-sm">
                        <p class="card-title"><strong class="fa-solid fa-users"></strong> <?php echo $strings['followed'] ?>  : <?php echo FollowedCount($userid) ?></p>
                      </div>
                      </div>
                    </div>
                    <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php
                          foreach($followed as $f){
                            $f_pic = $f['followed_picture'];
                            if (!isset($f_pic) || !is_file(ROOT.$f_pic)) {
                              //Default user pic
                              $f_pic = $cfg->GetURL('defaultpfp');
                            } else {
                              $f_pic = SERVER_URL.$f_pic;
                            }
                            echo '
                            <li class="list-group-item">
                            <div class="row">
                              <div class="user-block">
                                <img class="img-circle" src="'.$f_pic.'" alt="'.$strings['alt_pfp'].'">
                                <span class="username">
                                  <a href="'.$cfg->GetURL('profile').'?user='.$f['followed_id'].'">'.$f['followed_username'].'</a>
                                </span>
                              </div>
                            </div>
                            </li>';
                          }
                        ?>
                      </ul>
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