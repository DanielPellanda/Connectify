<?php 

require_once $cfg->GetAbsolutePath('posthelp');

$postids = array (
	'post_title' => 'txtTitle',
	'post_text' => 'txtText',
	'post_img' => 'btnAddImg',
	'post_remove' => 'btnRemoveImg',
	'post_visibility' => 'chkVisible',
	'post_imgpreview' => 'imgPreview',
	'post_boxpreview' => 'divPreview',
	'post_boxadd' => 'divAddImage',
	'post_boxremove' => 'divRemoveImage',
);

$poststrings = array(
	'post_title' => 'Titolo',
	'post_placeholder' => 'Scrivi qui ...',
	'post_content' => 'Descrizione',
	'post_preview' => 'Anteprima immagine',
	'post_attach' => 'Allega immagine',
	'post_rmattach' => 'Rimuovi immagine',
	'post_visibility' => 'Rendi pubblico',
    'post_create' => 'Crea nuovo post',
    'post_edit' => 'Modifica post',
    'alt_pfp' => 'Immagine di Profilo',
	'alt_preview' => 'Anteprima immagine',
	'error_generic'=> 'Oops! Qualcosa è andato storto. Per favore riprova più tardi.',
	'missing_title' => 'Per creare un post è necessario un titolo.',
	'missing_text' => 'Inserisci il testo del post.',
);

$posticons = array (
	'create' => '<i class="fa-solid fa-square-plus"></i>',
	'edit' => '<i class="fa-solid fa-pencil"></i>',
);

// Creates new post from form post data and sends it to the db, returns an exit code with the esit
function AddNewPost($cfg, $id_title, $id_text, $id_img, $id_visible) {
    require_once $cfg->GetAbsolutePath('filemgr');

    // User needs to have an active session before posting
    if (empty($_SESSION['userid'])) {
        return PostExitCode::ErrGeneric;
    }
    $userid = (int)$_SESSION['userid'];
    
    // Check if title field is empty
    if (empty($_POST[$id_title])) {
        return PostExitCode::ErrEmptyTitle;
    }
    $title = $_POST[$id_title];

    // Check if text field is empty
    if (empty($_POST[$id_text])) {
        return PostExitCode::ErrEmptyText;
    }
    $text = $_POST[$id_text];
    
    // Get the last post id
    $next_id = 0;
    $res = $cfg->db->getPostMaxId();
    if (!empty($res) && isset($res[0]['MAX(`postid`)']) && is_numeric($res[0]['MAX(`postid`)'])) {
        // Get the next post id
        $next_id = $res[0]['MAX(`postid`)'] + 1;
    }

    $img = '';

    // Check if image file is present and it's an actual image
    if (!empty($_FILES[$id_img]) && !empty($_FILES[$id_img]['name']) && !empty($_FILES[$id_img]['size']) && getimagesize($_FILES[$id_img]['tmp_name'])) {
        // Create base directory
        if (!is_dir(ROOT.Post::$img_storage)) {
            mkdir(ROOT.Post::$img_storage);
        }
        
        $max_pad = 5;
        // Generate the image name
        $img_name = str_pad(strval($next_id), $max_pad, '0', STR_PAD_LEFT);

        // Upload the image
        $path = FileManager::Upload($_FILES[$id_img], Post::$img_storage, $img_name);
        if ($path) {
            $img = $path;
        }
        else {
            $cfg->ShowAlert(Post::$upload_fail_msg);
        }
    }

    $visible = 0;
    if (isset($_POST[$id_visible]) && $_POST[$id_visible]) {
        $visible = 1;
    }

    // Add new post in the db
    if ($cfg->db->newPost($next_id, $title, $text, $text, $img, $userid, $visible)) {
        // Get all the user's followers
        $followerlist = $cfg->db->getFollowersList($userid);
        if (empty($followerlist) || count($followerlist) == 0) {
            return PostExitCode::Success;
        }

        foreach ($followerlist as $follower) {
            if (isset($follower['follower_id'])) {
                $cfg->db->notifyPost($userid, $follower['follower_id'], $next_id);
            }
        }

        return PostExitCode::Success;
    }
    // Cannot add the post
    return PostExitCode::ErrGeneric;
}

// Edits a post using form post data and sends it to the db, returns an exit code with the esit
function EditPost($cfg, $post, $id_title, $id_text, $id_img, $id_visible) {
    require_once $cfg->GetAbsolutePath('filemgr');

    // User needs to have an active session before posting
    if (empty($_SESSION['userid'])) {
        return PostExitCode::ErrGeneric;
    }
    $userid = (int)$_SESSION['userid'];
    
    // Check if title field is empty
    if (empty($_POST[$id_title])) {
        return PostExitCode::ErrEmptyTitle;
    }
    $title = $_POST[$id_title];

    // Check if text field is empty
    if (empty($_POST[$id_text])) {
        return PostExitCode::ErrEmptyText;
    }
    $text = $_POST[$id_text];

    $img = '';
    // Check if image file is present and it's an actual image
    if (!empty($_FILES[$id_img]) && !empty($_FILES[$id_img]['name']) && !empty($_FILES[$id_img]['size']) && getimagesize($_FILES[$id_img]['tmp_name'])) {
        $img = $post->img;
        
        // Create base directory
        if (!is_dir(ROOT.Post::$img_storage)) {
            mkdir(ROOT.Post::$img_storage);
        }

        // Get the last post id
        $next_id = 0;
        $res = $cfg->db->getPostMaxId();
        if (!empty($res) && !empty($res[0]['MAX(`postid`)']) && is_numeric($res[0]['MAX(`postid`)'])) {
            // Get the next post id
            $next_id = $res[0]['MAX(`postid`)'] + 1;
        }
        
        $max_pad = 5;
        // Generate the image name
        $img_name = str_pad(strval($next_id), $max_pad, '0', STR_PAD_LEFT);

        // Upload the image
        $path = FileManager::Upload($_FILES[$id_img], Post::$img_storage, $img_name);
        if ($path) {
            if (!strcmp($img, $path)) {
                // Delete older image saved
                if (is_file(ROOT.$post->img)) {
                    unlink(ROOT.$post->img);
                }
            }
            $img = $path;
        }
        else {
            $cfg->ShowAlert(Post::$upload_fail_msg);
        }
    }
    else {
        if (is_file(ROOT.$post->img)) {
            $img = $post->img;
        }
    }

    $visible = 0;
    if (isset($_POST[$id_visible]) && $_POST[$id_visible]) {
        $visible = 1;
    }

    // Edit post
    $id = $post->id;
    if ($cfg->db->updatePost($title, $text, $text, $img, $id)) {
        if ($cfg->db->changePostVisibility($visible, $id)) {
            return PostExitCode::Success;
        }
    }
    // Cannot edit the post
    return PostExitCode::ErrGeneric;
}

function PrintPostStringOrDefault($key, $default = '') {
	global $post;

    if (!empty($post)) {
        $array = get_object_vars($post);
        if (!empty($array[$key])) {
            if (!strcmp($key, 'img'))
            {
                echo SERVER_URL.$array[$key];
                return;      
            }
            echo $array[$key];
            return;
        }
    }
    echo $default;
}

function PrintIfOrDefault($string, $condition, $default = '') {
    if ($condition) {
        echo $string;
    }
    echo $default;
}

if (!empty($_POST['post'])) {
    $_REQUEST['post'] = $_POST['post'];
}
if (!empty($_POST['redirect'])) {
    $_REQUEST['redirect'] = $_POST['redirect'];
}

$edit_mode = false;
if (!empty($_REQUEST['post']) && is_numeric($_REQUEST['post'])) {
    $post = Post::CreateListFromQuery($cfg, $cfg->db->getPost($_REQUEST['post']));
    if (!empty($post)) {
        $post = $post[0];
        $edit_mode = true;
    }
}

$is_imgpresent = !empty($post) && !empty($post->img) && is_file(ROOT.$post->img);
$submit_icon = $posticons['create'];
$submit_string= $poststrings['post_create'];
if ($edit_mode) {
    $submit_string = $poststrings['post_edit'];
    $submit_icon = $posticons['edit'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Handle post data
    if ($edit_mode) {
        $code = EditPost($cfg, $post, $postids['post_title'], $postids['post_text'], $postids['post_img'], $postids['post_visibility']);
    } else {
        $code = AddNewPost($cfg, $postids['post_title'], $postids['post_text'], $postids['post_img'], $postids['post_visibility']);
    }

    $error = match ($code) {
        //PostExitCode::ErrEmptyTitle => $poststrings['missing_title'],
        PostExitCode::ErrEmptyText => $poststrings['missing_text'],
        PostExitCode::ErrGeneric => $poststrings['error_generic'],
        default => null,
    };

    if (!empty($error)) {
        $cfg->ShowAlert($error);
    }
    else if (!empty($_REQUEST['redirect'])) {
        $cfg->Redirect($_REQUEST['redirect']);
    }
}

?>