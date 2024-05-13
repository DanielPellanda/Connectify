<?php 

require_once $cfg->GetAbsolutePath('posteditorhelp');

?>
<div class="card card-widget">
  <!-- Current user name and image -->
  <div class="card-header">
	<div class="user-block">
	  <img class="img-circle" src="<?php echo $_SESSION['pfp'] ?>" alt="<?php echo $poststrings['alt_pfp'] ?>">
	  <span class="username">
	  	<a href="<?php echo $cfg->GetURL('profile').'?user='.$_SESSION['userid'] ?>"><?php echo $_SESSION['fullname'] ?></a>
	  </span>
	</div>
  </div>
  <div class="card-body">
	<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">
		<?php if (!empty($_REQUEST['post'])): ?>			
			<input type="hidden" name="post" value="<?php echo $_REQUEST['post'] ?>" />
		<?php endif; ?>
		<?php if (!empty($_REQUEST['redirect'])): ?>			
			<input type="hidden" name="redirect" value="<?php echo $_REQUEST['redirect'] ?>" />
		<?php endif; ?>
		<!-- Title -->
		<div class="row">
		  <div class="col-sm-12">
		  	<div class="form-group">
		  	  <label for="<?php echo $postids['post_title'] ?>"><?php echo $poststrings['post_title'] ?></label>
		  	  <input type="text" class="form-control" placeholder="<?php echo $poststrings['post_placeholder'] ?>" name="<?php echo $postids['post_title'] ?>" id="<?php echo $postids['post_title'] ?>" value="<?php PrintPostStringOrDefault('title') ?>">
		  	</div>
		  </div>
		</div>
		<!-- Description -->
		<div class="row">
		  <div class="col-sm-12">
		  	<div class="form-group">
		  	  <label for="<?php echo $postids['post_text'] ?>"><?php echo $poststrings['post_content'] ?></label>
		  	  <textarea class="form-control" rows="10" placeholder="<?php echo $poststrings['post_placeholder'] ?>" name="<?php echo $postids['post_text'] ?>" id="<?php echo $postids['post_text'] ?>"><?php PrintPostStringOrDefault('content') ?></textarea>
		  	</div>
		  </div>
		</div>
		<!-- Image preview -->
		<div class="row" id="<?php echo $postids['post_boxpreview'] ?>" <?php PrintIfOrDefault('hidden', !$is_imgpresent) ?>>
			<div class="col-sm-12">
				<label><?php echo $poststrings['post_preview'] ?></label>
				<div class="attachment-block clearfix">
					<img class="img-fluid" style="max-height: 200px; max-width: 600px;" src="<?php PrintPostStringOrDefault('img', '#') ?>" alt="<?php echo $poststrings['alt_preview'] ?>" id="<?php echo $postids['post_imgpreview'] ?>">
				</div>
			</div>
		</div>
		<!-- End buttons -->
		<div class="row">
		  <!-- Attach image -->
		  <div class="col-4 col-lg-2" id="<?php echo $postids['post_boxadd'] ?>" <?php PrintIfOrDefault('hidden', $is_imgpresent) ?>>
		  	<input type="file" accept="image/*" name="<?php echo $postids['post_img'] ?>" id="<?php echo $postids['post_img'] ?>" onchange="CheckAttachment(<?php echo '\''.$postids['post_img'].'\', \''.$postids['post_boxpreview'].'\', \''. $postids['post_boxadd'].'\', \''. $postids['post_boxremove'].'\', \''.$postids['post_imgpreview'].'\''; ?>)" hidden />
		  	<label for="<?php echo $postids['post_img'] ?>" class="btn btn-warning btn-block form-check-label"><i class="fa-solid fa-paperclip"></i>  <?php echo $poststrings['post_attach'] ?></label>
		  </div>
		  <!-- Remove attachment -->
		  <div class="col-4 col-lg-2" id="<?php echo $postids['post_boxremove'] ?>" <?php PrintIfOrDefault('hidden', !$is_imgpresent) ?>>
		  	<button type="button" class="btn btn-danger btn-block form-check-label" id="<?php echo $postids['post_remove'] ?>" onclick="RemoveAttachedImage(<?php echo '\''.$postids['post_img'].'\', \''.$postids['post_boxpreview'].'\', \''. $postids['post_boxadd'].'\', \''. $postids['post_boxremove'].'\', \''.$postids['post_imgpreview'].'\''; ?>)"><i class="fa-solid fa-trash"></i>  <?php echo $poststrings['post_rmattach'] ?></button>
		  </div>
		  <!-- Should the post be public? -->
		  <div class="col-4 col-lg-8 float-right text-right">
		  	<div class="icheck-primary">
		  		<input type="checkbox" name="<?php echo $postids['post_visibility'] ?>" id="<?php echo $postids['post_visibility'] ?>" <?php PrintIfOrDefault('checked="checked"', !empty($post) && isset($post->public) && $post->public) ?>>
		  		<label for="<?php echo $postids['post_visibility'] ?>"><?php echo $poststrings['post_visibility'] ?></label>
		  	</div>
		  </div>
		  <!-- Create post -->
		  <div class="col-4 col-lg-2">			
		  	<button type="submit" class="btn btn-primary btn-block"><?php echo $submit_icon ?>  <?php echo $submit_string ?></button>
		  </div>
		</div>
	</form>
  </div>
</div>
<script src="<?php echo $cfg->GetURL('posteditorscript') ?>" type="text/javascript"></script>