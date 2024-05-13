<?php 

require_once ROOT.'/init.php';

class User {

    public $id = -1;

    private $default = 'Undefined';
    private $default_pfp = '';

    private $username = 'Undefined';
    private $name = 'Undefined';
    private $surname = '';
    private $biography = '';
    private $pfp = '';
    private $password_hash = '';

    private $db;

    function __construct($userid) {
        global $cfg;
        $this->default_pfp = $cfg->GetURL('defaultpfp');
        $this->db = $cfg->db;

        $this->username = $this->default;
        $this->name = $this->default;
        $this->pfp = $this->default_pfp;

        $this->id = $userid;
    }

    public static function CreateFromUsername($db, $username) {
        $res = $db->getUserid($username);
        if (!empty($res) && count($res) == 1) {
            return new Post($res[0]['userid']);
        }
        return false;
    }

    private function UpdateRegistryFromDB() {
        $res = $this->db->getProfileRegistry($this->id);
		if (!empty($res)) {
			// First name
			if (!empty($res[0]['firstname'])) {
				$this->name = $res[0]['firstname'];
			}
			// Surname
			if (!empty($res[0]['surname'])) {
				$this->surname = $res[0]['surname'];
			}
			// Profile picture
			if (!empty($res[0]['profilePicture']) && is_file(ROOT.$res[0]['profilePicture'])) {
				$this->pfp = SERVER_URL.$res[0]['profilePicture'];
			}
            // Biography
			if (!empty($res[0]['biography'])) {
				$this->biography = $res[0]['biography'];
			}
		}
    }

    public function GetUsername() {
        $res = $this->db->getUsernameFromUserId($this->id);
        if (!empty($res) && count($res) == 1) {
            $this->username = $res[0]['username'];
        }
        return $this->username;
    }

    public function GetPasswordHash() {
        $res = $this->db->getLoginData($this->id);
        if (!empty($res) && count($res) == 1) {
            $this->password_hash = $res[0]['password'];
        }
        return false;
    }

    public function GetGeneralInfo(&$name, &$surname, &$pfp) {
        $name = $this->GetFirstname();
        $surname = $this->GetSurname();
        $pfp = $this->GetProfilePicture();
    }

    public function GetFirstname() {
        if (strcmp($this->name, $this->default)) {
            return $this->name;
        }
        $this->UpdateRegistryFromDB();
        return $this->name;
	}

    public function GetSurname() {
        if (!empty($this->surname)) {
            return $this->surname;
        }
        $this->UpdateRegistryFromDB();
        return $this->surname;
	}

    public function GetProfilePicture() {
        if (strcmp($this->pfp, $this->default_pfp)) {
            return $this->pfp;
        }
        $this->UpdateRegistryFromDB();
        return $this->pfp;
	}

    public function GetBiography() {
        if (!empty($this->biography)) {
            return $this->biography;
        }
        $this->UpdateRegistryFromDB();
        return $this->biography;
    }
}

?>