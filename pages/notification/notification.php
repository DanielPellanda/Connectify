<?php

// Connect config helper
require_once '../../init.php';
require_once $cfg->GetAbsolutePath('notificationhelp');

$cfg->ValidateSession();
InitSession();

$ids = array (
  'main' => 'divMainContainer',
);

$strings = array(
	  'alt_pfp' => 'Immagine di Profilo',
    'header' => ' Notifiche ',
    'toberead' => 'Da leggere',
    'inboxempty' => 'Non ci sono notifiche....',
    'follow' => 'ha iniziato a seguirti.',
    'like' => 'ha messo like al tuo post : ',
	  'comment1' => 'ha commentato "',
    'comment2' => '" il tuo post : "',
    'post' => 'ha pubblicato un nuovo post : "',
    'check' => 'Segna come letta',
    'read' => 'Lette',
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
        $toBeRead = $cfg->db->getNotificationList($_SESSION['userid']);
        $read = $cfg->db->getReadNotificationList($_SESSION['userid']);
        $num_notifications = NotificationCount($_SESSION['userid']);
        $num_read = ReadNotificationCount($_SESSION['userid']);
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
                    <img class="img-circle" src="<?php echo $_SESSION['pfp'] ?>" alt="<?php echo $strs_post['alt_pfp'] ?>">
                    <span class="username">
                      <h4> <?php echo $_SESSION['username'] ?> </h4>
                    </span>
                  </div>
                </div>
                <div class="card">
                    <div class="card-header" id="tobeRead">
                      <div class="row">
                      <div class="col-sm">
                        <h5 class="card-title"><i class="fa-solid fa-envelope"></i> <?php echo $strings['toberead'] ?> : <?php echo $num_notifications ?></h5>
                      </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <?php
                        if($num_notifications == 0){
                          echo $strings['inboxempty'];
                        } else {
                          GenerateList($toBeRead);
                        }
                      ?>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="read">
                      <div class="row">
                      <div class="col-sm">
                        <h5 class="card-title"><i class="fa-solid fa-envelope-circle-check"></i> <?php echo $strings['read'] ?>  : <?php echo $num_read ?></h5>
                      </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <?php                        
                        if($num_read == 0){
                          echo $strings['inboxempty'];
                        } else {
                          GenerateList($read);
                        }?>
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
  </body>
</html>