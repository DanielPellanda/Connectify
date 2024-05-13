<?php

require_once ROOT.'/settings/settings.php';

$styles = array(
	'style'    	  => SERVER_URL.'/plugins/adminlte/adminlte.css',
	'icons'    	  => SERVER_URL.'/plugins/fontawesome-free/css/all.min.css',
	'google_font' => 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
);

?>
<link rel="stylesheet" href='<?php echo $styles['style'] ?>'>
<link rel="stylesheet" href='<?php echo $styles['icons'] ?>'>
<link rel="stylesheet" href='<?php echo $styles['google_font'] ?>'>