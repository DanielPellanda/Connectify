<?php
/* enumerates the different types of notification that can be sent */
enum Notification : string
{
    case Follow = 'Follow';
    case Like = 'Like';
    case Comment = 'Comment';
    case Post = 'Post';
}

class DatabaseHelper{
    private $db;

    public function __construct($servername, $username, $password, $dbname, $port){
        $this->db = new mysqli($servername, $username, $password, $dbname, $port);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }        
    }

/******************************** LOGIN AND REGISTRATION *********************************/

    public function register( $username, $password, $firstname, $surname ){
        $query = "INSERT INTO `user` (`username`, `password`, `firstname`, `surname`) VALUES (?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssss', $username, $password, $firstname, $surname);

        return $stmt->execute();
    }

    public function getUserid( $username ){
        $query = "SELECT `userid` FROM `user` WHERE `username` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUsernameFromUserId ( $userid ){
        $query = "SELECT `username` FROM `user` WHERE `userid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLoginData( $userid ){
        $query = "SELECT `password` FROM `user` WHERE `userid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProfileRegistry( $userid ){
        $query = "SELECT `firstname`, `surname`, `biography`, `profilePicture` FROM `user` WHERE `userid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
/******************** PROFILE CUSTOMIZATION *********************/

    public function changeUsername( $userid, $username ){
        $query = "UPDATE `user` SET `username` = ? WHERE `userid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $username, $userid);

        return $stmt->execute();
    }

    public function changePassword( $userid, $password ){
        $query = "UPDATE `user` SET `password` = ? WHERE `userid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $password, $userid);

        return $stmt->execute();
    }

    public function updateBiography ( $userid, $text ){
        $query = "UPDATE `user` SET `biography` = ? WHERE `userid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $text, $userid);

        return $stmt->execute();
    }

    public function updateProfilePicture( $userid, $img ){
        $query = "UPDATE `user` SET `profilePicture` = ? WHERE `userid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $img, $userid);

        return $stmt->execute();
    }

    public function updateFirstName( $userid, $firstname){
        $query = "UPDATE `user` SET `firstname` = ? WHERE `userid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $firstname, $userid);

        return $stmt->execute();
    }

    public function updateSurname( $userid, $surname ){
        $query = "UPDATE `user` SET `surname` = ? WHERE `userid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $surname, $userid);

        return $stmt->execute();
    }

/************************** FOLLOWS *****************************/

    public function getFollowedList ($userid){
        $query = "SELECT u.userid AS followed_id, u.username AS followed_username, u.profilePicture AS followed_picture
        FROM 
            `follow` f
        JOIN 
            `user` u ON f.followedid = u.userid
        WHERE 
            f.followerid = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getFollowersList ( $userid ){
        $query = "SELECT u.userid AS follower_id, u.username AS follower_username, u.profilePicture AS follower_picture
            FROM 
                `follow` f
            JOIN 
                `user` u ON f.followerid = u.userid
            WHERE 
                f.followedid = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getFollowedCount ( $userid ){
        $query = "SELECT COUNT(*) AS num_followed FROM `follow` WHERE `followerid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getFollowersCount ( $userid ){
        $query = "SELECT COUNT(*) AS num_followers FROM `follow` WHERE `followedid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function follows ( $myId, $userid ){
        $query = "SELECT EXISTS ( SELECT 1 FROM WebProj.follow
            WHERE followerid = ? AND followedid = ?
        ) AS is_following";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $myId, $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function follow ( $userid, $followed_id ){
        $query = "INSERT INTO `follow` (`followerid`, `followedid`) VALUES (?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userid, $followed_id );

        return $stmt->execute();
    }

    public function unfollow ( $userid, $followed_id ){
        $query = "DELETE FROM `follow` WHERE `followerid` = ? AND `followedid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii',$userid, $followed_id);
        $stmt->execute();
        var_dump($stmt->error);
        return true;
    }

/************************** POSTS *****************************/

    public function getPostsFromUser ( $myId, $targetId ){
        $query = "SELECT * FROM `post` p WHERE 
        p.user = ? AND 
        (
            (? = ? 
                OR ? IN
                (
                    SELECT
                        followerid 
                    FROM 
                        `follow` 
                    WHERE
                        followedid = ?
                )
            )
            OR 
            (? NOT IN 
                (
                    SELECT 
                        followerid 
                    FROM 
                        `follow`
                    WHERE 
                        followedid = ?
                ) 
                AND p.is_public = 1
            )
        )
        ORDER BY
            p.date DESC ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiiiiii',$targetId,$myId,$targetId,$myId,$targetId,$myId,$targetId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPostMaxId (){
        $query = "SELECT MAX(`postid`) FROM `post`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPost ( $postid ){
        $query = "SELECT * FROM `post` WHERE `postid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i',$postid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPostTitle ( $postid ){
        $query = "SELECT `title` FROM `post` WHERE `postid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i',$postid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function newPost ( $postid, $title, $text, $postpreview, $postimg, $userid, $is_public ){
        $query = "INSERT INTO `post` (`postid`, `title`, `text`, `postpreview`, `postimg`, `user`, `is_public`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('issssii', $postid, $title, $text, $postpreview, $postimg, $userid, $is_public);
        $stmt->execute();

        return $stmt->insert_id;
    }

    public function updatePost( $title, $text, $postpreview, $postimg, $postid ){
        $query = "UPDATE `post` SET `title` = ?, `text` = ?, `postpreview` = ?, `postimg` = ? WHERE `postid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssssi',$title, $text, $postpreview, $postimg, $postid );

        return $stmt->execute();
    }

    public function changePostVisibility( $is_public, $postid ){
        $query = "UPDATE `post` SET `is_public` = ? WHERE `postid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $is_public, $postid );

        return $stmt->execute();
    }

    public function getFollowedPostFeed ($userid){
        $query = " SELECT * FROM `post` p WHERE  p.user = ? OR p.user IN
            (
                SELECT 
                    followedid
                FROM 
                    `follow`
                WHERE 
                    followerid = ?
            ) 
        ORDER BY
           p.date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userid, $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMixedPostFeed ($userid){
        $query = " SELECT * FROM `post` p WHERE 
            p.user = ? OR
            (p.is_public = 1 OR p.user IN 
                (
                    SELECT 
                        followedid
                    FROM 
                        `follow`
                    WHERE 
                        followerid = ?
                )
            )
        ORDER BY 
            p.date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userid, $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function deletePost( $postid ){
        $query = "DELETE FROM `post` WHERE `postid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $postid);

        return $stmt->execute();
    }

/************************** COMMENTS *****************************/

    public function newPostComment ( $userid, $postid, $text ){
        $query = "INSERT INTO `comment` (`userid`, `postid`, `text`) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iis', $userid, $postid, $text);
        $stmt->execute();

        return $stmt->insert_id;
    }

    public function updateComment( $commentid, $text ){
        $query = "UPDATE `comment` SET `text` = ? WHERE `commentid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $text, $commentid);

        return $stmt->execute();
    }

    public function deletePostComment( $commentid ){
        $query = "DELETE FROM `comment` WHERE `commentid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i',  $commentid);

        return $stmt->execute();
    }

    public function getCommentbyPost ( $postid ){
        $query = "SELECT * FROM `comment` WHERE `postid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $postid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCommentTextbyId ( $commentid ){
        $query = "SELECT `text` FROM `comment` WHERE `commentid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $commentid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

/************************** LIKES *****************************/

    public function getLikesList ( $postid ){
        $query = "SELECT `userid`, `date` FROM `like` WHERE `postid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $postid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLikesCount ( $postid ){
        $query = "SELECT COUNT(*) FROM `like` WHERE `postid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $postid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function like ($userid, $postid){
        $query = "INSERT INTO `like` (`userid`, `postid`) VALUES (?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userid, $postid );

        return $stmt->execute();
    }

    public function removeLike ($userid, $postid){
        $query = "DELETE FROM `like` WHERE `userid` = ? AND `postid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii',$userid, $postid);
        $stmt->execute();
        var_dump($stmt->error);
        return true;
    }

/************************** NOTIFICATIONS *****************************/

    public function notifyPost ( $senderid, $receiverid, $postid ){
        $type = Notification::Post->value ;
        $query = "INSERT INTO `notification` (`senderid`, `receiverid`, `postid`, `type`) VALUES (?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiis', $senderid, $receiverid, $postid, $type );

        return $stmt->execute();

    }

    public function notifyComment ( $senderid, $receiverid, $postid, $commentid){
        $type = Notification::Comment->value ;
        $query = "INSERT INTO `notification` (`senderid`, `receiverid`, `postid`, `commentid`, `type`) VALUES (?,?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiiis', $senderid, $receiverid, $postid, $commentid, $type );

        return $stmt->execute();
    }

    public function notifyLike ( $senderid, $receiverid, $postid){
        $type = Notification::Like->value ;
        $query = "INSERT INTO `notification` (`senderid`, `receiverid`, `postid`, `type`) VALUES (?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiis', $senderid, $receiverid, $postid, $type );

        return $stmt->execute();
    }

    public function notifyFollow ( $senderid, $receiverid ){
        $type = Notification::Follow->value ;
        $query = "INSERT INTO `notification` (`senderid`, `receiverid`, `type`) VALUES (?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iis', $senderid, $receiverid, $type );

        return $stmt->execute();
    }

    public function getNotificationList ( $receiverid ){
        $query = "SELECT * FROM `notification` 
        WHERE `receiverid` = ? AND `visualized` = 0 ORDER BY `date` DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $receiverid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getReadNotificationList ( $receiverid ){
        $query = "SELECT * FROM `notification` 
        WHERE `receiverid` = ? AND `visualized` = 1 ORDER BY `date` DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $receiverid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getNotificationCount ( $receiverid ){
        $query = "SELECT COUNT(*) AS num_notifications FROM `notification` 
        WHERE `receiverid` = ? AND `visualized` = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $receiverid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getReadNotificationCount ( $receiverid ){
        $query = "SELECT COUNT(*) AS num_notifications FROM `notification` 
        WHERE `receiverid` = ? AND `visualized` = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $receiverid);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function visualizeNotification ( $notificationid ){
        $query = "UPDATE `notification` SET `visualized` = 1 WHERE `notificationid` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $notificationid);

        return $stmt->execute();
    }
}
?>