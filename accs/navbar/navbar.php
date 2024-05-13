<?php 

// Connect config helper if it wasn't already
require_once ROOT.'/init.php';

// logo
$icon_img = SERVER_URL.'/img/logo.png';

$strs_nbar = array(
	'alt_logo' => $cfg->GetTitle().' Logo',
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
			<b>'.$str.'</b>
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
	   <img src="<?php echo $icon_img ?>" alt="<?php echo $strs_nbar['alt_logo'] ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
       <span class="brand-text font-weight-light"><?php echo $cfg->GetFormattedTitle() ?></span>
     </a>
     <div class="navbar-collapse order-3">
	     <!-- Left navbar links -->
       <ul class="navbar-nav">
         <?php PrintMenuEntries() ?>
         <li class="nav-item dropdown">
          <a class="nav-link" href="<?php echo $cfg->getURL('notification')?>">
            <i class="far fa-bell"></i>
            <?php
              if($num_notifications > 0){
                echo '<span class="badge badge-danger navbar-badge">'.$num_notifications.'</span>';
              }
            ?>
          </a>
        </li>
	     </ul>
       <!-- SEARCH FORM --
       <form class="form-inline ml-0 ml-md-3">
         <div class="input-group input-group-sm">
           <input class="form-control form-control-navbar" type="search" placeholder="<?php echo $strs_nbar['placeholder_search'] ?>" aria-label="<?php echo $strs_nbar['placeholder_search'] ?>">
           <div class="input-group-append">
             <button class="btn btn-navbar" type="submit">
               <i class="fas fa-search"></i>
             </button>
           </div>
         </div>
       </form>
      -->
	 </div>
     <!-- Right navbar links -->
     <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
     </ul>
   </div>
</nav>