<?php

require_once $cfg->GetAbsolutePath('posthelp');

$alt_img = 'Immagine di profilo';

$commentid = 'comment'.$c->id;

$fullname = '';
if (!empty($c->user->GetFirstname())) {
	$fullname = $c->user->GetFirstname();
}
if (!empty($c->user->GetSurname())) {
	$fullname = $fullname.' '.$c->user->GetSurname();
}

?>
<div class="card-comment" id="<?php echo $commentid ?>">
  <!-- User image -->
  <img class="img-circle img-sm" src="<?php echo $c->user->GetProfilePicture() ?>" alt="<?php echo $alt_img ?>"> 
  <div class="comment-text">
	<?php if (!empty($fullname)): ?>
    <span class="username"> 
		  <?php echo $fullname ?> 
      <span class="text-muted float-right">
        <?php if ($_SESSION['userid'] == $c->user->id): ?>
          <button class="btn btn-tool" onclick="DeleteCommentButtonOnClick('<?php echo $c->id?>', '<?php echo $c->postid ?>')"><em class="fa-solid fa-xmark"></em></button>
        <?php endif; ?>
        <?php echo $c->date ?>
      </span>
    </span>
	<?php endif; ?>
	<?php echo $c->content ?>
  </div>
</div>
