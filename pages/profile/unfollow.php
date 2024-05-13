<?php
require_once '../../init.php';

// Controlla se il parametro user_id è stato inviato
if (!isset($_POST['userid']) && !isset($_POST['myid'])) {
  // Redirect o gestione dell'errore
  exit("Missing user ID");
}

$cfg->db->unfollow($_POST['myid'], $_POST['userid']);

// Reindirizza l'utente alla pagina del profilo dopo il follow
header("Location: profile.php?user=".$_POST['userid']);
exit;
?>