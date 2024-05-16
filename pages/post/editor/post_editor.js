let postEditPath = document.getElementsByTagName("script")[document.getElementsByTagName("script").length - 1].src;
let postEditDirectory = postEditPath.replace(postEditPath.split('/')[postEditPath.split('/').length - 1],'');

// Shows the preview image in the "Add new post" form
function CheckAttachment(idimg, idboxprev, idboxadd, idboxrm, idimgprev) {
    let previewForm = document.getElementById(idimg);
    let previewBox = document.getElementById(idboxprev);
    let formBox = document.getElementById(idboxadd);
    let rmBox = document.getElementById(idboxrm);
    let imgBox = document.getElementById(idimgprev);

    if (previewForm != null) {
        if ('files' in previewForm) {
            if (previewForm.files.length > 0) {
                let fileUploaded = previewForm.files[0] ;
                if (fileUploaded != null) {
                    imgBox.setAttribute('src', URL.createObjectURL(fileUploaded));
                    previewBox.removeAttribute('hidden');
                    formBox.setAttribute('hidden', true);
                    rmBox.removeAttribute('hidden');
                    return;
                }
            }
        }
    }
    imgBox.setAttribute('src', '#');
    previewBox.setAttribute('hidden', true);
    formBox.removeAttribute('hidden');
    rmBox.setAttribute('hidden', true);
}

function RemoveAttachedImage(idimg, idboxprev, idboxadd, idboxrm, idimgprev, postid = -1) {
    let previewForm = document.getElementById(idimg);
    previewForm.value = '';

    if (postid >= 0) {
        // Delete the older image if it's already saved
        let url = postEditDirectory + 'unlink_postimg.php?post='+postid;
        const xhttp = new XMLHttpRequest();
        xhttp.open('GET', url, true);
        xhttp.send();
    }

    CheckAttachment(idimg, idboxprev, idboxadd, idboxrm, idimgprev);
}