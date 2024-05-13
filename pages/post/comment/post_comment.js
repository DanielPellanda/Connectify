let commentHelpPath = document.getElementsByTagName("script")[document.getElementsByTagName("script").length - 1].src;
let commentHelpDirectory = commentHelpPath.replace(commentHelpPath.split('/')[commentHelpPath.split('/').length - 1],'');

let deleteCommWarningTxt = 'Sei sicuro di voler eliminare questo commento ?';
let deleteCommHelperURL = 'post_comment_delete.php';
let emptyCommentURL = 'post_comment_empty.php';

let counterPrefix = 'txtCount';

function DeleteCommentButtonOnClick(commentid, postid) {
    if (confirm(deleteCommWarningTxt)) {
        let commentBox = document.getElementById('listComment' + postid);
        let commentCard = document.getElementById('comment' + commentid);
        let counter = document.getElementById(counterPrefix + postid);

        let commentCount = 0;
        if (counter.innerHTML.split('-').length > 1) {
            commentCount = counter.innerHTML.split('-')[1].charAt(1);
        }
        if (commentCount > 0) {
            commentCount = commentCount - 1;
        }
        let emptyComment = '';
        let counterString = 'Commenti';
        if (commentCount == 0) {
            let emptyCommUrl = commentHelpDirectory + emptyCommentURL;
            const xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange  = function () {
                if (xhttp.readyState === 4 && xhttp.status === 200) {
                    emptyComment = this.responseText;
                    console.log(emptyComment);
                }
            }
            xhttp.open('GET', emptyCommUrl, true);
            xhttp.send();
        }
        if (commentCount == 1) {
            counterString = 'Commento';
        }
        let url = commentHelpDirectory + deleteCommHelperURL + '?comment=' + commentid;

        const xhttp = new XMLHttpRequest();
        xhttp.onload = function () {
            // Remove the comment from the post
            if (commentCount == 0) {
                commentBox.innerHTML = emptyComment;
            } else {
                commentCard.remove();
            }
            counter.innerHTML = counter.innerHTML.split('-')[0] + '-' +counter.innerHTML.split('-')[1].charAt(0) + commentCount + ' ' + counterString;
        }
        xhttp.open('GET', url, true);
        xhttp.send();
    }
}