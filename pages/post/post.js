let postHelpPath = document.getElementsByTagName("script")[document.getElementsByTagName("script").length - 1].src;
let postHelpDirectory = postHelpPath.replace(postHelpPath.split('/')[postHelpPath.split('/').length - 1],'');

let likeOnIcon = '<i class="fa-solid fa-thumbs-up"></i>';
let likeOffIcon = '<i class="fa-regular fa-thumbs-up"></i>';
let likeHelperURL = 'post_likes.php';

let editWarningTxt = 'Vuoi modificare questo post ?';
let editHelperURL = 'editor/edit_post.php';

let deleteWarningTxt = 'Sei sicuro di voler eliminare questo post ?';
let deleteHelperURL = 'post_delete.php';

function LikeButtonOnClick(postId, userId, buttonId, counterId) {
    let button = document.getElementById(buttonId);
    let counter = document.getElementById(counterId);
    let likeCount = counter.innerHTML.charAt(0);

    if (button.innerHTML.includes(likeOffIcon)) {
        let text = button.innerHTML.substring(button.innerHTML.search(likeOffIcon) + likeOffIcon.length);
        likeCount = Number(likeCount) + 1;

        // Add like
        let url = postHelpDirectory + likeHelperURL + '?user=' + userId + '&post=' + postId +'&add=1';
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function () {
            button.innerHTML = likeOnIcon + text;
            counter.innerHTML = likeCount + counter.innerHTML.substring(1);
        }
        xhttp.open('GET', url, true);
        xhttp.send();
        return;
    }
    if (button.innerHTML.includes(likeOnIcon)) {
        let text = button.innerHTML.substring(button.innerHTML.search(likeOnIcon) + likeOnIcon.length);
        if (likeCount > 0) {
            likeCount = likeCount - 1;
        }
        
        // Remove like
        let url = postHelpDirectory + likeHelperURL + '?user=' + userId + '&post=' + postId +'&add=0';
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function () {
            button.innerHTML = likeOffIcon + text;
            counter.innerHTML = likeCount + counter.innerHTML.substring(1);
        }
        xhttp.open('GET', url, true);
        xhttp.send();
    }
}

function DeleteButtonOnClick(postid) {
    if (confirm(deleteWarningTxt)) {
        let postCard = document.getElementById('post' + postid);
        let url = postHelpDirectory + deleteHelperURL + '?post=' + postid;
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function () {
            // Remove the post from the feed
            postCard.remove();
        }
        xhttp.open('GET', url, true);
        xhttp.send();
    }
}

function EditButtonOnClick(postid, redirect) {
    if (confirm(editWarningTxt)) {
        window.location.href = postHelpDirectory + editHelperURL + '?post=' + postid + '&redirect=' + redirect;
    }
}