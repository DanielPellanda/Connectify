<?php

require_once $cfg->GetAbsolutePath('posthelp');

$ids = array(
	'post' => 'post'.$p->id,
	'like' => 'btnLike'.$p->id,
	'count' => 'txtCount'.$p->id,
	'comment' => 'txtComment'.$p->id,
	'commentlist' => 'listComment'.$p->id,
);

$strs_post = array (
	'alt_pfp' => 'Immagine di Profilo',
	'alt_img' => 'Immagine allegata',
	'placeholder_comment' => 'Premi Invio per inserire un commento ...',
	'private' => 'Solo Amici',
	'public' => 'Pubblico',
	'like' => 'Mi Piace',
	'likes' => 'Mi Piace',
	'comment' => 'Commento',
	'comments' => 'Commenti',
	'none' => 'Nessun',
);

// "Vittorio Ghini"
$fullname = 'undefined';
if (!empty($p->user->GetFirstname())) {
	$fullname = $p->user->GetFirstname();
}
if (!empty($p->user->GetSurname())) {
	$fullname = $fullname.' '.$p->user->GetSurname();
}

// "Friends Only - 02/07/2024"
$visiblity = $strs_post['private'];
if ($p->public) {
	$visiblity = $strs_post['public'];
}
$description = $visiblity;
if (!empty($p->date)) {
	$description = $description.' - '.$p->date;
}

$social = '';
// "X Likes - X Comments"
if (isset($p->numlikes) && is_numeric($p->numlikes)) {
	// "1 Like"
	if ($p->numlikes == 1){
		$social = $p->numlikes.' '.$strs_post['like']; 
	}
	// "X Likes"
	else {
		$social = $p->numlikes.' '.$strs_post['likes']; 
	}
}
if (isset($p->numcomments) && is_numeric($p->numcomments)) {
	// "1 Comment"
	if ($p->numcomments == 1){
		$social = $social.' - '.$p->numcomments.' '.$strs_post['comment']; 
	}
	// "X Comments"
	else {
		$social = $social.' - '.$p->numcomments.' '.$strs_post['comments']; 
	}
}

$like_icon = '<i class="fa-regular fa-thumbs-up"></i>';
if (isset($p->liked) && $p->liked) {
	$like_icon = '<i class="fa-solid fa-thumbs-up"></i>';
}

?>
<div class="card card-widget" id="<?php echo $ids['post'] ?>">
  <div class="card-header">
    <div class="user-block">
	  <img class="img-circle" src="<?php echo $p->user->GetProfilePicture() ?>" alt="<?php echo $strs_post['alt_pfp'] ?>">
      <span class="username">
        <a href="<?php echo $cfg->GetURL('profile').'?user='.$p->user->id ?>"><?php echo $fullname ?></a>
      </span>
      <span class="description"><?php echo $description ?></span>
    </div>
	<?php if ($_SESSION['userid'] == $p->user->id): ?>
      <div class="card-tools">
	    <!-- Edit Post -->
        <button type="button" class="btn btn-tool" onclick="EditButtonOnClick('<?php echo $p->id?>', '<?php echo $cfg->GetURL('dashboard') ?>')">
	  	  <i class="fa-regular fa-pen-to-square"></i>
        </button>
	    <!-- Delete Post -->
        <button type="button" class="btn btn-tool" onclick="DeleteButtonOnClick('<?php echo $p->id?>')">
	  	  <i class="fa-regular fa-trash-can"></i>
        </button>
      </div>
	<?php endif; ?>
  </div>
  <div class="card-body">
    <!-- Post text -->
	<p><h4><?php echo $p->title ?></h4></p>
    <!-- Attachment -->
	<?php if (!empty($p->img && is_file(ROOT.$p->img))): ?>
	  <img class="img-fluid" style="max-height: 200px; max-width: 600px;" src="<?php echo SERVER_URL.$p->img ?>" alt="<?php echo $strs_post['alt_img'] ?>">
	<?php endif; ?>
	<p><?php echo $p->content ?></p>
    <!-- Social sharing buttons -->
    <button type="button" class="btn btn-default btn-md" id="<?php echo $ids['like'] ?>" onclick="LikeButtonOnClick(<?php echo $p->id ?>, <?php echo $_SESSION['userid'] ?>, '<?php echo $ids['like'] ?>', '<?php echo $ids['count'] ?>')">
		<?php echo $like_icon.' '.$strs_post['like'] ?> 
	</button>
    <span class="float-right text-muted" id="<?php echo $ids['count'] ?>"><?php echo $social ?></span>
  </div>
  <div class="card-footer card-comments" id="<?php echo $ids['commentlist'] ?>">
    <?php $p->GenerateComments($cfg) ?>
  </div>
  <div class="card-footer">
    <form action="<?php echo $cfg->GetURL('postcommentnew') ?>?user=<?php echo $_SESSION['userid'] ?>&post=<?php echo $p->id ?>" method="post">
	  <img class="img-fluid img-circle img-sm" src="<?php echo $_SESSION['pfp'] ?>" alt="<?php echo $strs_post['alt_pfp'] ?>">
      <div class="img-push">
        <input type="text" class="form-control form-control-sm" id="<?php echo $ids['comment'] ?>" name="<?php echo $ids['comment'] ?>" placeholder="<?php echo $strs_post['placeholder_comment'] ?>">
      </div>
    </form>
  </div>
</div>