<?php 

// Connect config helper if it wasn't already
require_once ROOT.'/init.php';

// logo
$icon_img = SERVER_URL.'/img/logo.png';

$strs_nbar = array(
	'alt_logo' => $cfg->GetTitle().' Logo',
	'alt_notify' => 'Notifiche',
	'placeholder_search' => 'Cerca'
);

$menu_entries = array (
	array ('Home', $cfg->GetURL('dashboard')),
	array ('Profilo', $cfg->GetURL('profile').'?user='.$_SESSION['userid']),
	array ('Logout', $cfg->GetURL('login')),
);

function PrintMenuEntries() {
	global $menu_entries;

	foreach ($menu_entries as $entry) {
		$str = $entry[0];
		if (isset($_SESSION['context']) && !strcmp($_SESSION['context'], $str)) {
			$str = '
			<strong>'.$str.'</strong>
			';
		}
		
		echo '
		<li class="nav-item">
          <a href="'.$entry[1].'" class="nav-link">'.$str.'</a>
        </li>
		';
	}
}

$num_notifications = $cfg->db->getNotificationCount($_SESSION['userid'])[0]['num_notifications']

?>

<nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
   <div class="container">
     <a href="<?php echo $cfg->GetURL('dashboard') ?>" class="navbar-brand">
       <span class="brand-text font-weight-light"><?php echo $cfg->GetFormattedTitle() ?></span>
     </a>
     <div class="navbar-collapse order-3">
	     <!-- Left navbar links -->
       <ul class="navbar-nav">
         <?php PrintMenuEntries() ?>
         <li class="nav-item dropdown">
          <a class="nav-link" href="<?php echo $cfg->getURL('notification')?>">
            <em class="far fa-bell"></em>
			<p hidden><?php echo $strs_nbar['alt_notify'] ?></p>
            <?php
              if($num_notifications > 0){
                echo '<span class="badge badge-danger navbar-badge">'.$num_notifications.'</span>';
              }
            ?>
          </a>
        </li>
	   </ul>
	 </div>
     <!-- Right navbar links -->
     <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
     </ul>
   </div>
</nav>